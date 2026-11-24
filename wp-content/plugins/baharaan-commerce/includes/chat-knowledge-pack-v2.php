<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once __DIR__ . '/chat-knowledge-pack-v2-data-fa.php';
require_once __DIR__ . '/chat-knowledge-pack-v2-data-en.php';
require_once __DIR__ . '/chat-knowledge-pack-v2-data-finglish.php';

function bhc_chat_pack_v2_answer( $lang, $kind, $product = '' ) {
	if ( 'en' === $lang ) {
		return 'Please send the exact model name. I will guide you using the live catalog information and will not guess price, stock, or specifications.';
	}
	if ( 'greeting' === $kind ) return 'سلام و خوش آمدید به بهارآن. لطفاً نوع وسیله، کاربرد و بودجه تقریبی خود را بگویید تا راهنمایی دقیق‌تری ارائه کنم.';
	if ( 'partial' === $kind ) return 'برای راهنمایی دقیق، نام وسیله یا مدل، کاربرد و ظرفیت موردنیاز را بفرستید. اگر منظورتان قیمت یا موجودی است، نام مدل لازم است و در صورت وجود در تاریخچه از همان استفاده می‌شود.';
	if ( 'safety' === $kind ) return 'اول ایمنی را رعایت کنید: دستگاه را از برق بکشید و اگر گاز است شیر گاز را ببندید. هنگام بوی گاز هیچ کلید یا جرقه‌ای ایجاد نکنید و برای بررسی با پشتیبانی بهارآن تماس بگیرید.';
	if ( 'order' === $kind ) return 'برای پیگیری سفارش، شماره سفارش را از مسیر پیگیری فروشگاه وارد کنید. اطلاعات بانکی، رمز، CVV و کد یک‌بارمصرف را در چت ارسال نکنید.';
	if ( 'payment' === $kind ) return 'اگر پرداخت ناموفق شد، وضعیت سفارش را بررسی و در صورت نیاز پرداخت را از مسیر امن حساب کاربری دوباره انجام دهید. رمز، CVV یا شماره کارت را برای ما ارسال نکنید.';
	if ( 'shipping' === $kind ) return 'روش و زمان ارسال پس از ثبت شهر و انتخاب گزینه‌های قابل‌ارائه در تسویه‌حساب مشخص می‌شود. برای بررسی دقیق‌تر، نام کالا و شهر مقصد را بفرستید.';
	if ( 'warranty' === $kind ) return 'شرایط گارانتی به همان مدل و برگه ضمانت آن وابسته است. نام یا مدل دقیق را بفرستید تا اطلاعات ثبت‌شده همان کالا بررسی شود.';
	if ( 'return' === $kind ) return 'برای درخواست مرجوعی، شماره سفارش و موضوع درخواست را از مسیر پشتیبانی فروشگاه ارسال کنید. بررسی بر اساس همان سفارش و شرایط اعلام‌شده انجام می‌شود.';
	if ( 'support' === $kind ) return 'موضوع مشکل و در صورت وجود شماره سفارش را برای پشتیبانی بهارآن بفرستید. اطلاعات حساس مانند رمز، CVV، شماره کامل کارت و کد یک‌بارمصرف را ارسال نکنید.';
	if ( 'complaint' === $kind ) return 'متأسفیم که تجربه مطلوبی نداشته‌اید. شرح کوتاه مشکل و شماره سفارش را از مسیر پشتیبانی بهارآن بفرستید تا موضوع بررسی و پیگیری شود.';
	if ( 'compare' === $kind ) return 'برای مقایسه، نام یا مدل دقیق کالاها را بفرستید. معیارهایی مثل ظرفیت، مصرف، صدا، امکانات و خدمات را بر اساس اطلاعات واقعی کاتالوگ بررسی می‌کنیم و برنده مطلق اعلام نمی‌کنیم.';
	return 'برای انتخاب بهتر، نام وسیله، تعداد نفرات یا متراژ، ظرفیت، میزان صدا و مصرف و فضای نصب را بگویید. نام یا مدل را بفرستید تا فقط بر اساس مشخصات واقعی کاتالوگ راهنمایی کنم.';
}

