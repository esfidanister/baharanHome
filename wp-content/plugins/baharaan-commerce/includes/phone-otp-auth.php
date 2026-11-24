<?php
/**
 * ورود و ثبت‌نام با شماره موبایل و کد یک‌بارمصرف پیامکی (api.ir SmsOTP).
 * کد فقط پیامک می‌شود، در گذرا به‌صورت هش می‌ماند و بعد از مصرف حذف می‌شود.
 */
if (!defined('ABSPATH')) {
    exit;
}

const BHC_OTP_TTL = 120;

function bhc_otp_token_ready() {
    return defined('APIIR_TOKEN') && is_string(APIIR_TOKEN) && APIIR_TOKEN !== '' && APIIR_TOKEN !== 'YOUR_API_TOKEN';
}

function bhc_otp_mobile($raw) {
    $raw = strtr((string) $raw, ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']);
    $mobile = preg_replace('/\D+/', '', $raw);
    if (strpos($mobile, '98') === 0 && strlen($mobile) === 12) {
        $mobile = '0' . substr($mobile, 2);
    }
    return preg_match('/^09\d{9}$/', $mobile) ? $mobile : '';
}

function bhc_otp_account_url() {
    if (function_exists('wc_get_page_permalink')) {
        $url = wc_get_page_permalink('myaccount');
        if ($url) {
            return $url;
        }
    }
    return wp_login_url();
}

function bhc_otp_redirect($args) {
    wp_safe_redirect(add_query_arg($args, bhc_otp_account_url()));
    exit;
}

function bhc_otp_notice_from_query() {
    $code = isset($_GET['bhc_otp']) ? sanitize_key(wp_unslash($_GET['bhc_otp'])) : '';
    $messages = [
        'sent' => 'کد ورود پیامک شد. تا دو دقیقه معتبر است.',
        'bad-mobile' => 'شماره موبایل باید با ۰۹ و ۱۱ رقم باشد.',
        'wait' => 'کد قبلی هنوز معتبر است. همان را وارد کنید یا تا پایان دو دقیقه صبر کنید.',
        'limit' => 'تعداد درخواست از این اتصال زیاد شده است. یک ساعت بعد دوباره تلاش کنید.',
        'no-token' => 'کلید api.ir تنظیم نشده است. مدیر سایت باید APIIR_TOKEN را در wp-config.php بگذارد.',
        'send-fail' => 'ارسال پیامک انجام نشد. دوباره درخواست ندهید تا پیام خطا را ببینید.',
        'bad-code' => 'کد نادرست یا منقضی شده است.',
        'locked' => 'تلاش‌های این کد تمام شد. دو دقیقه بعد کد تازه بگیرید.',
        'ambiguous' => 'این شماره به بیش از یک حساب وصل است. برای ورود با پشتیبانی فروشگاه تماس بگیرید.',
        'staff' => 'این حساب با کد پیامکی وارد نمی‌شود. از صفحه مدیریت و رمز عبور استفاده کنید.',
    ];
    if ($code === '' || !isset($messages[$code])) {
        return;
    }
    $detail = get_transient('bhc_otp_notice_' . hash('sha256', bhc_otp_client_key()));
    $text = $messages[$code];
    if ($code === 'send-fail' && is_string($detail) && $detail !== '') {
        $text = $detail;
    }
    echo '<div class="bhc-otp-notice" role="status">' . esc_html($text) . '</div>';
}

function bhc_otp_client_key() {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '0';
    return hash('sha256', $ip . '|' . wp_salt('auth'));
}

function bhc_otp_enabled() {
    $settings = function_exists('bhc_apiir_settings') ? bhc_apiir_settings() : ['phone_login' => true];
    return !empty($settings['phone_login']);
}

function bhc_otp_form() {
    static $shown = false;
    if ($shown || is_user_logged_in() || !bhc_otp_enabled()) {
        return;
    }
    $shown = true;
    $mobile = isset($_GET['mobile']) ? bhc_otp_mobile(wp_unslash($_GET['mobile'])) : '';
    $step = isset($_GET['bhc_otp']) && sanitize_key(wp_unslash($_GET['bhc_otp'])) === 'sent' && $mobile !== '';
    echo '<section class="bhc-otp" aria-labelledby="bhc-otp-title">';
    echo '<h2 id="bhc-otp-title">ورود یا ثبت‌نام با شماره موبایل</h2>';
    echo '<p>کد یک‌بارمصرف پیامک می‌شود. اگر شماره تازه باشد، حساب ساخته می‌شود.</p>';
    bhc_otp_notice_from_query();
    if (!$step) {
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="bhc_phone_otp_send">';
        wp_nonce_field('bhc_phone_otp_send');
        echo '<label for="bhc-otp-mobile">شماره موبایل</label>';
        echo '<input id="bhc-otp-mobile" name="mobile" type="tel" inputmode="numeric" autocomplete="tel" placeholder="09120000000" required pattern="09[0-9]{9}">';
        echo '<button type="submit">دریافت کد</button></form>';
    } else {
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="bhc_phone_otp_verify">';
        echo '<input type="hidden" name="mobile" value="' . esc_attr($mobile) . '">';
        wp_nonce_field('bhc_phone_otp_verify');
        echo '<label for="bhc-otp-code">کد پیامک‌شده برای ' . esc_html($mobile) . '</label>';
        echo '<input id="bhc-otp-code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required pattern="[0-9]{6}" maxlength="6">';
        echo '<button type="submit">ورود</button></form>';
        echo '<p><a href="' . esc_url(bhc_otp_account_url()) . '">شماره را عوض می‌کنم</a></p>';
    }
    echo '</section>';
    echo '<style>.bhc-otp{margin:0 0 22px;padding:18px 18px 8px;border:1px solid #d5e5f4;border-radius:16px;background:#f7fbff}.bhc-otp h2{margin:0 0 6px;font-size:1.15rem}.bhc-otp p{margin:0 0 12px;color:#4d647c}.bhc-otp label{display:block;margin-bottom:6px;font-weight:700}.bhc-otp input{width:100%;max-width:320px;margin-bottom:12px}.bhc-otp button{min-height:42px;padding:0 18px;border:0;border-radius:10px;background:#087f72;color:#fff;font-weight:700;cursor:pointer}.bhc-otp-notice{margin:0 0 12px;padding:10px 12px;border-radius:10px;background:#fff;border:1px solid #d5e5f4}</style>';
}

add_action('woocommerce_before_customer_login_form', 'bhc_otp_form');

function bhc_otp_send() {
    if (!bhc_otp_enabled()) {
        wp_die('ورود پیامکی خاموش است.', 403);
    }
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'bhc_phone_otp_send')) {
        wp_die('درخواست نامعتبر است.', 403);
    }
    $mobile = bhc_otp_mobile(wp_unslash($_POST['mobile'] ?? ''));
    if ($mobile === '') {
        bhc_otp_redirect(['bhc_otp' => 'bad-mobile']);
    }
    if (get_transient('bhc_otp_code_' . $mobile) !== false) {
        bhc_otp_redirect(['bhc_otp' => 'wait', 'mobile' => $mobile]);
    }
    $ip_key = 'bhc_otp_ip_' . bhc_otp_client_key();
    $hits = (int) get_transient($ip_key);
    if ($hits >= 8) {
        bhc_otp_redirect(['bhc_otp' => 'limit']);
    }
    if (!bhc_otp_token_ready() || !function_exists('apiir_sms_otp')) {
        bhc_otp_redirect(['bhc_otp' => 'no-token']);
    }
    $code = (string) random_int(100000, 999999);
    $result = apiir_sms_otp($code, $mobile, 1);
    if (empty($result['success'])) {
        set_transient('bhc_otp_notice_' . hash('sha256', bhc_otp_client_key()), 'ارسال پیامک ناموفق بود: ' . (string) ($result['message'] ?? ''), MINUTE_IN_SECONDS);
        bhc_otp_redirect(['bhc_otp' => 'send-fail']);
    }
    set_transient($ip_key, $hits + 1, HOUR_IN_SECONDS);
    set_transient('bhc_otp_code_' . $mobile, [
        'hash' => hash_hmac('sha256', $code, wp_salt('auth')),
        'tries' => 0,
        'exp' => time() + BHC_OTP_TTL,
    ], BHC_OTP_TTL);
    bhc_otp_redirect(['bhc_otp' => 'sent', 'mobile' => $mobile]);
}

