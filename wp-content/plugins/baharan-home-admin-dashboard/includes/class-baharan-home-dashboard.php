<?php
defined( 'ABSPATH' ) || exit;

final class Baharan_Home_Dashboard {
	private static $instance;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		Baharan_Home_Dashboard_Settings::init();
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );
		add_action( 'wp_ajax_bh_dashboard_refresh', array( $this, 'ajax_refresh' ) );
	}

	public function register_menu() {
		if ( ! Baharan_Home_Dashboard_Data::can_view_sales() ) {
			return;
		}

		add_menu_page(
			__( 'مرکز عملیات فروش | بهاران هوم', 'baharan-home-dashboard' ),
			__( 'مرکز عملیات فروش', 'baharan-home-dashboard' ),
			'read',
			'bh-dashboard',
			array( $this, 'render_dashboard' ),
			'dashicons-store',
			3
		);

		add_submenu_page(
			'bh-dashboard',
			__( 'مرکز عملیات فروشگاه', 'baharan-home-dashboard' ),
			__( 'نمای کلی فروش', 'baharan-home-dashboard' ),
			'read',
			'bh-dashboard',
			array( $this, 'render_dashboard' )
		);

		if ( current_user_can( 'manage_options' ) ) {
			add_submenu_page(
			'bh-dashboard',
			__( 'تنظیمات مرکز عملیات فروش', 'baharan-home-dashboard' ),
			__( 'تنظیمات مرکز عملیات', 'baharan-home-dashboard' ),
			'manage_options',
			'bh-dashboard-settings',
			array( 'Baharan_Home_Dashboard_Settings', 'render' )
			);

		}
	}

	public function enqueue_assets( $hook ) {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		if ( ! in_array( $hook, array( 'toplevel_page_bh-dashboard', 'bh-dashboard_page_bh-dashboard-settings', 'bh-dashboard_page_bh-cart-settings' ), true ) && ! in_array( $page, array( 'bh-dashboard', 'bh-dashboard-settings', 'bh-cart-settings' ), true ) ) {
			return;
		}

		wp_enqueue_style(
			'bh-dashboard-admin',
			BAHARAN_HOME_DASHBOARD_URL . 'assets/css/dashboard.css',
			array(),
			BAHARAN_HOME_DASHBOARD_VERSION
		);
		wp_enqueue_style(
			'bh-dashboard-polish',
			BAHARAN_HOME_DASHBOARD_URL . 'assets/css/dashboard-polish.css',
			array( 'bh-dashboard-admin' ),
			BAHARAN_HOME_DASHBOARD_VERSION
		);
		if ( 'bh-dashboard_page_bh-dashboard-settings' === $hook || 'bh-dashboard-settings' === $page ) {
			wp_enqueue_style(
			'bh-dashboard-settings-polish',
			BAHARAN_HOME_DASHBOARD_URL . 'assets/css/settings-polish.css',
			array( 'bh-dashboard-admin' ),
			BAHARAN_HOME_DASHBOARD_VERSION
			);
		}
		if ( 'bh-dashboard_page_bh-cart-settings' === $hook || 'bh-cart-settings' === $page ) {
			wp_enqueue_style(
				'bh-cart-settings-polish',
				BAHARAN_HOME_DASHBOARD_URL . 'assets/css/settings-polish.css',
				array( 'bh-dashboard-admin' ),
				BAHARAN_HOME_DASHBOARD_VERSION
			);
		}
		wp_enqueue_script(
			'bh-dashboard-admin',
			BAHARAN_HOME_DASHBOARD_URL . 'assets/js/dashboard.js',
			array(),
			BAHARAN_HOME_DASHBOARD_VERSION,
			true
		);
		wp_localize_script( 'bh-dashboard-admin', 'BaharanHomeDashboard', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'bh_dashboard_refresh' ),
		) );
	}

	public function body_class( $classes ) {
		if ( isset( $_GET['page'] ) && 'bh-dashboard' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
			$classes .= ' bh-dashboard-screen';
		}
		return $classes;
	}

	public function render_dashboard() {
		if ( ! Baharan_Home_Dashboard_Data::can_view_sales() ) {
			wp_die( esc_html__( 'شما اجازه مشاهده این صفحه را ندارید.', 'baharan-home-dashboard' ) );
		}

		$settings = Baharan_Home_Dashboard_Settings::get();
		$range = isset( $_GET['bh_range'] ) ? sanitize_key( wp_unslash( $_GET['bh_range'] ) ) : $settings['default_range'];
		$after = isset( $_GET['bh_after'] ) ? sanitize_text_field( wp_unslash( $_GET['bh_after'] ) ) : '';
		$before = isset( $_GET['bh_before'] ) ? sanitize_text_field( wp_unslash( $_GET['bh_before'] ) ) : '';
		$data = Baharan_Home_Dashboard_Data::get_snapshot( $range, $after, $before );
		include BAHARAN_HOME_DASHBOARD_DIR . 'templates/dashboard.php';
	}

	public function ajax_refresh() {
		check_ajax_referer( 'bh_dashboard_refresh', 'nonce' );
		if ( ! Baharan_Home_Dashboard_Data::can_view_sales() ) {
			wp_send_json_error( array( 'message' => __( 'دسترسی کافی ندارید.', 'baharan-home-dashboard' ) ), 403 );
		}
		$range = isset( $_POST['range'] ) ? sanitize_key( wp_unslash( $_POST['range'] ) ) : 'today';
		wp_send_json_success( Baharan_Home_Dashboard_Data::get_snapshot( $range, '', '' ) );
	}
}

