<?php
/**
 * Baharan Home account dashboard.
 *
 * This theme override intentionally removes WooCommerce's English default
 * greeting. The localized dashboard UI is rendered by the theme action below.
 * Keep the standard dashboard hooks so extensions remain compatible.
 *
 * @package BaharaanHome
 * @version 4.4.0-compatible
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
