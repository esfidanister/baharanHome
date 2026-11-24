<?php
defined( 'ABSPATH' ) || exit;

final class Baharan_Home_Dashboard_Settings {
	const OPTION = 'baharan_home_dashboard_settings';
	public static function init() {
		add_action( 'admin_post_bh_dashboard_export', array( __CLASS__, 'export' ) );
		add_action( 'admin_post_bh_dashboard_import', array( __CLASS__, 'import' ) );
	}

	public static function defaults() {
		return array(
			'default_range' => 'today',
			'theme'        => 'light',
			'show_kpis'    => 1,
			'show_chart'   => 1,
			'show_orders'  => 1,
			'show_products' => 1,
				'jalali_dates' => 1,
			'cart_steps' => 1,
			'cart_trust' => 1,
			'cart_coupon' => 1,
			'cart_sticky_summary' => 1,
			'cart_recommendations' => 1,
			'cart_recommendation_count' => 4,
			'cart_empty_title' => 'سبد خرید شما خالی است',
			'cart_empty_text' => 'محصولات منتخب خود را از فروشگاه پیدا کنید و برای خرید به سبد اضافه کنید.',
		);
	}

	public static function get() {
		return wp_parse_args( get_option( self::OPTION, array() ), self::defaults() );
	}

	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'شما اجازه مشاهده این صفحه را ندارید.', 'baharan-home-dashboard' ) );
		}

		if ( isset( $_POST['bh_dashboard_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bh_dashboard_settings_nonce'] ) ), 'bh_save_settings' ) ) {
			$input = isset( $_POST['bh_settings'] ) && is_array( $_POST['bh_settings'] ) ? wp_unslash( $_POST['bh_settings'] ) : array();
			$settings = array(
				'default_range'  => in_array( $input['default_range'] ?? '', array( 'today', 'week', 'month' ), true ) ? $input['default_range'] : 'today',
				'theme'         => 'dark' === ( $input['theme'] ?? '' ) ? 'dark' : 'light',
				'show_kpis'     => empty( $input['show_kpis'] ) ? 0 : 1,
				'show_chart'    => empty( $input['show_chart'] ) ? 0 : 1,
				'show_orders'   => empty( $input['show_orders'] ) ? 0 : 1,
				'show_products' => empty( $input['show_products'] ) ? 0 : 1,
				'jalali_dates'  => empty( $input['jalali_dates'] ) ? 0 : 1,
				'cart_steps' => empty( $input['cart_steps'] ) ? 0 : 1,
				'cart_trust' => empty( $input['cart_trust'] ) ? 0 : 1,
				'cart_coupon' => empty( $input['cart_coupon'] ) ? 0 : 1,
				'cart_sticky_summary' => empty( $input['cart_sticky_summary'] ) ? 0 : 1,
				'cart_recommendations' => empty( $input['cart_recommendations'] ) ? 0 : 1,
				'cart_recommendation_count' => min( 8, max( 2, absint( $input['cart_recommendation_count'] ?? 4 ) ) ),
				'cart_empty_title' => sanitize_text_field( $input['cart_empty_title'] ?? 'سبد خرید شما خالی است' ),
				'cart_empty_text' => sanitize_textarea_field( $input['cart_empty_text'] ?? '' ),
			);
			update_option( self::OPTION, $settings, false );
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'تنظیمات ذخیره شد.', 'baharan-home-dashboard' ) . '</p></div>';
		}

		$settings = self::get();
		wp_enqueue_style( 'bh-dashboard-settings-polish-direct', BAHARAN_HOME_DASHBOARD_URL . 'assets/css/settings-polish.css', array(), BAHARAN_HOME_DASHBOARD_VERSION );
		include BAHARAN_HOME_DASHBOARD_DIR . 'templates/settings.php';
	}

	public static function render_cart_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'شما اجازه مشاهده این صفحه را ندارید.', 'baharan-home-dashboard' ) );
		}
		if ( isset( $_POST['bh_cart_reset_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bh_cart_reset_nonce'] ) ), 'bh_reset_cart_settings' ) ) {
			$defaults = self::defaults();
			update_option( self::OPTION, $defaults, false );
			update_option( 'bh_cart_settings', array( 'steps' => 1, 'trust' => 1, 'coupon' => 1, 'sticky' => 1, 'recommendations' => 1, 'recommendation_count' => 4, 'empty_title' => $defaults['cart_empty_title'], 'empty_text' => $defaults['cart_empty_text'] ), false );
			add_settings_error( 'bh_cart_settings', 'reset', 'تنظیمات سبد خرید به حالت استاندارد بازگردانده شد.', 'updated' );
		}
		if ( isset( $_POST['bh_cart_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bh_cart_settings_nonce'] ) ), 'bh_save_cart_settings' ) ) {
			$input = isset( $_POST['bh_settings'] ) && is_array( $_POST['bh_settings'] ) ? wp_unslash( $_POST['bh_settings'] ) : array();
			$current = self::get();
			$current['cart_steps'] = empty( $input['cart_steps'] ) ? 0 : 1;
			$current['cart_trust'] = empty( $input['cart_trust'] ) ? 0 : 1;
			$current['cart_coupon'] = empty( $input['cart_coupon'] ) ? 0 : 1;
			$current['cart_sticky_summary'] = empty( $input['cart_sticky_summary'] ) ? 0 : 1;
			$current['cart_recommendations'] = empty( $input['cart_recommendations'] ) ? 0 : 1;
			$current['cart_recommendation_count'] = min( 8, max( 2, absint( $input['cart_recommendation_count'] ?? 4 ) ) );
			$current['cart_empty_title'] = sanitize_text_field( $input['cart_empty_title'] ?? self::defaults()['cart_empty_title'] );
			$current['cart_empty_text'] = sanitize_textarea_field( $input['cart_empty_text'] ?? self::defaults()['cart_empty_text'] );
			update_option( self::OPTION, $current, false );
			update_option( 'bh_cart_settings', array( 'steps' => $current['cart_steps'], 'trust' => $current['cart_trust'], 'coupon' => $current['cart_coupon'], 'sticky' => $current['cart_sticky_summary'], 'recommendations' => $current['cart_recommendations'], 'recommendation_count' => $current['cart_recommendation_count'], 'empty_title' => $current['cart_empty_title'], 'empty_text' => $current['cart_empty_text'] ), false );
			add_settings_error( 'bh_cart_settings', 'saved', 'تنظیمات سبد خرید با موفقیت ذخیره شد.', 'updated' );
		}
		$settings = self::get();
		wp_enqueue_style( 'bh-cart-settings-polish-direct', BAHARAN_HOME_DASHBOARD_URL . 'assets/css/settings-polish.css', array(), BAHARAN_HOME_DASHBOARD_VERSION );
		wp_enqueue_style( 'bh-cart-settings-polish-extra', BAHARAN_HOME_DASHBOARD_URL . 'assets/css/cart-settings-polish.css', array( 'bh-cart-settings-polish-direct' ), BAHARAN_HOME_DASHBOARD_VERSION );
		include BAHARAN_HOME_DASHBOARD_DIR . 'templates/cart-settings.php';
	}

	public static function export() {
		if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'دسترسی کافی ندارید.', 'baharan-home-dashboard' ) ); }
		check_admin_referer( 'bh_dashboard_export' );
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=baharan-home-dashboard-settings.json' );
		echo wp_json_encode( array( '_format' => 'baharan-home-dashboard-settings-v1', '_version' => BAHARAN_HOME_DASHBOARD_VERSION, 'settings' => self::get() ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		exit;
	}

	public static function import() {
		if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'دسترسی کافی ندارید.', 'baharan-home-dashboard' ) ); }
		check_admin_referer( 'bh_dashboard_import' );
		if ( empty( $_FILES['bh_dashboard_file']['tmp_name'] ) || ( $_FILES['bh_dashboard_file']['size'] ?? 0 ) > 65536 ) { wp_die( esc_html__( 'فایل تنظیمات معتبر نیست یا بیش از حد بزرگ است.', 'baharan-home-dashboard' ) ); }
		$data = json_decode( file_get_contents( $_FILES['bh_dashboard_file']['tmp_name'] ), true );
		if ( ! is_array( $data ) || 'baharan-home-dashboard-settings-v1' !== ( $data['_format'] ?? '' ) || ! is_array( $data['settings'] ?? null ) ) { wp_die( esc_html__( 'ساختار فایل تنظیمات معتبر نیست.', 'baharan-home-dashboard' ) ); }
		$input = $data['settings'];

		$settings = wp_parse_args( $input, self::defaults() );
		update_option( self::OPTION, $settings, false );
		wp_safe_redirect( add_query_arg( 'bh_dashboard_imported', '1', admin_url( 'admin.php?page=bh-dashboard-settings' ) ) );
		exit;
	}
}

