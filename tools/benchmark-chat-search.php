<?php
if ( ! defined( 'ABSPATH' ) ) { require dirname( __DIR__ ) . '/wp-load.php'; }
$queries = array( 'یخچال مناسب خانواده چهار نفره', 'بهترین برند لباسشویی', 'جارو رباتیک برای خانه کوچک', 'fridge for a family of four', 'yakhchal kam masraf' );
$out = array();
foreach ( $queries as $query ) { $start = microtime( true ); $rows = BHC_Chat_Knowledge_Store::search( $query, 1 ); $out[] = array( 'question' => $query, 'milliseconds' => round( ( microtime( true ) - $start ) * 1000, 1 ), 'matched' => ! empty( $rows ), 'source_answer' => $rows[0]['answer'] ?? '' ); }
echo wp_json_encode( $out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . PHP_EOL;
