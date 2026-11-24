<?php
if (!defined('ABSPATH')) {
    exit;
}

function bha_otp_key($channel, $identifier) {
    return 'bhaotp_' . substr(hash('sha256', $channel . '|' . $identifier . '|' . wp_salt('auth')), 0, 40);
}

function bha_sms_ready() {
    return function_exists('bhc_otp_token_ready') && bhc_otp_token_ready() && function_exists('apiir_sms_otp');
}

function bha_sms_providers() {
    $providers = [
        'apiir' => [
            'label' => 'api.ir — کلید فعلی فروشگاه',
            'ready' => 'bha_sms_ready',
            'send' => 'bha_sms_send_apiir',
        ],
    ];
    $filtered = apply_filters('bha_sms_providers', $providers);
    if (!is_array($filtered)) {
        return $providers;
    }
    $clean = [];
    foreach ($filtered as $id => $provider) {
        $id = sanitize_key((string) $id);
        if ($id === '' || $id === 'mock' || !is_array($provider) || empty($provider['send']) || !is_callable($provider['send'])) {
            continue;
        }
        $clean[$id] = $provider;
    }
    return $clean;
}

function bha_sms_send_apiir($code, $mobile, $template) {
    unset($template);
    $result = apiir_sms_otp($code, $mobile, 1);
    if (!empty($result['success'])) {
        return true;
    }
    $detail = (string) ($result['message'] ?? '');
    if ((int) ($result['code'] ?? 0) === 401) {
        return new WP_Error('sms_down', 'ارسال پیامک انجام نشد. کلید سرویس پیامک پذیرفته نشد. از پیشخوان، منوی سرویس api.ir، کلید تازه را بدون کلمه Bearer ذخیره کنید.');
    }
    if (stripos($detail, 'timeout') !== false || stripos($detail, 'timed out') !== false || stripos($detail, 'cURL error') !== false) {
        return new WP_Error('sms_down', 'ارسال پیامک انجام نشد. ارتباط سرور با سرویس پیامک برقرار نشد. کمی بعد دوباره تلاش کنید.');
    }
    return new WP_Error('sms_down', 'ارسال پیامک انجام نشد. کمی بعد دوباره تلاش کنید.');
}

function bha_dispatch_code($channel, $identifier, $code) {
    $settings = bha_settings();
    if ($channel === 'mobile') {
        if ($settings['sms_provider'] === 'mock') {
            bha_log('otp_mock_mobile');
            return true;
        }
        $provider = (string) $settings['sms_provider'];
        $handled = apply_filters('bha_send_login_sms', null, $code, $identifier, $provider);
        if ($handled instanceof WP_Error) {
            bha_log('sms_send_fail');
            return $handled;
        }
        if ($handled === true) {
            bha_log('sms_send_ok');
            return true;
        }
        if ($handled === false) {
            bha_log('sms_send_fail');
            return new WP_Error('sms_down', 'ارسال پیامک با سرویس انتخاب‌شده انجام نشد. کمی بعد دوباره تلاش کنید.');
        }
        $providers = bha_sms_providers();
        $send = $providers[$provider]['send'] ?? null;
        if (!is_callable($send)) {
            bha_log('sms_unavailable');
            return new WP_Error('sms_down', 'سرویس پیامک انتخاب‌شده در دسترس نیست. از بهاران هوم، ورود و عضویت، سرویس دیگری را انتخاب کنید یا افزونه‌اش را فعال کنید.');
        }
        $result = call_user_func($send, $code, $identifier, (string) $settings['sms_template']);
        if ($result instanceof WP_Error) {
            bha_log('sms_send_fail');
            return $result;
        }
        if ($result !== true) {
            bha_log('sms_send_fail');
            return new WP_Error('sms_down', 'ارسال پیامک با سرویس انتخاب‌شده انجام نشد. کمی بعد دوباره تلاش کنید.');
        }
        bha_log('sms_send_ok');
        return true;
    }
    $minutes = max(1, (int) ceil($settings['otp_ttl'] / 60));
    $body = strtr($settings['email_body'], [
        '{code}' => $code,
        '{minutes}' => (string) $minutes,
    ]);
    if ($settings['sms_provider'] === 'mock') {
        bha_log('otp_mock_email');
        return true;
    }
    $sent = wp_mail($identifier, $settings['email_subject'], $body);
    bha_log($sent ? 'email_send_ok' : 'email_send_fail');
    if (!$sent) {
        return new WP_Error('email_down', 'ارسال ایمیل انجام نشد. کمی بعد دوباره تلاش کنید یا با رمز عبور وارد شوید.');
    }
    return true;
}

