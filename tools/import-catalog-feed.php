<?php
/**
 * Safe WooCommerce catalog feed importer.
 * CSV columns: sku,price,stock_status,stock_quantity,specs_json
 * Default is dry-run. Apply only with --apply.
 */
if ( ! defined( 'ABSPATH' ) ) { require dirname( __DIR__ ) . '/wp-load.php'; }
$args = function_exists( 'wp_get_cli_args' ) ? wp_get_cli_args() : array();
$file = $args['file'] ?? ( $argv[1] ?? '' );
$apply = in_array( '--apply', $argv ?? array(), true );
if ( ! $file || ! is_readable( $file ) ) { fwrite( STDERR, "Usage: php tools/import-catalog-feed.php <csv-file> [--apply]\n" ); exit( 2 ); }
$fh = fopen( $file, 'rb' );
$header = fgetcsv( $fh );
$header = array_map( static function ( $v ) { return strtolower( trim( (string) $v ) ); }, (array) $header );
$required = array( 'sku', 'price', 'stock_status' );
foreach ( $required as $column ) if ( ! in_array( $column, $header, true ) ) { fclose( $fh ); fwrite( STDERR, "Missing CSV column: {$column}\n" ); exit( 3 ); }
$stats = array( 'rows' => 0, 'matched' => 0, 'updated' => 0, 'skipped' => 0, 'invalid' => 0, 'samples' => array() );
while ( ( $row = fgetcsv( $fh ) ) !== false ) {
	$stats['rows']++;
	$data = array(); foreach ( $header as $i => $key ) $data[ $key ] = trim( (string) ( $row[ $i ] ?? '' ) );
	$sku = sanitize_text_field( $data['sku'] );
	$id = function_exists( 'wc_get_product_id_by_sku' ) ? wc_get_product_id_by_sku( $sku ) : 0;
	$price = preg_replace( '/[^0-9.]/', '', str_replace( ',', '.', $data['price'] ) );
	$status = strtolower( $data['stock_status'] );
	$valid_status = in_array( $status, array( 'instock', 'outofstock', 'onbackorder' ), true );
	if ( ! $id || ! $sku ) { $stats['skipped']++; continue; }
	if ( $price === '' || ! is_numeric( $price ) || (float) $price < 0 || ! $valid_status ) { $stats['invalid']++; continue; }
	$p = wc_get_product( $id ); if ( ! $p ) { $stats['skipped']++; continue; }
	$stats['matched']++;
	if ( count( $stats['samples'] ) < 10 ) $stats['samples'][] = array( 'sku' => $sku, 'name' => $p->get_name(), 'price' => (float) $price, 'stock_status' => $status, 'apply' => $apply );
	if ( ! $apply ) continue;
	update_post_meta( $id, '_bhc_external_price', (string) $price );
	update_post_meta( $id, '_bhc_external_stock_status', $status );
	if ( isset( $data['stock_quantity'] ) && $data['stock_quantity'] !== '' && is_numeric( $data['stock_quantity'] ) ) update_post_meta( $id, '_bhc_external_stock_quantity', max( 0, (int) $data['stock_quantity'] ) );
	update_post_meta( $id, '_bhc_external_checked_at', current_time( 'mysql' ) );
	if ( ! empty( $data['specs_json'] ) ) { $specs = json_decode( $data['specs_json'], true ); if ( is_array( $specs ) ) update_post_meta( $id, '_bhc_external_specs', array_map( 'sanitize_text_field', $specs ) ); }
	$stats['updated']++;
}
fclose( $fh );
echo wp_json_encode( array( 'mode' => $apply ? 'apply' : 'dry_run', 'stats' => $stats, 'message' => $apply ? 'فید معتبر روی SKUهای منطبق اعمال شد.' : 'پیش‌نمایش انجام شد؛ برای اعمال واقعی گزینهٔ --apply لازم است.' ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . PHP_EOL;
