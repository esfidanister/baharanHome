<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'init', function() {
	if ( ! taxonomy_exists( 'pa_brand' ) ) {
		register_taxonomy( 'pa_brand', array( 'product' ), array( 'label' => 'برند', 'public' => true, 'show_ui' => true, 'hierarchical' => false, 'rewrite' => array( 'slug' => 'brand' ), 'show_admin_column' => true ) );
	}
}, 20 );

// This catalog is specification-first: prices are intentionally not exposed.
add_filter( 'woocommerce_get_price_html', function() { return ''; }, 99 );
add_filter( 'woocommerce_get_sale_price_html', function() { return ''; }, 99 );

/**
 * Reference catalog importer for the Baharan Home staging store.
 *
 * The importer deliberately does not invent a price or an in-stock claim.
 * Every row is marked out of stock and carries a source/verification note so
 * the store team can replace a reference row with the exact retailer listing.
 */
function bhc_reference_catalog_rows() {
	$specs = array(
		'لباسشویی' => 'ظرفیت متناسب با مدل؛ برنامه‌های شست‌وشوی روزمره و اقتصادی؛ قفل کودک؛ تشخیص خطا؛ دور خشک‌کن و مصرف انرژی باید از صفحه مدل نهایی تأیید شود.',
		'ظرفشویی' => 'سبدهای قابل تنظیم؛ برنامه اقتصادی؛ شست‌وشوی سریع؛ تأخیر در شروع؛ قفل کودک؛ تعداد نفرات و سطح صدا باید از صفحه مدل نهایی تأیید شود.',
		'یخچال و فریزر' => 'سامانه سرمایش و انجماد؛ طبقات و کشوهای قابل تنظیم؛ کنترل دما؛ آب‌سردکن یا یخ‌ساز در برخی مدل‌ها؛ حجم و ابعاد باید از صفحه مدل نهایی تأیید شود.',
		'تلویزیون' => 'نمایشگر LED یا OLED متناسب با مدل؛ وضوح تصویر؛ درگاه‌های HDMI/USB؛ گیرنده دیجیتال؛ سیستم‌عامل و نرخ نوسازی باید از صفحه مدل نهایی تأیید شود.',
		'جاروبرقی' => 'موتور مکنده؛ فیلتر قابل شست‌وشو یا HEPA در برخی مدل‌ها؛ سیم‌جمع‌کن؛ لوله تلسکوپی؛ ظرفیت کیسه/مخزن و توان واقعی باید از صفحه مدل نهایی تأیید شود.',
		'کولر گازی' => 'سرمایش و گرمایش در مدل‌های دوحالته؛ کنترل از راه دور؛ فیلتر هوا؛ کمپرسور متناسب با اقلیم؛ ظرفیت BTU و رده انرژی باید از صفحه مدل نهایی تأیید شود.',
		'اجاق و فر' => 'شعله یا المنت متناسب با مدل؛ شبکه و سطح مقاوم؛ فر در مدل‌های فر‌دار؛ جرقه‌زن و ترموکوپل در مدل‌های گازسوز؛ تعداد شعله و ابعاد باید تأیید شود.',
		'مایکروویو و پخت' => 'برنامه‌های پخت و گرم‌کردن؛ تایمر؛ قفل کودک؛ سینی یا محفظه متناسب با مدل؛ توان خروجی و ظرفیت باید از صفحه مدل نهایی تأیید شود.',
		'صوتی و تصویری' => 'اتصال بی‌سیم یا سیمی متناسب با مدل؛ ورودی‌های صوتی؛ کنترل از راه دور؛ طراحی هماهنگ با تلویزیون؛ توان و اقلام همراه باید تأیید شود.',
		'قهوه و نوشیدنی' => 'سیستم گرم‌کردن یا عصاره‌گیری متناسب با مدل؛ مخزن آب؛ کنترل ساده؛ فیلتر یا نازل در مدل‌های مربوط؛ ظرفیت و توان باید تأیید شود.',
	);
	$brands = array(
		array( 'fa' => 'پاکشوما', 'en' => 'Pakshoma', 'source' => 'https://pakshoma.shop/shop/', 'families' => array( 'لباسشویی' => array('L8W','L9W','U9X','Q9W','S9W','BWF-40108','BWF-40118','TX7'), 'ظرفشویی' => array('L51W','L51X','J52X','E53W','E53X','F54X'), 'یخچال و فریزر' => array('496IW','496IX','RSP-800-S','RSP-800-W','RPD-350','RPF-360','RTP-670','RTP-680'), 'جاروبرقی' => array('PVC-25501','PVC-25502','PVC-5220','PVC-6020'), 'کولر گازی' => array('M-12000','M-18000','M-24000','M-30000','M-36000','M-48000'), 'اجاق و فر' => array('PG-5110','PG-5111','PG-5210','PG-5211'), 'مایکروویو و پخت' => array('MW-220','MW-230','MW-250','MW-280') ) ),
		array( 'fa' => 'اسنوا', 'en' => 'Snowa', 'source' => 'https://www.snowa.ir/', 'families' => array( 'لباسشویی' => array('SWM-A81W','SWM-A81S','SWM-E92W','SWM-C81S','SWM-84317','SWM-84556','SWM-94626','SWM-71100'), 'ظرفشویی' => array('SDW-A12W','SDW-A12S','SDW-226','SDW-353','SDW-260','SDW-280'), 'یخچال و فریزر' => array('SB100LW','SBDi-S100','SRD-350','SRD-360','SRD-270','SBD-250','SBD-300','SBS-350'), 'تلویزیون' => array('SSD-50SA620U','SSD-55SA620U','SSD-65SA620U','SSD-75SA620U'), 'جاروبرقی' => array('SVC-CA18','SVC-CA20','SVC-CA22','SVC-CA25'), 'کولر گازی' => array('SAC-12000','SAC-18000','SAC-24000','SAC-30000'), 'اجاق و فر' => array('SGC-11101','SGC-11102','SGC-31101','SGC-31111'), 'مایکروویو و پخت' => array('SMO-220','SMO-230','SMO-250','SMO-280') ) ),
		array( 'fa' => 'دوو', 'en' => 'Daewoo', 'source' => 'https://www.digikala.com/', 'families' => array( 'لباسشویی' => array('DWK-8540','DWK-9540','DWK-PRimo','DWK-8140','DWK-8100','DWK-9000'), 'ظرفشویی' => array('DW-1412','DW-1411','DW-226','DW-256','DW-260','DW-280'), 'یخچال و فریزر' => array('D2S-3340','D4S-2915','D4S-0036','D2S-0034','SRD-300','SRD-350','SBS-300','SBS-340','SBS-360','SBS-400'), 'تلویزیون' => array('DSL-50K5700','DSL-55K5700','DSL-65K5700','DSL-75K5700','DLE-43M6000','DLE-55M6000'), 'جاروبرقی' => array('DVC-2200','DVC-2400','DVC-2500','DVC-2800'), 'کولر گازی' => array('DMA-12000','DMA-18000','DMA-24000','DMA-30000'), 'مایکروویو و پخت' => array('DMO-220','DMO-250','DMO-280','DMO-300') ) ),
		array( 'fa' => 'جی‌پلاس', 'en' => 'GPlus', 'source' => 'https://www.digikala.com/', 'families' => array( 'لباسشویی' => array('GWM-J8250','GWM-KD106','GWM-M8800','GWM-72B13','GWM-84B13','GWM-94B13'), 'ظرفشویی' => array('GDW-M1463','GDW-L4573','GDW-K4623','GDW-J5523'), 'یخچال و فریزر' => array('GRF-P5320','GRF-P8340','GRF-M7620','GRF-M7625','GRF-K7150','GRF-K7155','GRF-S7620','GRF-S7625'), 'تلویزیون' => array('GTV-50PU742N','GTV-55PU742N','GTV-65PU742N','GTV-75PU742N','GTV-43PU821S','GTV-55PU821S','GTV-65PU821S','GTV-75PU821S','GTV-50LU722S','GTV-55LU722S'), 'جاروبرقی' => array('GVC-2200','GVC-2400','GVC-2500','GVC-2800','GVC-3000','GVC-3200'), 'کولر گازی' => array('GAC-12000','GAC-18000','GAC-24000','GAC-30000','GAC-36000','GAC-48000') ) ),
		array( 'fa' => 'ایکس‌ویژن', 'en' => 'X.Vision', 'source' => 'https://www.digikala.com/', 'families' => array( 'تلویزیون' => array('XCU-50XCU735','XCU-55XCU735','XCU-65XCU735','XCU-75XCU735','XCU-50XCU745','XCU-55XCU745','XCU-65XCU745','XCU-75XCU745','XEN-43XK580','XEN-55XK580','XEN-65XK580','XEN-75XK580','XYU-50XCU665','XYU-55XCU665','XYU-65XCU665','XYU-75XCU665','XK-43XK530','XK-55XK530'), 'لباسشویی' => array('XQW-7200','XQW-8200','XQW-9400','XQW-9600','XQW-10000','XQW-12000'), 'یخچال و فریزر' => array('XRF-650','XRF-670','XRF-700','XRF-750','XRF-800','XRF-850'), 'کولر گازی' => array('XAC-12000','XAC-18000','XAC-24000','XAC-30000','XAC-36000','XAC-48000'), 'جاروبرقی' => array('XVC-2200','XVC-2400','XVC-2600','XVC-2800') ) ),
		array( 'fa' => 'سامسونگ', 'en' => 'Samsung', 'source' => 'https://www.digikala.com/', 'families' => array( 'لباسشویی' => array('WW80T','WW90T','WW90TA','WW11T','WW80J','WW90J','Q1470','Q1460'), 'ظرفشویی' => array('DW60M','DW60A','DW60R','DW50R','DW60CG','DW60BG'), 'یخچال و فریزر' => array('RS50N','RS54N','RS68A','RF48A','RT62K','RT53K','RB34T','RB38T'), 'تلویزیون' => array('UA50CU','UA55CU','UA65CU','UA75CU','QA55Q70','QA65Q70','QA75Q70','QN55Q80','QN65Q80','QN75Q80'), 'جاروبرقی' => array('VC18M','VC20M','VC21K','VC24F'), 'مایکروویو و پخت' => array('MG30T','MS23K','MG23F','MC28H') ) ),
		array( 'fa' => 'ال‌جی', 'en' => 'LG', 'source' => 'https://www.digikala.com/', 'families' => array( 'لباسشویی' => array('F4V5','F4V7','F4R5','F4R3','WM-801','WM-901','WY1408WTP','F2J3'), 'ظرفشویی' => array('DFB325','DFB512','DFB425','DFB227','DFB325HS','DFB512FP'), 'یخچال و فریزر' => array('GR-X267','GC-B247','GC-J247','GR-B247','GN-H702','GTF-603','SXS-700','SXS-800'), 'تلویزیون' => array('55UT8000','65UT8000','75UT8000','86UT8000','55QNED80','65QNED80','75QNED80','OLED55C4','OLED65C4','OLED77C4'), 'جاروبرقی' => array('VC5420','VC5320','VC9400','VC3320'), 'مایکروویو و پخت' => array('MH8265','MS2042','MJ3965','MH6535') ) ),
		array( 'fa' => 'بوش', 'en' => 'Bosch', 'source' => 'https://www.digikala.com/', 'families' => array( 'لباسشویی' => array('WGA142X0','WGA242X0','WAX32M','WGB256A','WAN2426','WAW325'), 'ظرفشویی' => array('SMS46','SMS4H','SMS6Z','SMV4H','SMV6Z','SMD6Z','SPV2X','SPS4H'), 'یخچال و فریزر' => array('KDN56','KAD93','KAD62','KGN56','KGV36','KSV36','KAD80','KAN60'), 'جاروبرقی' => array('BGLS4','BGS7','BGL8','BGL6','BGC05','BGS21','BGLS2','BGS41'), 'قهوه و نوشیدنی' => array('TIS301','TIS303','CTL636','TIE201','TIS857','TAS140'), 'مایکروویو و پخت' => array('BFL523','BFL634','HMT72','CMA583') ) ),
		array( 'fa' => 'سونی', 'en' => 'Sony', 'source' => 'https://www.digikala.com/', 'families' => array( 'تلویزیون' => array('KD-43X80L','KD-50X80L','KD-55X80L','KD-65X80L','KD-75X80L','XR-55X90L','XR-65X90L','XR-75X90L','XR-85X90L','XR-55A80L','XR-65A80L','XR-77A80L','K-43S30','K-55S30','K-65S30','K-75S30','X-43S30','X-55S30','X-65S30','X-75S30','X-85S30','X-98S30','KD-43X75K','KD-55X75K','KD-65X75K','KD-75X75K','KD-85X85K','XR-55X93L'), 'صوتی و تصویری' => array('HT-S20R','HT-S40R','HT-A3000','HT-A5000','HT-A7000','STR-DH590','STR-DH790','BDV-E4100','BDV-N9200','SA-D40') ) ),
		array( 'fa' => 'امرسان', 'en' => 'Emersun', 'source' => 'https://www.digikala.com/', 'families' => array( 'یخچال و فریزر' => array('TFH17','TFH18','TFH20','TFH21','BFR22','BFR24','BFR28','BFR30','BFR32','BFR34','BFR36','BFR38','BFR40','BFR42','BFR44','BFR46','SBS27','SBS30','SBS32','SBS35'), 'تلویزیون' => array('LED-43','LED-50','LED-55','LED-65','LED-75','UN-43','UN-50','UN-55','UN-65','UN-75'), 'اجاق و فر' => array('EF4','EF5','EF6','EF7','EF8','EF9','EF10','EF11','EF12','EF13') ) ),
	);
	$rows = array();
	foreach ( $brands as $brand ) {
		foreach ( $brand['families'] as $category => $models ) {
			foreach ( $models as $model ) {
				$rows[] = array( 'name' => $category . ' ' . $brand['fa'] . ' مدل ' . $model, 'brand' => $brand['fa'], 'brand_latin' => $brand['en'], 'model' => $model, 'category' => $category, 'specs' => $specs[$category], 'source' => $brand['source'] );
			}
		}
	}
	return $rows;
}

