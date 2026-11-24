<?php
if ( ! defined( 'ABSPATH' ) ) { require dirname( __DIR__ ) . '/wp-load.php'; }
$products = function_exists( 'wc_get_products' ) ? wc_get_products( array( 'status' => array( 'publish', 'private', 'draft' ), 'limit' => -1, 'return' => 'objects' ) ) : array();
$out = array( 'products' => count( $products ), 'published' => 0, 'with_sku' => 0, 'with_specs_meta' => 0, 'with_reviews' => 0, 'with_sales_count' => 0, 'with_price' => 0, 'in_stock' => 0, 'categories' => array(), 'brands' => array(), 'samples' => array() );
foreach ( $products as $product ) {
	if ( 'publish' === $product->get_status() ) $out['published']++;
	if ( $product->get_sku() !== '' ) $out['with_sku']++;
	if ( get_post_meta( $product->get_id(), '_bhc_specs', true ) ) $out['with_specs_meta']++;
	if ( (int) $product->get_review_count() > 0 ) $out['with_reviews']++;
	if ( (int) $product->get_total_sales() > 0 ) $out['with_sales_count']++;
	if ( $product->get_price() !== '' ) $out['with_price']++;
	if ( $product->is_in_stock() ) $out['in_stock']++;
	foreach ( wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) ) as $cat ) $out['categories'][ $cat ] = ( $out['categories'][ $cat ] ?? 0 ) + 1;
	foreach ( wp_get_post_terms( $product->get_id(), array( 'product_brand', 'pa_brand' ), array( 'fields' => 'names' ) ) as $brand ) $out['brands'][ $brand ] = ( $out['brands'][ $brand ] ?? 0 ) + 1;
	if ( count( $out['samples'] ) < 10 && 'publish' === $product->get_status() ) $out['samples'][] = array( 'id' => $product->get_id(), 'name' => $product->get_name(), 'sku' => $product->get_sku(), 'price' => $product->get_price(), 'stock' => $product->is_in_stock(), 'reviews' => $product->get_review_count(), 'rating' => $product->get_average_rating(), 'sales' => $product->get_total_sales(), 'specs' => (bool) get_post_meta( $product->get_id(), '_bhc_specs', true ) );
}
arsort( $out['categories'] ); arsort( $out['brands'] );
echo wp_json_encode( $out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . PHP_EOL;
