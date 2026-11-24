<?php
if (!defined('ABSPATH')) exit;

add_action('admin_enqueue_scripts', function () {
	if (sanitize_key($_GET['page'] ?? '') !== 'bhc-chat-unanswered') return;
	wp_enqueue_style('bhc-admin', plugins_url('../assets/admin.css', __FILE__), [], '1.5.0');
});

function bhc_chat_contains_secret($text) {
	$text = (string) $text;
	if (preg_match('/(?:رمز|password|پسورد|cvv|کد\s*ملی|شماره\s*کارت|otp|رمز\s*یک\s*بارمصرف|رمز\s*پویا)/iu', $text)) return true;
	return (bool) preg_match('/(?:\d[ \-]*){13,19}/', $text);
}
function bhc_unanswered_with_lock($callback) {
	$key = 'bhc_unanswered_lock';
	$got = false;
	for ($i = 0; $i < 8 && !$got; $i++) {
		if (add_option($key, (string) (time() + 8), '', false)) { $got = true; break; }
		$until = (int) get_option($key, 0);
		if ($until > 0 && $until < time()) $got = (bool) update_option($key, (string) (time() + 8), false);
		if (!$got) usleep(80000);
	}
	if (!$got) return new WP_Error('bhc_unanswered_lock', 'صف هم‌زمان در حال به‌روزرسانی است. چند ثانیه بعد دوباره تلاش کنید.');
	try { return $callback(); }
	finally { delete_option($key); }
}
function bhc_unanswered_cap(array $rows) {
	$rows = array_values($rows);
	if (count($rows) <= 200) return $rows;
	usort($rows, function ($a, $b) {
		$rank = function ($row) {
			$status = $row['status'] ?? 'open';
			$urgent = in_array($row['intent'] ?? '', ['complaint', 'order'], true);
			if ($status === 'open' && ($urgent || (int) ($row['count'] ?? 1) >= 2)) return 0;
			if ($status === 'open') return 1;
			if ($status === 'approved') return 2;
			return 3;
		};
		$diff = $rank($a) <=> $rank($b);
		if ($diff !== 0) return $diff;
		return strcmp((string) ($b['last_seen'] ?? ''), (string) ($a['last_seen'] ?? ''));
	});
	return array_slice($rows, 0, 200);
}
function bhc_chat_unanswered_add($question, $intent = 'unknown', $reason = '') {
	$question = sanitize_textarea_field($question);
	$question = preg_replace('/\s+/u', ' ', trim((string) $question));
	if ($question === '' || mb_strlen($question) > 700 || bhc_chat_contains_secret($question)) return false;
	$intent = in_array($intent, ['purchase', 'budget', 'compare', 'support', 'order', 'complaint', 'unknown'], true) ? $intent : 'unknown';
	$result = bhc_unanswered_with_lock(function () use ($question, $intent, $reason) {
		$rows = array_values((array) get_option('bhc_chat_unanswered', []));
		$normalized = mb_strtolower($question, 'UTF-8');
		foreach ($rows as &$row) {
			if (($row['normalized'] ?? '') === $normalized && ($row['status'] ?? 'open') === 'open') {
				$row['count'] = max(1, (int) ($row['count'] ?? 1)) + 1;
				$row['last_seen'] = current_time('mysql');
				if (empty($row['id'])) $row['id'] = wp_generate_password(16, false, false);
				update_option('bhc_chat_unanswered', bhc_unanswered_cap($rows), false);
				return true;
			}
		}
		unset($row);
		$rows[] = ['id' => wp_generate_password(16, false, false), 'question' => $question, 'normalized' => $normalized, 'intent' => $intent, 'reason' => sanitize_key($reason), 'count' => 1, 'first_seen' => current_time('mysql'), 'last_seen' => current_time('mysql'), 'status' => 'open', 'suggested_answer' => '', 'history' => []];
		update_option('bhc_chat_unanswered', bhc_unanswered_cap($rows), false);
		return true;
	});
	return !is_wp_error($result) && $result;
}