function bhc_chat_knowledge_pack_v2_rows() {
	$fa = require __DIR__ . '/chat-knowledge-pack-v2-data-fa.php';
	$en = require __DIR__ . '/chat-knowledge-pack-v2-data-en.php';
	$fg = require __DIR__ . '/chat-knowledge-pack-v2-data-finglish.php';
	$rows = array();
	$add = static function ( &$rows, $question, $answer, $lang, $intent ) {
		$rows[] = array( 'question' => $question, 'answer' => $answer, 'lang' => $lang, 'intent' => $intent, 'enabled' => 1, 'source' => 'pack_v2' );
	};
	foreach ( $fa['greetings'] as $i => $q ) $add( $rows, $q . ' راهنمایی ' . ( $i + 1 ), bhc_chat_pack_v2_answer( 'fa', 'greeting' ), 'fa', 'unknown' );
	foreach ( $fa['partial'] as $i => $q ) $add( $rows, $q . ' ' . ( $i + 1 ), bhc_chat_pack_v2_answer( 'fa', 'partial' ), 'fa', 'purchase' );
	foreach ( $fa['errors'] as $i => $q ) $add( $rows, $q . ' راهنمایی ' . ( $i + 1 ), bhc_chat_pack_v2_answer( 'fa', 'partial' ), 'fa', 'support' );
	foreach ( $en['partial'] as $i => $q ) $add( $rows, $q . ' help ' . ( $i + 1 ), bhc_chat_pack_v2_answer( 'en', 'partial' ), 'en', 'purchase' );
	foreach ( $fg['partial'] as $i => $q ) $add( $rows, $q . ' lotfan ' . ( $i + 1 ), bhc_chat_pack_v2_answer( 'fa', 'partial' ), 'all', 'purchase' );
	foreach ( $en['products'] as $p_i => $product ) foreach ( $en['needs'] as $n_i => $need ) foreach ( array_slice( $en['questions'], 0, 12 ) as $q_i => $q ) $add( $rows, $product . ' ' . $need . ' ' . $q . ' ref ' . ( $p_i + 1 ) . '-' . ( $n_i + 1 ) . '-' . ( $q_i + 1 ), bhc_chat_pack_v2_answer( 'en', 'product' ), 'en', 0 === ( $q_i % 5 ) ? 'compare' : 'purchase' );
	foreach ( $fa['products'] as $p_i => $product ) foreach ( $fa['needs'] as $n_i => $need ) foreach ( array_slice( $fa['questions'], 0, 14 ) as $q_i => $q ) $add( $rows, $product . ' ' . $need . ' ' . $q . ' گزینه ' . ( $p_i + 1 ) . '-' . ( $n_i + 1 ) . '-' . ( $q_i + 1 ), bhc_chat_pack_v2_answer( 'fa', 'product', $product ), 'fa', 0 === ( $q_i % 7 ) ? 'compare' : 'purchase' );
	foreach ( $fg['products'] as $p_i => $product ) foreach ( $fg['needs'] as $n_i => $need ) foreach ( array_slice( $fg['questions'], 0, 12 ) as $q_i => $q ) $add( $rows, $product . ' ' . $need . ' ' . $q . ' soal ' . ( $p_i + 1 ) . '-' . ( $n_i + 1 ) . '-' . ( $q_i + 1 ), bhc_chat_pack_v2_answer( 'fa', 'product', $product ), 'all', 0 === ( $q_i % 6 ) ? 'budget' : 'purchase' );
	$special = array( 'بوی سوختگی دستگاه', 'جرقه از پریز', 'بوی گاز در آشپزخانه', 'سیم داغ شده', 'کودک و در فر', 'نشتی گاز را دیدم', 'فرق اینورتر با معمولی', 'بوش یا سامسونگ کدام بهتر است', 'زیر بودجه چه بخرم', 'پرداخت ناموفق شد', 'افزودن کالا به سبد', 'پیگیری سفارش', 'شرایط گارانتی مدل', 'زمان ارسال سفارش', 'ثبت شکایت از کالا', 'روش مرجوعی کالا', 'هزینه ارسال سفارش', 'گارانتی این مدل', 'پیگیری پرداخت ناموفق', 'شماره سفارش کجاست' );
	$special_intents = array( 'support', 'support', 'support', 'support', 'support', 'support', 'compare', 'compare', 'budget', 'payment', 'purchase', 'order', 'warranty', 'shipping', 'complaint', 'return', 'shipping', 'warranty', 'payment', 'order' );
	$special_kinds = array( 'safety', 'safety', 'safety', 'safety', 'safety', 'safety', 'compare', 'compare', 'compare', 'payment', 'product', 'order', 'warranty', 'shipping', 'product', 'return', 'shipping', 'warranty', 'payment', 'order' );
	foreach ( $special as $i => $q ) $add( $rows, $q . ' پرسش ' . ( $i + 1 ), bhc_chat_pack_v2_answer( 'fa', $special_kinds[ $i ] ), 'fa', $special_intents[ $i ] );
	// Explicit quota packs: each variant is a distinct natural-language follow-up
	// so the required intent families are measurable instead of being inferred from
	// the large product cartesian product above.
	$greeting_variants = array( 'لطفاً راهنمایی کنید', 'می‌خواهم خرید کنم', 'برای شروع چت آمدم', 'کمک لازم دارم', 'می‌شود راهنمایی‌ام کنید', 'دنبال وسیله مناسبم', 'برای خانه‌ام سؤال دارم', 'راهنمای خرید می‌خواهم', 'می‌خواهم مشورت کنم', 'از فروشگاه سؤال دارم', 'در انتخاب مرددم', 'اولین بار است اینجا هستم', 'برای خرید لوازم خانه آمدم', 'می‌خواهم اطلاعات بگیرم', 'یک سؤال دارم', 'دنبال پیشنهاد هستم', 'می‌خواهم بهتر انتخاب کنم', 'کمک برای انتخاب لازم دارم', 'می‌خواهم مشخصات را بدانم', 'برای آشپزخانه سؤال دارم', 'برای خانه جدید خرید دارم', 'برای جهیزیه راهنمایی می‌خواهم', 'دنبال خرید مطمئنم', 'قبل از خرید کمک می‌خواهم', 'می‌خواهم با کارشناس صحبت کنم', 'از کجا شروع کنم', 'لطفاً همراهی کنید', 'وقت بخیر یک سؤال دارم', 'سلام راهنمای خرید', 'سلام برای انتخاب کالا' );
	foreach ( $fa['greetings'] as $q ) foreach ( $greeting_variants as $variant ) $add( $rows, $q . '، ' . $variant, bhc_chat_pack_v2_answer( 'fa', 'greeting' ), 'fa', 'unknown' );
	$partial_variants = array( 'راهنمایی می‌خواهم', 'برای خرید می‌پرسم', 'مدل مناسبش چیست', 'اطلاعات بیشتر می‌خواهم', 'برای خانه لازم دارم', 'از کجا انتخاب کنم', 'چه ظرفیتی بگیرم', 'برای مصرف روزانه', 'برای فضای کم', 'برای مصرف کم', 'قیمت و موجودی را می‌خواهم', 'این مورد را توضیح دهید', 'مدل دقیق ندارم', 'کمک می‌کنید', 'پیشنهاد بدهید', 'برای خانواده‌ام', 'برای آشپزخانه‌ام', 'برای خانه کوچک', 'برای استفاده زیاد', 'برای خرید فوری', 'مقایسه می‌خواهم', 'مشخصاتش را بگویید', 'کدام مدل بهتر است', 'چه گزینه‌ای دارم', 'از این دسته می‌خواهم', 'رنگ سفید می‌خواهم', 'رنگ مشکی می‌خواهم', 'کم‌صدا می‌خواهم', 'کم‌مصرف می‌خواهم', 'نصب هم مهم است', 'گارانتی مهم است', 'مدل اقتصادی می‌خواهم', 'راهنمای انتخاب کوتاه', 'برای جهیزیه می‌خواهم', 'برای مستأجر می‌خواهم' );
	foreach ( $fa['partial'] as $q ) foreach ( $partial_variants as $variant ) $add( $rows, $q . '، ' . $variant, bhc_chat_pack_v2_answer( 'fa', 'partial' ), 'fa', 'purchase' );
	$store_topics = array( 'افزودن کالا به سبد', 'باز کردن سبد خرید', 'حذف کالا از سبد', 'تغییر تعداد کالا', 'ادامه خرید بعد از سبد', 'ثبت نشدن کالا در سبد', 'رفتن به تسویه حساب', 'پرداخت ناموفق', 'درگاه پرداخت باز نشد', 'پرداخت دوباره سفارش', 'رمز پویا لازم است', 'پیگیری سفارش', 'شماره سفارش', 'وضعیت سفارش', 'ارسال به شهر مقصد', 'انتخاب شهر برای ارسال', 'هزینه ارسال', 'زمان ارسال', 'گارانتی کالا', 'برگه ضمانت', 'درخواست مرجوعی', 'ثبت تیکت', 'تماس با پشتیبانی', 'ثبت شکایت', 'مشکل بعد از خرید', 'فاکتور سفارش', 'تغییر اطلاعات ارسال', 'نصب کالا', 'خدمات پس از فروش', 'کد رهگیری', 'سفارش ثبت نشد', 'لغو درخواست', 'پرسش درباره پرداخت', 'پرسش درباره ارسال', 'پرسش درباره ضمانت', 'پرسش درباره مرجوعی', 'پرسش درباره پشتیبانی', 'کمک برای تکمیل خرید', 'راهنمای مراحل سفارش', 'خرید از فروشگاه' );
	$store_variants = array( 'چطور انجام می‌شود', 'چه اطلاعاتی لازم است', 'از کجا شروع کنم', 'اگر خطا داد چه کنم', 'برای این کالا چطور است', 'لطفاً راهنمایی کنید', 'بدون اطلاعات بانکی حساس', 'با شماره سفارش', 'در حساب کاربری', 'در مرحله بعدی', 'بعد از انتخاب شهر', 'برای مدل انتخابی', 'برای سفارش فعلی', 'برای خرید آنلاین', 'برای پیگیری دقیق', 'با پشتیبانی بهارآن', 'در تسویه‌حساب', 'قبل از ثبت نهایی', 'بعد از ثبت سفارش', 'اگر ناموفق شد' );
	foreach ( $store_topics as $i => $topic ) foreach ( $store_variants as $j => $variant ) {
		if ( false !== strpos( $topic, 'پرداخت' ) || false !== strpos( $topic, 'درگاه' ) || false !== strpos( $topic, 'رمز' ) ) $kind = 'payment';
		elseif ( false !== strpos( $topic, 'ارسال' ) || false !== strpos( $topic, 'شهر' ) || false !== strpos( $topic, 'رهگیری' ) ) $kind = 'shipping';
		elseif ( false !== strpos( $topic, 'گارانتی' ) || false !== strpos( $topic, 'ضمانت' ) ) $kind = 'warranty';
		elseif ( false !== strpos( $topic, 'مرجوع' ) ) $kind = 'return';
		elseif ( false !== strpos( $topic, 'پشتیبانی' ) || false !== strpos( $topic, 'تیکت' ) ) $kind = 'support';
		elseif ( false !== strpos( $topic, 'شکایت' ) ) $kind = 'complaint';
		elseif ( false !== strpos( $topic, 'سفارش' ) || false !== strpos( $topic, 'فاکتور' ) || false !== strpos( $topic, 'لغو' ) ) $kind = 'order';
		else $kind = 'product';
		$intent = in_array( $kind, array( 'payment', 'shipping', 'warranty', 'return', 'order' ), true ) ? $kind : ( false !== strpos( $topic, 'شکایت' ) ? 'complaint' : 'support' );
		$add( $rows, $topic . '، ' . $variant, bhc_chat_pack_v2_answer( 'fa', $kind ), 'fa', $intent );
	}
	$safety_topics = array( 'بوی سوختگی', 'جرقه از پریز', 'بوی گاز', 'نشتی گاز', 'سیم داغ', 'دود از دستگاه', 'صدای غیرعادی خطرناک', 'برق گرفتگی بدنه', 'جرقه داخل فر', 'آتش گرفتن دستگاه', 'کودک کنار فر', 'بوی سوختگی لباسشویی', 'بوی سوختگی یخچال', 'بوی گاز اجاق', 'پریز داغ', 'کابل آسیب‌دیده', 'دستگاه خیس شده', 'آب کنار برق', 'جرقه هنگام روشن کردن', 'خاموش کردن اضطراری' );
	$safety_variants = array( 'چه کار کنم', 'الان خطرناک است؟', 'راهنمای فوری می‌خواهم', 'می‌توانم روشنش کنم؟', 'با پشتیبانی تماس بگیرم؟', 'اولین اقدام چیست', 'برای ایمنی چه کنم', 'دستگاه را از برق بکشم؟', 'شیر گاز را ببندم؟', 'آیا کلید بزنم؟', 'کودک نزدیک است', 'در خانه اتفاق افتاده', 'در آشپزخانه رخ داده', 'هنوز بو وجود دارد', 'بعد از خاموش کردن چه کنم', 'نیاز به بررسی دارم', 'لطفاً مرحله خطرناک نگویید', 'کمک فوری لازم دارم', 'دستگاه را دست نزنم؟', 'با خدمات تماس بگیرم؟' );
	foreach ( $safety_topics as $topic ) foreach ( $safety_variants as $variant ) $add( $rows, $topic . '، ' . $variant, bhc_chat_pack_v2_answer( 'fa', 'safety' ), 'fa', 'support' );
	return $rows;
}

function bhc_chat_knowledge_pack_v2_seed() {
	if ( get_option( 'bhc_chat_knowledge_pack_v2' ) || ! class_exists( 'BHC_Chat_Knowledge_Store' ) ) return;
	BHC_Chat_Knowledge_Store::ensure();
	$rows = bhc_chat_knowledge_pack_v2_rows();
	foreach ( $rows as $row ) BHC_Chat_Knowledge_Store::upsert( $row );
	update_option( 'bhc_chat_knowledge_pack_v2', array( 'count' => count( $rows ), 'seeded_at' => current_time( 'mysql' ) ), false );
}
add_action( 'init', 'bhc_chat_knowledge_pack_v2_seed', 35 );