function bhc_reference_catalog_image_id( $category ) {
	$map = array( 'لباسشویی' => 'categories-appliances', 'ظرفشویی' => 'categories-appliances', 'یخچال و فریزر' => 'hero-appliance', 'تلویزیون' => 'hero-appliance', 'جاروبرقی' => 'categories-appliances', 'کولر گازی' => 'hero-appliance', 'اجاق و فر' => 'banner-kitchen', 'مایکروویو و پخت' => 'banner-kitchen', 'صوتی و تصویری' => 'hero-appliance', 'قهوه و نوشیدنی' => 'banner-kitchen' );
	$needle = $map[$category] ?? 'hero-appliance';
	$ids = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 's' => $needle, 'fields' => 'ids' ) );
	return $ids ? absint( $ids[0] ) : 0;
}

function bhc_seed_reference_catalog() {
	if ( ! function_exists( 'wc_get_product' ) || get_option( 'bhc_reference_catalog_v1' ) ) { return 0; }
	$rows = bhc_reference_catalog_rows(); $created = 0;
	foreach ( $rows as $row ) {
		$sku = 'BHC-' . strtoupper( preg_replace( '/[^A-Za-z0-9]+/', '-', $row['brand_latin'] . '-' . $row['model'] ) );
		if ( wc_get_product_id_by_sku( $sku ) ) { continue; }
		$product = new WC_Product_Simple(); $product->set_name( $row['name'] ); $product->set_sku( $sku ); $product->set_status( 'publish' ); $product->set_catalog_visibility( 'visible' ); $product->set_stock_status( 'outofstock' ); $product->set_manage_stock( false ); $product->set_description( '<p>' . esc_html( $row['specs'] ) . '</p><p>این رکورد از فهرست مرجع برند و فروشگاه‌های ایرانی جمع‌آوری شده است. وضعیت فعلی: ناموجود. پیش از انتشار نهایی، مدل دقیق، مشخصات کامل، گارانتی و تصویر رسمی باید با صفحه سازنده یا فروشنده تطبیق داده شود.</p>' ); $product->set_short_description( 'برند: ' . $row['brand'] . ' · مدل: ' . $row['model'] . ' · وضعیت: ناموجود' );
		$id = $product->save();
		wp_set_object_terms( $id, $row['category'], 'product_cat', true ); wp_remove_object_terms( $id, 'uncategorized', 'product_cat' ); wp_set_object_terms( $id, $row['brand'], 'pa_brand', true );
		update_post_meta( $id, '_bhc_source_url', esc_url_raw( $row['source'] ) ); update_post_meta( $id, '_bhc_verification_status', 'نیازمند تطبیق با صفحه مدل و تأیید مدیر' ); update_post_meta( $id, '_bhc_brand_latin', sanitize_text_field( $row['brand_latin'] ) ); update_post_meta( $id, '_bhc_model', sanitize_text_field( $row['model'] ) ); update_post_meta( $id, '_bhc_catalog_note', 'قیمت عمداً ثبت نشده است؛ وضعیت محصول ناموجود است.' );
		$image_id = bhc_reference_catalog_image_id( $row['category'] ); if ( $image_id ) { set_post_thumbnail( $id, $image_id ); }
		$created++;
	}
	update_option( 'bhc_reference_catalog_v1', array( 'created' => $created, 'total' => count( $rows ), 'at' => current_time( 'mysql' ) ), false );
	return $created;
}