function bhc_unanswered_labels() {
	return ['purchase' => 'خرید', 'budget' => 'بودجه', 'compare' => 'مقایسه', 'support' => 'پشتیبانی', 'order' => 'پیگیری سفارش', 'complaint' => 'شکایت', 'unknown' => 'نامشخص'];
}
function bhc_unanswered_reasons() {
	return ['manual' => 'ثبت دستی', 'no_catalog_match' => 'کالایی پیدا نشد', 'ai_disabled' => 'هوش مصنوعی خاموش', 'ai_unavailable' => 'هوش مصنوعی در دسترس نبود', 'empty_answer' => 'پاسخ خالی'];
}
function bhc_unanswered_ignore_reasons() {
	return ['not_relevant' => 'نامرتبط با فروشگاه', 'duplicate' => 'تکراری', 'spam' => 'هرزنامه', 'needs_human_channel' => 'باید در کانال انسانی پیگیری شود', 'other' => 'دلیل دیگر'];
}
function bhc_unanswered_jalali($mysql) {
	if (!$mysql) return '—';
	$ts = (int) mysql2date('U', $mysql, false);
	if (!$ts) return '—';
	if (class_exists('Baharaan_Commerce')) return Baharaan_Commerce::jalali_date($ts);
	return mysql2date('Y/m/d H:i', $mysql);
}
function bhc_unanswered_ensure_ids(array $rows) {
	$changed = false;
	foreach ($rows as &$row) {
		if (empty($row['id'])) { $row['id'] = wp_generate_password(16, false, false); $changed = true; }
	}
	unset($row);
	return [$rows, $changed];
}
function bhc_unanswered_index(array $rows, $id) {
	$id = (string) $id;
	foreach ($rows as $index => $row) {
		if ((string) ($row['id'] ?? '') === $id) return $index;
	}
	return null;
}
function bhc_unanswered_touch(array $row, $action) {
	$history = array_values((array) ($row['history'] ?? []));
	$history[] = ['at' => current_time('mysql'), 'action' => sanitize_key($action), 'user' => get_current_user_id()];
	$row['history'] = array_slice($history, -8);
	$row['reviewed_by'] = get_current_user_id();
	return $row;
}
function bhc_unanswered_priority(array $row) {
	$intent = $row['intent'] ?? '';
	$score = (int) ($row['count'] ?? 1) * 5;
	if ($intent === 'complaint') $score += 100;
	elseif ($intent === 'order') $score += 80;
	elseif ($intent === 'support') $score += 30;
	if (($row['status'] ?? 'open') === 'open' && !empty($row['last_seen'])) {
		$age = time() - (int) strtotime($row['last_seen']);
		if ($age > 48 * HOUR_IN_SECONDS) $score += 25;
	}
	return $score;
}
function bhc_unanswered_notice($ok, $text) {
	set_transient('bhc_unanswered_notice_' . get_current_user_id(), ['ok' => (bool) $ok, 'text' => $text], 60);
}
function bhc_unanswered_redirect($status, $query, $page) {
	wp_safe_redirect(add_query_arg(['page' => 'bhc-chat-unanswered', 'status' => $status, 's' => $query, 'paged' => max(1, (int) $page)], admin_url('admin.php')));
	exit;
}

