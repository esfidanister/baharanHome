<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function bhc_catalog_dashboard_save() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'دسترسی مجاز نیست.' );
	check_admin_referer( 'bhc_catalog_settings' );
	$schedule = sanitize_key( wp_unslash( $_POST['schedule'] ?? 'daily' ) );
	if ( ! in_array( $schedule, array( 'daily', 'manual' ), true ) ) $schedule = 'daily';
	$max = max( 1, min( 20, absint( $_POST['max_items'] ?? 10 ) ) );
	update_option( 'bhc_market_schedule', $schedule, false );
	update_option( 'bhc_barchin_max_items', $max, false );
	$proxy = sanitize_key( wp_unslash( $_POST['proxy_mode'] ?? 'auto' ) );
	if ( ! in_array( $proxy, array( 'auto', 'datacenter', 'residential' ), true ) ) $proxy = 'auto';
	update_option( 'bhc_barchin_proxy_mode', $proxy, false );
	update_option( 'bhc_barchin_daily_limit', max( 1, min( 10, absint( $_POST['daily_limit'] ?? 3 ) ) ), false );
	$cadence = sanitize_key( wp_unslash( $_POST['cadence'] ?? 'weekly' ) );
	if ( ! in_array( $cadence, array( 'weekly', 'daily', 'off' ), true ) ) $cadence = 'weekly';
	update_option( 'bhc_barchin_cadence', $cadence, false );
	update_option( 'bhc_barchin_credit_budget', max( 30, min( 800, absint( $_POST['credit_budget'] ?? 200 ) ) ), false );
	update_option( 'bhc_catalog_feed_url', esc_url_raw( wp_unslash( $_POST['feed_url'] ?? '' ) ), false );
	$key = trim( (string) wp_unslash( $_POST['api_key'] ?? '' ) );
	if ( ! empty( $_POST['clear_key'] ) ) {
		delete_option( 'bhc_barchin_api_key' );
		$key = '';
	}
	if ( $key !== '' ) {
		if ( ! preg_match( '/^bk_live_[A-Za-z0-9]{20,}$/', $key ) ) {
			wp_safe_redirect( add_query_arg( 'bhc_key', 'bad', admin_url( 'admin.php?page=bhc-catalog-health' ) ) );
			exit;
		}
		update_option( 'bhc_barchin_api_key', function_exists( 'bhc_barchin_seal' ) ? bhc_barchin_seal( $key ) : $key, false );
	}
	wp_safe_redirect( add_query_arg( 'bhc_saved', '1', admin_url( 'admin.php?page=bhc-catalog-health' ) ) );
	exit;
}
add_action( 'admin_post_bhc_catalog_settings', 'bhc_catalog_dashboard_save' );

function bhc_catalog_dashboard_probe() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'دسترسی مجاز نیست.' );
	check_admin_referer( 'bhc_barchin_probe' );
	$id = absint( $_POST['product_id'] ?? 0 );
	$mode = sanitize_key( wp_unslash( $_POST['probe'] ?? 'estimate' ) );
	$result = function_exists( 'bhc_barchin_sync_product' ) ? bhc_barchin_sync_product( $id, $mode === 'run' ) : new WP_Error( 'missing', 'اتصال برچین بارگذاری نشده.' );
	update_option( 'bhc_barchin_last_probe', array( 'at' => current_time( 'mysql' ), 'mode' => $mode, 'product_id' => $id, 'ok' => ! is_wp_error( $result ), 'data' => is_wp_error( $result ) ? array( 'code' => $result->get_error_code(), 'message' => $result->get_error_message() ) : $result ), false );
	wp_safe_redirect( add_query_arg( 'bhc_probed', '1', admin_url( 'admin.php?page=bhc-catalog-health' ) ) );
	exit;
}
add_action( 'admin_post_bhc_barchin_probe', 'bhc_catalog_dashboard_probe' );

function bhc_catalog_dashboard_import() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'دسترسی مجاز نیست.' );
	check_admin_referer( 'bhc_barchin_import' );
	if ( ! empty( $_POST['save_auto'] ) ) update_option( 'bhc_barchin_auto_import', array( 'brand' => sanitize_text_field( wp_unslash( $_POST['brand'] ?? '' ) ), 'category' => sanitize_text_field( wp_unslash( $_POST['category_name'] ?? '' ) ) ), false );
	else delete_option( 'bhc_barchin_auto_import' );
	$mode = sanitize_key( wp_unslash( $_POST['import_mode'] ?? 'estimate' ) );
	$result = function_exists( 'bhc_barchin_import_catalog' ) ? bhc_barchin_import_catalog( wp_unslash( $_POST['brand'] ?? '' ), wp_unslash( $_POST['category_name'] ?? '' ), $_POST['import_max'] ?? 5, $mode === 'import' ) : new WP_Error( 'missing', 'اتصال برچین بارگذاری نشده.' );
	update_option( 'bhc_barchin_last_import', array( 'at' => current_time( 'mysql' ), 'ok' => ! is_wp_error( $result ), 'data' => is_wp_error( $result ) ? array( 'message' => $result->get_error_message() ) : $result ), false );
	wp_safe_redirect( add_query_arg( 'bhc_imported', '1', admin_url( 'admin.php?page=bhc-catalog-health' ) ) );
	exit;
}
add_action( 'admin_post_bhc_barchin_import', 'bhc_catalog_dashboard_import' );

