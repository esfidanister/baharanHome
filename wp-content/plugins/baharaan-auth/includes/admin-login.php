<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('login_enqueue_scripts', function () {
    wp_enqueue_style('bha-vazir', 'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@500;700;800&display=swap', [], null);
    wp_enqueue_style('bha-admin-login', BHA_URL . 'assets/admin-login.css', [], BHA_VERSION);
}, 100);

add_filter('login_message', function ($message) {
    $action = isset($_GET['action']) ? sanitize_key(wp_unslash($_GET['action'])) : 'login';
    if ($action !== 'login' && $action !== '') {
        return $message;
    }
    return '<p class="bha-admin-kicker">ورود به مدیریت بهاران هوم</p>' . $message;
}, 5);

add_action('login_form', function () {
    echo '<p class="bha-mfa-field"><label for="bha_totp">کد تأیید دومرحله‌ای</label><input type="text" name="bha_totp" id="bha_totp" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="اگر فعال است"><span>فقط برای مدیرانی که MFA را روشن کرده‌اند.</span></p>';
});
