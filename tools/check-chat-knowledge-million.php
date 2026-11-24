<?php
if ( ! defined( 'ABSPATH' ) ) { require dirname( __DIR__ ) . '/wp-load.php'; }
global $wpdb;
$table = BHC_Chat_Knowledge_Store::table();
$summary = $wpdb->get_row( "SELECT COUNT(*) AS rows_count, COUNT(DISTINCT fingerprint) AS unique_count, SUM(answer='') AS empty_answers, SUM(question='') AS empty_questions FROM {$table} WHERE source='pack_v3m' AND enabled=1", ARRAY_A );
$intents = $wpdb->get_results( "SELECT intent, COUNT(*) AS count_rows FROM {$table} WHERE source='pack_v3m' AND enabled=1 GROUP BY intent ORDER BY intent", ARRAY_A );
$bad = $wpdb->get_row( "SELECT SUM(answer REGEXP 'تومان|ریال|<[^>]+>') AS bad_answers, MAX(CHAR_LENGTH(normalized)) AS max_normalized_length FROM {$table} WHERE source='pack_v3m' AND enabled=1", ARRAY_A );
echo wp_json_encode( array( 'summary' => $summary, 'quality' => $bad, 'intents' => $intents, 'progress' => get_option( 'bhc_chat_knowledge_pack_v3m_progress' ) ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . PHP_EOL;
