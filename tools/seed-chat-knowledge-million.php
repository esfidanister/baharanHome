<?php
if ( ! defined( 'ABSPATH' ) ) { require dirname( __DIR__ ) . '/wp-load.php'; }
require_once ABSPATH . 'wp-content/plugins/baharaan-commerce/includes/chat-knowledge-pack-v3-million.php';
$batch = isset( $argv[1] ) ? absint( $argv[1] ) : 2000;
$started = microtime( true );
while ( true ) {
	$result = bhc_chat_pack_v3m_run_batch( $batch );
	if ( ! empty( $result['error'] ) ) { fwrite( STDERR, $result['error'] . PHP_EOL ); exit( 1 ); }
	echo wp_json_encode( $result, JSON_UNESCAPED_UNICODE ) . PHP_EOL;
	if ( ! empty( $result['done'] ) ) break;
}
echo 'elapsed_seconds=' . round( microtime( true ) - $started, 1 ) . PHP_EOL;