add_action('admin_post_nopriv_bhc_phone_otp_send', 'bhc_otp_send');
add_action('admin_post_bhc_phone_otp_send', 'bhc_otp_send');

function bhc_otp_privileged($user) {
    return $user instanceof WP_User && (
        user_can($user, 'manage_options')
        || user_can($user, 'manage_woocommerce')
        || user_can($user, 'edit_users')
        || user_can($user, 'edit_others_posts')
    );
}

function bhc_otp_users_for_mobile($mobile) {
    $ids = [];
    $by_login = get_user_by('login', $mobile);
    if ($by_login instanceof WP_User) {
        $ids[] = (int) $by_login->ID;
    }
    $found = get_users([
        'meta_key' => 'billing_phone',
        'meta_value' => $mobile,
        'number' => 5,
        'fields' => 'ID',
        'count_total' => false,
    ]);
    foreach ((array) $found as $id) {
        $ids[] = (int) $id;
    }
    $ids = array_values(array_unique(array_filter($ids)));
    $users = [];
    foreach ($ids as $id) {
        $user = get_user_by('id', $id);
        if ($user instanceof WP_User) {
            $users[] = $user;
        }
    }
    return $users;
}

function bhc_otp_user_for_mobile($mobile) {
    $users = bhc_otp_users_for_mobile($mobile);
    return $users[0] ?? null;
}

