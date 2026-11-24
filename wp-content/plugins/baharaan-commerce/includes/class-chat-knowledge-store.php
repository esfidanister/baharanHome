<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class BHC_Chat_Knowledge_Store {
	const VERSION = '1.0.4';

	public static function table() { global $wpdb; return $wpdb->prefix . 'bhc_chat_knowledge'; }

	public static function ensure() {
		global $wpdb;
		$table = self::table();
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			question text NOT NULL,
			answer longtext NOT NULL,
			normalized varchar(191) NOT NULL,
			fingerprint char(64) NOT NULL,
			lang varchar(8) NOT NULL DEFAULT 'all',
			intent varchar(32) NOT NULL DEFAULT 'unknown',
			enabled tinyint(1) NOT NULL DEFAULT 1,
			source varchar(32) NOT NULL DEFAULT 'manual',
			usage_count bigint(20) unsigned NOT NULL DEFAULT 0,
			reviewed_by bigint(20) unsigned NOT NULL DEFAULT 0,
			reviewed_at datetime NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY fingerprint (fingerprint),
			KEY enabled_intent (enabled,intent),
			KEY normalized (normalized),
			FULLTEXT KEY normalized_fulltext (normalized),
			KEY source_enabled (source,enabled),
			KEY updated_at (updated_at)
		) {$charset};";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
	if ( get_option( 'bhc_chat_knowledge_store_version' ) !== self::VERSION ) { self::migrate_option(); update_option( 'bhc_chat_knowledge_store_version', self::VERSION, false ); }
	}

	public static function normalize( $text ) {
		$text = function_exists( 'bhc_chat_normalize' ) ? bhc_chat_normalize( $text ) : mb_strtolower( trim( wp_strip_all_tags( (string) $text ) ), 'UTF-8' );
		return mb_substr( preg_replace( '/\s+/u', ' ', trim( (string) $text ) ), 0, 191, 'UTF-8' );
	}

	public static function upsert( $entry ) {
		global $wpdb;
		$question = sanitize_text_field( $entry['question'] ?? '' ); $answer = sanitize_textarea_field( $entry['answer'] ?? '' );
		if ( '' === $question || '' === $answer ) { return 0; }
		$normalized = self::normalize( $question ); $now = current_time( 'mysql' ); $fingerprint = hash( 'sha256', $normalized . '|' . sanitize_key( $entry['lang'] ?? 'all' ) );
		$table = self::table();
		$wpdb->query( $wpdb->prepare( "INSERT INTO {$table} (question,answer,normalized,fingerprint,lang,intent,enabled,source,created_at,updated_at) VALUES (%s,%s,%s,%s,%s,%s,%d,%s,%s,%s) ON DUPLICATE KEY UPDATE question=VALUES(question), answer=VALUES(answer), intent=VALUES(intent), enabled=VALUES(enabled), source=VALUES(source), updated_at=VALUES(updated_at)", $question, $answer, $normalized, $fingerprint, sanitize_key( $entry['lang'] ?? 'all' ), sanitize_key( $entry['intent'] ?? 'unknown' ), empty( $entry['enabled'] ) ? 0 : 1, sanitize_key( $entry['source'] ?? 'manual' ), $now, $now ) );
		return (int) $wpdb->insert_id;
	}

	public static function search( $query, $limit = 5, $track = true ) {
		global $wpdb; if ( get_option( 'bhc_chat_knowledge_store_version' ) !== self::VERSION ) { self::ensure(); } $normalized = self::normalize( $query ); if ( '' === $normalized ) { return array(); }
		$table = self::table(); $take = min( 20, max( 1, absint( $limit ) ) );
		// The normalized column has a B-tree index: exact matches are the fast path.
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT id,question,answer,intent,lang,usage_count FROM {$table} WHERE enabled=1 AND normalized=%s ORDER BY usage_count DESC, updated_at DESC LIMIT %d", $normalized, $take ), ARRAY_A );
		$like = '%' . $wpdb->esc_like( $normalized ) . '%';
		if ( ! $rows && mb_strlen( $normalized, 'UTF-8' ) <= 80 ) {
			// The compact reviewed bank is the interactive search index. Generated
			// combinations remain available for coverage/reporting, not hot-path scans.
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT id,question,answer,normalized,intent,lang,usage_count FROM {$table} WHERE enabled=1 AND source='pack_v2' AND normalized LIKE %s ORDER BY updated_at DESC LIMIT %d", $like, 20 ), ARRAY_A );
		}
		if ( ! $rows ) {
			$stop = array( 'محصول', 'چطور', 'چگونه', 'چیست', 'برای', 'این', 'کالا', 'میخوام', 'می‌خواهم', 'لطفا' );
			$words = array_values( array_filter( preg_split( '/\s+/u', $normalized ), function ( $word ) use ( $stop ) {
				return mb_strlen( $word, 'UTF-8' ) >= 4 && ! in_array( $word, $stop, true );
			} ) );
			if ( $words ) {
				$where = array(); $args = array();
				foreach ( array_slice( $words, 0, 4 ) as $word ) { $where[] = 'normalized LIKE %s'; $args[] = '%' . $wpdb->esc_like( $word ) . '%'; }
				$args[] = min( 20, max( 1, absint( $limit ) ) );
				$rows = $wpdb->get_results( $wpdb->prepare( "SELECT id,question,answer,normalized,intent,lang,usage_count FROM {$table} WHERE enabled=1 AND source='pack_v2' AND (" . implode( ' OR ', $where ) . ") ORDER BY updated_at DESC LIMIT %d", $args ), ARRAY_A );
			}
		}
		if ( $rows && isset( $rows[0]['normalized'] ) ) {
			$stop = array( 'برای', 'این', 'است', 'چهار', 'نفره', 'می', 'را', 'با', 'از', 'the', 'for', 'a', 'and' );
			$words = array_values( array_filter( preg_split( '/\s+/u', $normalized ), function ( $word ) use ( $stop ) { return mb_strlen( $word, 'UTF-8' ) >= 3 && ! in_array( $word, $stop, true ); } ) );
			foreach ( $rows as &$candidate ) {
				$score = ( $candidate['normalized'] === $normalized ? 1000 : 0 );
				foreach ( $words as $word ) if ( mb_stripos( $candidate['normalized'], $word, 0, 'UTF-8' ) !== false ) $score += 10;
				if ( mb_stripos( $candidate['normalized'], $normalized, 0, 'UTF-8' ) !== false ) $score += 100;
				$candidate['_score'] = $score;
			}
			unset( $candidate );
			usort( $rows, function ( $a, $b ) { return (int) ( $b['_score'] ?? 0 ) <=> (int) ( $a['_score'] ?? 0 ); } );
			$rows = array_slice( $rows, 0, $take );
		}
		if ( $track ) {
			foreach ( $rows as $row ) { $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET usage_count=usage_count+1 WHERE id=%d", $row['id'] ) ); }
		}
		return $rows;
	}

	public static function get( $id ) {
		global $wpdb;
		$table = self::table();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id=%d", absint( $id ) ), ARRAY_A );
	}

	public static function save( $id, $entry ) {
		global $wpdb;
		if ( get_option( 'bhc_chat_knowledge_store_version' ) !== self::VERSION ) { self::ensure(); }
		$question = sanitize_text_field( $entry['question'] ?? '' );
		$answer = sanitize_textarea_field( $entry['answer'] ?? '' );
		if ( '' === $question || '' === $answer ) { return new WP_Error( 'bhc_kb_empty', 'پرسش و پاسخ هر دو لازم است.' ); }
		if ( mb_strlen( $question ) > 500 || mb_strlen( $answer ) > 4000 ) { return new WP_Error( 'bhc_kb_length', 'پرسش حداکثر ۵۰۰ و پاسخ حداکثر ۴۰۰۰ نویسه است.' ); }
		if ( function_exists( 'bhc_chat_contains_secret' ) && ( bhc_chat_contains_secret( $question ) || bhc_chat_contains_secret( $answer ) ) ) { return new WP_Error( 'bhc_kb_sensitive', 'پرسش یا پاسخ شامل اطلاعات حساس است و ذخیره نشد.' ); }
		if ( preg_match( '/(?:cvv|otp|رمز\s*پویا|شماره\s*کارت|کد\s*ملی)/iu', $question . ' ' . $answer ) ) { return new WP_Error( 'bhc_kb_sensitive', 'پرسش یا پاسخ شامل اطلاعات حساس است و ذخیره نشد.' ); }
		$intents = array( 'purchase', 'budget', 'compare', 'support', 'order', 'complaint', 'payment', 'shipping', 'return', 'warranty', 'unknown' );
		$intent = sanitize_key( $entry['intent'] ?? 'unknown' );
		if ( ! in_array( $intent, $intents, true ) ) { $intent = 'unknown'; }
		$enabled = empty( $entry['enabled'] ) ? 0 : 1;
		$source = sanitize_key( $entry['source'] ?? 'manual' );
		if ( ! in_array( $source, array( 'manual', 'human', 'seed', 'catalog', 'pack', 'pack_v2' ), true ) ) { $source = 'manual'; }
		$lang = sanitize_key( $entry['lang'] ?? 'fa' );
		if ( ! in_array( $lang, array( 'fa', 'all', 'en' ), true ) ) { $lang = 'fa'; }
		$normalized = self::normalize( $question );
		$fingerprint = hash( 'sha256', $normalized . '|' . $lang );
		$now = current_time( 'mysql' );
		$reviewer = get_current_user_id();
		$table = self::table();
		$id = absint( $id );
		$clash = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE fingerprint=%s AND id<>%d", $fingerprint, $id ) );
		if ( $clash && $id ) { return new WP_Error( 'bhc_kb_duplicate', 'پرسش مشابه با همین زبان از قبل وجود دارد.' ); }
		if ( $clash ) { $id = $clash; }
		$data = array(
			'question' => $question,
			'answer' => $answer,
			'normalized' => $normalized,
			'fingerprint' => $fingerprint,
			'lang' => $lang,
			'intent' => $intent,
			'enabled' => $enabled,
			'source' => $source,
			'reviewed_by' => $reviewer,
			'reviewed_at' => $now,
			'updated_at' => $now,
		);
		$format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s' );
		if ( $id && self::get( $id ) ) {
			$ok = $wpdb->update( $table, $data, array( 'id' => $id ), $format, array( '%d' ) );
			return false === $ok ? new WP_Error( 'bhc_kb_db', 'ذخیره در پایگاه داده انجام نشد.' ) : $id;
		}
		$data['created_at'] = $now;
		$format[] = '%s';
		$ok = $wpdb->insert( $table, $data, $format );
		return $ok ? (int) $wpdb->insert_id : new WP_Error( 'bhc_kb_db', 'ثبت ردیف انجام نشد.' );
	}

	public static function set_enabled( $id, $enabled ) {
		global $wpdb;
		$table = self::table();
		$ok = $wpdb->update( $table, array( 'enabled' => $enabled ? 1 : 0, 'updated_at' => current_time( 'mysql' ), 'reviewed_by' => get_current_user_id(), 'reviewed_at' => current_time( 'mysql' ) ), array( 'id' => absint( $id ) ), array( '%d', '%s', '%d', '%s' ), array( '%d' ) );
		return false !== $ok && $ok > 0;
	}

	public static function remove( $id ) {
		global $wpdb;
		return (bool) $wpdb->delete( self::table(), array( 'id' => absint( $id ) ), array( '%d' ) );
	}

	public static function disable_source( $source ) {
		global $wpdb;
		$source = sanitize_key( $source );
		if ( ! in_array( $source, array( 'catalog', 'seed', 'pack', 'pack_v2' ), true ) ) { return 0; }
		$table = self::table();
		$wpdb->query( $wpdb->prepare( "UPDATE {$table} SET enabled=0, updated_at=%s, reviewed_by=%d, reviewed_at=%s WHERE source=%s AND enabled=1", current_time( 'mysql' ), get_current_user_id(), current_time( 'mysql' ), $source ) );
		return (int) $wpdb->rows_affected;
	}

	public static function seed_catalog() {
		if ( get_option( 'bhc_chat_knowledge_seed_v1' ) ) { return; }
		self::ensure();
		$faqs = array(
			array( 'question' => 'چطور خرید کنم؟', 'answer' => 'محصول را انتخاب کنید، مشخصات و موجودی را بررسی کنید، آن را به سبد خرید اضافه کنید و سپس مراحل سبد و پرداخت را کامل کنید.', 'intent' => 'purchase' ),
			array( 'question' => 'روش خرید از فروشگاه چیست؟', 'answer' => 'برای خرید، محصول موردنظر را باز کنید، گزینه افزودن به سبد را بزنید و پس از بررسی سبد، اطلاعات ارسال و پرداخت را تکمیل کنید.', 'intent' => 'purchase' ),
			array( 'question' => 'آیا قیمت محصولات به‌روز است؟', 'answer' => 'قیمت نمایش‌داده‌شده از کاتالوگ زنده فروشگاه خوانده می‌شود؛ مبلغ نهایی را در سبد و صفحه پرداخت بررسی کنید.', 'intent' => 'purchase' ),
			array( 'question' => 'چطور محصول مناسب انتخاب کنم؟', 'answer' => 'مدل، بودجه، ظرفیت، ویژگی‌های مهم و نوع مصرف خود را بگویید تا فقط بر اساس اطلاعات واقعی کاتالوگ راهنمایی کنم.', 'intent' => 'purchase' ),
			array( 'question' => 'چطور محصولات را مقایسه کنم؟', 'answer' => 'نام یا مدل محصولات را بفرستید؛ مقایسه فقط بر اساس ویژگی‌های ثبت‌شده در کاتالوگ انجام می‌شود و موارد نامشخص اعلام خواهد شد.', 'intent' => 'compare' ),
			array( 'question' => 'آیا کالا موجود است؟', 'answer' => 'موجودی هر محصول از کاتالوگ زنده فروشگاه بررسی می‌شود. نام یا مدل محصول را ارسال کنید تا وضعیت دقیق اعلام شود.', 'intent' => 'support' ),
			array( 'question' => 'چطور سفارش خود را پیگیری کنم؟', 'answer' => 'برای پیگیری، شماره سفارش یا اطلاعات حداقلی قابل‌قبول فروشگاه را از مسیر پشتیبانی ارسال کنید. رمز، CVV یا کد یک‌بارمصرف لازم نیست.', 'intent' => 'order' ),
			array( 'question' => 'برای پشتیبانی چه اطلاعاتی لازم است؟', 'answer' => 'موضوع مشکل و شماره سفارش کافی است. هرگز رمز، CVV، رمز یک‌بارمصرف یا اطلاعات کامل کارت بانکی را ارسال نکنید.', 'intent' => 'support' ),
		);
		foreach ( $faqs as $faq ) { self::upsert( array_merge( $faq, array( 'lang' => 'fa', 'enabled' => true, 'source' => 'seed' ) ) ); }
		if ( function_exists( 'wc_get_products' ) ) {
			$products = wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'return' => 'objects' ) );
			foreach ( $products as $product ) {
				$name = $product->get_name(); $price = wp_strip_all_tags( $product->get_price_html() ); $stock = $product->is_in_stock() ? 'موجود' : 'ناموجود';
				$attributes = array(); foreach ( $product->get_attributes() as $attribute ) { $values = $attribute->is_taxonomy() ? wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) ) : $attribute->get_options(); if ( $values ) { $attributes[] = wc_attribute_label( $attribute->get_name() ) . ': ' . implode( '، ', array_map( 'sanitize_text_field', (array) $values ) ); } }
				$specs = $attributes ? ' مشخصات ثبت‌شده: ' . implode( '؛ ', $attributes ) . '.' : ' برای این محصول مشخصات تکمیلی ثبت نشده است.';
				$entries = array(
					array( 'question' => $name . ' چه قیمتی دارد؟', 'answer' => 'قیمت فعلی «' . $name . '» ' . $price . ' است. قیمت نهایی را هنگام ثبت سفارش بررسی کنید.', 'intent' => 'purchase' ),
					array( 'question' => 'آیا ' . $name . ' موجود است؟', 'answer' => 'وضعیت موجودی «' . $name . '»: ' . $stock . ' است.', 'intent' => 'purchase' ),
					array( 'question' => 'مشخصات ' . $name . ' چیست؟', 'answer' => 'اطلاعات ثبت‌شده برای «' . $name . '».' . $specs, 'intent' => 'purchase' ),
					array( 'question' => 'چرا ' . $name . ' را بخرم؟', 'answer' => '«' . $name . '» بر اساس اطلاعات واقعی ثبت‌شده در کاتالوگ پیشنهاد می‌شود. برای انتخاب دقیق‌تر، بودجه و کاربرد خود را مشخص کنید.', 'intent' => 'purchase' ),
				);
				foreach ( $entries as $entry ) { self::upsert( array_merge( $entry, array( 'lang' => 'fa', 'enabled' => true, 'source' => 'catalog' ) ) ); }
			}
		}
		update_option( 'bhc_chat_knowledge_seed_v1', current_time( 'mysql' ), false );
	}

	private static function migrate_option() {
		$rows = (array) get_option( 'bhc_chat_kb', array() ); foreach ( $rows as $row ) { self::upsert( $row ); }
	}
}