function bhc_catalog_dashboard_page() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'دسترسی مجاز نیست.' );
	global $wpdb;
	$counts = wp_count_posts( 'product' );
	$stats = array(
		'total' => (int) ( $counts->publish ?? 0 ),
		'price' => (int) $wpdb->get_var( "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID=pm.post_id WHERE p.post_type='product' AND p.post_status='publish' AND pm.meta_key='_price' AND pm.meta_value<>''" ),
		'stock' => (int) $wpdb->get_var( "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID=pm.post_id WHERE p.post_type='product' AND p.post_status='publish' AND pm.meta_key='_stock_status' AND pm.meta_value='instock'" ),
		'quoted' => (int) $wpdb->get_var( 'SELECT COUNT(DISTINCT product_id) FROM ' . bhc_market_table() ),
		'fresh' => (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(DISTINCT product_id) FROM ' . bhc_market_table() . ' WHERE checked_at > %s', wp_date( 'Y-m-d H:i:s', time() - 2 * DAY_IN_SECONDS ) ) ),
		'missing' => 0,
	);
	$stats['missing'] = max( 0, $stats['total'] - $stats['quoted'] );
	$missing_ids = $wpdb->get_col( 'SELECT p.ID FROM ' . $wpdb->posts . ' p LEFT JOIN ' . bhc_market_table() . " q ON q.product_id=p.ID WHERE p.post_type='product' AND p.post_status='publish' AND q.id IS NULL ORDER BY p.post_date DESC LIMIT 12" );
	$missing = array();
	foreach ( (array) $missing_ids as $mid ) { $item = wc_get_product( (int) $mid ); if ( $item ) $missing[] = $item; }
	$quote_count = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . bhc_market_table() );
	$recent_quotes = $wpdb->get_results( 'SELECT q.product_id, q.sku, q.price, q.currency, q.availability, q.seller, q.checked_at, q.source_url, p.post_title FROM ' . bhc_market_table() . " q LEFT JOIN {$wpdb->posts} p ON p.ID=q.product_id WHERE q.source='barchin_digikala' ORDER BY q.checked_at DESC LIMIT 15", ARRAY_A );
	$open_run = get_option( 'bhc_barchin_open_run', array() );
	$alerts = (array) get_option( 'bhc_market_alerts', array() );
	$configured = function_exists( 'bhc_barchin_key' ) && bhc_barchin_key() !== '';
	$account = ( $configured && function_exists( 'bhc_barchin_account' ) ) ? bhc_barchin_account() : null;
	$balance = is_array( $account ) ? $account['credits_balance'] : null;
	$schedule = get_option( 'bhc_market_schedule', 'daily' );
	$max_items = (int) get_option( 'bhc_barchin_max_items', 10 );
	$proxy_mode = get_option( 'bhc_barchin_proxy_mode', 'auto' );
	$daily_limit = (int) get_option( 'bhc_barchin_daily_limit', 3 );
	$cadence = get_option( 'bhc_barchin_cadence', 'weekly' );
	$credit_budget = (int) get_option( 'bhc_barchin_credit_budget', 200 );
	$queue_last = get_option( 'bhc_barchin_queue_last', array() );
	$category_status = (array) get_option( 'bhc_barchin_category_status', array() );
	$categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'number' => 80 ) );
	if ( is_wp_error( $categories ) ) $categories = array();
	$key_source = function_exists( 'bhc_barchin_key_source' ) ? bhc_barchin_key_source() : '';
	$key_mask = function_exists( 'bhc_barchin_key_mask' ) ? bhc_barchin_key_mask() : '';
	$next = wp_next_scheduled( 'bhc_catalog_feed_sync' );
	$probe = get_option( 'bhc_barchin_last_probe', array() );
	$import = get_option( 'bhc_barchin_last_import', array() );
	$auto_import = get_option( 'bhc_barchin_auto_import', array() );
	$brand_terms = get_terms( array( 'taxonomy' => taxonomy_exists( 'pa_brand' ) ? 'pa_brand' : 'product_brand', 'hide_empty' => false, 'number' => 80 ) );
	if ( is_wp_error( $brand_terms ) ) $brand_terms = array();
	$product_choices = $wpdb->get_results( "SELECT p.ID, p.post_title, sku.meta_value AS sku FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} sku ON sku.post_id=p.ID AND sku.meta_key='_sku' WHERE p.post_type='product' AND p.post_status='publish' ORDER BY p.post_title ASC" );
	$ready = $configured ? 'کلید آماده است' : 'کلید تنظیم نشده';
	?>
	<style>
		.bhc-health{max-width:1120px}
		.bhc-health h1{margin-bottom:12px}
		.bhc-health h2{margin:28px 0 10px;font-size:18px}
		.bhc-health .bhc-hero{display:flex;justify-content:space-between;align-items:flex-start;gap:24px;background:#fff;border:1px solid #dcdcde;border-right:4px solid <?php echo $configured ? '#1d7a34' : '#b32d2e'; ?>;border-radius:8px;padding:20px 22px;margin:0 0 16px}
		.bhc-health .bhc-balance{min-width:180px;text-align:left}
		.bhc-health .bhc-balance small{color:#646970}
		.bhc-health .bhc-balance strong{display:block;font-size:32px;line-height:1.2;margin:4px 0 10px}
		.bhc-health .bhc-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:0 0 8px}
		.bhc-health .bhc-card,.bhc-health .bhc-box{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:16px 18px;margin:0 0 12px}
		.bhc-health .bhc-card small{color:#646970;display:block;font-size:12px}
		.bhc-health .bhc-card strong{display:block;font-size:26px;line-height:1.2;margin-top:6px}
		.bhc-health .bhc-table-wrap{overflow:auto;background:#fff;border:1px solid #dcdcde;border-radius:8px;margin:0 0 8px}
		.bhc-health table{border-collapse:collapse;width:100%;margin:0}
		.bhc-health th,.bhc-health td{border-bottom:1px solid #f0f0f1;padding:10px 12px;text-align:right;vertical-align:top}
		.bhc-health th{background:#f6f7f7;font-weight:600}
		.bhc-health tr:last-child td{border-bottom:0}
		.bhc-health code,.bhc-health pre{direction:ltr;unicode-bidi:embed}
		.bhc-health pre{background:#f6f7f7;padding:12px;border-radius:6px;overflow:auto}
		.bhc-health .bhc-note{color:#50575e;line-height:1.8;margin:0 0 8px}
		.bhc-health .bhc-fields{display:grid;grid-template-columns:1fr 1fr;gap:14px 18px}
		.bhc-health .bhc-fields label,.bhc-health .bhc-span{display:flex;flex-direction:column;gap:6px;font-weight:600}
		.bhc-health .bhc-fields input,.bhc-health .bhc-fields select{max-width:100%;font-weight:400}
		.bhc-health .bhc-actions{display:flex;flex-wrap:wrap;gap:8px;margin:12px 0}
		.bhc-health a{text-underline-offset:2px}
		.bhc-health .bhc-pill{display:inline-block;margin-right:6px;padding:1px 8px;border-radius:99px;background:#fcf0f1;color:#8a2424;font-size:12px}
		@media(max-width:960px){.bhc-health .bhc-grid,.bhc-health .bhc-fields{grid-template-columns:1fr 1fr}}
		@media(max-width:782px){.bhc-health .bhc-hero,.bhc-health .bhc-balance{display:block;text-align:right}.bhc-health .bhc-grid,.bhc-health .bhc-fields{grid-template-columns:1fr}}
	</style>
	<div class="wrap bhc-health" dir="rtl">
		<h1>سلامت کاتالوگ و قیمت بازار</h1>
		<?php if ( isset( $_GET['bhc_saved'] ) ) echo '<div class="notice notice-success"><p>تنظیمات ذخیره شد. کلید کامل دیگر نمایش داده نمی‌شود.</p></div>'; ?>
		<?php if ( isset( $_GET['bhc_key'] ) && $_GET['bhc_key'] === 'bad' ) echo '<div class="notice notice-error"><p>کلید باید با bk_live_ شروع شود. کلید را از داشبورد برچین، بخش کلیدهای API، همان لحظهٔ ساخت کپی کنید.</p></div>'; ?>
		<?php if ( isset( $_GET['bhc_probed'] ) ) echo '<div class="notice notice-info"><p>نتیجهٔ آزمون در پایین صفحه ثبت شد. قیمت فروش ووکامرس تغییر نکرده است.</p></div>'; ?>
		<?php if ( ! $configured && get_option( 'bhc_barchin_api_key', '' ) !== '' ) echo '<div class="notice notice-error"><p>کلید ذخیره شده باز نمی‌شود. اگر نمک وردپرس عوض شده، کلید را دوباره در فرم پایین بگذارید.</p></div>'; elseif ( ! $configured ) echo '<div class="notice notice-error"><p>تا وقتی کلید ذخیره نشود دکمه‌های آزمون و آوردن کالا غیرفعال‌اند.</p></div>'; ?>
		<div class="bhc-hero">
			<div>
				<h2 style="margin:0 0 6px"><?php echo esc_html( $ready ); ?></h2>
				<p class="bhc-note" style="margin:0">قیمت دیجی‌کالا فقط مرجع بازار است و قیمت فروش را عوض نمی‌کند. موجودی کردیت <?php echo ! empty( $account['checked_at'] ) ? 'در ' . esc_html( $account['checked_at'] ) . ' خوانده شده' : 'هنوز از برچین خوانده نشده'; ?> و دو دقیقه در حافظه می‌ماند تا هر بار باز کردن صفحه درخواست تازه نزند.</p>
			</div>
			<div class="bhc-balance">
				<small>موجودی کردیت</small>
				<strong><?php echo $balance === null ? '—' : esc_html( number_format_i18n( $balance ) ); ?></strong>
				<a class="button button-secondary" href="https://barchin.net/dashboard" target="_blank" rel="noopener">خرید کردیت</a>
			</div>
		</div>
		<div class="bhc-grid">
			<?php foreach ( array( 'total' => 'محصول منتشرشده', 'price' => 'دارای قیمت بهاران', 'stock' => 'موجود بهاران', 'quoted' => 'دارای قیمت بازار', 'fresh' => 'استعلام تازه (۴۸ ساعت)', 'missing' => 'بدون هیچ استعلام' ) as $key => $label ) : ?>
				<div class="bhc-card"><small><?php echo esc_html( $label ); ?></small><strong><?php echo esc_html( number_format_i18n( $stats[ $key ] ) ); ?></strong></div>
			<?php endforeach; ?>
			<div class="bhc-card"><small>رکورد بازار</small><strong><?php echo esc_html( number_format_i18n( $quote_count ) ); ?></strong></div>
			<div class="bhc-card"><small>هشدار تغییر</small><strong><?php echo esc_html( number_format_i18n( count( $alerts ) ) ); ?></strong></div>
		</div>

		<h2>دفتر خواندن</h2>
		<div class="bhc-box bhc-note">
			<p>آخرین دور صف: <strong><?php echo esc_html( (string) ( $queue_last['at'] ?? 'هنوز اجرا نشده' ) ); ?></strong>. جست‌وجوی خودکار: <strong><?php echo ! empty( $auto_import['brand'] ) || ! empty( $auto_import['category'] ) ? esc_html( trim( ( $auto_import['brand'] ?? '' ) . ' / ' . ( $auto_import['category'] ?? '' ), ' /' ) ) : 'خاموش'; ?></strong>. اجرای باز: <strong><?php echo ! empty( $open_run['run_id'] ) ? esc_html( $open_run['run_id'] ) : 'ندارد'; ?></strong>.</p>
			<p>خطای یک کالا دور را قطع نمی‌کند. موجودی ناکافی، سقف درخواست، یا اجرای هنوز تمام‌نشده صف را همان‌جا نگه می‌دارد تا درخواست تکراری ساخته نشود. قیمت این جدول قیمت فروش نیست.</p>
		</div>
		<div class="bhc-table-wrap"><table>
			<thead><tr><th>خوانده‌شده</th><th>کالا</th><th>مدل</th><th>قیمت مرجع</th><th>موجودی بازار</th><th>فروشنده</th></tr></thead>
			<tbody>
			<?php if ( ! $recent_quotes ) : ?><tr><td colspan="6">هنوز قیمتی از دیجی‌کالا ذخیره نشده است.</td></tr><?php endif; ?>
			<?php foreach ( $recent_quotes as $quote ) :
				$age = strtotime( (string) $quote['checked_at'] );
				$fresh = $age && $age > time() - 2 * DAY_IN_SECONDS;
				?>
				<tr>
					<td><?php echo esc_html( (string) $quote['checked_at'] ); ?><?php if ( ! $fresh ) echo ' <span class="bhc-pill">کهنه</span>'; ?></td>
					<td><?php echo esc_html( (string) ( $quote['post_title'] ?: $quote['product_id'] ) ); ?></td>
					<td><code><?php echo esc_html( (string) $quote['sku'] ); ?></code></td>
					<td><?php $raw = (float) $quote['price']; echo esc_html( number_format_i18n( $raw ) ); ?><?php if ( $raw >= 100000 ) echo '<br><small>' . esc_html( number_format_i18n( $raw / 10 ) . ' تومان، اگر واحد ریال باشد' ) . '</small>'; ?></td>
					<td><?php echo esc_html( $quote['availability'] === 'in_stock' ? 'موجود' : ( $quote['availability'] === 'out_of_stock' ? 'ناموجود' : 'نامشخص' ) ); ?></td>
					<td><?php echo $quote['source_url'] ? '<a href="' . esc_url( $quote['source_url'] ) . '" target="_blank" rel="noopener">' . esc_html( (string) $quote['seller'] ) . '</a>' : esc_html( (string) $quote['seller'] ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table></div>
		<h2>آزمون یک محصول</h2>
		<div class="bhc-box">
			<p class="bhc-note">جست‌وجو با کد مدل انجام می‌شود. برآورد رایگان است. اجرای واقعی روی دیتاسنتر کردیت رزرو می‌کند و اگر صفحه نیاید هزینه‌ای نمی‌ماند.</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="bhc_barchin_probe">
				<?php wp_nonce_field( 'bhc_barchin_probe' ); ?>
				<p class="bhc-span"><label>محصول<br><select name="product_id" required><option value="">انتخاب کنید</option><?php foreach ( (array) $product_choices as $choice ) echo '<option value="' . esc_attr( $choice->ID ) . '">' . esc_html( $choice->post_title . ( $choice->sku ? ' — ' . $choice->sku : '' ) ) . '</option>'; ?></select></label></p>
				<div class="bhc-actions">
					<button class="button" name="probe" value="estimate" <?php disabled( ! $configured ); ?>>برآورد رایگان</button>
					<button class="button button-primary" name="probe" value="run" <?php disabled( ! $configured ); ?> onclick="return confirm('این اجرا روی دیتاسنتر است و کردیت رزرو می‌کند. ادامه می‌دهید؟');">اجرای واقعی روی دیتاسنتر</button>
				</div>
			</form>
			<?php if ( $probe ) : ?>
				<h3>آخرین آزمون — <?php echo esc_html( (string) ( $probe['at'] ?? '' ) ); ?></h3>
				<pre style="white-space:pre-wrap"><?php echo esc_html( wp_json_encode( $probe, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) ); ?></pre>
			<?php endif; ?>
		</div>

		<h2>آوردن کالای تازه از یک برند و دسته</h2>
		<div class="bhc-box">
			<p class="bhc-note">یک جست‌وجو برای برند و دسته، حداکثر ۱۰ کالا، روی دیتاسنتر. بدون کد مدل چیزی ساخته نمی‌شود. اگر مدل از قبل باشد رد می‌شود. عنوان، خلاصه، شرح، مشخصات، لینک منبع، عکس شاخص و تا سه عکس گالری ذخیره می‌شود. محصول پیش‌نویس و ناموجود می‌ماند و قیمت فروش خالی است.</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="bhc_barchin_import">
				<?php wp_nonce_field( 'bhc_barchin_import' ); ?>
				<div class="bhc-fields">
					<label>برند<select name="brand"><option value="">از روی پاسخ دیجی‌کالا</option><?php foreach ( $brand_terms as $term ) echo '<option value="' . esc_attr( $term->name ) . '"' . selected( $auto_import['brand'] ?? '', $term->name, false ) . '>' . esc_html( $term->name ) . '</option>'; ?></select></label>
					<label>دسته<select name="category_name"><option value="">از روی پاسخ دیجی‌کالا</option><?php foreach ( $categories as $term ) echo '<option value="' . esc_attr( $term->name ) . '"' . selected( $auto_import['category'] ?? '', $term->name, false ) . '>' . esc_html( $term->name ) . '</option>'; ?></select></label>
					<label>حداکثر کالا<input type="number" name="import_max" min="1" max="10" value="5"></label>
					<label class="bhc-span"><span><input type="checkbox" name="save_auto" value="1" <?php checked( ! empty( $auto_import['brand'] ) || ! empty( $auto_import['category'] ) ); ?>> در دور بعدی قیمت، همین برند و دسته تا ۳ کالای تازه خودکار پیش‌نویس شود</span></label>
				</div>
				<div class="bhc-actions">
					<button class="button" name="import_mode" value="estimate" <?php disabled( ! $configured ); ?>>برآورد این جست‌وجو</button>
					<button class="button button-primary" name="import_mode" value="import" <?php disabled( ! $configured ); ?> onclick="return confirm('کالاهای تازه به‌صورت پیش‌نویس ساخته می‌شوند و کردیت دیتاسنتر رزرو می‌شود. ادامه می‌دهید؟');">آوردن به محصولات</button>
				</div>
			</form>
			<?php if ( $import ) : ?>
				<p class="bhc-note"><?php echo ! empty( $import['ok'] ) ? 'آخرین عملیات در ' . esc_html( (string) ( $import['at'] ?? '' ) ) . ' انجام شد.' : 'آخرین عملیات ناموفق بود: ' . esc_html( (string) ( $import['data']['message'] ?? '' ) ); ?></p>
				<?php if ( ! empty( $import['data']['datacenter_hold'] ) ) echo '<p class="bhc-note">رزرو دیتاسنتر این جست‌وجو: ' . esc_html( number_format_i18n( (int) $import['data']['datacenter_hold'] ) ) . ' کردیت.</p>'; ?>
				<?php if ( ! empty( $import['data']['items'] ) && is_array( $import['data']['items'] ) ) : ?>
					<div class="bhc-table-wrap"><table><thead><tr><th>شناسه</th><th>عنوان</th><th>مدل</th><th>نتیجه</th></tr></thead><tbody>
					<?php foreach ( $import['data']['items'] as $item ) : ?>
						<tr><td><?php echo esc_html( (string) ( $item['id'] ?? '—' ) ); ?></td><td><?php echo esc_html( (string) ( $item['title'] ?? '' ) ); ?></td><td><code><?php echo esc_html( (string) ( $item['sku'] ?? '' ) ); ?></code></td><td><?php echo esc_html( trim( ( $item['brand'] ?? '' ) . ' / ' . ( $item['category'] ?? '' ) . ' / ' . ( isset( $item['images'] ) ? (int) $item['images'] . ' عکس' : '' ) . ' ' . ( $item['result'] ?? '' ) ) ); ?></td></tr>
					<?php endforeach; ?>
					</tbody></table></div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<h2>دسته‌بندی‌ها</h2>
		<div class="bhc-box bhc-note">
			<p>هر دور به تعداد تنظیم «هر نوبت»، مدل‌های بدون قیمت را روی دیتاسنتر و با حداکثر ۸ نتیجه می‌خواند. همان نتیجه با مدل بقیهٔ کالاها هم سنجیده می‌شود. مدل باید داخل عنوان یا آدرس باشد. ردیف بی‌مدل محصول جدید نمی‌سازد.</p>
			<p>اگر موجودی به رزرو نرسد صف همان‌جا می‌ایستد. شارژ: <a href="https://barchin.net/dashboard" target="_blank" rel="noopener">داشبورد برچین</a>.</p>
		</div>
		<div class="bhc-table-wrap"><table>
			<thead><tr><th>دسته</th><th>کالای منتشرشده</th></tr></thead>
			<tbody>
			<?php if ( ! $categories ) : ?><tr><td colspan="2">دسته‌ای با کالای منتشرشده نیست.</td></tr><?php endif; ?>
			<?php foreach ( array_slice( $categories, 0, 20 ) as $term ) : ?>
				<tr>
					<td><?php echo esc_html( $term->name ); ?></td>
					<td><?php echo esc_html( number_format_i18n( (int) $term->count ) ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table></div>
		<h2>محصول‌های بدون استعلام</h2>
		<div class="bhc-table-wrap"><table>
			<thead><tr><th>شناسه</th><th>نام</th><th>SKU</th></tr></thead>
			<tbody>
			<?php if ( ! $missing ) : ?><tr><td colspan="3">همهٔ محصولات منتشرشده دست‌کم یک رکورد بازار دارند.</td></tr><?php endif; ?>
			<?php foreach ( $missing as $p ) : ?><tr><td><?php echo esc_html( (string) $p->get_id() ); ?></td><td><?php echo esc_html( $p->get_name() ); ?></td><td><code><?php echo esc_html( $p->get_sku() ); ?></code></td></tr><?php endforeach; ?>
			</tbody>
		</table></div>
		<?php if ( $stats['missing'] > count( $missing ) ) echo '<p class="bhc-note">و ' . esc_html( number_format_i18n( $stats['missing'] - count( $missing ) ) ) . ' محصول دیگر.</p>'; ?>

		<h2>کلید API کجا تنظیم شود</h2>
		<div class="bhc-box bhc-note">
			<ol>
				<li>در <a href="https://barchin.net/docs" target="_blank" rel="noopener">مستندات برچین</a> وارد شوید و از بخش کلیدهای API یک کلید بسازید. کلید با <code>bk_live_</code> شروع می‌شود و فقط همان لحظه کامل دیده می‌شود.</li>
				<li>کلید را اینجا بچسبانید و ذخیره کنید. در دیتابیس وردپرس می‌ماند و در صفحه دوباره نشان داده نمی‌شود. اگر در <code>wp-config.php</code> ثابت <code>BARCHIN_API_KEY</code> باشد، همان اولویت دارد.</li>
				<li>کلید را در قالب، جاوااسکریپت فروشگاه یا مخزن گیت نگذارید. مسیرهای <code>/dashboard/*</code> برچین با این کلید کار نمی‌کنند.</li>
			</ol>
			<p>وضعیت فعلی: <strong><?php echo $configured ? 'فعال' : 'خالی'; ?></strong><?php if ( $key_mask ) echo ' — <code>' . esc_html( $key_mask ) . '</code> از ' . esc_html( $key_source === 'wp-config.php' ? 'wp-config.php' : ( $key_source === 'env' ? 'متغیر محیطی' : 'همین صفحه' ) ); ?></p>
			<p>نمونه در <code>wp-config.php</code>، بالای «That's all, stop editing»:</p>
			<pre>define( 'BARCHIN_API_KEY', 'bk_live_...' );</pre>
		</div>
		<h2>تنظیم خواندن</h2>
		<form class="bhc-box" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="bhc_catalog_settings">
			<?php wp_nonce_field( 'bhc_catalog_settings' ); ?>
			<div class="bhc-fields">
				<label class="bhc-span">کلید تازه، خالی یعنی همان کلید فعلی بماند<input type="password" name="api_key" autocomplete="off" placeholder="bk_live_..."></label>
				<label class="bhc-span"><span><input type="checkbox" name="clear_key" value="1"> حذف کلید ذخیره‌شده در وردپرس</span></label>
				<label class="bhc-span">آدرس فید CSV<input type="url" name="feed_url" value="<?php echo esc_attr( (string) get_option( 'bhc_catalog_feed_url', '' ) ); ?>" placeholder="https://"></label>
				<label>زمان‌بندی فید CSV<select name="schedule"><option value="daily" <?php selected( $schedule, 'daily' ); ?>>روزانه</option><option value="manual" <?php selected( $schedule, 'manual' ); ?>>فقط دستی</option></select></label>
				<label>دور قیمت دیجی‌کالا<select name="cadence"><option value="weekly" <?php selected( $cadence, 'weekly' ); ?>>هفتگی</option><option value="daily" <?php selected( $cadence, 'daily' ); ?>>روزانه</option><option value="off" <?php selected( $cadence, 'off' ); ?>>خاموش</option></select></label>
				<label>سقف کردیت هر دور<input type="number" name="credit_budget" min="30" max="800" value="<?php echo esc_attr( $credit_budget ); ?>"></label>
				<label>حداکثر نتیجه هر استعلام<input type="number" name="max_items" min="1" max="20" value="<?php echo esc_attr( $max_items ); ?>"></label>
				<label>تعداد مدل در هر نوبت<input type="number" name="daily_limit" min="1" max="10" value="<?php echo esc_attr( $daily_limit ); ?>"></label>
				<label>پروکسی اجرای تکی اگر مسکونی نباشد دیتاسنتر است<select name="proxy_mode"><option value="datacenter" <?php selected( $proxy_mode, 'datacenter' ); ?>>دیتاسنتر</option><option value="residential" <?php selected( $proxy_mode, 'residential' ); ?>>مسکونی</option><option value="auto" <?php selected( $proxy_mode, 'auto' ); ?>>خودکار، در عمل برای این فروشگاه دیتاسنتر اجرا می‌شود</option></select></label>
			</div>
			<div class="bhc-actions"><button class="button button-primary">ذخیره تنظیمات</button></div>
			<p class="bhc-note">نوبت بعدی فید: <strong><?php echo $next ? esc_html( wp_date( 'Y/m/d H:i', $next ) ) : 'ثبت نشده'; ?></strong>. قیمت دیجی‌کالا <?php echo $cadence === 'daily' ? 'هر روز' : ( $cadence === 'off' ? 'خاموش است' : 'هفته‌ای یک بار' ); ?> و در هر دور حداکثر تا <?php echo esc_html( number_format_i18n( $credit_budget ) ); ?> کردیت، فقط مدل‌های بدون قیمت را روی دیتاسنتر می‌خواند.</p>
			<?php if ( ! empty( $queue_last['items'] ) && is_array( $queue_last['items'] ) ) : ?>
				<div class="bhc-table-wrap"><table><thead><tr><th>مدل یا کار</th><th>نتیجهٔ همان دور</th></tr></thead><tbody>
				<?php foreach ( $queue_last['items'] as $item ) : if ( ! is_array( $item ) ) continue; ?>
					<tr><td><?php echo esc_html( (string) ( $item['model'] ?? $item['resumed'] ?? 'ورود خودکار' ) ); ?></td><td><?php echo esc_html( (string) ( $item['error'] ?? $item['import'] ?? ( isset( $item['matched'] ) ? $item['matched'] . ' تطبیق' : ( isset( $item['import_created'] ) ? $item['import_created'] . ' پیش‌نویس' : '' ) ) ) ); ?></td></tr>
				<?php endforeach; ?>
				</tbody></table></div>
			<?php endif; ?>
		</form>

		<h2>چه چیزی از برچین برای بهاران روشن است</h2>
		<div class="bhc-box bhc-note">
			<table>
				<thead><tr><th>امکان</th><th>در این فروشگاه</th></tr></thead>
				<tbody>
					<tr><td>اکتور <code>digikala-scraper</code></td><td>روشن. قیمت مرجع بازار؛ قیمت ووکامرس را عوض نمی‌کند.</td></tr>
					<tr><td>برآورد <code>/estimate</code></td><td>روشن. دکمهٔ برآورد رایگان رزرو نمی‌کند.</td></tr>
					<tr><td>اجرای async و خواندن <code>status_url</code></td><td>روشن. پاسخ ۲۰۲ شکست نیست و اجرای دوم ساخته نمی‌شود.</td></tr>
					<tr><td>حالت auto / manual</td><td>روشن، از تنظیم پروکسی همین صفحه.</td></tr>
					<tr><td>اسکرپ خام، تصویر، ترب، دیوار، باما، OAuth، وب‌هوک</td><td>برای پایش قیمت این کاتالوگ استفاده نمی‌شود. اتصال دیوار فقط وقتی لازم است که شمارهٔ آگهی خواسته شود و کردیت اتصال صفر است.</td></tr>
					<tr><td>MCP برای Cursor</td><td>جدا از وردپرس است: <code>https://barchin.net/mcp</code> و <a href="https://github.com/barchin-net/barchin-mcp" target="_blank" rel="noopener">barchin-mcp</a>. ایجنت‌ها: <a href="https://barchin.net/en/ai-agents" target="_blank" rel="noopener">barchin.net/en/ai-agents</a>.</td></tr>
				</tbody>
			</table>
			<p>جدول پروکسی هر درخواست: دیتاسنتر ۱ یا ۶ کردیت، مسکونی ۱۵ یا ۲۰، آنبلاکر ۲۵ یا ۳۰ (با رندر ۵ کردیت بیشتر). رندر اکتور را خود اکتور تعیین می‌کند. اجرای ناموفق صفر کردیت می‌ماند. موجودی auto باید به اندازهٔ <code>auto_hold</code> باشد حتی اگر در پایان کمتر کسر شود.</p>
		</div>
		<h2>قرارداد هزینه که این پنل رعایت می‌کند</h2>
		<div class="bhc-box bhc-note">
			<p>قبل از اجرا <code>POST /v1/actors/digikala-scraper/estimate</code> زده می‌شود. اجرا با <code>country=ir</code> و بدون انتظار اجباری ثبت می‌شود. اگر پاسخ ۲۰۲ باشد، همان <code>status_url</code> خوانده می‌شود و درخواست دوم ساخته نمی‌شود. وضعیت <code>failed</code> قیمتی ذخیره نمی‌کند. حالت پیش‌فرض auto است: رزرو تا گران‌ترین پروکسی مجاز، کسر فقط به اندازهٔ پروکسی‌ای که صفحه را آورده.</p>
			<p>کلید از <code>wp-config.php</code>، بعد از آن از ذخیرهٔ همین صفحه، و در آخر از متغیر محیطی خوانده می‌شود. مقدار کامل هیچ‌جا چاپ نمی‌شود. فید CSV جدا: <code><?php echo esc_html( (string) get_option( 'bhc_catalog_feed_url', 'تنظیم نشده' ) ); ?></code></p>
		</div>
		<h2>آخرین هشدارهای تغییر قیمت مرجع</h2>
		<pre class="bhc-box" style="white-space:pre-wrap"><?php echo esc_html( wp_json_encode( array_slice( $alerts, 0, 8 ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) ); ?></pre>
	</div>
	<?php
}
add_action( 'admin_menu', function () { add_submenu_page( 'baharaan-commerce', 'سلامت کاتالوگ', 'سلامت کاتالوگ', 'manage_options', 'bhc-catalog-health', 'bhc_catalog_dashboard_page' ); }, 40 );
