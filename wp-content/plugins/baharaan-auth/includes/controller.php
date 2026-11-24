<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_nopriv_bha_auth', 'bha_auth_request');
add_action('wp_ajax_bha_auth', 'bha_auth_request');

function bha_auth_fail($error) {
    $status = 400;
    $data = ['message' => $error->get_error_message(), 'code' => $error->get_error_code()];
    if ($error->get_error_code() === 'limited') {
        $status = 429;
    }
    wp_send_json_error($data, $status);
}

function bha_auth_request() {
    if (!check_ajax_referer('bha_auth', 'nonce', false)) {
        wp_send_json_error(['message' => 'نشست فرم منقضی شده است. صفحه را تازه کنید.', 'code' => 'nonce'], 403);
    }
    if (!empty($_POST['company'])) {
        wp_send_json_success(['message' => 'اگر اطلاعات درست باشد، ادامه مسیر نمایش داده می‌شود.', 'step' => 'identify']);
    }
    $intent = sanitize_key(wp_unslash($_POST['intent'] ?? ''));
    if ($intent === 'profile') {
        bha_handle_profile();
    }
    if (is_user_logged_in() && $intent !== 'profile') {
        wp_send_json_success(['redirect' => bha_safe_redirect(wp_unslash($_POST['redirect_to'] ?? ''))]);
    }
    if ($intent === 'send') {
        bha_handle_send();
    } elseif ($intent === 'verify') {
        bha_handle_verify();
    } elseif ($intent === 'password') {
        bha_handle_password();
    } elseif ($intent === 'reset') {
        bha_handle_reset();
    }
    wp_send_json_error(['message' => 'درخواست شناخته نشد.', 'code' => 'intent'], 400);
}

