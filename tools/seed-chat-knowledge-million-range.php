<?php
if ( ! defined( 'ABSPATH' ) ) { require dirname( __DIR__ ) . '/wp-load.php'; }
$start = isset( $argv[1] ) ? max( 0, absint( $argv[1] ) ) : 0;
$end = isset( $argv[2] ) ? min( 1000000, absint( $argv[2] ) ) : $start;
$step = 5000; $last = $start;
for ( $i = $start; $i < $end; $i++ ) {
	BHC_Chat_Knowledge_Store::upsert( bhc_chat_pack_v3m_row( $i ) );
	if ( ( $i + 1 ) % $step === 0 ) { $last = $i + 1; echo wp_json_encode( array( 'cursor' => $last, 'end' => $end, 'percent_of_total' => round( $last / 1000000 * 100, 2 ) ), JSON_UNESCAPED_UNICODE ) . PHP_EOL; }
}
echo wp_json_encode( array( 'done' => true, 'cursor' => $end, 'end' => $end ), JSON_UNESCAPED_UNICODE ) . PHP_EOL;
