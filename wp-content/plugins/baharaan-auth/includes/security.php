<?php
if (!defined('ABSPATH')) {
    exit;
}

function bha_digits($value) {
    $value = strtr((string) $value, [
        '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
        '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
        '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
    ]);
    return preg_replace('/\D+/', '', $value);
}

function bha_mobile($value) {
    $mobile = bha_digits($value);
    if (strpos($mobile, '98') === 0 && strlen($mobile) === 12) {
        $mobile = '0' . substr($mobile, 2);
    }
    if (strpos($mobile, '9') === 0 && strlen($mobile) === 10) {
        $mobile = '0' . $mobile;
    }
    return preg_match('/^09\d{9}$/', $mobile) ? $mobile : '';
}

function bha_client_hash() {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '0';
    return substr(hash('sha256', $ip . '|' . wp_salt('auth')), 0, 16);
}

function bha_mask_mobile($mobile) {
    if (strlen($mobile) < 8) {
        return 'شماره موبایل';
    }
    return substr($mobile, 0, 4) . '***' . substr($mobile, -4);
}

function bha_mask_email($email) {
    $parts = explode('@', $email);
    if (count($parts) !== 2) {
        return 'ایمیل';
    }
    $name = $parts[0];
    $keep = function_exists('mb_substr') ? mb_substr($name, 0, 2) : substr($name, 0, 2);
    return $keep . '***@' . $parts[1];
}

function bha_log($event) {
    $log = get_option('bha_security_log', []);
    if (!is_array($log)) {
        $log = [];
    }
    array_unshift($log, [
        't' => time(),
        'e' => sanitize_key($event),
        'ip' => bha_client_hash(),
    ]);
    update_option('bha_security_log', array_slice($log, 0, 40), false);
}

function bha_rate_key($bucket) {
    return 'bha_rate_' . $bucket . '_' . bha_client_hash();
}

function bha_rate_hit($bucket, $limit, $window) {
    $key = bha_rate_key($bucket);
    $hits = (int) get_transient($key);
    if ($hits >= $limit) {
        return false;
    }
    set_transient($key, $hits + 1, $window);
    return true;
}

function bha_rate_refund($bucket) {
    $key = bha_rate_key($bucket);
    $hits = (int) get_transient($key);
    if ($hits <= 1) {
        delete_transient($key);
        return;
    }
    $timeout = (int) get_option('_transient_timeout_' . $key);
    $left = max(60, $timeout - time());
    set_transient($key, $hits - 1, $left);
}

function bha_privileged($user) {
    return $user instanceof WP_User && (
        user_can($user, 'manage_options')
        || user_can($user, 'manage_woocommerce')
        || user_can($user, 'edit_users')
        || user_can($user, 'edit_others_posts')
    );
}

function bha_safe_redirect($candidate = '') {
    $settings = bha_settings();
    $account = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/');
    $fallback = $settings['redirect'] !== '' ? $settings['redirect'] : $account;
    $target = is_string($candidate) ? $candidate : '';
    if ($target === '' && function_exists('wc_get_checkout_url') && !empty($_SERVER['HTTP_REFERER'])) {
        $referer = (string) wp_unslash($_SERVER['HTTP_REFERER']);
        $checkout = wc_get_checkout_url();
        if ($checkout && strpos($referer, wp_parse_url($checkout, PHP_URL_PATH) ?: '/checkout') !== false) {
            $target = $checkout;
        }
    }
    return wp_validate_redirect($target, wp_validate_redirect($fallback, $account));
}

function bha_totp_secret() {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < 32; $i++) {
        $secret .= $alphabet[random_int(0, 31)];
    }
    return $secret;
}

function bha_base32_decode($value) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $value = strtoupper(preg_replace('/[^A-Z2-7]/', '', (string) $value));
    $bits = '';
    $length = strlen($value);
    for ($i = 0; $i < $length; $i++) {
        $pos = strpos($alphabet, $value[$i]);
        if ($pos === false) {
            continue;
        }
        $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
    }
    $binary = '';
    foreach (str_split($bits, 8) as $chunk) {
        if (strlen($chunk) === 8) {
            $binary .= chr(bindec($chunk));
        }
    }
    return $binary;
}

function bha_totp_code($secret, $time = null) {
    $counter = intdiv($time ?? time(), 30);
    $binary = pack('N*', 0) . pack('N*', $counter);
    $hash = hash_hmac('sha1', $binary, bha_base32_decode($secret), true);
    $offset = ord(substr($hash, -1)) & 0x0f;
    $piece = substr($hash, $offset, 4);
    $value = unpack('N', $piece);
    $number = ($value[1] & 0x7fffffff) % 1000000;
    return str_pad((string) $number, 6, '0', STR_PAD_LEFT);
}

function bha_totp_verify($secret, $code) {
    $code = bha_digits($code);
    if (!is_string($secret) || strlen($secret) < 16 || strlen($code) !== 6) {
        return false;
    }
    $now = time();
    for ($window = -1; $window <= 1; $window++) {
        if (hash_equals(bha_totp_code($secret, $now + ($window * 30)), $code)) {
            return true;
        }
    }
    return false;
}

add_filter('auth_cookie_expiration', function ($length, $user_id, $remember) {
    if (!$remember) {
        return $length;
    }
    $user = get_userdata((int) $user_id);
    if (!$user || user_can($user, 'manage_options') || user_can($user, 'manage_woocommerce')) {
        return $length;
    }
    return (int) bha_settings()['session_days'] * DAY_IN_SECONDS;
}, 10, 3);

add_filter('authenticate', function ($user) {
    if (is_wp_error($user) || !($user instanceof WP_User)) {
        return $user;
    }
    if (!get_user_meta($user->ID, '_bha_totp_enabled', true)) {
        return $user;
    }
    $secret = (string) get_user_meta($user->ID, '_bha_totp_secret', true);
    $code = isset($_POST['bha_totp']) ? wp_unslash($_POST['bha_totp']) : '';
    if (!bha_totp_verify($secret, $code)) {
        bha_log('admin_mfa_fail');
        return new WP_Error('bha_mfa', 'کد تأیید دومرحله‌ای نادرست است یا وارد نشده. کد برنامه تأیید هویت را همراه رمز عبور وارد کنید.');
    }
    bha_log('admin_mfa_ok');
    return $user;
}, 99);
