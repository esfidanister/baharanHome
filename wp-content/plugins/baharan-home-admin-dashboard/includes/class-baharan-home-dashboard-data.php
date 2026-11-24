<?php
defined( 'ABSPATH' ) || exit;

final class Baharan_Home_Dashboard_Data {
	public static function format_date( $timestamp, $short = false ) {
		$timestamp = (int) $timestamp;
		$pattern = $short ? 'yyyy/MM/dd' : 'yyyy/MM/dd HH:mm';
		// Visible dates are always Persian; the legacy setting remains stored for
		// compatibility but cannot switch the UI back to Gregorian dates.
		if ( class_exists( 'IntlDateFormatter' ) ) {
			$timezone = wp_timezone_string() ?: 'Asia/Tehran';
			$formatter = new IntlDateFormatter( 'fa_IR@calendar=persian', IntlDateFormatter::NONE, IntlDateFormatter::NONE, $timezone, IntlDateFormatter::TRADITIONAL, $pattern );
			$value = $formatter->format( $timestamp );
			if ( false !== $value ) { return self::persian_digits( $value ); }
		}
		$parts = self::gregorian_to_jalali( (int) wp_date( 'Y', $timestamp, wp_timezone() ), (int) wp_date( 'n', $timestamp, wp_timezone() ), (int) wp_date( 'j', $timestamp, wp_timezone() ) );
		$date = sprintf( '%04d/%02d/%02d', $parts[0], $parts[1], $parts[2] );
		return self::persian_digits( $short ? $date : $date . ' ' . wp_date( 'H:i', $timestamp, wp_timezone() ) );
	}

	private static function persian_digits( $value ) {
		return strtr( (string) $value, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
	}

	private static function gregorian_to_jalali( $gy, $gm, $gd ) {
		$gdm = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 ); $gy2 = $gy - 1600; $gm2 = $gm - 1; $gd2 = $gd - 1;
		$gday = 365 * $gy2 + (int) ( ( $gy2 + 3 ) / 4 ) - (int) ( ( $gy2 + 99 ) / 100 ) + (int) ( ( $gy2 + 399 ) / 400 ) + $gdm[ $gm2 ] + $gd2; if ( $gm2 > 1 && ( 0 === $gy % 4 && 0 !== $gy % 100 || 0 === $gy % 400 ) ) { $gday++; }
		$jday = $gday - 79; $jnp = (int) ( $jday / 12053 ); $jday %= 12053; $jy = 979 + 33 * $jnp + 4 * (int) ( $jday / 1461 ); $jday %= 1461; if ( $jday >= 366 ) { $jy += (int) ( ( $jday - 1 ) / 365 ); $jday = ( $jday - 1 ) % 365; }
		$jm = $jday < 186 ? 1 + (int) ( $jday / 31 ) : 7 + (int) ( ( $jday - 186 ) / 30 ); $jd = 1 + ( $jday < 186 ? $jday % 31 : ( $jday - 186 ) % 30 ); return array( $jy, $jm, $jd );
	}

	private static function jalali_to_gregorian( $jy, $jm, $jd ) {
		$jy -= 979; $jm--; $jd--; $jday = 365 * $jy + (int) ( $jy / 33 ) * 8 + (int) ( ( ( $jy % 33 ) + 3 ) / 4 ) + ( $jm < 6 ? $jm * 31 : ( $jm - 6 ) * 30 + 186 ) + $jd; $gday = $jday + 79; $gy = 1600 + 400 * (int) ( $gday / 146097 ); $gday %= 146097; $leap = true; if ( $gday >= 36525 ) { $gday--; $gy += 100 * (int) ( $gday / 36524 ); $gday %= 36524; if ( $gday >= 365 ) { $gday++; } else { $leap = false; } } $gy += 4 * (int) ( $gday / 1461 ); $gday %= 1461; if ( $gday >= 366 ) { $leap = false; $gday--; $gy += (int) ( $gday / 365 ); $gday %= 365; } $days = array( 0, 31, $leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ); $gm = 1; for ( $i = 1; $i <= 12; $i++ ) { if ( $gday < $days[ $i ] ) { return array( $gy, $i, $gday + 1 ); } $gday -= $days[ $i ]; } return array( $gy, 12, 31 );
	}
	public static function can_view_sales() {
		return current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( 'view_woocommerce_reports' );
	}

