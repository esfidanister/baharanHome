<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once __DIR__ . '/chat-knowledge-pack-v2-data-fa.php';

function bhc_chat_pack_v3m_contexts() {
	$places = array( 'آپارتمان کوچک', 'خانه خانوادگی', 'آشپزخانه بزرگ', 'آشپزخانه کم‌جا', 'خانه اجاره‌ای', 'خانه نوساز', 'خانه قدیمی', 'دفتر کوچک', 'ویلا', 'آشپزخانه جهیزیه' );
	$uses = array( 'مصرف روزانه', 'استفاده کم‌صدا', 'مصرف کم‌انرژی', 'استفاده پرحجم', 'خرید مطمئن', 'نگهداری ساده', 'استفاده طولانی', 'بودجه محدود', 'انتخاب کاربردی', 'مقایسه چند مدل' );
	$out = array();
	foreach ( $places as $place ) foreach ( $uses as $use ) $out[] = 'برای ' . $place . ' و ' . $use;
	return $out;
}

function bhc_chat_pack_v3m_answer( $question_index ) {
	if ( 0 === $question_index % 17 ) return 'برای مقایسه دقیق، نام یا مدل مشخص کالاها را بفرستید. ظرفیت، مصرف، صدا، امکانات و خدمات را فقط با اطلاعات واقعی کاتالوگ بررسی می‌کنیم و برنده مطلق اعلام نمی‌کنیم.';
	if ( 0 === $question_index % 19 ) return 'برای انتخاب در محدوده بودجه، نوع وسیله، ظرفیت و کاربرد را بگویید. قیمت و موجودی فقط از کاتالوگ زنده خوانده می‌شود و عددی از خودم نمی‌سازم.';
	if ( 0 === $question_index % 23 ) return 'اگر مدل دقیق را بفرستید، مشخصات ثبت‌شده همان کالا را بررسی می‌کنم. در صورت نبود مدل، ابتدا نوع وسیله، ظرفیت و فضای نصب را بگویید تا انتخاب درست‌تری داشته باشید.';
	return 'برای راهنمایی دقیق‌تر، نام وسیله یا مدل، تعداد نفرات یا متراژ، ظرفیت و ویژگی مهمتان را بفرستید. قیمت، موجودی و مشخصات مدل فقط از کاتالوگ زنده خوانده می‌شود.';
}

function bhc_chat_pack_v3m_row( $index ) {
	$data = require __DIR__ . '/chat-knowledge-pack-v2-data-fa.php';
	$contexts = bhc_chat_pack_v3m_contexts();
	$question_count = count( $data['questions'] ); $need_count = count( $data['needs'] ); $product_count = count( $data['products'] );
	$question_index = $index % $question_count;
	$context_index = (int) floor( $index / $question_count ) % count( $contexts );
	$need_index = (int) floor( $index / ( $question_count * count( $contexts ) ) ) % $need_count;
	$product_index = (int) floor( $index / ( $question_count * count( $contexts ) * $need_count ) ) % $product_count;
	$product = $data['products'][ $product_index ]; $need = $data['needs'][ $need_index ]; $question = $data['questions'][ $question_index ];
	$intent = 0 === $question_index % 17 ? 'compare' : ( 0 === $question_index % 19 ? 'budget' : 'purchase' );
	return array( 'question' => $product . '، ' . $need . '، ' . $contexts[ $context_index ] . '؛ ' . $question, 'answer' => bhc_chat_pack_v3m_answer( $question_index ), 'lang' => 'fa', 'intent' => $intent, 'enabled' => 1, 'source' => 'pack_v3m' );
}

function bhc_chat_pack_v3m_run_batch( $batch_size = 2000 ) {
	if ( ! class_exists( 'BHC_Chat_Knowledge_Store' ) ) return array( 'error' => 'store_missing' );
	$total = 1000000; $cursor = (int) get_option( 'bhc_chat_knowledge_pack_v3m_cursor', 0 ); $batch_size = max( 100, min( 5000, absint( $batch_size ) ) );
	if ( $cursor >= $total ) return array( 'done' => true, 'cursor' => $cursor, 'total' => $total );
	BHC_Chat_Knowledge_Store::ensure(); $end = min( $total, $cursor + $batch_size );
	for ( $i = $cursor; $i < $end; $i++ ) BHC_Chat_Knowledge_Store::upsert( bhc_chat_pack_v3m_row( $i ) );
	update_option( 'bhc_chat_knowledge_pack_v3m_cursor', $end, false );
	update_option( 'bhc_chat_knowledge_pack_v3m_progress', array( 'cursor' => $end, 'total' => $total, 'percent' => round( $end / $total * 100, 2 ), 'updated_at' => current_time( 'mysql' ) ), false );
	return array( 'done' => $end >= $total, 'cursor' => $end, 'total' => $total, 'percent' => round( $end / $total * 100, 2 ) );
}
