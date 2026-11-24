<?php
/**
 * Read-only market quote API. It never writes WooCommerce selling prices.
 * External adapters may store quotes through bhc_market_store_quote().
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function bhc_market_table() { global $wpdb; return $wpdb->prefix . 'bhc_market_quotes'; }

function bhc_market_cache_path() { return dirname( __DIR__ ) . '/market-cache.json'; }

function bhc_market_schedule_cache_rebuild() {
	static $registered = false;
	$GLOBALS['bhc_market_cache_dirty'] = true;
	if ( $registered ) return;
	$registered = true;
	add_action( 'shutdown', function () {
		if ( ! empty( $GLOBALS['bhc_market_cache_dirty'] ) ) bhc_market_rebuild_static_cache();
	}, 999 );
}

function bhc_market_rebuild_static_cache() {
	if ( ! function_exists( 'wc_get_products' ) ) return false;
	global $wpdb; $quotes = $wpdb->get_results( 'SELECT product_id,sku,source,source_url,price,currency,availability,seller,checked_at,expires_at FROM ' . bhc_market_table() . ' ORDER BY checked_at DESC', ARRAY_A );
	$grouped = array(); foreach ( $quotes as $quote ) { $id = absint( $quote['product_id'] ); if ( ! isset( $grouped[ $id ] ) ) $grouped[ $id ] = array(); if ( count( $grouped[ $id ] ) < 10 ) $grouped[ $id ][] = $quote; }
	$products = array(); foreach ( wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'return' => 'objects' ) ) as $product ) { $id = $product->get_id(); $products[ $id ] = array( 'id' => $id, 'name' => $product->get_name(), 'sku' => $product->get_sku(), 'baharan_price' => wp_strip_all_tags( $product->get_price_html() ), 'baharan_stock' => $product->is_in_stock() ? 'موجود' : 'ناموجود', 'market_quotes' => $grouped[ $id ] ?? array() ); }
	$data = array( 'version' => 1, 'generated_at' => current_time( 'mysql' ), 'products' => $products, 'by_sku' => array() ); foreach ( $products as $item ) if ( $item['sku'] !== '' ) $data['by_sku'][ strtoupper( $item['sku'] ) ] = $item['id'];
	$tmp = bhc_market_cache_path() . '.tmp'; $json = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); if ( ! $json || file_put_contents( $tmp, $json, LOCK_EX ) === false ) return false; return rename( $tmp, bhc_market_cache_path() );
}

function bhc_market_install() {
	global $wpdb;
	$table = bhc_market_table(); $charset = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE {$table} (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, product_id BIGINT UNSIGNED NOT NULL, sku VARCHAR(120) NOT NULL, source VARCHAR(64) NOT NULL, source_url TEXT NULL, price DECIMAL(20,2) NULL, currency VARCHAR(12) NOT NULL DEFAULT 'IRR', availability VARCHAR(24) NOT NULL DEFAULT 'unknown', seller VARCHAR(190) NULL, checked_at DATETIME NOT NULL, expires_at DATETIME NULL, payload LONGTEXT NULL, PRIMARY KEY(id), KEY product_source(product_id,source), KEY sku_source(sku,source), KEY checked_at(checked_at)) {$charset};";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php'; dbDelta( $sql ); update_option( 'bhc_market_api_version', '1.0.0', false );
}
add_action( 'init', function () { if ( get_option( 'bhc_market_api_version' ) !== '1.0.0' ) bhc_market_install(); }, 20 );
add_action( 'init', function () { if ( ! is_file( bhc_market_cache_path() ) ) bhc_market_rebuild_static_cache(); }, 21 );

function bhc_market_store_quote( array $quote ) {
	global $wpdb; $sku = sanitize_text_field( $quote['sku'] ?? '' ); $product_id = absint( $quote['product_id'] ?? 0 );
	if ( ! $product_id && $sku && function_exists( 'wc_get_product_id_by_sku' ) ) $product_id = absint( wc_get_product_id_by_sku( $sku ) );
	if ( ! $product_id || ! $sku || empty( $quote['source'] ) ) return false;
	$source = sanitize_key( $quote['source'] ); $price = isset( $quote['price'] ) && is_numeric( $quote['price'] ) ? (float) $quote['price'] : null;
	$availability = in_array( $quote['availability'] ?? 'unknown', array( 'in_stock', 'out_of_stock', 'unknown' ), true ) ? $quote['availability'] : 'unknown';
	$previous = $wpdb->get_row( $wpdb->prepare( 'SELECT id,price,availability,checked_at FROM ' . bhc_market_table() . ' WHERE product_id=%d AND source=%s ORDER BY id DESC LIMIT 1', $product_id, $source ), ARRAY_A );
	$now = current_time( 'mysql' );
	$row = array( 'product_id' => $product_id, 'sku' => $sku, 'source' => $source, 'source_url' => esc_url_raw( $quote['source_url'] ?? '' ), 'price' => $price, 'currency' => sanitize_text_field( $quote['currency'] ?? 'IRR' ), 'availability' => $availability, 'seller' => sanitize_text_field( $quote['seller'] ?? '' ), 'checked_at' => $now, 'expires_at' => sanitize_text_field( $quote['expires_at'] ?? '' ) ?: null, 'payload' => wp_json_encode( $quote, JSON_UNESCAPED_UNICODE ) );
	$format = array( '%d','%s','%s','%s','%f','%s','%s','%s','%s','%s','%s' );
	if ( $previous ) { $wpdb->update( bhc_market_table(), $row, array( 'id' => (int) $previous['id'] ), $format, array( '%d' ) ); $wpdb->query( $wpdb->prepare( 'DELETE FROM ' . bhc_market_table() . ' WHERE product_id=%d AND source=%s AND id<>%d', $product_id, $source, (int) $previous['id'] ) ); }
	else { $wpdb->insert( bhc_market_table(), $row, $format ); }
	if ( $previous && ( $previous['availability'] !== $availability || ( $price !== null && (float) $previous['price'] !== $price ) ) ) {
		$alerts = (array) get_option( 'bhc_market_alerts', array() ); array_unshift( $alerts, array( 'product_id' => $product_id, 'sku' => $sku, 'source' => $source, 'old_price' => $previous['price'], 'new_price' => $price, 'old_availability' => $previous['availability'], 'new_availability' => $availability, 'at' => $now ) ); update_option( 'bhc_market_alerts', array_slice( $alerts, 0, 100 ), false );
	}
	update_post_meta( $product_id, '_bhc_market_last_checked', $now ); delete_transient( 'bhc_market_quote_' . $product_id ); bhc_market_schedule_cache_rebuild(); return true;
}

function bhc_market_quote_health( $product_id, $quote ) {
	$checked = ! empty( $quote['checked_at'] ) ? strtotime( $quote['checked_at'] ) : 0;
	$age = $checked ? max( 0, time() - $checked ) : PHP_INT_MAX;
	return array( 'stale' => $age > 48 * HOUR_IN_SECONDS, 'age_hours' => $age === PHP_INT_MAX ? null : round( $age / HOUR_IN_SECONDS, 1 ), 'usable' => $age <= 48 * HOUR_IN_SECONDS && ! empty( $quote['price'] ) && ( $quote['availability'] ?? 'unknown' ) !== 'unknown' );
}

function bhc_market_quotes_for_product( $product_id, $limit = 20 ) {
	global $wpdb; $table = bhc_market_table(); $limit = max( 1, min( 50, absint( $limit ) ) );
	return $wpdb->get_results( $wpdb->prepare( "SELECT source,source_url,price,currency,availability,seller,checked_at,expires_at FROM {$table} WHERE product_id=%d ORDER BY checked_at DESC LIMIT %d", absint( $product_id ), $limit ), ARRAY_A );
}

function bhc_market_rest_quote( WP_REST_Request $request ) {
	$sku = sanitize_text_field( $request->get_param( 'sku' ) ); $id = absint( $request->get_param( 'product_id' ) );
	if ( ! $id && $sku && function_exists( 'wc_get_product_id_by_sku' ) ) $id = absint( wc_get_product_id_by_sku( $sku ) );
	if ( ! $id || ! wc_get_product( $id ) ) return new WP_Error( 'not_found', 'محصول با این SKU پیدا نشد.', array( 'status' => 404 ) );
	$cache_key = 'bhc_market_quote_' . $id; $cached = get_transient( $cache_key ); if ( is_array( $cached ) ) return rest_ensure_response( $cached );
	$product = wc_get_product( $id );
	$quotes = bhc_market_quotes_for_product( $id ); foreach ( $quotes as &$quote ) $quote['health'] = bhc_market_quote_health( $id, $quote ); unset( $quote );
	$data = array( 'product' => array( 'id' => $id, 'name' => $product->get_name(), 'sku' => $product->get_sku(), 'baharan_price' => wp_strip_all_tags( $product->get_price_html() ), 'baharan_stock' => $product->is_in_stock() ? 'موجود' : 'ناموجود' ), 'market_quotes' => $quotes, 'alerts' => array_slice( (array) get_option( 'bhc_market_alerts', array() ), 0, 10 ), 'policy' => 'قیمت فروش بهاران مستقل است و این داده‌ها فقط استعلام مرجع بازار هستند.' );
	set_transient( $cache_key, $data, 30 ); return rest_ensure_response( $data );
}
function bhc_market_rest_sync() {
	if ( ! current_user_can( 'manage_options' ) ) return new WP_Error( 'forbidden', 'دسترسی مجاز نیست.', array( 'status' => 403 ) );
	return rest_ensure_response( function_exists( 'bhc_chat_catalog_sync_feed' ) ? bhc_chat_catalog_sync_feed() : array( 'status' => 'sync_unavailable' ) );
}
function bhc_market_rest_barchin_sync( WP_REST_Request $request ) {
	if ( ! current_user_can( 'manage_options' ) ) return new WP_Error( 'forbidden', 'دسترسی مجاز نیست.', array( 'status' => 403 ) );
	if ( ! function_exists( 'bhc_barchin_sync_product' ) ) return new WP_Error( 'connector_missing', 'Barchin connector is unavailable.', array( 'status' => 503 ) );
	$id = absint( $request->get_param( 'product_id' ) ); if ( ! $id ) return new WP_Error( 'product_required', 'product_id is required.', array( 'status' => 400 ) );
	$result = bhc_barchin_sync_product( $id ); if ( is_wp_error( $result ) ) return $result; return rest_ensure_response( $result );
}
add_action( 'rest_api_init', function () {
	register_rest_route( 'bhc/v1', '/market/quote', array( 'methods' => WP_REST_Server::READABLE, 'callback' => 'bhc_market_rest_quote', 'permission_callback' => '__return_true', 'args' => array( 'sku' => array( 'required' => false ), 'product_id' => array( 'required' => false ) ) ) );
	register_rest_route( 'bhc/v1', '/market/sync', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => 'bhc_market_rest_sync', 'permission_callback' => function () { return current_user_can( 'manage_options' ); } ) );
	register_rest_route( 'bhc/v1', '/market/barchin-sync', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => 'bhc_market_rest_barchin_sync', 'permission_callback' => function () { return current_user_can( 'manage_options' ); } ) );
} );

add_filter( 'rest_post_dispatch', function ( $result, $server, $request ) {
	if ( strpos( $request->get_route(), '/bhc/v1/market/quote' ) !== false && $result instanceof WP_REST_Response ) {
		$result->header( 'Cache-Control', 'public, max-age=30, stale-while-revalidate=60' );
		$result->header( 'X-BHC-Source', 'market-quote-cache' );
	}
	return $result;
}, 10, 3 );