	public static function get_snapshot( $range = 'today', $custom_after = '', $custom_before = '' ) {
		$range = in_array( $range, array( 'today', 'week', 'month', 'custom' ), true ) ? $range : 'today';
		$has_woocommerce = class_exists( 'WooCommerce' ) && function_exists( 'wc_get_orders' );
		$snapshot = array(
			'has_woocommerce' => $has_woocommerce,
			'can_view_sales'  => self::can_view_sales(),
			'currency'        => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : '',
			'currency_label'  => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
			'range'           => $range,
			'generated_at'    => current_time( 'timestamp' ),
			'notice'          => '',
			'kpis'            => array( 'sales' => null, 'orders' => null, 'new_orders' => null, 'processing' => null, 'completed' => null, 'cancelled' => null, 'refunded' => null, 'low_stock' => null, 'outofstock' => null, 'attention' => null ),
			'recent_orders'   => array(),
			'attention_orders' => array(),
			'low_stock_items' => array(),
			'top_products'    => array(),
			'chart'           => array(),
		);

		if ( ! $has_woocommerce ) {
			$snapshot['notice'] = __( 'برای نمایش فروش، سفارش و موجودی واقعی، ووکامرس باید نصب و فعال باشد.', 'baharan-home-dashboard' );
			return $snapshot;
		}
		if ( ! $snapshot['can_view_sales'] ) {
			$snapshot['notice'] = __( 'نقش کاربری شما مجوز مشاهده گزارش‌های فروش را ندارد.', 'baharan-home-dashboard' );
			return $snapshot;
		}

		$bounds = self::get_bounds( $range, $custom_after, $custom_before );
		$order_ids = wc_get_orders( array( 'limit' => -1, 'orderby' => 'date', 'order' => 'DESC', 'date_created' => $bounds['after'] . '...' . $bounds['before'], 'return' => 'ids' ) );
		$orders = array();
		foreach ( $order_ids as $order_id ) { $order = wc_get_order( $order_id ); if ( $order ) { $orders[] = $order; } }

		$total = 0.0;
		$status_counts = array( 'processing' => 0, 'completed' => 0, 'cancelled' => 0, 'refunded' => 0, 'new' => 0, 'attention' => 0 );
		$products = array();
		$chart = array();
		$paid_statuses = array( 'processing', 'completed', 'on-hold' );
		foreach ( $orders as $order ) {
			$status = $order->get_status();
			if ( in_array( $status, $paid_statuses, true ) ) { $total += (float) $order->get_total(); }
			if ( isset( $status_counts[ $status ] ) ) { $status_counts[ $status ]++; }
			if ( in_array( $status, array( 'pending', 'on-hold' ), true ) ) { $status_counts['new']++; }
			if ( in_array( $status, array( 'pending', 'on-hold', 'failed' ), true ) ) { $status_counts['attention']++; }
			if ( 'refunded' === $status || $order->get_total_refunded() > 0 ) { $status_counts['refunded']++; }
			$created = $order->get_date_created();
			$day = $created ? wp_date( 'Y-m-d', $created->getTimestamp() ) : '';
			if ( $day ) {
				if ( ! isset( $chart[ $day ] ) ) { $chart[ $day ] = array( 'date' => $day, 'sales' => 0.0, 'orders' => 0 ); }
				$chart[ $day ]['orders']++;
				if ( in_array( $status, $paid_statuses, true ) ) { $chart[ $day ]['sales'] += (float) $order->get_total(); }
			}
			foreach ( $order->get_items( 'line_item' ) as $item ) {
				$product_id = $item->get_product_id();
				if ( ! $product_id ) { continue; }
				if ( ! isset( $products[ $product_id ] ) ) { $products[ $product_id ] = array( 'id' => $product_id, 'name' => $item->get_name(), 'quantity' => 0, 'revenue' => 0.0 ); }
				$products[ $product_id ]['quantity'] += (int) $item->get_quantity();
				$products[ $product_id ]['revenue'] += (float) $item->get_total();
			}
		}
		usort( $products, function ( $a, $b ) { return $b['quantity'] <=> $a['quantity']; } );
		ksort( $chart );
		$snapshot['kpis'] = array( 'sales' => wc_price( $total ), 'orders' => count( $orders ), 'new_orders' => $status_counts['new'], 'processing' => $status_counts['processing'], 'completed' => $status_counts['completed'], 'cancelled' => $status_counts['cancelled'], 'refunded' => $status_counts['refunded'], 'low_stock' => 0, 'outofstock' => 0, 'attention' => $status_counts['attention'] );
		$snapshot['recent_orders'] = array_map( array( __CLASS__, 'format_order' ), array_slice( $orders, 0, 8 ) );
		$attention = array_filter( $orders, function ( $order ) { return in_array( $order->get_status(), array( 'pending', 'on-hold', 'failed' ), true ); } );
		$snapshot['attention_orders'] = array_map( array( __CLASS__, 'format_order' ), array_slice( array_values( $attention ), 0, 6 ) );
		$snapshot['top_products'] = array_slice( array_map( array( __CLASS__, 'format_product' ), $products ), 0, 8 );
		$snapshot['chart'] = array_values( $chart );

		$stock_products = wc_get_products( array( 'limit' => -1, 'status' => 'publish', 'manage_stock' => true, 'return' => 'objects' ) );
		$threshold = (int) get_option( 'woocommerce_notify_low_stock_amount', 2 );
		foreach ( $stock_products as $product ) {
			$qty = $product->get_stock_quantity();
			if ( null === $qty ) { continue; }
			if ( $qty <= 0 ) { $snapshot['kpis']['outofstock']++; }
			elseif ( $qty <= $threshold ) { $snapshot['kpis']['low_stock']++; $snapshot['low_stock_items'][] = array( 'id' => $product->get_id(), 'name' => $product->get_name(), 'quantity' => $qty, 'status' => 'low', 'image' => $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) : ( function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src() : '' ) ); }
		}
		return $snapshot;
	}

	private static function format_order( $order ) {
		$status_key = $order->get_status();
		$status_labels = array( 'pending' => __( 'در انتظار پرداخت', 'baharan-home-dashboard' ), 'processing' => __( 'در حال پردازش', 'baharan-home-dashboard' ), 'on-hold' => __( 'در انتظار بررسی', 'baharan-home-dashboard' ), 'completed' => __( 'تکمیل‌شده', 'baharan-home-dashboard' ), 'cancelled' => __( 'لغوشده', 'baharan-home-dashboard' ), 'refunded' => __( 'بازپرداخت‌شده', 'baharan-home-dashboard' ), 'failed' => __( 'ناموفق', 'baharan-home-dashboard' ), 'draft' => __( 'پیش‌نویس', 'baharan-home-dashboard' ) );
		return array( 'id' => $order->get_id(), 'number' => $order->get_order_number(), 'status' => $status_labels[ $status_key ] ?? wc_get_order_status_name( $status_key ), 'status_key' => $status_key, 'total' => wc_price( $order->get_total() ), 'date' => $order->get_date_created() ? self::format_date( $order->get_date_created()->getTimestamp() ) : '', 'url' => admin_url( 'admin.php?page=wc-orders&action=edit&id=' . $order->get_id() ) );
	}

	private static function format_product( $product ) {
		return array( 'id' => $product['id'], 'name' => $product['name'], 'quantity' => $product['quantity'], 'revenue' => wc_price( $product['revenue'] ), 'image' => get_the_post_thumbnail_url( $product['id'], 'thumbnail' ) ?: ( function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src() : '' ) );
	}

	private static function get_bounds( $range, $custom_after = '', $custom_before = '' ) {
		$now = current_time( 'timestamp' );
		if ( 'custom' === $range && $custom_after && $custom_before ) {
			$custom_after = strtr( str_replace( '/', '-', $custom_after ), array( '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9' ) ); $custom_before = strtr( str_replace( '/', '-', $custom_before ), array( '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9' ) );
			$after = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $custom_after ) ? $custom_after . ' 00:00:00' : ''; $before = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $custom_before ) ? $custom_before . ' 23:59:59' : '';
			if ( $after && (int) substr( $custom_after, 0, 4 ) < 1700 ) { $g = self::jalali_to_gregorian( (int) substr( $custom_after, 0, 4 ), (int) substr( $custom_after, 5, 2 ), (int) substr( $custom_after, 8, 2 ) ); $after = sprintf( '%04d-%02d-%02d 00:00:00', $g[0], $g[1], $g[2] ); }
			if ( $before && (int) substr( $custom_before, 0, 4 ) < 1700 ) { $g = self::jalali_to_gregorian( (int) substr( $custom_before, 0, 4 ), (int) substr( $custom_before, 5, 2 ), (int) substr( $custom_before, 8, 2 ) ); $before = sprintf( '%04d-%02d-%02d 23:59:59', $g[0], $g[1], $g[2] ); }
			if ( $after && $before ) { return array( 'after' => $after, 'before' => $before ); }
		}
		$start = 'today' === $range ? strtotime( 'today', $now ) : ( 'week' === $range ? strtotime( 'monday this week', $now ) : strtotime( 'first day of this month', $now ) );
		return array( 'after' => wp_date( 'Y-m-d H:i:s', $start ), 'before' => wp_date( 'Y-m-d H:i:s', $now ) );
	}
}