function bhc_otp_verify() {
    if (!bhc_otp_enabled()) {
        wp_die('ورود پیامکی خاموش است.', 403);
    }
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'bhc_phone_otp_verify')) {
        wp_die('درخواست نامعتبر است.', 403);
    }
    $mobile = bhc_otp_mobile(wp_unslash($_POST['mobile'] ?? ''));
    $input = preg_replace('/\D+/', '', (string) wp_unslash($_POST['code'] ?? ''));
    $key = 'bhc_otp_code_' . $mobile;
    $saved = $mobile !== '' ? get_transient($key) : false;
    $hash = is_array($saved) ? (string) ($saved['hash'] ?? '') : '';
    $tries = is_array($saved) ? (int) ($saved['tries'] ?? 0) : 0;
    if ($hash === '' || $tries >= 5 || !hash_equals($hash, hash_hmac('sha256', $input, wp_salt('auth')))) {
        if (is_array($saved)) {
            $saved['tries'] = $tries + 1;
            if ($saved['tries'] >= 5) {
                delete_transient($key);
                bhc_otp_redirect(['bhc_otp' => 'locked', 'mobile' => $mobile]);
            }
            $left = max(1, (int) ($saved['exp'] ?? (time() + BHC_OTP_TTL)) - time());
            set_transient($key, $saved, $left);
        }
        bhc_otp_redirect(['bhc_otp' => 'bad-code', 'mobile' => $mobile]);
    }
    delete_transient($key);
    $matches = bhc_otp_users_for_mobile($mobile);
    if (count($matches) > 1) {
        bhc_otp_redirect(['bhc_otp' => 'ambiguous', 'mobile' => $mobile]);
    }
    $user = $matches[0] ?? null;
    if ($user && bhc_otp_privileged($user)) {
        bhc_otp_redirect(['bhc_otp' => 'staff', 'mobile' => $mobile]);
    }
    if (!$user) {
        $user_id = wp_insert_user([
            'user_login' => $mobile,
            'user_pass' => wp_generate_password(32, true, true),
            'user_email' => $mobile . '@users.baharaan.invalid',
            'display_name' => $mobile,
            'role' => get_role('customer') ? 'customer' : get_option('default_role', 'subscriber'),
        ]);
        if (is_wp_error($user_id)) {
            wp_die(esc_html($user_id->get_error_message()));
        }
        $user = get_user_by('id', $user_id);
        update_user_meta($user_id, 'billing_phone', $mobile);
    } else {
        update_user_meta($user->ID, 'billing_phone', $mobile);
    }
    if (function_exists('wc_set_customer_auth_cookie')) {
        wc_set_customer_auth_cookie($user->ID);
    } else {
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);
    }
    do_action('wp_login', $user->user_login, $user);
    wp_safe_redirect(bhc_otp_account_url());
    exit;
}

add_action('admin_post_nopriv_bhc_phone_otp_verify', 'bhc_otp_verify');
add_action('admin_post_bhc_phone_otp_verify', 'bhc_otp_verify');

add_action('admin_notices', function () {
    if (!current_user_can('manage_options') || bhc_otp_token_ready()) {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || !in_array($screen->id, ['toplevel_page_baharaan-commerce', 'dashboard'], true)) {
        return;
    }
    echo '<div class="notice notice-warning"><p>ورود پیامکی کلید api.ir ندارد. از منوی <a href="' . esc_url(admin_url('admin.php?page=bhc-apiir')) . '">سرویس api.ir</a> کلید را ذخیره کنید.</p></div>';
});
