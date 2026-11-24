<?php
/**
 * پیشخوان تنظیم کلید api.ir. مقدار ذخیره‌شده قبل از بارگذاری SDK به ثابت‌ها می‌رسد
 * و در صفحهٔ سایت یا پاسخ مرورگر چاپ نمی‌شود.
 */
if (!defined('ABSPATH')) {
    exit;
}

const BHC_APIIR_OPTION = 'bhc_apiir';

function bhc_apiir_settings() {
    $saved = get_option(BHC_APIIR_OPTION, []);
    $saved = is_array($saved) ? $saved : [];
    return [
        'token' => isset($saved['token']) ? (string) $saved['token'] : '',
        'timeout' => isset($saved['timeout']) ? (int) $saved['timeout'] : 30,
        'sslverify' => !isset($saved['sslverify']) || !empty($saved['sslverify']),
        'phone_login' => !isset($saved['phone_login']) || !empty($saved['phone_login']),
    ];
}

function bhc_apiir_apply_constants() {
    $settings = bhc_apiir_settings();
    $GLOBALS['bhc_apiir_token_from_env'] = defined('APIIR_TOKEN');
    if (!defined('APIIR_TOKEN') && $settings['token'] !== '') {
        define('APIIR_TOKEN', $settings['token']);
    }
    if (!defined('APIIR_TIMEOUT')) {
        define('APIIR_TIMEOUT', max(5, min(120, $settings['timeout'] ?: 30)));
    }
    if (!defined('APIIR_SSL_VERIFY')) {
        define('APIIR_SSL_VERIFY', $settings['sslverify']);
    }
}

bhc_apiir_apply_constants();

function bhc_apiir_constant_locked($name) {
    $settings = bhc_apiir_settings();
    if ($name === 'APIIR_TOKEN') {
        return !empty($GLOBALS['bhc_apiir_token_from_env']);
    }
    if ($name === 'APIIR_TIMEOUT') {
        return defined('APIIR_TIMEOUT') && (int) APIIR_TIMEOUT !== max(5, min(120, $settings['timeout'] ?: 30));
    }
    if ($name === 'APIIR_SSL_VERIFY') {
        return defined('APIIR_SSL_VERIFY') && (bool) APIIR_SSL_VERIFY !== $settings['sslverify'];
    }
    return false;
}

add_action('admin_menu', function () {
    add_menu_page(
        'تنظیمات api.ir',
        'سرویس api.ir',
        'manage_options',
        'bhc-apiir',
        'bhc_apiir_settings_page',
        'dashicons-rest-api',
        59
    );
});

