<?php
if ( ! defined( 'ABSPATH' ) ) { require dirname( __DIR__ ) . '/wp-load.php'; }
global $wpdb;
$table = BHC_Chat_Knowledge_Store::table();
$rows = $wpdb->get_results( "SELECT source, lang, intent, COUNT(*) AS n FROM {$table} WHERE enabled=1 GROUP BY source, lang, intent ORDER BY source, lang, intent", ARRAY_A );
echo wp_json_encode( $rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . PHP_EOL;
