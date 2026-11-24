<?php
/**
 * cPanel Cron entry point. Example:
 * php /home/USER/public_html/wp-content/plugins/baharaan-commerce/tools/run-market-sync-cpanel.php
 */
$root = dirname( __DIR__, 4 );
$candidates = array( $root . '/wp-load.php', dirname( __DIR__, 3 ) . '/wp-load.php', dirname( __DIR__, 2 ) . '/wp-load.php' );
$loaded = false;
foreach ( $candidates as $wp_load ) { if ( is_file( $wp_load ) ) { require_once $wp_load; $loaded = true; break; } }
if ( ! $loaded ) { fwrite( STDERR, "wp-load.php not found. Set the correct absolute path in the cPanel Cron command.\n" ); exit( 2 ); }
if ( ! function_exists( 'bhc_chat_catalog_sync_feed' ) ) { fwrite( STDERR, "Baharaan Commerce sync function is unavailable.\n" ); exit( 3 ); }
$result = bhc_chat_catalog_sync_feed();
echo wp_json_encode( array( 'at' => current_time( 'mysql' ), 'result' => $result ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . PHP_EOL;
exit( ( $result['status'] ?? '' ) === 'ok' || ( $result['status'] ?? '' ) === 'not_configured' ? 0 : 1 );