function bhc_apiir_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $settings = bhc_apiir_settings();
    $ready = function_exists('bhc_otp_token_ready') ? bhc_otp_token_ready() : (defined('APIIR_TOKEN') && APIIR_TOKEN !== '' && APIIR_TOKEN !== 'YOUR_API_TOKEN');
    $notice = get_transient('bhc_apiir_notice_' . get_current_user_id());
    if ($notice !== false) {
        delete_transient('bhc_apiir_notice_' . get_current_user_id());
    }
    $services = function_exists('apiir_echo') ? count(glob(dirname(__DIR__) . '/apiir/services/*.php') ?: []) : 0;
    echo '<div class="wrap">';
    echo '<h1>سرویس api.ir</h1>';
    echo '<p>کلید را از <a href="https://p.api.ir" target="_blank" rel="noopener">پنل p.api.ir</a> بسازید. ورود و ثبت‌نام پیامکی فروشگاه از همین کلید استفاده می‌کند.</p>';
    if (is_string($notice) && $notice !== '') {
        echo '<div class="notice notice-info"><p>' . esc_html($notice) . '</p></div>';
    }
    if (isset($_GET['bhc_apiir_saved'])) {
        echo '<div class="notice notice-success"><p>تنظیم‌ها ذخیره شد.</p></div>';
    }
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="bhc_apiir_save">';
    wp_nonce_field('bhc_apiir_save');
    echo '<table class="form-table"><tbody>';
    echo '<tr><th scope="row"><label for="bhc-apiir-token">کلید API</label></th><td>';
    echo '<input id="bhc-apiir-token" name="token" type="password" class="regular-text" autocomplete="new-password" placeholder="' . esc_attr($settings['token'] !== '' ? 'کلید ذخیره شده؛ برای تعویض، کلید تازه را بنویسید' : 'کلید پنل') . '">';
    echo '<p class="description">وضعیت: ' . ($ready ? 'کلید برای درخواست‌ها فعال است.' : 'کلید هنوز قابل استفاده نیست.') . '</p>';
    if (bhc_apiir_constant_locked('APIIR_TOKEN')) {
        echo '<p class="description">یک کلید در wp-config.php یا متغیر محیطی تعریف شده و بر این صفحه اولویت دارد.</p>';
    }
    echo '</td></tr>';
    echo '<tr><th scope="row"><label for="bhc-apiir-timeout">مهلت پاسخ (ثانیه)</label></th><td>';
    echo '<input id="bhc-apiir-timeout" name="timeout" type="number" min="5" max="120" value="' . esc_attr((string) max(5, min(120, $settings['timeout'] ?: 30))) . '">';
    echo '<p class="description">پیش‌فرض سرویس‌های سبک. سرویس ویدئو و پیامک گروهی مهلت بیشتر خودشان را دارند.</p>';
    if (bhc_apiir_constant_locked('APIIR_TIMEOUT')) {
        echo '<p class="description">مهلت در wp-config.php قفل شده و مقدار این صفحه روی درخواست‌ها اثر نمی‌گذارد.</p>';
    }
    echo '</td></tr>';
    echo '<tr><th scope="row">بررسی گواهی SSL</th><td><label><input type="checkbox" name="sslverify" value="1" ' . checked($settings['sslverify'], true, false) . '> گواهی s.api.ir بررسی شود</label>';
    if (bhc_apiir_constant_locked('APIIR_SSL_VERIFY')) {
        echo '<p class="description">این مقدار در wp-config.php قفل شده است.</p>';
    }
    echo '</td></tr>';
    echo '<tr><th scope="row">ورود پیامکی فروشگاه</th><td><label><input type="checkbox" name="phone_login" value="1" ' . checked($settings['phone_login'], true, false) . '> مشتری با شماره موبایل و کد یک‌بارمصرف وارد شود یا ثبت‌نام کند</label></td></tr>';
    echo '</tbody></table>';
    submit_button('ذخیره تنظیم‌ها');
    echo '</form>';
    echo '<hr><h2>آزمایش اتصال</h2>';
    echo '<p>' . esc_html((string) $services) . ' سرویس رسمی در افزونه بارگذاری شده است. آزمایش با Echo انجام می‌شود و هزینه‌ای ندارد.</p>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="bhc_apiir_test">';
    wp_nonce_field('bhc_apiir_test');
    submit_button('آزمایش کلید و اتصال', 'secondary', 'submit', false);
    echo '</form>';
    if (function_exists('bhc_apiir_console')) {
        bhc_apiir_console();
    }
    echo '</div>';
}

function bhc_apiir_save() {
    if (!current_user_can('manage_options')) {
        wp_die('دسترسی مجاز نیست');
    }
    check_admin_referer('bhc_apiir_save');
    $old = bhc_apiir_settings();
    $token = sanitize_text_field(wp_unslash($_POST['token'] ?? ''));
    $token = preg_replace('/^bearer\s+/i', '', trim($token));
    $token = preg_replace('/\s+/', '', $token);
    if ($token === '') {
        $token = $old['token'];
    }
    if (strlen($token) > 200) {
        $token = $old['token'];
    }
    $timeout = max(5, min(120, absint($_POST['timeout'] ?? 30)));
    update_option(BHC_APIIR_OPTION, [
        'token' => $token,
        'timeout' => $timeout,
        'sslverify' => !empty($_POST['sslverify']),
        'phone_login' => !empty($_POST['phone_login']),
    ], false);
    wp_safe_redirect(add_query_arg('bhc_apiir_saved', '1', admin_url('admin.php?page=bhc-apiir')));
    exit;
}

add_action('admin_post_bhc_apiir_save', 'bhc_apiir_save');

function bhc_apiir_test() {
    if (!current_user_can('manage_options')) {
        wp_die('دسترسی مجاز نیست');
    }
    check_admin_referer('bhc_apiir_test');
    if (!function_exists('apiir_echo')) {
        $message = 'SDK بارگذاری نشده است.';
    } elseif (!function_exists('bhc_otp_token_ready') || !bhc_otp_token_ready()) {
        $message = 'اول کلید را ذخیره کنید. اگر همین الان ذخیره کرده‌اید، صفحه را یک‌بار تازه کنید و دوباره آزمایش کنید.';
    } else {
        $result = apiir_echo('تست اتصال');
        $message = !empty($result['success'])
            ? 'اتصال برقرار است و کلید پذیرفته شد.'
            : 'اتصال ناموفق بود: ' . (string) ($result['message'] ?? '');
    }
    set_transient('bhc_apiir_notice_' . get_current_user_id(), $message, MINUTE_IN_SECONDS);
    wp_safe_redirect(admin_url('admin.php?page=bhc-apiir'));
    exit;
}

add_action('admin_post_bhc_apiir_test', 'bhc_apiir_test');