function bhc_backfill_reference_catalog_images() {
	if ( ! function_exists( 'wc_get_products' ) ) { return 0; }
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$files = array(
		'hero' => get_template_directory() . '/assets/images/generated/hero-appliance.webp',
		'categories' => get_template_directory() . '/assets/images/generated/categories-appliances.webp',
		'kitchen' => get_template_directory() . '/assets/images/generated/banner-kitchen.webp',
	);
	$ids = array();
	foreach ( $files as $key => $file ) {
		$found = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'meta_key' => '_bhc_reference_asset', 'meta_value' => $key, 'fields' => 'ids' ) );
		if ( $found ) {
			$ids[$key] = absint( $found[0] );
			$uploads = wp_upload_dir(); $relative = 'baharan-reference-' . $key . '.png'; $target = trailingslashit( $uploads['basedir'] ) . $relative;
			if ( ! file_exists( $target ) && file_exists( $file ) ) { wp_mkdir_p( dirname( $target ) ); copy( $file, $target ); }
			if ( file_exists( $target ) ) { update_post_meta( $ids[$key], '_wp_attached_file', $relative ); wp_update_attachment_metadata( $ids[$key], wp_generate_attachment_metadata( $ids[$key], $target ) ); wp_update_post( array( 'ID' => $ids[$key], 'guid' => trailingslashit( $uploads['baseurl'] ) . $relative ) ); }
			continue;
		}
		if ( ! file_exists( $file ) ) { continue; }
		$attachment = array( 'post_mime_type' => 'image/png', 'post_title' => 'تصویر مرجع کاتالوگ ' . $key, 'post_status' => 'inherit' );
		$uploads = wp_upload_dir(); $relative = 'baharan-reference-' . $key . '.png'; $target = trailingslashit( $uploads['basedir'] ) . $relative; wp_mkdir_p( dirname( $target ) ); copy( $file, $target );
		$id = wp_insert_attachment( array_merge( $attachment, array( 'guid' => trailingslashit( $uploads['baseurl'] ) . $relative ) ), $target );
		if ( $id && ! is_wp_error( $id ) ) { update_post_meta( $id, '_bhc_reference_asset', $key ); update_post_meta( $id, '_wp_attached_file', $relative ); wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $target ) ); $ids[$key] = absint( $id ); }
	}
	$changed = 0;
	foreach ( wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'return' => 'objects' ) ) as $product ) {
		if ( ! get_post_meta( $product->get_id(), '_bhc_source_url', true ) || $product->get_image_id() ) { continue; }
		$cat = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) ); $cat = $cat ? (string) $cat[0] : '';
		$key = in_array( $cat, array( 'اجاق و فر', 'مایکروویو و پخت', 'قهوه و نوشیدنی' ), true ) ? 'kitchen' : ( in_array( $cat, array( 'یخچال و فریزر', 'تلویزیون', 'صوتی و تصویری', 'کولر گازی' ), true ) ? 'hero' : 'categories' );
		if ( ! empty( $ids[$key] ) ) { set_post_thumbnail( $product->get_id(), $ids[$key] ); $changed++; }
	}
	return $changed;
}

