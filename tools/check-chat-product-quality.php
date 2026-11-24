<?php
if ( ! defined( 'ABSPATH' ) ) { require dirname( __DIR__ ) . '/wp-load.php'; }
$products = function_exists( 'wc_get_products' ) ? wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'return' => 'objects' ) ) : array();
$out = array( 'published' => count( $products ), 'with_price' => 0, 'in_stock' => 0, 'with_specs' => 0, 'with_reviews' => 0, 'with_sales' => 0, 'recommendable' => 0, 'blocked_samples' => array() );
foreach ( $products as $p ) {
	$price = $p->get_price() !== '';
	$stock = $p->is_in_stock();
	$specs = (bool) get_post_meta( $p->get_id(), '_bhc_specs', true );
	$reviews = (int) $p->get_review_count() > 0;
	$sales = (int) $p->get_total_sales() > 0;
	if ( $price ) $out['with_price']++;
	if ( $stock ) $out['in_stock']++;
	if ( $specs ) $out['with_specs']++;
	if ( $reviews ) $out['with_reviews']++;
	if ( $sales ) $out['with_sales']++;
	if ( $price && $stock ) $out['recommendable']++;
	if ( count( $out['blocked_samples'] ) < 20 && ( ! $price || ! $stock ) ) $out['blocked_samples'][] = array( 'id' => $p->get_id(), 'name' => $p->get_name(), 'missing_price' => ! $price, 'out_of_stock' => ! $stock, 'has_specs' => $specs );
}
echo wp_json_encode( $out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . PHP_EOL;