function bha_issue_code($channel, $identifier) {
    $settings = bha_settings();
    $key = bha_otp_key($channel, $identifier);
    $existing = get_transient($key);
    if (is_array($existing) && !empty($existing['sent'])) {
        $wait = ((int) $existing['sent'] + (int) $settings['resend']) - time();
        if ($wait > 0) {
            return [
                'ok' => true,
                'resent' => false,
                'resend_in' => $wait,
                'message' => 'کد قبلی هنوز معتبر است. همان را وارد کنید یا تا پایان شمارش صبر کنید.',
            ];
        }
    }
    if (!bha_rate_hit('send_' . $channel, (int) $settings['send_limit'], HOUR_IN_SECONDS)) {
        bha_log('otp_rate_limited');
        return new WP_Error('limited', 'تعداد درخواست از این اتصال زیاد شده است. یک ساعت بعد دوباره تلاش کنید.');
    }
    $code = (string) random_int(100000, 999999);
    if ($settings['sms_provider'] === 'mock') {
        $forced = apply_filters('bha_testing_otp_code', $code, $channel, $identifier);
        if (preg_match('/^\d{6}$/', (string) $forced)) {
            $code = (string) $forced;
        }
    }
    $sent = bha_dispatch_code($channel, $identifier, $code);
    if (is_wp_error($sent)) {
        bha_rate_refund('send_' . $channel);
        return $sent;
    }
    set_transient($key, [
        'hash' => hash_hmac('sha256', $code, wp_salt('auth')),
        'tries' => 0,
        'exp' => time() + (int) $settings['otp_ttl'],
        'sent' => time(),
    ], (int) $settings['otp_ttl']);
    if ($settings['sms_provider'] === 'mock') {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bha-otp-debug.txt';
        file_put_contents($file, $channel . ' ' . hash('sha256', $identifier) . ' ' . $code . PHP_EOL, LOCK_EX);
    }
    return [
        'ok' => true,
        'resent' => true,
        'resend_in' => (int) $settings['resend'],
        'message' => $channel === 'mobile'
            ? 'کد تأیید پیامک شد. تا پایان اعتبار آن را وارد کنید.'
            : 'کد تأیید به ایمیل فرستاده شد. صندوق ورودی و هرزنامه را هم ببینید.',
    ];
}

function bha_check_code($channel, $identifier, $code) {
    $settings = bha_settings();
    $key = bha_otp_key($channel, $identifier);
    $saved = get_transient($key);
    $input = bha_digits($code);
    $hash = is_array($saved) ? (string) ($saved['hash'] ?? '') : '';
    $tries = is_array($saved) ? (int) ($saved['tries'] ?? 0) : 0;
    if ($hash === '') {
        bha_log('otp_expired');
        return new WP_Error('expired', 'کد منقضی شده یا هنوز درخواستی ثبت نشده است. کد تازه بگیرید.');
    }
    if ($tries >= (int) $settings['max_tries'] || !hash_equals($hash, hash_hmac('sha256', $input, wp_salt('auth')))) {
        $saved['tries'] = $tries + 1;
        if ($saved['tries'] >= (int) $settings['max_tries']) {
            delete_transient($key);
            bha_log('otp_locked');
            return new WP_Error('locked', 'تلاش‌های این کد تمام شد. کد تازه‌ای بگیرید.');
        }
        $left = max(1, (int) ($saved['exp'] ?? (time() + 60)) - time());
        set_transient($key, $saved, $left);
        bha_log('otp_bad_code');
        $remain = (int) $settings['max_tries'] - (int) $saved['tries'];
        return new WP_Error('bad_code', 'کد نادرست است. ' . $remain . ' تلاش دیگر باقی مانده.');
    }
    delete_transient($key);
    bha_log('otp_verified');
    return true;
}

function bha_users_for_mobile($mobile) {
    $ids = [];
    $by_login = get_user_by('login', $mobile);
    if ($by_login instanceof WP_User) {
        $ids[] = (int) $by_login->ID;
    }
    foreach (['billing_phone', '_bha_phone'] as $meta) {
        $found = get_users([
            'meta_key' => $meta,
            'meta_value' => $mobile,
            'number' => 5,
            'fields' => 'ID',
            'count_total' => false,
        ]);
        foreach ((array) $found as $id) {
            $ids[] = (int) $id;
        }
    }
    $users = [];
    foreach (array_unique($ids) as $id) {
        $user = get_user_by('id', $id);
        if ($user instanceof WP_User) {
            $users[] = $user;
        }
    }
    return $users;
}

function bha_login_customer($user, $created = false) {
    if (function_exists('wc_set_customer_auth_cookie')) {
        wc_set_customer_auth_cookie($user->ID);
    } else {
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);
    }
    do_action('wp_login', $user->user_login, $user);
    if (function_exists('WC') && WC()->session && is_callable([WC()->session, 'set_customer_session_cookie'])) {
        WC()->session->set_customer_session_cookie(true);
    }
    bha_log($created ? 'register_login' : 'customer_login');
}