function bhc_chat_unanswered_page_v2() {
	if (!current_user_can('manage_baharan_commerce') && !current_user_can('manage_options')) {
		wp_die('دسترسی به صف پرسش‌های بی‌پاسخ برای این حساب فعال نیست.', 'دسترسی ندارید', ['response' => 403]);
	}
	$list = bhc_unanswered_ensure_ids(array_values((array) get_option('bhc_chat_unanswered', [])));
	$rows = $list[0];
	if ($list[1]) update_option('bhc_chat_unanswered', $rows, false);
	$action = sanitize_key($_POST['bhc_unanswered_action'] ?? '');
	$status = sanitize_key($_REQUEST['status'] ?? 'open');
	if (!in_array($status, ['open', 'approved', 'ignored', 'all'], true)) $status = 'open';
	$query = sanitize_text_field(wp_unslash($_REQUEST['s'] ?? ''));
	$paged = max(1, absint($_REQUEST['paged'] ?? 1));
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
		check_admin_referer('bhc_unanswered_crud');
		$rid = sanitize_text_field(wp_unslash($_POST['bhc_unanswered_id'] ?? ''));
		$question = sanitize_textarea_field(wp_unslash($_POST['bhc_unanswered_question'] ?? ''));
		$answer = sanitize_textarea_field(wp_unslash($_POST['bhc_suggested_answer'] ?? ''));
		$intent = sanitize_key($_POST['bhc_unanswered_intent'] ?? 'unknown');
		$allowed = array_keys(bhc_unanswered_labels());
		$fail = '';
		if ($action === 'save' && ($question === '' || mb_strlen($question) > 700 || !in_array($intent, $allowed, true))) $fail = 'متن پرسش و نیت را کامل و معتبر وارد کنید.';
		elseif ($action === 'approve' && ($answer === '' || mb_strlen($answer) > 2000)) $fail = 'برای تأیید، پاسخ بین ۱ تا ۲۰۰۰ نویسه وارد کنید.';
		elseif (in_array($action, ['approve', 'save'], true) && function_exists('bhc_chat_contains_secret') && (bhc_chat_contains_secret($answer) || ($action === 'save' && bhc_chat_contains_secret($question)))) $fail = 'متن شامل اطلاعات حساس است و ذخیره نشد.';
		elseif ($action === 'ignore' && !isset(bhc_unanswered_ignore_reasons()[sanitize_key($_POST['bhc_ignore_reason'] ?? '')])) $fail = 'برای نادیده گرفتن، یک دلیل انتخاب کنید.';
		if ($fail) { bhc_unanswered_notice(false, $fail); bhc_unanswered_redirect($status, $query, $paged); }
		$saved = bhc_unanswered_with_lock(function () use ($action, $rid, $question, $answer, $intent) {
			$rows = bhc_unanswered_ensure_ids(array_values((array) get_option('bhc_chat_unanswered', [])))[0];
			$index = $rid !== '' ? bhc_unanswered_index($rows, $rid) : null;
			if (in_array($action, ['approve', 'ignore', 'reopen', 'delete'], true) && $index === null) return new WP_Error('missing', 'این پرسش دیگر در صف نیست.');
			if ($action === 'save') {
				if (function_exists('bhc_chat_contains_secret') && bhc_chat_contains_secret($question)) return new WP_Error('secret', 'متن شامل اطلاعات حساس است و ذخیره نشد.');
				$normalized = mb_strtolower(preg_replace('/\s+/u', ' ', trim($question)), 'UTF-8');
				if ($index !== null) {
					$rows[$index] = array_merge($rows[$index], ['question' => $question, 'normalized' => $normalized, 'intent' => $intent, 'updated_at' => current_time('mysql')]);
					$rows[$index] = bhc_unanswered_touch($rows[$index], 'edit');
					$kb = absint($rows[$index]['kb_id'] ?? 0);
					if ($kb && class_exists('BHC_Chat_Knowledge_Store') && ($rows[$index]['status'] ?? '') === 'approved') {
						if (mb_strlen($question) > 500) return new WP_Error('len', 'پرسش تأییدشده حداکثر ۵۰۰ نویسه است تا بانک دانش ناقص به‌روز نشود.');
						$kb_saved = BHC_Chat_Knowledge_Store::save($kb, ['question' => $question, 'answer' => (string) ($rows[$index]['suggested_answer'] ?? ''), 'lang' => 'fa', 'intent' => $intent, 'enabled' => true, 'source' => 'human']);
						if (is_wp_error($kb_saved)) return $kb_saved;
					}
					update_option('bhc_chat_unanswered', bhc_unanswered_cap($rows), false);
					return 'پرسش «' . mb_substr($question, 0, 80) . '» ویرایش شد.';
				}
				$rows[] = bhc_unanswered_touch(['id' => wp_generate_password(16, false, false), 'question' => $question, 'normalized' => $normalized, 'intent' => $intent, 'reason' => 'manual', 'count' => 1, 'first_seen' => current_time('mysql'), 'last_seen' => current_time('mysql'), 'status' => 'open', 'suggested_answer' => '', 'history' => []], 'create');
				update_option('bhc_chat_unanswered', bhc_unanswered_cap($rows), false);
				return 'پرسش جدید به صف اضافه شد.';
			}
			if ($action === 'approve') {
				$row = $rows[$index];
				$q = (string) ($row['question'] ?? '');
				if (mb_strlen($q) > 500) return new WP_Error('len', 'پرسش طولانی‌تر از ۵۰۰ نویسه است. ابتدا آن را ویرایش و کوتاه کنید.');
				if (function_exists('bhc_chat_contains_secret') && (bhc_chat_contains_secret($q) || bhc_chat_contains_secret($answer))) return new WP_Error('secret', 'پرسش یا پاسخ شامل اطلاعات حساس است و ذخیره نشد.');
				if (!class_exists('BHC_Chat_Knowledge_Store')) return new WP_Error('kb', 'بانک دانش در دسترس نیست.');
				$kb_saved = BHC_Chat_Knowledge_Store::save(absint($row['kb_id'] ?? 0), ['question' => $q, 'answer' => $answer, 'lang' => 'fa', 'intent' => $row['intent'] ?? 'unknown', 'enabled' => true, 'source' => 'human']);
				if (is_wp_error($kb_saved)) return $kb_saved;
				$rows[$index]['kb_id'] = (int) $kb_saved;
				$rows[$index]['status'] = 'approved';
				$rows[$index]['suggested_answer'] = $answer;
				$rows[$index]['reviewed_at'] = current_time('mysql');
				$rows[$index] = bhc_unanswered_touch($rows[$index], 'approve');
				update_option('bhc_chat_unanswered', bhc_unanswered_cap($rows), false);
				return 'پاسخ «' . mb_substr($q, 0, 80) . '» تأیید و در بانک دانش ذخیره شد.';
			}
			if ($action === 'ignore') {
				$rows[$index]['status'] = 'ignored';
				$rows[$index]['ignore_reason'] = sanitize_key($_POST['bhc_ignore_reason'] ?? '');
				$rows[$index]['reviewed_at'] = current_time('mysql');
				$rows[$index] = bhc_unanswered_touch($rows[$index], 'ignore');
				update_option('bhc_chat_unanswered', $rows, false);
				return 'پرسش با دلیل مشخص از صف فعال خارج شد.';
			}
			if ($action === 'reopen') {
				$rows[$index]['status'] = 'open';
				$rows[$index] = bhc_unanswered_touch($rows[$index], 'reopen');
				update_option('bhc_chat_unanswered', $rows, false);
				return 'پرسش به صف فعال برگشت.';
			}
			array_splice($rows, $index, 1);
			update_option('bhc_chat_unanswered', $rows, false);
			return 'پرسش حذف شد.';
		});
		if (is_wp_error($saved)) bhc_unanswered_notice(false, $saved->get_error_message());
		else bhc_unanswered_notice(true, (string) $saved);
		bhc_unanswered_redirect($status, $query, $paged);
	}
	$rows = array_values((array) get_option('bhc_chat_unanswered', []));
	$labels = bhc_unanswered_labels();
	$reasons = bhc_unanswered_reasons();
	$ignore_labels = bhc_unanswered_ignore_reasons();
	$filter = $status;
	$filtered = [];
	foreach ($rows as $row) {
		$row_status = $row['status'] ?? 'open';
		if ($filter !== 'all' && $filter !== $row_status) continue;
		$hay = mb_strtolower(implode(' ', [$row['question'] ?? '', $row['suggested_answer'] ?? '', $labels[$row['intent'] ?? ''] ?? '', $reasons[$row['reason'] ?? ''] ?? '', $ignore_labels[$row['ignore_reason'] ?? ''] ?? '']), 'UTF-8');
		if ($query !== '' && mb_stripos($hay, mb_strtolower($query, 'UTF-8')) === false) continue;
		$filtered[] = $row;
	}
	usort($filtered, function ($a, $b) { return bhc_unanswered_priority($b) <=> bhc_unanswered_priority($a); });
	$per_page = 20;
	$pages = max(1, (int) ceil(count($filtered) / $per_page));
	$paged = min($paged, $pages);
	$page_rows = array_slice($filtered, ($paged - 1) * $per_page, $per_page);
	$open = count(array_filter($rows, function ($r) { return ($r['status'] ?? 'open') === 'open'; }));
	$approved = count(array_filter($rows, function ($r) { return ($r['status'] ?? '') === 'approved'; }));
	$ignored = count(array_filter($rows, function ($r) { return ($r['status'] ?? '') === 'ignored'; }));
	$overdue = count(array_filter($rows, function ($r) {
		if (($r['status'] ?? 'open') !== 'open' || empty($r['last_seen'])) return false;
		return (time() - (int) strtotime($r['last_seen'])) > 48 * HOUR_IN_SECONDS;
	}));
	$edit_id = sanitize_text_field(wp_unslash($_GET['edit'] ?? ''));
	$edit_index = $edit_id !== '' ? bhc_unanswered_index($rows, $edit_id) : null;
	$edit = $edit_index !== null ? $rows[$edit_index] : [];
	$base = admin_url('admin.php');
	$link = function ($next) use ($base, $query) { return esc_url(add_query_arg(['page' => 'bhc-chat-unanswered', 'status' => $next, 's' => $query], $base)); };
	$back = esc_url(add_query_arg(['page' => 'bhc-chat-unanswered', 'status' => $filter, 's' => $query, 'paged' => $paged], $base));
	$flash = get_transient('bhc_unanswered_notice_' . get_current_user_id());
	if ($flash) delete_transient('bhc_unanswered_notice_' . get_current_user_id());
	$notice = $flash ? '<div class="notice notice-' . (!empty($flash['ok']) ? 'success' : 'error') . ' is-dismissible"><p>' . esc_html($flash['text']) . '</p></div>' : '';
	echo $notice;
	echo '<div class="wrap bhc-admin bhc-unanswered" dir="rtl"><header class="bhc-unanswered-hero"><div class="bhc-ua-hero-copy"><span class="bhc-eyebrow">بررسی انسانی</span><h1>مرکز پرسش‌های بی‌پاسخ</h1><p>پرسش واقعی را بررسی کنید. فقط پاسخ کوتاه، غیرحساس و تأییدشده به بانک دانش می‌رود. شکایت و پیگیری سفارش بالاتر از تکرار ساده می‌آیند.</p></div><div class="bhc-unanswered-actions"><a class="button" href="' . esc_url(admin_url('admin.php?page=bhc-chat-kb')) . '">بانک دانش</a><a class="button" href="' . esc_url(admin_url('admin.php?page=bhc-chat-quality')) . '">کیفیت پاسخ‌گویی</a><a class="button button-primary" href="#bhc-unanswered-editor">افزودن پرسش</a></div></header>';
	echo '<div class="bhc-unanswered-metrics"><a class="' . ($filter === 'all' ? 'is-active' : '') . '" href="' . $link('all') . '"><b>' . esc_html(number_format_i18n(count($rows))) . '</b><span>کل پرسش‌ها</span></a><a class="is-warn' . ($filter === 'open' ? ' is-active' : '') . '" href="' . $link('open') . '"><b>' . esc_html(number_format_i18n($open)) . '</b><span>در صف بررسی</span></a><a class="is-good' . ($filter === 'approved' ? ' is-active' : '') . '" href="' . $link('approved') . '"><b>' . esc_html(number_format_i18n($approved)) . '</b><span>تأییدشده</span></a><a class="is-info' . ($filter === 'ignored' ? ' is-active' : '') . '" href="' . $link('ignored') . '"><b>' . esc_html(number_format_i18n($ignored)) . '</b><span>نادیده‌گرفته‌شده</span></a></div>';
	if ($overdue) echo '<p class="bhc-unanswered-sla">' . esc_html(number_format_i18n($overdue)) . ' پرسش باز بیش از ۴۸ ساعت بدون بررسی تازه مانده است.</p>';
	echo '<div class="bhc-ua-workspace"><section class="bhc-unanswered-panel bhc-ua-editor" id="bhc-unanswered-editor"><span class="bhc-section-kicker">ثبت پرسش</span><h2>' . ($edit ? 'ویرایش پرسش' : 'افزودن پرسش به صف') . '</h2><p class="bhc-ua-lead">پرسش حساس ذخیره نمی‌شود. ویرایش مورد تأییدشده، همان ردیف بانک دانش را به‌روز می‌کند.</p><form method="post" class="bhc-unanswered-editor-form">';
	wp_nonce_field('bhc_unanswered_crud');
	echo '<input type="hidden" name="bhc_unanswered_action" value="save"><input type="hidden" name="status" value="' . esc_attr($filter) . '"><input type="hidden" name="s" value="' . esc_attr($query) . '"><input type="hidden" name="paged" value="' . esc_attr($paged) . '">';
	if ($edit) echo '<input type="hidden" name="bhc_unanswered_id" value="' . esc_attr($edit['id']) . '">';
	echo '<label for="bhc-unanswered-question">متن پرسش<textarea id="bhc-unanswered-question" required maxlength="700" name="bhc_unanswered_question" rows="3">' . esc_textarea($edit['question'] ?? '') . '</textarea><small>حداکثر ۷۰۰ نویسه در صف و ۵۰۰ نویسه برای ورود به بانک دانش</small></label><label for="bhc-unanswered-intent">نیت کاربر<select id="bhc-unanswered-intent" name="bhc_unanswered_intent">';
	foreach ($labels as $key => $label) echo '<option value="' . esc_attr($key) . '" ' . selected($edit['intent'] ?? 'unknown', $key, false) . '>' . esc_html($label) . '</option>';
	echo '</select></label><p class="bhc-unanswered-actions-row"><button class="button button-primary">' . ($edit ? 'ذخیره ویرایش' : 'افزودن به صف') . '</button>';
	if ($edit) echo '<a class="button" href="' . $back . '">انصراف</a>';
	echo '</p></form></section><section class="bhc-unanswered-panel bhc-ua-queue"><header class="bhc-unanswered-list-head"><div><span class="bhc-section-kicker">مدیریت صف</span><h2>فهرست پرسش‌ها</h2><p class="bhc-unanswered-countline">' . esc_html(number_format_i18n(count($filtered))) . ' مورد · صفحه ' . esc_html(number_format_i18n($paged)) . ' از ' . esc_html(number_format_i18n($pages)) . '</p></div><form method="get" class="bhc-unanswered-search" role="search"><input type="hidden" name="page" value="bhc-chat-unanswered"><label class="screen-reader-text" for="bhc-unanswered-s">جست‌وجو</label><input id="bhc-unanswered-s" name="s" value="' . esc_attr($query) . '" placeholder="پرسش، پاسخ، نیت یا علت"><label class="screen-reader-text" for="bhc-unanswered-status">وضعیت</label><select id="bhc-unanswered-status" name="status"><option value="open" ' . selected($filter, 'open', false) . '>در صف بررسی</option><option value="approved" ' . selected($filter, 'approved', false) . '>تأییدشده</option><option value="ignored" ' . selected($filter, 'ignored', false) . '>نادیده‌گرفته‌شده</option><option value="all" ' . selected($filter, 'all', false) . '>همه وضعیت‌ها</option></select><button class="button button-primary" type="submit">جست‌وجو</button></form></header><div class="bhc-unanswered-list">';
	if (!$page_rows) echo '<div class="bhc-unanswered-empty"><b>' . ($rows ? 'نتیجه‌ای پیدا نشد' : 'صف پرسش‌ها خالی است') . '</b><span>' . ($rows ? 'فیلتر یا عبارت جست‌وجو را تغییر دهید.' : 'پرسش واقعی کاربران اینجا برای بررسی انسانی جمع می‌شود.') . '</span><a class="button button-primary" href="#bhc-unanswered-editor">ثبت پرسش</a></div>';
	foreach ($page_rows as $row) {
		$row_status = $row['status'] ?? 'open';
		$label = $row_status === 'approved' ? 'تأییدشده' : ($row_status === 'ignored' ? 'نادیده‌گرفته‌شده' : 'در صف بررسی');
		$rid = (string) ($row['id'] ?? '');
		$over = $row_status === 'open' && !empty($row['last_seen']) && (time() - (int) strtotime($row['last_seen'])) > 48 * HOUR_IN_SECONDS;
		$urgent = in_array($row['intent'] ?? '', ['complaint', 'order'], true);
		echo '<article class="bhc-unanswered-card ' . esc_attr($row_status . ($edit_id === $rid ? ' is-editing' : '')) . '" id="bhc-unanswered-' . esc_attr($rid) . '"><header class="bhc-ua-card-head"><div class="bhc-unanswered-badges"><span class="bhc-unanswered-status">' . esc_html($label) . '</span><span class="bhc-unanswered-intent">' . esc_html($labels[$row['intent'] ?? 'unknown'] ?? 'نامشخص') . '</span><span class="bhc-unanswered-reason">' . esc_html($reasons[$row['reason'] ?? ''] ?? 'علت نامشخص') . '</span>';
		if ($urgent) echo '<span class="bhc-unanswered-urgent">اولویت پشتیبانی</span>';
		if ($over) echo '<span class="bhc-unanswered-late">بیش از ۴۸ ساعت</span>';
		echo '</div><div class="bhc-unanswered-count"><b>' . esc_html(number_format_i18n((int) ($row['count'] ?? 1))) . '</b><span>تکرار</span></div></header><h3 class="bhc-ua-question">' . esc_html($row['question'] ?? '—') . '</h3><div class="bhc-heat" role="img" aria-label="شدت تکرار"><i style="width:' . (int) min(100, max(8, (int) ($row['count'] ?? 1) * 12)) . '%"></i></div><ul class="bhc-unanswered-facts"><li><span>اولین ثبت</span><b>' . esc_html(bhc_unanswered_jalali($row['first_seen'] ?? '')) . '</b></li><li><span>آخرین ثبت</span><b>' . esc_html(bhc_unanswered_jalali($row['last_seen'] ?? '')) . '</b></li><li><span>بررسی</span><b>' . esc_html(bhc_unanswered_jalali($row['reviewed_at'] ?? '')) . '</b></li></ul>';
		if (!empty($row['ignore_reason'])) echo '<p class="bhc-unanswered-meta">دلیل خروج از صف: ' . esc_html($ignore_labels[$row['ignore_reason']] ?? $row['ignore_reason']) . '</p>';
		if (!empty($row['kb_id'])) echo '<p class="bhc-unanswered-meta">ردیف بانک دانش: ' . esc_html((string) $row['kb_id']) . '</p>';
		if ($row_status === 'open') {
			echo '<form method="post" class="bhc-answer-form">';
			wp_nonce_field('bhc_unanswered_crud');
			echo '<input type="hidden" name="bhc_unanswered_id" value="' . esc_attr($rid) . '"><input type="hidden" name="status" value="' . esc_attr($filter) . '"><input type="hidden" name="s" value="' . esc_attr($query) . '"><input type="hidden" name="paged" value="' . esc_attr($paged) . '"><label for="bhc-answer-' . esc_attr($rid) . '">پاسخ تأییدشده<textarea id="bhc-answer-' . esc_attr($rid) . '" maxlength="2000" name="bhc_suggested_answer" rows="4">' . esc_textarea($row['suggested_answer'] ?? '') . '</textarea><small>حداکثر ۲۰۰۰ نویسه</small></label><p class="bhc-unanswered-actions-row"><button class="button button-primary" name="bhc_unanswered_action" value="approve">تأیید و افزودن به بانک دانش</button><a class="button" href="' . esc_url(add_query_arg(['page' => 'bhc-chat-unanswered', 'edit' => $rid, 'status' => $filter, 's' => $query, 'paged' => $paged], $base)) . '#bhc-unanswered-editor">ویرایش پرسش</a></p></form>';
			echo '<form method="post" class="bhc-ignore-form">';
			wp_nonce_field('bhc_unanswered_crud');
			echo '<input type="hidden" name="bhc_unanswered_action" value="ignore"><input type="hidden" name="bhc_unanswered_id" value="' . esc_attr($rid) . '"><input type="hidden" name="status" value="' . esc_attr($filter) . '"><input type="hidden" name="s" value="' . esc_attr($query) . '"><input type="hidden" name="paged" value="' . esc_attr($paged) . '"><label for="bhc-ignore-' . esc_attr($rid) . '">دلیل<select id="bhc-ignore-' . esc_attr($rid) . '" name="bhc_ignore_reason" required><option value="">انتخاب دلیل</option>';
			foreach ($ignore_labels as $key => $text) echo '<option value="' . esc_attr($key) . '">' . esc_html($text) . '</option>';
			echo '</select></label><button class="button bhc-btn-muted">نادیده گرفتن</button></form>';
		} else {
			echo '<p class="bhc-reviewed-answer"><b>پاسخ ثبت‌شده</b>' . esc_html(($row['suggested_answer'] ?? '') !== '' ? $row['suggested_answer'] : 'پاسخی ثبت نشده است') . '</p><form method="post" class="bhc-unanswered-actions-row">';
			wp_nonce_field('bhc_unanswered_crud');
			echo '<input type="hidden" name="bhc_unanswered_id" value="' . esc_attr($rid) . '"><input type="hidden" name="status" value="' . esc_attr($filter) . '"><input type="hidden" name="s" value="' . esc_attr($query) . '"><input type="hidden" name="paged" value="' . esc_attr($paged) . '"><button class="button" name="bhc_unanswered_action" value="reopen">بازگردانی به صف</button><button class="button bhc-btn-danger" name="bhc_unanswered_action" value="delete" onclick="return confirm(\'این پرسش برای همیشه حذف شود؟\')">حذف</button></form>';
		}
		echo '</article>';
	}
	if ($pages > 1) {
		echo '<nav class="bhc-unanswered-pager" aria-label="صفحه‌ها">';
		for ($i = 1; $i <= $pages; $i++) echo '<a class="' . ($i === $paged ? 'is-current' : '') . '" href="' . esc_url(add_query_arg(['page' => 'bhc-chat-unanswered', 'status' => $filter, 's' => $query, 'paged' => $i], $base)) . '">' . esc_html(number_format_i18n($i)) . '</a>';
		echo '</nav>';
	}
	echo '</div></section></div><p class="bhc-unanswered-note">پرسش مشتری بدون نام، ایمیل و IP نگه داشته می‌شود. شناسهٔ کاربر فقط برای ممیزی کسی است که تأیید یا رد کرده. متن رمز، کارت، کد ملی و OTP ذخیره نمی‌شود. سقف صف ۲۰۰ مورد است و اول موارد بسته‌شدهٔ قدیمی حذف می‌شوند، نه شکایت و پیگیری باز.</p></div>';
}
