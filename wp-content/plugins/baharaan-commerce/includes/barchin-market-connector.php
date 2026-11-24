<?php
/**
 * Barchin actor client for market quotes.
 * Estimate never reserves credits. A run holds credits, then charges only pages that succeeded.
 * HTTP 202 means the run is still going; this client polls status_url and never submits a second run.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function bhc_barchin_seal( $plain ) {
	if ( ! function_exists( 'openssl_encrypt' ) ) return $plain;
	$iv = random_bytes( 16 );
	$secret = hash( 'sha256', wp_salt( 'auth' ), true );
	$raw = openssl_encrypt( $plain, 'AES-256-CBC', $secret, OPENSSL_RAW_DATA, $iv );
	return 'enc:' . base64_encode( $iv . $raw );
}

function bhc_barchin_stored_key() {
	$stored = get_option( 'bhc_barchin_api_key', '' );
	if ( ! is_string( $stored ) || trim( $stored ) === '' ) return '';
	$stored = trim( $stored );
	if ( strpos( $stored, 'bk_live_' ) === 0 ) {
		update_option( 'bhc_barchin_api_key', bhc_barchin_seal( $stored ), false );
		return $stored;
	}
	if ( strpos( $stored, 'enc:' ) !== 0 || ! function_exists( 'openssl_decrypt' ) ) return '';
	$bin = base64_decode( substr( $stored, 4 ), true );
	if ( ! is_string( $bin ) || strlen( $bin ) < 17 ) return '';
	$plain = openssl_decrypt( substr( $bin, 16 ), 'AES-256-CBC', hash( 'sha256', wp_salt( 'auth' ), true ), OPENSSL_RAW_DATA, substr( $bin, 0, 16 ) );
	return is_string( $plain ) ? trim( $plain ) : '';
}

function bhc_barchin_key() {
	if ( defined( 'BARCHIN_API_KEY' ) && trim( (string) BARCHIN_API_KEY ) !== '' ) return trim( (string) BARCHIN_API_KEY );
	$stored = bhc_barchin_stored_key();
	if ( $stored !== '' ) return $stored;
	$key = getenv( 'BARCHIN_API_KEY' );
	return is_string( $key ) ? trim( $key ) : '';
}

function bhc_barchin_key_source() {
	if ( defined( 'BARCHIN_API_KEY' ) && trim( (string) BARCHIN_API_KEY ) !== '' ) return 'wp-config.php';
	if ( bhc_barchin_stored_key() !== '' ) return 'option';
	$key = getenv( 'BARCHIN_API_KEY' );
	return is_string( $key ) && trim( $key ) !== '' ? 'env' : '';
}

function bhc_barchin_key_mask() {
	$key = bhc_barchin_key();
	if ( $key === '' ) return '';
	return substr( $key, 0, 12 ) . '••••';
}

function bhc_barchin_request( $method, $path, $body = null, $timeout = 20 ) {
	$key = bhc_barchin_key();
	if ( $key === '' ) return new WP_Error( 'barchin_key_missing', 'کلید برچین در سرور تنظیم نشده است.' );
	$args = array(
		'timeout' => max( 5, min( 95, (int) $timeout ) ),
		'method'  => strtoupper( $method ),
		'headers' => array( 'Authorization' => 'Bearer ' . $key, 'Accept' => 'application/json', 'Content-Type' => 'application/json' ),
	);
	if ( $body !== null ) $args['body'] = wp_json_encode( $body, JSON_UNESCAPED_UNICODE );
	$response = wp_safe_remote_request( 'https://barchin.net/api' . $path, $args );
	if ( is_wp_error( $response ) ) return $response;
	$code = (int) wp_remote_retrieve_response_code( $response );
	$decoded = json_decode( wp_remote_retrieve_body( $response ), true );
	$retry = (int) wp_remote_retrieve_header( $response, 'retry-after' );
	if ( $code === 402 ) return new WP_Error( 'insufficient_credits', is_array( $decoded ) ? (string) ( $decoded['error']['message'] ?? 'موجودی برای رزرو کافی نیست.' ) : 'موجودی برای رزرو کافی نیست.', array( 'status' => 402 ) );
	if ( $code === 429 ) return new WP_Error( 'rate_limited', 'سقف درخواست برچین پر شده است.', array( 'status' => 429, 'retry_after' => max( 1, $retry ) ) );
	if ( $code >= 400 || ! is_array( $decoded ) ) {
		$message = is_array( $decoded ) ? (string) ( $decoded['error']['message'] ?? 'درخواست برچین ناموفق بود.' ) : 'پاسخ برچین قابل خواندن نیست.';
		return new WP_Error( 'barchin_http_error', $message, array( 'status' => $code ) );
	}
	$decoded['_http'] = $code;
	$decoded['_retry_after'] = $retry > 0 ? $retry : 2;
	return $decoded;
}

function bhc_barchin_account() {
	$cached = get_transient( 'bhc_barchin_account' );
	if ( is_array( $cached ) ) return $cached;
	$me = bhc_barchin_request( 'GET', '/v1/me', null, 12 );
	if ( is_wp_error( $me ) ) return $me;
	$account = array(
		'credits_balance' => isset( $me['credits_balance'] ) ? (int) $me['credits_balance'] : null,
		'display_name'    => (string) ( $me['display_name'] ?? '' ),
		'checked_at'      => current_time( 'mysql' ),
	);
	set_transient( 'bhc_barchin_account', $account, 2 * MINUTE_IN_SECONDS );
	return $account;
}

function bhc_barchin_poll_run( array $body, $deadline ) {
	$status = (string) ( $body['status'] ?? '' );
	while ( in_array( $status, array( 'queued', 'processing' ), true ) && time() < $deadline ) {
		$url = (string) ( $body['status_url'] ?? '' );
		if ( $url === '' && ! empty( $body['run_id'] ) && ! empty( $body['actor_slug'] ) ) $url = '/v1/actors/' . rawurlencode( $body['actor_slug'] ) . '/runs/' . rawurlencode( $body['run_id'] );
		if ( $url === '' ) break;
		if ( strpos( $url, 'https://barchin.net/api' ) === 0 ) $url = substr( $url, strlen( 'https://barchin.net/api' ) );
		sleep( max( 1, min( 8, (int) ( $body['_retry_after'] ?? 2 ) ) ) );
		$next = bhc_barchin_request( 'GET', $url, null, 20 );
		if ( is_wp_error( $next ) ) return $next;
		$body = $next;
		$status = (string) ( $body['status'] ?? '' );
	}
	return $body;
}

function bhc_barchin_estimate( $slug, array $input ) {
	return bhc_barchin_request( 'POST', '/v1/actors/' . rawurlencode( $slug ) . '/estimate', $input, 20 );
}

function bhc_barchin_run_actor( $slug, array $input, $wait = 0, $proxy = null ) {
	$wait = max( 0, min( 90, absint( $wait ) ) );
	$mode = $proxy ?: get_option( 'bhc_barchin_proxy_mode', 'auto' );
	$query = 'country=ir';
	if ( in_array( $mode, array( 'datacenter', 'residential' ), true ) ) $query .= '&mode=manual&proxy=' . rawurlencode( $mode );
	$path = '/v1/actors/' . rawurlencode( $slug ) . '/runs?' . $query . ( $wait ? '&wait=' . $wait : '' );
	$body = bhc_barchin_request( 'POST', $path, $input, $wait ? 95 : 25 );
	if ( is_wp_error( $body ) ) return $body;
	if ( in_array( (string) ( $body['status'] ?? '' ), array( 'queued', 'processing' ), true ) ) {
		$body = bhc_barchin_poll_run( $body, time() + 80 );
		if ( is_wp_error( $body ) ) return $body;
	}
	$status = (string) ( $body['status'] ?? '' );
	if ( $status === 'failed' || ( isset( $body['error']['code'] ) && $status !== 'succeeded' && $status !== 'done' ) ) {
		return new WP_Error( 'barchin_run_failed', (string) ( $body['error']['message'] ?? 'اجرای برچین ناموفق بود و کردیت رزروشده برمی‌گردد.' ), array( 'credits_charged' => (int) ( $body['credits_charged'] ?? 0 ), 'credits_released' => (int) ( $body['credits_released'] ?? 0 ) ) );
	}
	return $body;
}

function bhc_barchin_model_code( WC_Product $product ) {
	$sku = strtoupper( (string) $product->get_sku() );
	if ( preg_match( '/([A-Z]{2,}[0-9][A-Z0-9]{2,})/', $sku, $match ) ) return $match[1];
	return '';
}

function bhc_barchin_product_input( WC_Product $product ) {
	$max_items = max( 1, min( 20, (int) get_option( 'bhc_barchin_max_items', 5 ) ) );
	$model = bhc_barchin_model_code( $product );
	return array( 'query' => $model !== '' ? $model : trim( $product->get_name() ), 'maxItems' => $max_items, 'includeSpecs' => false );
}

function bhc_barchin_sync_product( $product_id, $charge = true ) {
	if ( ! function_exists( 'wc_get_product' ) ) return new WP_Error( 'woocommerce_missing', 'ووکامرس فعال نیست.' );
	$product = wc_get_product( absint( $product_id ) );
	if ( ! $product ) return new WP_Error( 'product_missing', 'محصول پیدا نشد.' );
	$input = bhc_barchin_product_input( $product );
	$estimate = bhc_barchin_estimate( 'digikala-scraper', $input );
	if ( is_wp_error( $estimate ) ) return $estimate;
	if ( ! $charge ) return array( 'status' => 'estimated', 'product_id' => $product->get_id(), 'query' => $input['query'], 'estimate' => $estimate );
	$proxy = get_option( 'bhc_barchin_proxy_mode', 'auto' );
	$result = bhc_barchin_run_actor( 'digikala-scraper', $input, 0, $proxy === 'residential' ? 'residential' : 'datacenter' );
	if ( is_wp_error( $result ) ) return $result;
	if ( in_array( (string) ( $result['status'] ?? '' ), array( 'queued', 'processing' ), true ) ) {
		update_option( 'bhc_barchin_open_run', array( 'run_id' => $result['run_id'] ?? '', 'actor_slug' => 'digikala-scraper', 'product_id' => $product->get_id(), 'status_url' => $result['status_url'] ?? '' ), false );
		return array( 'status' => 'still_running', 'product_id' => $product->get_id(), 'run_id' => $result['run_id'] ?? '', 'credits_held' => $result['credits_held'] ?? null, 'message' => 'اجرا هنوز تمام نشده. درخواست تازه نفرستید؛ همان اجرا ادامه دارد.' );
	}
	delete_option( 'bhc_barchin_open_run' );
	$rows = $result['dataset'] ?? $result['items'] ?? $result['result']['dataset'] ?? array();
	if ( ! is_array( $rows ) ) $rows = array();
	$best = null;
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || ( $row['itemType'] ?? 'product' ) !== 'product' ) continue;
		$model = strtolower( bhc_barchin_model_code( $product ) );
		if ( $model === '' || strpos( bhc_barchin_row_haystack( $row ), $model ) === false ) continue;
		$price = isset( $row['price'] ) && is_numeric( $row['price'] ) ? (float) $row['price'] : null;
		if ( $price === null ) continue;
		if ( $best === null || $price < $best ) $best = $price;
		if ( $best === null || $price > $best ) continue;
		$best_row = $row;
	}
	$stored = 0;
	if ( isset( $best_row ) && function_exists( 'bhc_market_store_quote' ) && bhc_market_store_quote( array( 'product_id' => $product->get_id(), 'sku' => $product->get_sku(), 'source' => 'barchin_digikala', 'price' => $best, 'currency' => 'IRR', 'availability' => ! empty( $best_row['isAvailable'] ) ? 'in_stock' : 'out_of_stock', 'seller' => sanitize_text_field( $best_row['defaultSeller']['name'] ?? 'دیجی‌کالا' ), 'source_url' => esc_url_raw( $best_row['sourceUrl'] ?? $best_row['url'] ?? '' ) ) ) ) $stored = 1;
	return array(
		'status' => 'ok', 'stored' => $stored, 'source' => 'barchin_digikala', 'product_id' => $product->get_id(),
		'credits_held' => $result['credits_held'] ?? null, 'credits_charged' => $result['credits_charged'] ?? null, 'credits_released' => $result['credits_released'] ?? null,
		'proxy_used' => $result['proxy_used'] ?? null, 'pages_fetched' => $result['pages_fetched'] ?? null, 'estimate' => array( 'auto_hold' => $estimate['auto_hold'] ?? null, 'page_budget' => $estimate['page_budget'] ?? null ),
	);
}

add_action( 'bhc_barchin_sync_product', 'bhc_barchin_sync_product' );

function bhc_barchin_queue_ids( $limit ) {
	if ( ! function_exists( 'wc_get_products' ) ) return array();
	global $wpdb;
	$since = wp_date( 'Y-m-d H:i:s', time() - 7 * DAY_IN_SECONDS );
	$quoted = $wpdb->get_col( $wpdb->prepare( 'SELECT DISTINCT product_id FROM ' . bhc_market_table() . ' WHERE source=%s AND checked_at > %s', 'barchin_digikala', $since ) );
	$quoted = array_map( 'absint', (array) $quoted );
	$ids = array();
	foreach ( wc_get_products( array( 'status' => 'publish', 'limit' => 500, 'orderby' => 'date', 'order' => 'DESC', 'return' => 'ids' ) ) as $id ) {
		$id = absint( $id );
		if ( in_array( $id, $quoted, true ) ) continue;
		$ids[] = $id;
		if ( count( $ids ) >= $limit ) break;
	}
	return $ids;
}

function bhc_barchin_row_haystack( array $row ) {
	$parts = array( $row['titleFa'] ?? '', $row['title'] ?? '', $row['model'] ?? '', $row['slug'] ?? '', $row['sourceUrl'] ?? '', $row['url'] ?? '' );
	return strtolower( preg_replace( '/[^a-z0-9]+/i', '', implode( ' ', $parts ) ) );
}

function bhc_barchin_match_rows( array $products, array $rows ) {
	$matched = array(); $unknown = array();
	$codes = array();
	foreach ( $products as $product ) {
		$code = strtolower( bhc_barchin_model_code( $product ) );
		if ( $code !== '' ) $codes[ $product->get_id() ] = $code;
	}
	uasort( $codes, function ( $a, $b ) { return strlen( $b ) <=> strlen( $a ); } );
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) || ( $row['itemType'] ?? 'product' ) !== 'product' ) continue;
		$haystack = bhc_barchin_row_haystack( $row );
		$price = isset( $row['price'] ) && is_numeric( $row['price'] ) ? (float) $row['price'] : null;
		if ( $haystack === '' || $price === null ) continue;
		$hit = null;
		foreach ( $codes as $product_id => $code ) {
			if ( strpos( $haystack, $code ) === false ) continue;
			$longer = false;
			foreach ( $codes as $other ) { if ( $other !== $code && strlen( $other ) > strlen( $code ) && strpos( $other, $code ) !== false && strpos( $haystack, $other ) !== false ) { $longer = true; break; } }
			if ( $longer ) continue;
			foreach ( $products as $product ) { if ( $product->get_id() === $product_id ) { $hit = $product; break; } }
			break;
		}
		if ( ! $hit ) { if ( count( $unknown ) < 8 ) $unknown[] = array( 'title' => (string) ( $row['titleFa'] ?? $row['title'] ?? '' ), 'price' => $price ); continue; }
		if ( isset( $matched[ $hit->get_id() ] ) && $price >= $matched[ $hit->get_id() ] ) continue;
		bhc_market_store_quote( array( 'product_id' => $hit->get_id(), 'sku' => $hit->get_sku(), 'source' => 'barchin_digikala', 'price' => $price, 'currency' => 'IRR', 'availability' => ! empty( $row['isAvailable'] ) ? 'in_stock' : 'out_of_stock', 'seller' => sanitize_text_field( $row['defaultSeller']['name'] ?? 'دیجی‌کالا' ), 'source_url' => esc_url_raw( $row['sourceUrl'] ?? $row['url'] ?? '' ) ) );
		$matched[ $hit->get_id() ] = $price;
	}
	return array( 'matched' => count( $matched ), 'unknown' => $unknown );
}

function bhc_barchin_scan_model( WC_Product $seed, array $catalog ) {
	$code = bhc_barchin_model_code( $seed );
	if ( $code === '' ) return new WP_Error( 'model_missing', 'این کالا کد مدل قابل جست‌وجو ندارد.' );
	$input = array( 'query' => $code, 'maxItems' => max( 3, min( 8, (int) get_option( 'bhc_barchin_max_items', 8 ) ) ), 'includeSpecs' => false );
	$estimate = bhc_barchin_estimate( 'digikala-scraper', $input );
	if ( is_wp_error( $estimate ) ) return $estimate;
	$hold = 0;
	foreach ( (array) ( $estimate['pools'] ?? array() ) as $pool ) { if ( ( $pool['proxy'] ?? '' ) === 'datacenter' ) $hold = (int) $pool['credits_held']; }
	$account = bhc_barchin_account();
	$balance = is_array( $account ) ? (int) $account['credits_balance'] : 0;
	if ( $hold > 0 && $balance < $hold ) return new WP_Error( 'insufficient_credits', 'موجودی به رزرو دیتاسنتر این مدل نمی‌رسد.', array( 'need' => $hold, 'balance' => $balance ) );
	$result = bhc_barchin_run_actor( 'digikala-scraper', $input, 0, 'datacenter' );
	if ( is_wp_error( $result ) ) return $result;
	if ( in_array( (string) ( $result['status'] ?? '' ), array( 'queued', 'processing' ), true ) ) {
		update_option( 'bhc_barchin_open_run', array( 'run_id' => $result['run_id'] ?? '', 'actor_slug' => 'digikala-scraper', 'product_id' => $seed->get_id(), 'status_url' => $result['status_url'] ?? '' ), false );
		return new WP_Error( 'still_running', 'اجرای قبلی هنوز تمام نشده و دوباره فرستاده نمی‌شود.' );
	}
	delete_option( 'bhc_barchin_open_run' );
	delete_transient( 'bhc_barchin_account' );
	$rows = $result['dataset'] ?? $result['items'] ?? array();
	$match = bhc_barchin_match_rows( $catalog, is_array( $rows ) ? $rows : array() );
	$tried = (array) get_option( 'bhc_barchin_model_tries', array() );
	$tried[ $code ] = current_time( 'mysql' );
	if ( count( $tried ) > 400 ) $tried = array_slice( $tried, -400, 400, true );
	update_option( 'bhc_barchin_model_tries', $tried, false );
	return array( 'model' => $code, 'matched' => $match['matched'], 'charged' => $result['credits_charged'] ?? null, 'unknown' => $match['unknown'], 'hold' => $hold );
}

function bhc_barchin_run_queue() {
	$cadence = get_option( 'bhc_barchin_cadence', 'weekly' );
	if ( $cadence === 'off' || get_transient( 'bhc_barchin_queue_lock' ) ) return array( 'status' => 'skipped' );
	$last = strtotime( (string) get_option( 'bhc_barchin_queue_ran_at', '' ) );
	$gap = $cadence === 'daily' ? DAY_IN_SECONDS : 7 * DAY_IN_SECONDS;
	if ( $last && time() - $last < $gap - HOUR_IN_SECONDS ) return array( 'status' => 'waiting' );
	set_transient( 'bhc_barchin_queue_lock', 1, 20 * MINUTE_IN_SECONDS );
	$budget = max( 30, min( 800, (int) get_option( 'bhc_barchin_credit_budget', 200 ) ) );
	$spent = 0; $done = array(); $commit = true;
	$catalog = wc_get_products( array( 'status' => array( 'publish', 'draft' ), 'limit' => -1, 'orderby' => 'date', 'order' => 'DESC', 'return' => 'objects' ) );
	$open = get_option( 'bhc_barchin_open_run', array() );
	if ( is_array( $open ) && ! empty( $open['status_url'] ) ) {
		$polled = bhc_barchin_poll_run( $open, time() + 40 );
		if ( is_wp_error( $polled ) || in_array( (string) ( $polled['status'] ?? '' ), array( 'queued', 'processing' ), true ) ) {
			$commit = false;
			delete_transient( 'bhc_barchin_queue_lock' );
			return array( 'status' => 'still_running', 'run_id' => $open['run_id'] ?? '' );
		}
		$rows = $polled['dataset'] ?? $polled['items'] ?? array();
		if ( is_array( $rows ) && $rows ) {
			$match = bhc_barchin_match_rows( $catalog, $rows );
			$done[] = array( 'resumed' => $open['run_id'] ?? '', 'matched' => $match['matched'] );
		}
		delete_option( 'bhc_barchin_open_run' );
	}
	$tries = (array) get_option( 'bhc_barchin_model_tries', array() );
	$fresh = wp_date( 'Y-m-d H:i:s', time() - ( $cadence === 'daily' ? DAY_IN_SECONDS : 7 * DAY_IN_SECONDS ) );
	foreach ( bhc_barchin_queue_ids( max( 1, min( 10, (int) get_option( 'bhc_barchin_daily_limit', 3 ) ) ) ) as $id ) {
		if ( $spent >= $budget ) break;
		$seed = wc_get_product( $id );
		if ( ! $seed ) continue;
		$code = bhc_barchin_model_code( $seed );
		if ( $code !== '' && isset( $tries[ $code ] ) && $tries[ $code ] > $fresh ) continue;
		try {
			$scan = bhc_barchin_scan_model( $seed, $catalog );
		} catch ( Throwable $e ) {
			$done[] = array( 'model' => $code, 'error' => $e->getMessage() );
			continue;
		}
		if ( is_wp_error( $scan ) ) {
			$done[] = array( 'model' => $code, 'error' => $scan->get_error_message() );
			if ( in_array( $scan->get_error_code(), array( 'insufficient_credits', 'rate_limited', 'still_running' ), true ) ) break;
			continue;
		}
		$spent += (int) ( $scan['charged'] ?? 0 );
		$done[] = array( 'model' => $scan['model'], 'matched' => $scan['matched'], 'charged' => $scan['charged'] );
	}
	$auto = get_option( 'bhc_barchin_auto_import', array() );
	if ( $spent < $budget && is_array( $auto ) && ( ( $auto['brand'] ?? '' ) !== '' || ( $auto['category'] ?? '' ) !== '' ) ) {
		try {
			$imported = bhc_barchin_import_catalog( $auto['brand'] ?? '', $auto['category'] ?? '', 3, true );
			$done[] = is_wp_error( $imported ) ? array( 'import' => $imported->get_error_message() ) : array( 'import_created' => $imported['created'] ?? 0, 'import_skipped' => $imported['skipped'] ?? 0 );
		} catch ( Throwable $e ) {
			$done[] = array( 'import' => $e->getMessage() );
		}
	}
	if ( $commit ) {
		update_option( 'bhc_barchin_queue_ran_at', current_time( 'mysql' ), false );
		update_option( 'bhc_barchin_queue_last', array( 'at' => current_time( 'mysql' ), 'cadence' => $cadence, 'spent' => $spent, 'items' => $done ), false );
	}
	delete_transient( 'bhc_barchin_queue_lock' );
	return array( 'status' => 'ok', 'spent' => $spent, 'items' => $done );
}
function bhc_barchin_collect_urls( $value, array &$urls, $depth = 0, $key = '' ) {
	if ( $depth > 5 || count( $urls ) >= 8 ) return;
	$image_key = preg_match( '/image|img|photo|thumb|gallery/i', (string) $key );
	if ( is_string( $value ) && preg_match( '#^https://#', $value ) && ( $image_key || preg_match( '/\.(jpe?g|png|webp)(\?|$)/i', $value ) || strpos( $value, 'dkstatics' ) !== false ) ) $urls[] = $value;
	elseif ( is_array( $value ) ) { foreach ( $value as $child_key => $item ) bhc_barchin_collect_urls( $item, $urls, $depth + 1, is_string( $child_key ) ? $child_key : $key ); }
}

function bhc_barchin_row_images( array $row ) {
	$urls = array();
	bhc_barchin_collect_urls( $row, $urls );
	$urls = array_values( array_unique( $urls ) );
	return array_slice( $urls, 0, 4 );
}

function bhc_barchin_row_text( array $row, array $keys ) {
	foreach ( $keys as $key ) {
		$value = $row[ $key ] ?? '';
		if ( is_array( $value ) ) $value = $value['titleFa'] ?? $value['title'] ?? $value['name'] ?? '';
		$value = sanitize_text_field( (string) $value );
		if ( $value !== '' ) return $value;
	}
	return '';
}

function bhc_barchin_sku_owner( $sku ) {
	$sku = strtoupper( preg_replace( '/[^A-Z0-9]/', '', (string) $sku ) );
	if ( $sku === '' || ! function_exists( 'wc_get_products' ) ) return 0;
	$found = wc_get_products( array( 'sku' => $sku, 'limit' => 1, 'return' => 'ids', 'status' => array( 'publish', 'draft', 'private' ) ) );
	if ( $found ) return (int) $found[0];
	foreach ( wc_get_products( array( 'limit' => -1, 'return' => 'ids', 'status' => array( 'publish', 'draft' ) ) ) as $id ) {
		$product = wc_get_product( $id );
		if ( $product && strtoupper( preg_replace( '/[^A-Z0-9]/', '', $product->get_sku() ) ) === $sku ) return (int) $id;
	}
	return 0;
}

function bhc_barchin_flat_specs( $specs, array &$out, $prefix = '' ) {
	foreach ( (array) $specs as $key => $value ) {
		$name = trim( $prefix . ( is_string( $key ) ? ' ' . $key : '' ) );
		if ( is_array( $value ) ) bhc_barchin_flat_specs( $value, $out, $name );
		elseif ( is_scalar( $value ) && trim( (string) $value ) !== '' ) $out[ sanitize_text_field( $name ) ] = sanitize_text_field( (string) $value );
	}
}

function bhc_barchin_import_catalog( $brand, $category, $max, $charge = false ) {
	$brand = sanitize_text_field( $brand );
	$category = sanitize_text_field( $category );
	$query = trim( $brand . ' ' . $category );
	if ( $query === '' ) return new WP_Error( 'query_missing', 'حداقل برند یا دسته را از فهرست انتخاب کنید.' );
	$max = max( 1, min( 10, absint( $max ) ) );
	$input = array( 'query' => $query, 'maxItems' => $max, 'includeSpecs' => true );
	$estimate = bhc_barchin_estimate( 'digikala-scraper', $input );
	if ( is_wp_error( $estimate ) ) return $estimate;
	$hold = 0;
	foreach ( (array) ( $estimate['pools'] ?? array() ) as $pool ) { if ( ( $pool['proxy'] ?? '' ) === 'datacenter' ) $hold = (int) $pool['credits_held']; }
	if ( ! $charge ) return array( 'status' => 'estimated', 'query' => $query, 'datacenter_hold' => $hold, 'base_credits' => $estimate['base_credits'] ?? null, 'page_budget' => $estimate['page_budget'] ?? null );
	$account = bhc_barchin_account();
	$balance = is_array( $account ) ? (int) $account['credits_balance'] : 0;
	if ( $hold > 0 && $balance < $hold ) return new WP_Error( 'insufficient_credits', 'موجودی به رزرو دیتاسنتر این جست‌وجو نمی‌رسد.' );
	$result = bhc_barchin_run_actor( 'digikala-scraper', $input, 0, 'datacenter' );
	if ( is_wp_error( $result ) ) return $result;
	$rows = $result['dataset'] ?? $result['items'] ?? array();
	$created = 0; $skipped = 0; $items = array();
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	foreach ( (array) $rows as $row ) {
		if ( ! is_array( $row ) || ( $row['itemType'] ?? 'product' ) !== 'product' ) continue;
		$title = sanitize_text_field( $row['titleFa'] ?? $row['title'] ?? '' );
		if ( $title === '' ) continue;
		$sku = strtoupper( (string) ( $row['model'] ?? '' ) );
		if ( ! preg_match( '/[A-Z]{2,}[0-9]/', $sku ) && preg_match( '/([A-Z]{2,}[0-9][A-Z0-9]{2,})/i', $title . ' ' . ( $row['url'] ?? '' ), $found ) ) $sku = $found[1];
		$sku = strtoupper( preg_replace( '/[^A-Z0-9]/', '', $sku ) );
		if ( $sku === '' ) { $skipped++; $items[] = array( 'title' => $title, 'result' => 'بدون کد مدل ساخته نشد' ); continue; }
		if ( bhc_barchin_sku_owner( $sku ) ) { $skipped++; $items[] = array( 'title' => $title, 'sku' => $sku, 'result' => 'از قبل موجود است' ); continue; }
		$product = new WC_Product_Simple();
		$product->set_name( $title );
		$product->set_status( 'draft' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_stock_status( 'outofstock' );
		$product->set_sku( $sku );
		$plain = trim( wp_strip_all_tags( (string) ( $row['description'] ?? '' ) ) );
		$product->set_short_description( mb_substr( $plain, 0, 180, 'UTF-8' ) );
		$specs = array(); $spec_html = '';
		bhc_barchin_flat_specs( $row['specs'] ?? array(), $specs );
		foreach ( $specs as $k => $v ) $spec_html .= '<li><strong>' . esc_html( $k ) . ':</strong> ' . esc_html( $v ) . '</li>';
		$desc = wp_kses_post( (string) ( $row['description'] ?? '' ) );
		if ( $spec_html !== '' ) $desc .= '<h3>مشخصات</h3><ul>' . $spec_html . '</ul>';
		$source = esc_url_raw( $row['sourceUrl'] ?? $row['url'] ?? '' );
		if ( $source !== '' ) $desc .= '<p>منبع: <a href="' . esc_url( $source ) . '">صفحهٔ دیجی‌کالا</a></p>';
		$product->set_description( $desc );
		try { $id = $product->save(); } catch ( Exception $e ) { $skipped++; $items[] = array( 'title' => $title, 'sku' => $sku, 'result' => 'ذخیره نشد' ); continue; }
		if ( ! $id ) continue;
		$row_category = bhc_barchin_row_text( $row, array( 'category', 'categoryFa', 'categoryTitle' ) );
		if ( $row_category === '' ) $row_category = $category;
		$row_brand = bhc_barchin_row_text( $row, array( 'brand', 'brandFa', 'brandTitle' ) );
		if ( $row_brand === '' ) $row_brand = $brand;
		$cat_name = $category !== '' ? $category : $row_category;
		if ( $cat_name !== '' ) {
			$term = term_exists( $cat_name, 'product_cat' );
			if ( ! $term && $cat_name === $category ) $term = wp_insert_term( $category, 'product_cat' );
			$term_id = is_wp_error( $term ) || ! $term ? 0 : ( is_array( $term ) ? (int) $term['term_id'] : (int) $term );
			if ( $term_id ) wp_set_object_terms( $id, array( $term_id ), 'product_cat', false );
		}
		if ( $row_brand !== '' ) {
			update_post_meta( $id, '_bhc_brand', $row_brand );
			if ( taxonomy_exists( 'pa_brand' ) ) wp_set_object_terms( $id, $row_brand, 'pa_brand', false );
		}
		if ( $specs ) update_post_meta( $id, '_bhc_specs', $specs );
		if ( $source !== '' ) update_post_meta( $id, '_bhc_source_url', $source );
		$gallery = array(); $saved_images = 0;
		foreach ( bhc_barchin_row_images( $row ) as $image ) {
			$file = media_sideload_image( $image, $id, $title, 'id' );
			if ( is_wp_error( $file ) ) continue;
			update_post_meta( (int) $file, '_wp_attachment_image_alt', $title );
			if ( $saved_images === 0 ) set_post_thumbnail( $id, (int) $file );
			else $gallery[] = (int) $file;
			$saved_images++;
		}
		if ( $gallery ) update_post_meta( $id, '_product_image_gallery', implode( ',', $gallery ) );
		if ( isset( $row['price'] ) && is_numeric( $row['price'] ) && function_exists( 'bhc_market_store_quote' ) ) bhc_market_store_quote( array( 'product_id' => $id, 'sku' => $sku, 'source' => 'barchin_digikala', 'price' => (float) $row['price'], 'currency' => 'IRR', 'availability' => ! empty( $row['isAvailable'] ) ? 'in_stock' : 'out_of_stock', 'seller' => sanitize_text_field( $row['defaultSeller']['name'] ?? 'دیجی‌کالا' ), 'source_url' => $source ) );
		update_post_meta( $id, '_bhc_no_price_message', 'قیمت فروش بهاران جداست. قیمت دیجی‌کالا فقط مرجع بازار است.' );
		$created++;
		$items[] = array( 'id' => $id, 'title' => $title, 'sku' => $sku, 'brand' => $row_brand, 'category' => $cat_name, 'images' => $saved_images, 'result' => 'پیش‌نویس شد' );
	}
	delete_transient( 'bhc_barchin_account' );
	return array( 'status' => 'imported', 'query' => $query, 'created' => $created, 'skipped' => $skipped, 'items' => $items, 'credits_charged' => $result['credits_charged'] ?? null );
}

add_action( 'bhc_barchin_queue', 'bhc_barchin_run_queue' );
add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'bhc_barchin_queue' ) ) wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'bhc_barchin_queue' );
}, 30 );
