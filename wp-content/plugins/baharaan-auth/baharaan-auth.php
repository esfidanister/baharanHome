<?php
/**
 * Plugin Name: بهاران هوم — ورود و عضویت
 * Description: تجربه ورود و عضویت فروشگاه لوازم خانگی بهاران هوم؛ موبایل، ایمیل، رمز عبور و تنظیمات مدیریت.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: Baharaan Home
 * Text Domain: baharaan-auth
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BHA_FILE', __FILE__);
define('BHA_DIR', plugin_dir_path(__FILE__));
define('BHA_URL', plugin_dir_url(__FILE__));
define('BHA_VERSION', '1.0.5');

require_once BHA_DIR . 'includes/settings.php';
require_once BHA_DIR . 'includes/security.php';
require_once BHA_DIR . 'includes/otp-service.php';
require_once BHA_DIR . 'includes/controller.php';
require_once BHA_DIR . 'includes/frontend.php';
require_once BHA_DIR . 'includes/admin-login.php';

register_activation_hook(__FILE__, function () {
    if (!get_option('bha_settings')) {
        add_option('bha_settings', bha_default_settings(), '', false);
    }
});
