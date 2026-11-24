<?php
/**
 * Plugin Name: Baharan Home Admin Dashboard
 * Description: مرکز عملیات فروش فارسی و راست‌چین بهاران هوم برای گزارش فروش، سفارش‌ها و موجودی با پشتیبانی HPOS.
 * Version: 0.3.7
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: Baharan Home
 * License: GPL-2.0-or-later
 * Text Domain: baharan-home-dashboard
 */

defined( 'ABSPATH' ) || exit;

define( 'BAHARAN_HOME_DASHBOARD_VERSION', '0.3.7' );
define( 'BAHARAN_HOME_DASHBOARD_FILE', __FILE__ );
define( 'BAHARAN_HOME_DASHBOARD_DIR', plugin_dir_path( __FILE__ ) );
define( 'BAHARAN_HOME_DASHBOARD_URL', plugin_dir_url( __FILE__ ) );

require_once BAHARAN_HOME_DASHBOARD_DIR . 'includes/class-baharan-home-dashboard.php';
require_once BAHARAN_HOME_DASHBOARD_DIR . 'includes/class-baharan-home-dashboard-data.php';
require_once BAHARAN_HOME_DASHBOARD_DIR . 'includes/class-baharan-home-dashboard-settings.php';

Baharan_Home_Dashboard::instance();

