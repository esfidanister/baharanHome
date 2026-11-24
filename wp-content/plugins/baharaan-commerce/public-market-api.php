<?php
// cPanel-compatible read-only endpoint. It does not load WordPress or WooCommerce.
header( 'Content-Type: application/json; charset=utf-8' );
header( 'Cache-Control: public, max-age=30, stale-while-revalidate=60' );
$respond = static function ( $payload, $status = 200 ) {
		http_response_code( $status );
		$json = json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		if ( $json === false ) $json = '{"error":"json_encode_failed"}';
		echo $json;
		exit;
	};
$path = __DIR__ . '/market-cache.json';
if ( ! is_readable( $path ) ) $respond( array( 'error' => 'market_cache_unavailable' ), 503 );
$raw = file_get_contents( $path );
$data = is_string( $raw ) ? json_decode( $raw, true ) : null;
if ( ! is_array( $data ) || ! isset( $data['products'], $data['by_sku'] ) || ! is_array( $data['products'] ) || ! is_array( $data['by_sku'] ) ) $respond( array( 'error' => 'market_cache_invalid' ), 503 );
$sku = strtoupper( preg_replace( '/[^A-Z0-9._-]/i', '', (string) ( $_GET['sku'] ?? '' ) ) );
$id = $sku !== '' && isset( $data['by_sku'][ $sku ] ) ? (int) $data['by_sku'][ $sku ] : 0;
if ( ! $id || empty( $data['products'][ $id ] ) ) $respond( array( 'error' => $sku === '' ? 'sku_required' : 'product_not_found', 'generated_at' => $data['generated_at'] ?? null ), $sku === '' ? 400 : 404 );
$payload = array( 'product' => $data['products'][ $id ], 'generated_at' => $data['generated_at'] ?? null, 'source' => 'static-market-cache' );
$etag = '"' . md5( json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) . '"'; header( 'ETag: ' . $etag ); if ( trim( (string) ( $_SERVER['HTTP_IF_NONE_MATCH'] ?? '' ) ) === $etag ) { http_response_code( 304 ); exit; }
$respond( $payload );