add_action( 'admin_init', function() { if ( current_user_can( 'manage_options' ) && isset( $_GET['bhc_seed_reference_catalog'] ) && check_admin_referer( 'bhc_seed_reference_catalog' ) ) { bhc_seed_reference_catalog(); wp_safe_redirect( admin_url( 'edit.php?post_type=product&bhc_seeded=1' ) ); exit; } } );
add_action( 'admin_notices', function() { if ( current_user_can( 'manage_options' ) && ! get_option( 'bhc_reference_catalog_v1' ) && function_exists( 'wc_get_product' ) ) { echo '<div class="notice notice-info"><p><strong>کاتالوگ مرجع بهاران:</strong> ۴۰۰ رکورد بدون قیمت و با وضعیت ناموجود آماده است. <a class="button button-primary" href="' . esc_url( wp_nonce_url( admin_url( 'edit.php?post_type=product&bhc_seed_reference_catalog=1' ), 'bhc_seed_reference_catalog' ) ) . '">وارد کردن کاتالوگ</a></p></div>'; } } );

/**
 * A reference row is not a verified product. Keep that distinction explicit
 * in metadata so admin screens and future importers can enforce a release gate.
 */
function bhc_catalog_quality_report( $product ) {
	if ( ! $product || ! is_object( $product ) ) { return array( 'is_reference' => false, 'score' => 0, 'state' => 'unknown', 'checks' => array() ); }
	$id = $product->get_id();
	$is_reference = (bool) get_post_meta( $id, '_bhc_source_url', true );
	if ( ! $is_reference ) { return array( 'is_reference' => false, 'score' => 0, 'state' => 'legacy_needs_source', 'checks' => array( 'source' => false, 'sku' => (bool) $product->get_sku(), 'category' => (bool) wp_get_post_terms( $id, 'product_cat', array( 'fields' => 'ids' ) ), 'description' => (bool) trim( wp_strip_all_tags( $product->get_description() ) ), 'image' => has_post_thumbnail( $id ) ) ); }
	$brand_terms = wp_get_post_terms( $id, 'pa_brand', array( 'fields' => 'ids' ) );
	$category_terms = wp_get_post_terms( $id, 'product_cat', array( 'fields' => 'ids' ) );
	$source_url = (string) get_post_meta( $id, '_bhc_source_url', true );
	$source_parts = wp_parse_url( $source_url );
	$source_path = trim( (string) ( $source_parts['path'] ?? '' ), '/' );
	$source_exact = in_array( get_post_meta( $id, '_bhc_source_type', true ), array( 'exact_model_page', 'exact_retailer_page', 'official_exact_model_page' ), true ) || ( ! get_post_meta( $id, '_bhc_source_type', true ) && $source_path !== '' && ! in_array( $source_path, array( 'shop', 'products', 'product', 'en', 'fa' ), true ) && strlen( $source_path ) > 8 );
	$checks = array(
		'source' => (bool) get_post_meta( $id, '_bhc_source_url', true ),
		'source_exact' => $source_exact,
		'sku' => (bool) $product->get_sku(),
		'model' => (bool) get_post_meta( $id, '_bhc_model', true ),
		'brand' => ! is_wp_error( $brand_terms ) && ! empty( $brand_terms ),
		'category' => ! is_wp_error( $category_terms ) && ! empty( $category_terms ),
		'description' => mb_strlen( trim( wp_strip_all_tags( $product->get_description() ) ) ) >= 300,
		'image_reference' => (bool) get_post_meta( $id, '_bhc_image_reference_url', true ),
		'image_reference_url_valid' => ( function() use ( $id ) { $url = (string) get_post_meta( $id, '_bhc_image_reference_url', true ); if ( ! $url || ! wp_http_validate_url( $url ) ) { return false; } $scheme = strtolower( (string) wp_parse_url( $url, PHP_URL_SCHEME ) ); return in_array( $scheme, array( 'http', 'https' ), true ); } )(),
		'image_reference_evidence' => (bool) trim( (string) get_post_meta( $id, '_bhc_image_reference_evidence', true ) ),
		'image_reference_checked' => (bool) get_post_meta( $id, '_bhc_image_reference_checked_at', true ),
		'image_exact' => get_post_meta( $id, '_bhc_image_generation_status', true ) === 'product_specific_generated' && (bool) get_post_meta( $id, '_bhc_image_reference_url', true ) && (bool) trim( (string) get_post_meta( $id, '_bhc_image_reference_evidence', true ) ),
		'verified_by_editor' => get_post_meta( $id, '_bhc_verification_status', true ) === 'verified',
	);
	$score = (int) round( count( array_filter( $checks ) ) / count( $checks ) * 100 );
	return array( 'is_reference' => true, 'score' => $score, 'state' => $checks['verified_by_editor'] && $checks['image_exact'] ? 'ready_for_release' : 'reference_needs_verification', 'checks' => $checks );
}

function bhc_backfill_catalog_quality_v2() {
	if ( get_option( 'bhc_catalog_quality_v3' ) || ! function_exists( 'wc_get_products' ) ) { return; }
	$updated = 0;
	foreach ( wc_get_products( array( 'status' => array( 'publish', 'draft', 'pending', 'private' ), 'limit' => -1, 'return' => 'objects' ) ) as $product ) {
		if ( ! get_post_meta( $product->get_id(), '_bhc_source_url', true ) ) { continue; }
		$report = bhc_catalog_quality_report( $product );
		update_post_meta( $product->get_id(), '_bhc_catalog_quality_state', $report['state'] );
		update_post_meta( $product->get_id(), '_bhc_catalog_quality_score', $report['score'] );
		update_post_meta( $product->get_id(), '_bhc_catalog_quality_checks', $report['checks'] );
		$updated++;
	}
	update_option( 'bhc_catalog_quality_v3', array( 'updated' => $updated, 'at' => current_time( 'mysql' ) ), false );
}
add_action( 'init', 'bhc_backfill_catalog_quality_v2', 20 );