function bha_request_channel() {
    $settings = bha_settings();
    $channel = sanitize_key(wp_unslash($_POST['channel'] ?? ''));
    if ($channel === 'mobile' && empty($settings['mobile_login'])) {
        return new WP_Error('closed', 'ورود با موبایل خاموش است.');
    }
    if ($channel === 'email' && empty($settings['email_login'])) {
        return new WP_Error('closed', 'ورود با ایمیل خاموش است.');
    }
    if (!in_array($channel, ['mobile', 'email'], true)) {
        return new WP_Error('channel', 'روش ورود را دوباره انتخاب کنید.');
    }
    $raw = isset($_POST['identifier']) ? wp_unslash($_POST['identifier']) : '';
    if ($channel === 'mobile') {
        $identifier = bha_mobile($raw);
        if ($identifier === '') {
            return new WP_Error('identifier', 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد.');
        }
        return ['channel' => $channel, 'identifier' => $identifier, 'masked' => bha_mask_mobile($identifier)];
    }
    $email = sanitize_email($raw);
    if (!is_email($email)) {
        return new WP_Error('identifier', 'یک نشانی ایمیل معتبر وارد کنید.');
    }
    return ['channel' => $channel, 'identifier' => $email, 'masked' => bha_mask_email($email)];
}

function bha_handle_send() {
    $parsed = bha_request_channel();
    if (is_wp_error($parsed)) {
        bha_auth_fail($parsed);
    }
    $issued = bha_issue_code($parsed['channel'], $parsed['identifier']);
    if (is_wp_error($issued)) {
        bha_auth_fail($issued);
    }
    wp_send_json_success([
        'step' => 'code',
        'masked' => $parsed['masked'],
        'message' => $issued['message'],
        'resend_in' => (int) $issued['resend_in'],
    ]);
}

function bha_finish_login($user, $created) {
    $redirect = bha_safe_redirect(isset($_POST['redirect_to']) ? wp_unslash($_POST['redirect_to']) : '');
    if ($created || get_user_meta($user->ID, '_bha_needs_profile', true)) {
        $redirect = add_query_arg('bha_profile', '1', $redirect);
    }
    wp_send_json_success([
        'step' => 'done',
        'created' => $created,
        'redirect' => $redirect,
        'message' => $created ? 'حساب شما ساخته شد و وارد شدید.' : 'ورود انجام شد.',
    ]);
}

function bha_create_customer($login, $email, $phone, $display) {
    if (empty(bha_settings()['allow_register'])) {
        return new WP_Error('closed', 'ساخت حساب جدید الان بسته است. اگر حساب دارید با رمز عبور وارد شوید.');
    }
    $role = get_role('customer') ? 'customer' : 'subscriber';
    $user_id = wp_insert_user([
        'user_login' => $login,
        'user_pass' => wp_generate_password(32, true, true),
        'user_email' => $email,
        'display_name' => $display,
        'role' => $role,
    ]);
    if (is_wp_error($user_id)) {
        bha_log('register_fail');
        return new WP_Error('register', 'ساخت حساب انجام نشد. کمی بعد دوباره تلاش کنید.');
    }
    if ($phone !== '') {
        update_user_meta($user_id, 'billing_phone', $phone);
        update_user_meta($user_id, '_bha_phone', $phone);
    }
    update_user_meta($user_id, '_bha_needs_profile', 1);
    if (function_exists('wc_create_new_customer') || function_exists('WC')) {
        do_action('woocommerce_created_customer', $user_id, ['user_email' => $email], true);
    }
    $user = get_user_by('id', $user_id);
    return $user instanceof WP_User ? $user : new WP_Error('register', 'ساخت حساب انجام نشد.');
}

function bha_handle_verify() {
    if (!bha_rate_hit('verify', 30, HOUR_IN_SECONDS)) {
        bha_auth_fail(new WP_Error('limited', 'تعداد بررسی کد از این اتصال زیاد شده است. یک ساعت بعد دوباره تلاش کنید.'));
    }
    $parsed = bha_request_channel();
    if (is_wp_error($parsed)) {
        bha_auth_fail($parsed);
    }
    $checked = bha_check_code($parsed['channel'], $parsed['identifier'], wp_unslash($_POST['code'] ?? ''));
    if (is_wp_error($checked)) {
        bha_auth_fail($checked);
    }
    if ($parsed['channel'] === 'mobile') {
        $users = bha_users_for_mobile($parsed['identifier']);
    } else {
        $found = get_user_by('email', $parsed['identifier']);
        $users = $found instanceof WP_User ? [$found] : [];
    }
    if (count($users) > 1) {
        bha_log('otp_ambiguous');
        bha_auth_fail(new WP_Error('ambiguous', 'این مشخصات به بیش از یک حساب وصل است. برای ورود با پشتیبانی فروشگاه تماس بگیرید.'));
    }
    $user = $users[0] ?? null;
    if ($user && bha_privileged($user)) {
        bha_log('otp_staff_blocked');
        bha_auth_fail(new WP_Error('staff', 'این حساب با کد فروشگاه وارد نمی‌شود. از صفحه مدیریت و رمز عبور استفاده کنید.'));
    }
    $created = false;
    if (!$user) {
        if ($parsed['channel'] === 'mobile') {
            $hash = substr(hash('sha256', $parsed['identifier'] . wp_salt('auth')), 0, 12);
            $user = bha_create_customer($parsed['identifier'], 'p' . $hash . '@customers.invalid', $parsed['identifier'], 'مشتری بهاران');
        } else {
            $local = sanitize_user(strstr($parsed['identifier'], '@', true) ?: 'customer', true);
            if ($local === '') {
                $local = 'customer';
            }
            $login = $local;
            $suffix = 1;
            while (username_exists($login)) {
                $login = $local . $suffix;
                $suffix++;
            }
            $user = bha_create_customer($login, $parsed['identifier'], '', $local);
        }
        if (is_wp_error($user)) {
            bha_auth_fail($user);
        }
        $created = true;
    } elseif ($parsed['channel'] === 'mobile') {
        update_user_meta($user->ID, 'billing_phone', $parsed['identifier']);
        update_user_meta($user->ID, '_bha_phone', $parsed['identifier']);
    }
    bha_login_customer($user, $created);
    bha_finish_login($user, $created);
}

function bha_handle_password() {
    if (empty(bha_settings()['password_login'])) {
        bha_auth_fail(new WP_Error('closed', 'ورود با رمز عبور خاموش است.'));
    }
    if (!bha_rate_hit('password', 8, 15 * MINUTE_IN_SECONDS)) {
        bha_log('password_limited');
        bha_auth_fail(new WP_Error('limited', 'تلاش‌های ورود زیاد شده است. ۱۵ دقیقه بعد دوباره تلاش کنید.'));
    }
    $login = sanitize_text_field(wp_unslash($_POST['login'] ?? ''));
    $password = (string) wp_unslash($_POST['password'] ?? '');
    if ($login === '' || $password === '') {
        bha_auth_fail(new WP_Error('fields', 'شناسه و رمز عبور را وارد کنید.'));
    }
    $username = $login;
    if (is_email($login)) {
        $by_email = get_user_by('email', $login);
        $username = $by_email instanceof WP_User ? $by_email->user_login : $login;
    } else {
        $mobile = bha_mobile($login);
        if ($mobile !== '') {
            $matches = bha_users_for_mobile($mobile);
            if (count($matches) === 1) {
                $username = $matches[0]->user_login;
            } else {
                $username = $mobile;
            }
        }
    }
    $user = wp_signon([
        'user_login' => $username,
        'user_password' => $password,
        'remember' => !empty($_POST['remember']),
    ], is_ssl());
    if (is_wp_error($user)) {
        bha_log('password_fail');
        $code = $user->get_error_code();
        if ($code === 'bha_mfa' || $code === 'bh_login_challenge') {
            bha_auth_fail($user);
        }
        bha_auth_fail(new WP_Error('credentials', 'شناسه یا رمز عبور نادرست است.'));
    }
    if (function_exists('WC') && WC()->session && is_callable([WC()->session, 'set_customer_session_cookie'])) {
        WC()->session->set_customer_session_cookie(true);
    }
    bha_log('password_login');
    bha_finish_login($user, false);
}

function bha_handle_reset() {
    $email = sanitize_email(wp_unslash($_POST['identifier'] ?? ''));
    if (!is_email($email)) {
        bha_auth_fail(new WP_Error('identifier', 'برای بازیابی رمز، ایمیل حساب را وارد کنید.'));
    }
    if (!bha_rate_hit('reset', 5, HOUR_IN_SECONDS)) {
        bha_auth_fail(new WP_Error('limited', 'درخواست بازیابی از این اتصال زیاد شده است. یک ساعت بعد دوباره تلاش کنید.'));
    }
    if (get_user_by('email', $email)) {
        retrieve_password($email);
        bha_log('reset_sent');
    } else {
        bha_log('reset_unknown');
    }
    wp_send_json_success([
        'step' => 'reset',
        'message' => 'اگر حسابی با این ایمیل وجود داشته باشد، لینک ساخت رمز جدید فرستاده می‌شود.',
    ]);
}

function bha_handle_profile() {
    if (!is_user_logged_in()) {
        bha_auth_fail(new WP_Error('auth', 'برای تکمیل مشخصات ابتدا وارد شوید.'));
    }
    $user = wp_get_current_user();
    if (bha_privileged($user)) {
        bha_auth_fail(new WP_Error('staff', 'این فرم فقط برای تکمیل حساب مشتری است.'));
    }
    $first = sanitize_text_field(wp_unslash($_POST['first_name'] ?? ''));
    $last = sanitize_text_field(wp_unslash($_POST['last_name'] ?? ''));
    if ($first === '') {
        bha_auth_fail(new WP_Error('name', 'نام را وارد کنید.'));
    }
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $update = [
        'ID' => $user->ID,
        'first_name' => $first,
        'last_name' => $last,
        'display_name' => trim($first . ' ' . $last),
    ];
    if ($email !== '') {
        if (!is_email($email)) {
            bha_auth_fail(new WP_Error('email', 'ایمیل معتبر نیست.'));
        }
        $owner = email_exists($email);
        if ($owner && (int) $owner !== (int) $user->ID) {
            bha_auth_fail(new WP_Error('email', 'این ایمیل برای حساب دیگری ثبت شده است. ایمیل دیگری وارد کنید.'));
        }
        $update['user_email'] = $email;
        update_user_meta($user->ID, 'billing_email', $email);
    }
    $result = wp_update_user($update);
    if (is_wp_error($result)) {
        bha_auth_fail(new WP_Error('profile', 'ذخیره مشخصات انجام نشد.'));
    }
    update_user_meta($user->ID, 'billing_first_name', $first);
    update_user_meta($user->ID, 'billing_last_name', $last);
    delete_user_meta($user->ID, '_bha_needs_profile');
    bha_log('profile_completed');
    wp_send_json_success([
        'message' => 'مشخصات حساب ذخیره شد.',
        'redirect' => bha_safe_redirect(''),
    ]);
}
