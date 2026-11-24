<?php
if ( ! defined( 'ABSPATH' ) ) { require dirname( __DIR__ ) . '/wp-load.php'; }
$queries = array( 'سلام', 'سلم', 'حالت چطوره', 'خوبی', 'یخچال مناسب خانواده چهار نفره', 'بهترین برند لباسشویی', 'لباسشویی کم مصرف', 'بوش یا سامسونگ', 'قیمت یخچال', 'یخچال موجوده', 'جارو رباتیک برای خانه کوچک', 'برای جهیزیه چه بخرم', 'صدای لباسشویی زیاد است', 'فیلتر تصفیه هوا', 'مقایسه ظرفشویی رومیزی و ایستاده', 'microwave for a small kitchen', 'fridge for a family of four', 'yakhchal kam masraf', 'kodom lebas shoyi behtare', 'گارانتی کالا', 'پیگیری سفارش' );
$out = array();
foreach ( $queries as $query ) {
	$smalltalk = function_exists( 'bhc_chat_smalltalk_reply' ) ? bhc_chat_smalltalk_reply( $query ) : '';
	$rows = class_exists( 'BHC_Chat_Knowledge_Store' ) ? BHC_Chat_Knowledge_Store::search( $query, 1 ) : array();
	$out[] = array( 'question' => $query, 'intent' => function_exists( 'bhc_chat_intent' ) ? bhc_chat_intent( $query ) : 'unknown', 'smalltalk' => $smalltalk !== '', 'knowledge_match' => ! empty( $rows ), 'matched_question' => $rows[0]['question'] ?? '', 'matched_answer' => $rows[0]['answer'] ?? '' );
}
echo wp_json_encode( $out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . PHP_EOL;
