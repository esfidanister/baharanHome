<?php
if (!defined('ABSPATH')) exit;

add_action('admin_enqueue_scripts', function () {
	if (sanitize_key($_GET['page'] ?? '') !== 'bhc-chat-kb') return;
	wp_enqueue_style('bhc-admin', plugins_url('../assets/admin.css', __FILE__), [], '1.5.0');
});

function bhc_kb_intents() {
	return ['purchase' => 'خرید', 'budget' => 'بودجه', 'compare' => 'مقایسه', 'support' => 'پشتیبانی', 'order' => 'پیگیری سفارش', 'complaint' => 'شکایت', 'payment' => 'پرداخت', 'shipping' => 'ارسال', 'return' => 'مرجوعی', 'warranty' => 'ضمانت', 'unknown' => 'نامشخص'];
}

function bhc_kb_sources() {
	return ['human' => 'تأیید انسانی', 'manual' => 'ثبت دستی', 'seed' => 'پرسش پایه', 'catalog' => 'کپی کاتالوگ', 'pack' => 'بسته دانش', 'pack_v2' => 'بسته نسخه ۲'];
}

function bhc_kb_when($mysql) {
	if (!$mysql || $mysql === '0000-00-00 00:00:00') return '—';
	$ts = mysql2date('U', $mysql, false);
	if (class_exists('Baharaan_Commerce')) return Baharaan_Commerce::jalali_date((int) $ts);
	return mysql2date('Y/m/d H:i', $mysql);
}

function bhc_kb_user($id) {
	$user = $id ? get_userdata((int) $id) : false;
	return $user ? $user->display_name : 'ثبت نشده';
}

function bhc_kb_can() {
	return current_user_can('manage_baharan_commerce') || current_user_can('manage_options');
}

function bhc_kb_redirect($args, $notice) {
	set_transient('bhc_kb_notice_' . get_current_user_id(), $notice, 60);
	wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
	exit;
}

function bhc_kb_filters() {
	$intent = sanitize_key($_REQUEST['filter_intent'] ?? '');
	$source = sanitize_key($_REQUEST['filter_source'] ?? '');
	$enabled = (string) ($_REQUEST['filter_enabled'] ?? '');
	$sort = sanitize_key($_REQUEST['sort'] ?? 'updated');
	if (!isset(bhc_kb_intents()[$intent])) $intent = '';
	if (!isset(bhc_kb_sources()[$source])) $source = '';
	if (!in_array($enabled, ['', '1', '0'], true)) $enabled = '';
	if (!in_array($sort, ['updated', 'usage', 'question'], true)) $sort = 'updated';
	return [
		'page' => 'bhc-chat-kb',
		's' => sanitize_text_field(wp_unslash($_REQUEST['s'] ?? '')),
		'filter_intent' => $intent,
		'filter_source' => $source,
		'filter_enabled' => $enabled,
		'sort' => $sort,
		'paged' => max(1, absint($_REQUEST['paged'] ?? 1)),
	];
}

function bhc_kb_where($filters) {
	global $wpdb;
	$where = ['1=1'];
	$args = [];
	if ($filters['s'] !== '') {
		$like = '%' . $wpdb->esc_like($filters['s']) . '%';
		$where[] = '(question LIKE %s OR answer LIKE %s)';
		$args[] = $like;
		$args[] = $like;
	}
	if ($filters['filter_intent'] !== '') { $where[] = 'intent=%s'; $args[] = $filters['filter_intent']; }
	if ($filters['filter_source'] !== '') { $where[] = 'source=%s'; $args[] = $filters['filter_source']; }
	if ($filters['filter_enabled'] !== '') { $where[] = 'enabled=%d'; $args[] = (int) $filters['filter_enabled']; }
	return [implode(' AND ', $where), $args];
}

