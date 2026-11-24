<?php
/**
 * Keep chat catalog reads fresh after WooCommerce product mutations.
 * Product prices, stock and specs are never copied into the static knowledge
 * pack; chat-live.php reads them from the product at request time.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function bhc_chat_catalog_touch( $product_id = 0 ) {
	if ( is_object( $product_id ) && is_callable( array( $product_id, 'get_id' ) ) ) {
		$product_id = $product_id->get_id();
	}
	$product_id = absint( $product_id );
	update_option( 'bhc_chat_catalog_changed_at', time(), false );
	if ( function_exists( 'wp_cache_delete' ) ) {
		wp_cache_delete( 'bhc_chat_catalog_version', 'baharaan-commerce' );
	}
	// Remove only our short-lived catalog caches, never WooCommerce data.
	global $wpdb;
	$like = $wpdb->esc_like( '_transient_bhc_chat_catalog_' ) . '%';
	$timeout_like = $wpdb->esc_like( '_transient_timeout_bhc_chat_catalog_' ) . '%';
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", $like, $timeout_like ) );
}

add_action( 'woocommerce_update_product', 'bhc_chat_catalog_touch', 20 );
add_action( 'woocommerce_new_product', 'bhc_chat_catalog_touch', 20 );
add_action( 'woocommerce_product_set_stock', 'bhc_chat_catalog_touch', 20 );
add_action( 'woocommerce_variation_set_stock', 'bhc_chat_catalog_touch', 20 );
add_action( 'updated_post_meta', function( $meta_id, $object_id, $meta_key ) {
	if ( 'product' === get_post_type( $object_id ) && in_array( $meta_key, array( '_price', '_regular_price', '_sale_price', '_stock', '_stock_status', '_bhc_specs', '_sku' ), true ) ) {
		bhc_chat_catalog_touch( $object_id );
	}
}, 20, 3 );

function bhc_chat_catalog_freshness() {
	$changed = (int) get_option( 'bhc_chat_catalog_changed_at', 0 );
	return array(
		'changed_at' => $changed ? wp_date( DATE_ATOM, $changed ) : null,
		'source' => 'woocommerce_live',
		'policy' => 'قیمت، موجودی و مشخصات فقط از کاتالوگ زنده خوانده می‌شود.',
	);
}

function bhc_chat_catalog_sync_feed() {
	$url = esc_url_raw( (string) get_option( 'bhc_catalog_feed_url', '' ) );
	if ( $url === '' || ! wp_http_validate_url( $url ) || ! in_array( strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) ), array( 'https', 'http' ), true ) ) return array( 'status' => 'not_configured' );
	$response = wp_safe_remote_get( $url, array( 'timeout' => 20, 'redirection' => 2 ) );
	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) < 200 || wp_remote_retrieve_response_code( $response ) >= 300 ) {
		update_option( 'bhc_catalog_feed_last_error', current_time( 'mysql' ), false );
		return array( 'status' => 'error' );
	}
	$body = wp_remote_retrieve_body( $response );
	if ( strlen( $body ) > 10 * 1024 * 1024 ) return array( 'status' => 'too_large' );
	$stream = fopen( 'php://temp', 'r+' ); fwrite( $stream, $body ); rewind( $stream );
	$header = fgetcsv( $stream ); if ( ! is_array( $header ) ) { fclose( $stream ); return array( 'status' => 'invalid_header' ); }
	$header = array_map( static function ( $v ) { return strtolower( trim( (string) $v ) ); }, $header );
	$required = array( 'sku', 'price', 'stock_status' ); foreach ( $required as $column ) if ( ! in_array( $column, $header, true ) ) { fclose( $stream ); return array( 'status' => 'missing_column', 'column' => $column ); }
	$updated = 0; $skipped = 0;
	while ( ( $row = fgetcsv( $stream ) ) !== false ) {
		$data = array(); foreach ( $header as $i => $key ) $data[ $key ] = trim( (string) ( $row[ $i ] ?? '' ) );
		$sku = sanitize_text_field( $data['sku'] ); $price = preg_replace( '/[^0-9.]/', '', str_replace( ',', '.', $data['price'] ) ); $stock = strtolower( $data['stock_status'] );
		$id = function_exists( 'wc_get_product_id_by_sku' ) ? wc_get_product_id_by_sku( $sku ) : 0;
		if ( ! $id || $price === '' || ! is_numeric( $price ) || (float) $price < 0 || ! in_array( $stock, array( 'instock', 'outofstock', 'onbackorder' ), true ) ) { $skipped++; continue; }
		$product = wc_get_product( $id ); if ( ! $product ) { $skipped++; continue; }
		// External quote only: never overwrite Baharaan's selling price or stock.
		update_post_meta( $id, '_bhc_external_price', (string) $price );
		update_post_meta( $id, '_bhc_external_stock_status', $stock );
		if ( isset( $data['stock_quantity'] ) && $data['stock_quantity'] !== '' && is_numeric( $data['stock_quantity'] ) ) update_post_meta( $id, '_bhc_external_stock_quantity', max( 0, (int) $data['stock_quantity'] ) );
		update_post_meta( $id, '_bhc_external_checked_at', current_time( 'mysql' ) );
		if ( ! empty( $data['specs_json'] ) ) { $specs = json_decode( $data['specs_json'], true ); if ( is_array( $specs ) ) update_post_meta( $id, '_bhc_external_specs', array_map( 'sanitize_text_field', $specs ) ); }
		if ( function_exists( 'bhc_market_store_quote' ) ) bhc_market_store_quote( array( 'product_id' => $id, 'sku' => $sku, 'source' => 'supplier_feed', 'price' => (float) $price, 'currency' => sanitize_text_field( $data['currency'] ?? 'IRR' ), 'availability' => 'instock' === $stock ? 'in_stock' : ( 'outofstock' === $stock ? 'out_of_stock' : 'unknown' ), 'seller' => sanitize_text_field( $data['seller'] ?? '' ), 'source_url' => esc_url_raw( $data['source_url'] ?? '' ) ) );
		$updated++;
	}
	fclose( $stream ); update_option( 'bhc_catalog_feed_last_sync', current_time( 'mysql' ), false ); delete_option( 'bhc_catalog_feed_last_error' );
	return array( 'status' => 'ok', 'updated' => $updated, 'skipped' => $skipped );
}

add_action( 'bhc_catalog_feed_sync', 'bhc_chat_catalog_sync_feed' );
add_action( 'init', function () {
	$next = wp_next_scheduled( 'bhc_catalog_feed_sync' );
	if ( get_option( 'bhc_market_schedule', 'daily' ) === 'manual' ) { if ( $next ) wp_unschedule_event( $next, 'bhc_catalog_feed_sync' ); return; }
	$target = new DateTimeImmutable( 'today 03:00:00', wp_timezone() );
	if ( $target->getTimestamp() <= time() ) $target = $target->modify( '+1 day' );
	if ( $next && abs( $next - $target->getTimestamp() ) > 3600 ) { wp_unschedule_event( $next, 'bhc_catalog_feed_sync' ); $next = false; }
	if ( ! $next ) wp_schedule_event( $target->getTimestamp(), 'daily', 'bhc_catalog_feed_sync' );
}, 25 );