add_action('admin_post_bhc_kb_export', function () {
	if (!bhc_kb_can()) wp_die('دسترسی مجاز نیست');
	check_admin_referer('bhc_kb_export');
	if (!class_exists('BHC_Chat_Knowledge_Store')) wp_die('بانک دانش در دسترس نیست');
	global $wpdb;
	$filters = bhc_kb_filters();
	list($sql, $args) = bhc_kb_where($filters);
	$table = BHC_Chat_Knowledge_Store::table();
	$query = "SELECT id,question,answer,lang,intent,enabled,source,usage_count,reviewed_by,reviewed_at,created_at,updated_at FROM {$table} WHERE {$sql} ORDER BY updated_at DESC LIMIT 2000";
	$rows = $args ? $wpdb->get_results($wpdb->prepare($query, $args), ARRAY_A) : $wpdb->get_results($query, ARRAY_A);
	nocache_headers();
	header('Content-Type: application/json; charset=utf-8');
	header('Content-Disposition: attachment; filename=baharan-chat-kb.json');
	echo wp_json_encode(['format' => 'baharan-chat-kb-v1', 'rows' => $rows ?: []], JSON_UNESCAPED_UNICODE);
	exit;
});

function bhc_chat_kb_admin_page() {
	if (!bhc_kb_can()) wp_die('دسترسی مجاز نیست');
	if (!class_exists('BHC_Chat_Knowledge_Store')) wp_die('بانک دانش در دسترس نیست');
	if (get_option('bhc_chat_knowledge_store_version') !== BHC_Chat_Knowledge_Store::VERSION) BHC_Chat_Knowledge_Store::ensure();
	global $wpdb;
	$table = BHC_Chat_Knowledge_Store::table();
	$filters = bhc_kb_filters();
	$notice = get_transient('bhc_kb_notice_' . get_current_user_id());
	if ($notice) delete_transient('bhc_kb_notice_' . get_current_user_id());
	$preview = null;
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		check_admin_referer('bhc_kb_crud');
		$action = sanitize_key($_POST['bhc_kb_action'] ?? '');
		$id = absint($_POST['bhc_kb_id'] ?? 0);
		if ($action === 'preview') {
			$q = sanitize_text_field(wp_unslash($_POST['bhc_kb_probe'] ?? ''));
			$preview = ['q' => $q, 'rows' => $q === '' ? [] : BHC_Chat_Knowledge_Store::search($q, 3, false), 'model' => function_exists('bhc_chat_has_model_signal') && bhc_chat_has_model_signal($q)];
		} elseif ($action === 'save') {
			$result = BHC_Chat_Knowledge_Store::save($id, [
				'question' => wp_unslash($_POST['question'] ?? ''),
				'answer' => wp_unslash($_POST['answer'] ?? ''),
				'intent' => $_POST['intent'] ?? 'unknown',
				'lang' => $_POST['lang'] ?? 'fa',
				'enabled' => !empty($_POST['enabled']),
				'source' => $id ? ($_POST['source'] ?? 'manual') : 'manual',
			]);
			bhc_kb_redirect($filters, is_wp_error($result) ? ['error', $result->get_error_message()] : ['success', 'ردیف بانک دانش ذخیره شد.']);
		} elseif ($action === 'toggle' && $id) {
			$row = BHC_Chat_Knowledge_Store::get($id);
			$ok = $row && BHC_Chat_Knowledge_Store::set_enabled($id, empty($row['enabled']));
			bhc_kb_redirect($filters, $ok ? ['success', 'وضعیت ردیف عوض شد.'] : ['error', 'ردیف پیدا نشد.']);
		} elseif ($action === 'delete' && $id) {
			bhc_kb_redirect($filters, BHC_Chat_Knowledge_Store::remove($id) ? ['success', 'ردیف حذف شد.'] : ['error', 'حذف انجام نشد.']);
		} elseif ($action === 'disable_catalog') {
			$count = BHC_Chat_Knowledge_Store::disable_source('catalog');
			bhc_kb_redirect($filters, ['success', sprintf('%s ردیف کپی کاتالوگ غیرفعال شد تا قیمت کهنه در پاسخ نیاید.', number_format_i18n($count))]);
		} elseif ($action === 'import') {
			$file = $_FILES['kb_file'] ?? [];
			$raw = (!empty($file['tmp_name']) && (int) ($file['error'] ?? 1) === UPLOAD_ERR_OK && (int) ($file['size'] ?? 0) <= 1048576) ? file_get_contents($file['tmp_name']) : '';
			$data = json_decode((string) $raw, true);
			$incoming = [];
			if (is_array($data['rows'] ?? null)) $incoming = $data['rows'];
			elseif (is_array($data) && isset($data[0]['question'])) $incoming = $data;
			$rows = array_slice($incoming, 0, 200);
			$done = 0;
			foreach ($rows as $row) {
				if (!is_array($row)) continue;
				$saved = BHC_Chat_Knowledge_Store::save(0, array_merge($row, ['source' => 'manual', 'enabled' => !empty($row['enabled'])]));
				if (!is_wp_error($saved)) $done++;
			}
			bhc_kb_redirect($filters, $rows ? ['success', sprintf('%s ردیف از فایل وارد شد. سقف هر بار ۲۰۰ ردیف است.', number_format_i18n($done))] : ['error', 'فایل JSON معتبر نیست یا بزرگ‌تر از ۱ مگابایت است.']);
		}
	}
	list($where, $args) = bhc_kb_where($filters);
	$count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
	$total = (int) ($args ? $wpdb->get_var($wpdb->prepare($count_sql, $args)) : $wpdb->get_var($count_sql));
	if ($wpdb->last_error) wp_die(esc_html('خواندن بانک دانش ناموفق بود: ' . $wpdb->last_error));
	$pages = max(1, (int) ceil($total / 20));
	if ($filters['paged'] > $pages) $filters['paged'] = $pages;
	$order = ['usage' => 'usage_count DESC, updated_at DESC', 'question' => 'question ASC', 'updated' => 'updated_at DESC'][$filters['sort']];
	$list_sql = "SELECT * FROM {$table} WHERE {$where} ORDER BY {$order} LIMIT 20 OFFSET %d";
	$list_args = array_merge($args, [($filters['paged'] - 1) * 20]);
	$rows = $wpdb->get_results($wpdb->prepare($list_sql, $list_args), ARRAY_A);
	$stats = $wpdb->get_row("SELECT COUNT(*) total, SUM(enabled=1) active, SUM(enabled=0) inactive, SUM(source IN ('human','manual')) human_rows, SUM(source='catalog' AND enabled=1) stale FROM {$table}", ARRAY_A);
	$editing = absint($_GET['edit'] ?? 0);
	$edit = $editing ? BHC_Chat_Knowledge_Store::get($editing) : null;
	$intents = bhc_kb_intents();
	$sources = bhc_kb_sources();
	$base = $filters;
	unset($base['paged']);
	$link = function ($extra) use ($base) {
		return esc_url(add_query_arg(array_merge($base, $extra), admin_url('admin.php')));
	};
	$keep = '';
	foreach (['s', 'filter_intent', 'filter_source', 'filter_enabled', 'sort', 'paged'] as $key) {
		$keep .= '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($filters[$key]) . '">';
	}
	$nonce = wp_nonce_field('bhc_kb_crud', '_wpnonce', true, false);
	echo '<div class="wrap bhc-admin bhc-kb" dir="rtl">';
	if ($notice) echo '<div class="notice notice-' . ($notice[0] === 'error' ? 'error' : 'success') . '"><p>' . esc_html($notice[1]) . '</p></div>';
	echo '<header class="bhc-kb-hero"><div><span class="bhc-eyebrow">پاسخ تأییدشده</span><h1>بانک دانش چت</h1><p>فقط ردیف فعال، وقتی سؤال کد مدل نداشته باشد، در پاسخ ربات می‌آید. قیمت زنده از ووکامرس خوانده می‌شود. شمارنده استفاده کیفیت پاسخ نیست و آزمایش آن را زیاد نمی‌کند.</p></div><div class="bhc-kb-actions"><a class="button button-primary" href="#bhc-kb-editor">افزودن پاسخ</a><a class="button" href="' . esc_url(admin_url('admin.php?page=bhc-chat-unanswered')) . '">صف پرسش‌های بی‌پاسخ</a></div></header>';
	echo '<div class="bhc-kb-metrics"><article><b>' . esc_html(number_format_i18n((int) ($stats['total'] ?? 0))) . '</b><span>کل ردیف‌ها</span></article><article class="is-good"><b>' . esc_html(number_format_i18n((int) ($stats['active'] ?? 0))) . '</b><span>فعال در پاسخ خودکار</span></article><article class="is-info"><b>' . esc_html(number_format_i18n((int) ($stats['human_rows'] ?? 0))) . '</b><span>انسانی یا دستی</span></article><article class="is-warn"><b>' . esc_html(number_format_i18n((int) ($stats['stale'] ?? 0))) . '</b><span>کاتالوگ فعال با قیمت ذخیره‌شده</span></article></div>';
	echo '<div class="bhc-kb-split"><section class="bhc-kb-panel" id="bhc-kb-editor"><span class="bhc-section-kicker">ویرایش</span><h2>' . ($edit ? 'ویرایش ردیف #' . (int) $edit['id'] : 'پاسخ جدید') . '</h2><form method="post" class="bhc-kb-editor-form">' . $nonce . '<input type="hidden" name="bhc_kb_action" value="save"><input type="hidden" name="bhc_kb_id" value="' . esc_attr($edit['id'] ?? 0) . '">' . $keep;
	echo '<label>پرسش<input type="text" name="question" required maxlength="500" value="' . esc_attr($edit['question'] ?? '') . '"></label><label>پاسخ<textarea name="answer" required maxlength="4000" rows="5">' . esc_textarea($edit['answer'] ?? '') . '</textarea></label><div class="bhc-kb-selects"><label>نیت<select name="intent">';
	foreach ($intents as $key => $label) echo '<option value="' . esc_attr($key) . '" ' . selected($edit['intent'] ?? 'unknown', $key, false) . '>' . esc_html($label) . '</option>';
	echo '</select></label><label>زبان<select name="lang"><option value="fa" ' . selected($edit['lang'] ?? 'fa', 'fa', false) . '>فارسی</option><option value="en" ' . selected($edit['lang'] ?? '', 'en', false) . '>انگلیسی</option><option value="all" ' . selected($edit['lang'] ?? '', 'all', false) . '>همه</option></select></label></div>';
	if ($edit) echo '<input type="hidden" name="source" value="' . esc_attr($edit['source']) . '">';
	echo '<label class="bhc-kb-check"><input type="checkbox" name="enabled" value="1" ' . checked($edit ? (int) $edit['enabled'] : 1, 1, false) . '> در پاسخ خودکار استفاده شود</label><p class="bhc-kb-form-actions"><button class="button button-primary">ذخیره پاسخ</button>';
	if ($edit) echo ' <a class="button" href="' . $link([]) . '">انصراف</a>';
	echo '</p></form></section>';
	echo '<section class="bhc-kb-panel"><span class="bhc-section-kicker">آزمایش</span><h2>این سؤال چه پاسخی می‌گیرد؟</h2><p>همان جستجوی ربات، بدون افزایش شمارنده. اگر سؤال کد مدل داشته باشد، ربات قبل از بانک دانش سراغ کاتالوگ زنده می‌رود.</p><form method="post" class="bhc-kb-probe">' . $nonce . $keep . '<input type="hidden" name="bhc_kb_action" value="preview"><input type="text" name="bhc_kb_probe" value="' . esc_attr($preview['q'] ?? '') . '" placeholder="سؤال مشتری را بنویسید"><button class="button button-primary">آزمایش پاسخ</button></form>';
	if ($preview) {
		if ($preview['model']) echo '<p>این سؤال نشانهٔ مدل دارد؛ در گفتگوی واقعی بانک دانش رد می‌شود و کاتالوگ زنده اولویت دارد.</p>';
		if (!$preview['rows']) echo '<div class="bhc-kb-empty"><strong>ردیفی پیدا نشد</strong><span>سؤال را دقیق‌تر بنویسید یا پاسخ را دستی اضافه کنید.</span></div>';
		else {
			echo '<div class="bhc-kb-hits">';
			foreach ($preview['rows'] as $index => $hit) echo '<article class="bhc-kb-hit"><header><span class="bhc-kb-pill ' . ($index === 0 ? 'is-on' : 'is-muted') . '">' . ($index === 0 ? 'پاسخ انتخابی' : 'نامزد بعدی') . '</span><b>#' . esc_html($hit['id']) . '</b></header><h3>' . esc_html($hit['question']) . '</h3><p>' . esc_html($hit['answer']) . '</p></article>';
			echo '</div>';
		}
	}
	echo '</section></div>';
	echo '<section class="bhc-kb-panel"><header class="bhc-kb-list-head"><div><span class="bhc-section-kicker">فهرست</span><h2>ردیف‌های بانک</h2><p>' . esc_html(number_format_i18n($total)) . ' مورد با فیلتر فعلی · صفحه ' . esc_html(number_format_i18n($filters['paged'])) . ' از ' . esc_html(number_format_i18n($pages)) . '</p></div><form method="get" class="bhc-kb-filters"><input type="hidden" name="page" value="bhc-chat-kb"><label>جستجو<input type="search" name="s" value="' . esc_attr($filters['s']) . '" placeholder="پرسش یا پاسخ"></label><label>نیت<select name="filter_intent"><option value="">همه نیت‌ها</option>';
	foreach ($intents as $key => $label) echo '<option value="' . esc_attr($key) . '" ' . selected($filters['filter_intent'], $key, false) . '>' . esc_html($label) . '</option>';
	echo '</select></label><label>منبع<select name="filter_source"><option value="">همه منبع‌ها</option>';
	foreach ($sources as $key => $label) echo '<option value="' . esc_attr($key) . '" ' . selected($filters['filter_source'], $key, false) . '>' . esc_html($label) . '</option>';
	echo '</select></label><label>وضعیت<select name="filter_enabled"><option value="">فعال و غیرفعال</option><option value="1" ' . selected($filters['filter_enabled'], '1', false) . '>فقط فعال</option><option value="0" ' . selected($filters['filter_enabled'], '0', false) . '>فقط غیرفعال</option></select></label><label>ترتیب<select name="sort"><option value="updated" ' . selected($filters['sort'], 'updated', false) . '>جدیدترین ویرایش</option><option value="usage" ' . selected($filters['sort'], 'usage', false) . '>بیشترین استفاده</option><option value="question" ' . selected($filters['sort'], 'question', false) . '>حروف الفبا</option></select></label><button class="button button-primary">اعمال فیلتر</button></form></header>';
	echo '<div class="bhc-kb-table-wrap"><table class="bhc-kb-table"><thead><tr><th>وضعیت</th><th>پرسش و پاسخ</th><th>نیت</th><th>منبع</th><th>استفاده</th><th>آخرین ویرایش</th><th>عملیات</th></tr></thead><tbody>';
	if (!$rows) echo '<tr><td colspan="7"><div class="bhc-kb-empty"><strong>' . ($total ? 'در این صفحه ردیفی نیست' : 'ردیفی با این فیلتر نیست') . '</strong><span><a href="' . esc_url(admin_url('admin.php?page=bhc-chat-unanswered')) . '">از صف پرسش‌های بی‌پاسخ تأیید کنید</a> یا پاسخ را همین‌جا اضافه کنید.</span></div></td></tr>';
	foreach ((array) $rows as $row) {
		$answer = mb_strlen($row['answer']) > 180 ? mb_substr($row['answer'], 0, 180) . '…' : $row['answer'];
		$stale = ($row['source'] ?? '') === 'catalog';
		echo '<tr class="' . ((int) $row['enabled'] ? '' : 'is-inactive') . '"><td><span class="bhc-kb-pill ' . ((int) $row['enabled'] ? 'is-on' : 'is-off') . '">' . ((int) $row['enabled'] ? 'فعال' : 'غیرفعال') . '</span></td>';
		echo '<td><strong title="' . esc_attr($row['question']) . '">' . esc_html($row['question']) . '</strong><p>' . esc_html($answer) . '</p></td>';
		echo '<td><span class="bhc-kb-intent">' . esc_html($intents[$row['intent']] ?? $row['intent']) . '</span></td>';
		echo '<td><span class="bhc-kb-intent">' . esc_html($sources[$row['source']] ?? $row['source']) . '</span>' . ($stale ? '<small class="bhc-kb-stale">قیمت ذخیره‌شده</small>' : '') . '</td>';
		echo '<td>' . esc_html(number_format_i18n((int) $row['usage_count'])) . '</td>';
		echo '<td><b>' . esc_html(bhc_kb_when($row['updated_at'])) . '</b><small>' . esc_html(bhc_kb_user($row['reviewed_by'] ?? 0)) . '</small></td>';
		echo '<td><div class="bhc-kb-ops"><a class="button" href="' . $link(['edit' => (int) $row['id']]) . '#bhc-kb-editor">ویرایش</a><form method="post">' . $nonce . $keep . '<input type="hidden" name="bhc_kb_id" value="' . esc_attr($row['id']) . '"><button class="button" name="bhc_kb_action" value="toggle">' . ((int) $row['enabled'] ? 'توقف' : 'فعال') . '</button><button class="button bhc-kb-danger" name="bhc_kb_action" value="delete" onclick="return confirm(\'این ردیف حذف شود؟\')">حذف</button></form></div></td></tr>';
	}
	echo '</tbody></table></div>';
	if ($pages > 1) {
		echo '<nav class="bhc-kb-pager" aria-label="صفحه‌ها">';
		if ($filters['paged'] > 1) echo '<a class="button" href="' . $link(['paged' => $filters['paged'] - 1]) . '">صفحه قبل</a>';
		echo '<span>صفحه ' . esc_html(number_format_i18n($filters['paged'])) . ' از ' . esc_html(number_format_i18n($pages)) . '</span>';
		if ($filters['paged'] < $pages) echo '<a class="button" href="' . $link(['paged' => $filters['paged'] + 1]) . '">صفحه بعد</a>';
		echo '</nav>';
	}
	$export = esc_url(wp_nonce_url(add_query_arg(array_merge($filters, ['action' => 'bhc_kb_export']), admin_url('admin-post.php')), 'bhc_kb_export'));
	echo '<div class="bhc-kb-tools"><article class="bhc-kb-tool"><h3>کاتالوگ کهنه</h3><p>ردیف‌های کپی‌شده از کاتالوگ قیمت ثابت دارند. با این کار فقط غیرفعال می‌شوند.</p><form method="post">' . $nonce . $keep . '<button class="button" name="bhc_kb_action" value="disable_catalog" onclick="return confirm(\'همه ردیف‌های کپی کاتالوگ غیرفعال شوند؟\')">غیرفعال کردن کاتالوگ</button></form></article>';
	echo '<article class="bhc-kb-tool"><h3>درون‌ریزی</h3><p>فایل JSON با کلید rows، حداکثر ۱ مگابایت و ۲۰۰ ردیف. ردیف‌ها به‌صورت دستی ذخیره می‌شوند.</p><form method="post" enctype="multipart/form-data">' . $nonce . $keep . '<input type="hidden" name="bhc_kb_action" value="import"><input type="file" name="kb_file" accept="application/json,.json" required><button class="button">ورود فایل</button></form></article>';
	echo '<article class="bhc-kb-tool"><h3>برون‌بری</h3><p>خروجی، فیلتر فعلی را تا ۲۰۰۰ ردیف به JSON می‌دهد.</p><a class="button" href="' . $export . '">دریافت JSON</a></article></div>';
	echo '<p class="bhc-kb-note">جستجوی غیر دقیق ربات فقط منبع «بسته نسخه ۲» را می‌گردد. تطبیق دقیق روی همه ردیف‌های فعال است. پاسخ حاوی رمز، کارت یا کد ملی ذخیره نمی‌شود.</p></section></div>';
}
