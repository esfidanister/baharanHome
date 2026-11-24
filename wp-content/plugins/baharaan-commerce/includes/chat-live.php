<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/class-chat-knowledge-store.php';
require_once __DIR__ . '/chat-quality.php';
require_once __DIR__ . '/chat-unanswered-admin.php';
require_once __DIR__ . '/chat-kb-admin.php';

add_action('admin_menu', function () {
	add_submenu_page('baharaan-commerce', 'پرسش‌های بی‌پاسخ', 'پرسش‌های بی‌پاسخ', 'manage_baharan_commerce', 'bhc-chat-unanswered', 'bhc_chat_unanswered_page_v2', 29);
	add_submenu_page('baharaan-commerce', 'بانک دانش چت', 'بانک دانش چت', 'manage_baharan_commerce', 'bhc-chat-kb', 'bhc_chat_kb_page', 30);
	add_submenu_page('baharaan-commerce', 'تیکت‌های چت', 'تیکت‌های چت', 'manage_baharan_commerce', 'bhc-chat-tickets', 'bhc_chat_tickets_page', 31);
});

function bhc_ticket_can() {
	return current_user_can('manage_baharan_commerce') || current_user_can('manage_options') || current_user_can('edit_bhc_leads');
}

function bhc_ticket_stages() {
	return ['new' => 'جدید', 'contacted' => 'در حال پیگیری', 'resolved' => 'بسته‌شده'];
}

function bhc_ticket_rows($value) {
	if (!is_array($value)) return [];
	$out = [];
	foreach ($value as $row) {
		if (!is_array($row) || empty($row['text'])) continue;
		$out[] = $row;
	}
	return $out;
}

function bhc_ticket_when($mysql) {
	$ts = strtotime((string) $mysql);
	if (!$ts) return (string) $mysql;
	return Baharaan_Commerce::jalali_date($ts);
}

function bhc_ticket_audit($id, $action, $extra = []) {
	$rows = array_values((array) get_option('bhc_audit_log', []));
	$rows[] = array_merge(['action' => $action, 'ticket_id' => (int) $id, 'user' => get_current_user_id(), 'at' => current_time('mysql')], $extra);
	update_option('bhc_audit_log', array_slice($rows, -400), false);
}

function bhc_ticket_back($args = []) {
	$keep = [];
	foreach (['q' => 'return_q', 'stage' => 'return_stage', 'paged' => 'return_paged', 'mine' => 'return_mine'] as $key => $field) {
		$value = sanitize_text_field(wp_unslash($_POST[$field] ?? ''));
		if ($value !== '' && $value !== '0') $keep[$key] = $value;
	}
	wp_safe_redirect(add_query_arg(array_merge($keep, $args), admin_url('admin.php?page=bhc-chat-tickets')));
	exit;
}

function bhc_ticket_hidden_return() {
	$html = '';
	foreach (['q' => 'return_q', 'stage' => 'return_stage', 'paged' => 'return_paged', 'mine' => 'return_mine'] as $key => $field) {
		$html .= '<input type="hidden" name="' . esc_attr($field) . '" value="' . esc_attr(sanitize_text_field(wp_unslash($_GET[$key] ?? ''))) . '">';
	}
	return $html;
}

function bhc_ticket_lock_holder($id) {
	$until = (int) get_post_meta($id, '_bhc_lock_until', true);
	$uid = (int) get_post_meta($id, '_bhc_lock_user', true);
	if ($until < time() || $uid < 1) return 0;
	return $uid;
}

add_action('admin_enqueue_scripts', function ($hook) {
	if ($hook !== 'baharaan-commerce_page_bhc-chat-tickets') return;
	wp_enqueue_style('bhc-tickets', plugin_dir_url(__DIR__) . 'assets/tickets.css', [], '1.0.0');
});

function bhc_chat_tickets_page() {
	if (!bhc_ticket_can()) return;
	$stages = bhc_ticket_stages();
	$stage = sanitize_key(wp_unslash($_GET['stage'] ?? ''));
	if (!isset($stages[$stage])) $stage = '';
	$q = sanitize_text_field(wp_unslash($_GET['q'] ?? ''));
	$paged = max(1, absint($_GET['paged'] ?? 1));
	$notice = sanitize_key(wp_unslash($_GET['bhc_notice'] ?? ''));
	$notices = [
		'saved' => 'پاسخ ذخیره شد.',
		'noted' => 'یادداشت داخلی ذخیره شد و برای مشتری ارسال نمی‌شود.',
		'stage' => 'مرحله تیکت به‌روز شد.',
		'revealed' => 'راه تماس فقط برای همین نمایش رمزگشایی شد و در گزارش ثبت گردید.',
		'invalid' => 'متن معتبر نیست.',
		'missing' => 'تیکت پیدا نشد.',
		'bank' => 'پاسخ شامل اطلاعات بانکی است و ثبت نشد.',
		'reason' => 'برای دیدن راه تماس، دلیل کوتاه بنویسید.',
		'locked' => 'این تیکت فعلاً در اختیار اپراتور دیگری است.',
		'claimed' => 'تیکت به نام شما ثبت شد.',
		'released' => 'مسئولیت تیکت برداشته شد.',
	];
	$args = ['post_type' => 'bhc_lead', 'post_status' => 'private', 'posts_per_page' => 15, 'paged' => $paged, 'orderby' => 'date', 'order' => 'DESC'];
	$args['meta_query'] = [];
	if ($stage !== '') $args['meta_query'][] = ['key' => '_bhc_stage', 'value' => $stage];
	$mine = absint($_GET['mine'] ?? 0) === 1;
	if ($mine) $args['meta_query'][] = ['key' => '_bhc_assignee', 'value' => get_current_user_id()];
	if ($q !== '') {
		if (stripos($q, 'BH-') === 0) $args['meta_query'][] = ['key' => '_bhc_ticket', 'value' => $q];
		else $args['s'] = $q;
	}
	if (!$args['meta_query']) unset($args['meta_query']);
	$query = new WP_Query($args);
	$counts = [];
	foreach (array_merge(['' => 'همه'], $stages) as $key => $label) {
		$cargs = ['post_type' => 'bhc_lead', 'post_status' => 'private', 'posts_per_page' => 1, 'fields' => 'ids'];
		if ($key !== '') $cargs['meta_query'] = [['key' => '_bhc_stage', 'value' => $key]];
		$cq = new WP_Query($cargs);
		$counts[$key] = (int) $cq->found_posts;
	}
	echo '<div class="wrap bhc-desk" dir="rtl"><header class="bhc-desk-hero"><div><span class="bhc-section-kicker">پشتیبانی مشتریان</span><h1>تیکت‌های چت</h1><p>هر کارت یک درخواست است. پاسخ مشتری، یادداشت داخلی و تغییر مرحله جدا هستند و صافی صفحه بعد از ثبت می‌ماند.</p></div><a class="button button-primary" href="' . esc_url(admin_url('edit.php?post_type=bhc_lead')) . '">فهرست CRM</a></header>';
	$ok = in_array($notice, ['saved', 'noted', 'stage', 'revealed', 'claimed', 'released'], true);
	if (isset($notices[$notice])) echo '<div class="notice notice-' . ($ok ? 'success' : 'error') . '"><p>' . esc_html($notices[$notice]) . '</p></div>';
	$reveal_id = absint($_GET['reveal'] ?? 0);
	if ($notice === 'revealed' && $reveal_id && bhc_ticket_can()) {
		$plain = get_transient('bhc_reveal_' . get_current_user_id() . '_' . $reveal_id);
		if (is_string($plain) && $plain !== '') echo '<div class="notice notice-warning"><p>راه تماس تیکت ' . esc_html((string) get_post_meta($reveal_id, '_bhc_ticket', true)) . ': <strong>' . esc_html($plain) . '</strong></p></div>';
	}
	echo '<section class="bhc-desk-metrics"><article><b>' . esc_html(number_format_i18n($counts['new'] + $counts['contacted'])) . '</b><span>تیکت باز</span></article><article><b>' . esc_html(number_format_i18n($counts['new'])) . '</b><span>جدید</span></article><article><b>' . esc_html(number_format_i18n($counts['contacted'])) . '</b><span>در حال پیگیری</span></article><article><b>' . esc_html(number_format_i18n($counts['resolved'])) . '</b><span>بسته‌شده</span></article></section>';
	echo '<form class="bhc-desk-filters" method="get"><input type="hidden" name="page" value="bhc-chat-tickets"><label>جستجو<input name="q" value="' . esc_attr($q) . '" placeholder="شماره BH، نام یا متن"></label><label>مرحله<select name="stage"><option value="">همه</option>';
	foreach ($stages as $key => $label) echo '<option value="' . esc_attr($key) . '"' . selected($stage, $key, false) . '>' . esc_html($label) . '</option>';
	echo '</select></label><label class="bhc-desk-check"><input type="checkbox" name="mine" value="1"' . checked($mine, true, false) . '> تیکت‌های من</label><button class="button button-primary" type="submit">اعمال صافی</button></form>';
	if (!$query->have_posts()) echo '<div class="bhc-desk-empty"><strong>تیکتی با این صافی پیدا نشد.</strong><span>صافی را بردارید یا شماره BH را کامل وارد کنید.</span></div>';
	foreach ($query->posts as $post) {
		$id = (int) $post->ID;
		$st = sanitize_key(get_post_meta($id, '_bhc_stage', true) ?: 'new');
		$cid = (string) get_post_meta($id, '_bhc_conversation', true);
		$expires = (int) get_post_meta($id, '_bhc_privacy_expires', true);
		$linked = (bool) preg_match('/^[a-f0-9]{32}$/', $cid);
		$assignee_id = (int) get_post_meta($id, '_bhc_assignee', true);
		$assignee = $assignee_id ? get_userdata($assignee_id) : false;
		$lock = bhc_ticket_lock_holder($id);
		$lock_user = $lock ? get_userdata($lock) : false;
		$first = (string) get_post_meta($id, '_bhc_first_reply_at', true);
		$created = strtotime($post->post_date) ?: time();
		$late = $st !== 'resolved' && $first === '' && (time() - $created) > 4 * HOUR_IN_SECONDS;
		$sla = $first !== '' ? 'اولین پاسخ: ' . bhc_ticket_when($first) : ($late ? 'بیش از ۴ ساعت بی‌پاسخ' : 'در مهلت چهارساعته');
		$return = bhc_ticket_hidden_return();
		$action = esc_url(admin_url('admin-post.php'));
		echo '<article class="bhc-desk-card"><header><div class="bhc-desk-title"><strong>' . esc_html(get_post_meta($id, '_bhc_ticket', true) ?: 'بدون شماره') . '</strong><span class="bhc-desk-badge stage-' . esc_attr($st) . '">' . esc_html($stages[$st] ?? $stages['new']) . '</span><span class="bhc-desk-badge' . ($late ? ' is-late' : '') . '">' . esc_html($sla) . '</span></div>';
		echo '<dl class="bhc-desk-facts"><div><dt>مشتری</dt><dd>' . esc_html(get_the_title($post)) . '</dd></div><div><dt>ثبت</dt><dd>' . esc_html(bhc_ticket_when($post->post_date)) . '</dd></div><div><dt>تماس</dt><dd>' . esc_html(get_post_meta($id, '_bhc_contact', true) ?: 'ثبت نشده') . '</dd></div><div><dt>کانال</dt><dd>' . ($linked ? 'چت وصل است' : 'فقط ایمیل، اگر تماس ایمیل باشد') . '</dd></div><div><dt>مسئول</dt><dd>' . esc_html($assignee ? $assignee->display_name : 'تعیین نشده') . '</dd></div>';
		if ($lock_user) echo '<div><dt>قفل</dt><dd>' . esc_html($lock_user->display_name) . '</dd></div>';
		if ($expires) echo '<div><dt>نگهداری</dt><dd>' . esc_html(Baharaan_Commerce::jalali_date($expires)) . '</dd></div>';
		echo '</dl><p class="bhc-desk-links">';
		$page = (string) get_post_meta($id, '_bhc_page', true);
		if ($page !== '') echo '<a href="' . esc_url($page) . '">صفحه مبدأ</a>';
		$edit = get_edit_post_link($id);
		if ($edit) echo '<a href="' . esc_url($edit) . '">ویرایش لید</a>';
		echo '</p></header><section class="bhc-desk-message"><h2>پیام مشتری</h2><p>' . esc_html(wp_strip_all_tags($post->post_content)) . '</p></section>';
		$rows = bhc_ticket_rows(get_post_meta($id, '_bhc_replies', true));
		echo '<section class="bhc-desk-thread"><h2>گفتگو</h2>';
		if (!$rows) echo '<p class="bhc-desk-muted">هنوز پاسخی ثبت نشده است.</p>';
		foreach ($rows as $row) {
			$kind = ($row['kind'] ?? 'reply') === 'note' ? 'note' : 'reply';
			echo '<article class="bhc-desk-reply is-' . esc_attr($kind) . '"><header><b>' . ($kind === 'note' ? 'یادداشت داخلی' : 'پاسخ به مشتری') . '</b><span>' . esc_html($row['name'] ?? '') . '</span><time>' . esc_html(bhc_ticket_when($row['at'] ?? '')) . '</time></header><p>' . esc_html($row['text']) . '</p>';
			if ($kind === 'reply' && !empty($row['delivery_note'])) echo '<small>' . esc_html($row['delivery_note']) . '</small>';
			echo '</article>';
		}
		echo '</section><div class="bhc-desk-compose">';
		echo '<form method="post" action="' . $action . '">' . wp_nonce_field('bhc_ticket_reply_' . $id, '_wpnonce', true, false) . $return . '<input type="hidden" name="action" value="bhc_ticket_reply"><input type="hidden" name="post_id" value="' . $id . '"><input type="hidden" name="kind" value="reply"><label for="reply-' . $id . '">پاسخ به مشتری</label><textarea id="reply-' . $id . '" name="reply" maxlength="1500" required rows="4" placeholder="متن قابل‌ارسال برای مشتری"></textarea><button class="button button-primary" type="submit">ثبت پاسخ</button></form>';
		echo '<form method="post" action="' . $action . '">' . wp_nonce_field('bhc_ticket_reply_' . $id, '_wpnonce', true, false) . $return . '<input type="hidden" name="action" value="bhc_ticket_reply"><input type="hidden" name="post_id" value="' . $id . '"><input type="hidden" name="kind" value="note"><label for="note-' . $id . '">یادداشت داخلی</label><textarea id="note-' . $id . '" name="reply" maxlength="1500" required rows="4" placeholder="فقط برای تیم؛ مشتری نمی‌بیند"></textarea><button class="button" type="submit">ثبت یادداشت</button></form>';
		echo '</div><footer class="bhc-desk-tools">';
		echo '<form method="post" action="' . $action . '">' . wp_nonce_field('bhc_ticket_stage_' . $id, '_wpnonce', true, false) . $return . '<input type="hidden" name="action" value="bhc_ticket_stage"><input type="hidden" name="post_id" value="' . $id . '"><label>مرحله<select name="stage">';
		foreach ($stages as $key => $label) echo '<option value="' . esc_attr($key) . '"' . selected($st, $key, false) . '>' . esc_html($label) . '</option>';
		echo '</select></label><button class="button" type="submit">به‌روزرسانی</button></form>';
		echo '<form method="post" action="' . $action . '">' . wp_nonce_field('bhc_ticket_claim_' . $id, '_wpnonce', true, false) . $return . '<input type="hidden" name="action" value="bhc_ticket_claim"><input type="hidden" name="post_id" value="' . $id . '"><input type="hidden" name="mode" value="' . ($assignee_id === get_current_user_id() ? 'release' : 'claim') . '"><button class="button" type="submit">' . ($assignee_id === get_current_user_id() ? 'رها کردن تیکت' : 'بر عهده گرفتن') . '</button></form>';
		echo '<form method="post" action="' . $action . '">' . wp_nonce_field('bhc_ticket_reveal_' . $id, '_wpnonce', true, false) . $return . '<input type="hidden" name="action" value="bhc_ticket_reveal"><input type="hidden" name="post_id" value="' . $id . '"><label>دلیل دیدن تماس<input name="reason" maxlength="180" required placeholder="مثلاً تماس تلفنی"></label><button class="button" type="submit">نمایش</button></form>';
		echo '</footer></article>';
	}
	$pagination = paginate_links(['base' => add_query_arg('paged', '%#%'), 'format' => '', 'current' => $paged, 'total' => max(1, (int) $query->max_num_pages), 'type' => 'plain']);
	if ($pagination) echo '<nav class="bhc-desk-pager" aria-label="صفحه تیکت‌ها">' . wp_kses_post($pagination) . '</nav>';
	echo '</div>';
	wp_reset_postdata();
}

function bhc_ticket_reply() {
	if (!bhc_ticket_can()) wp_die('دسترسی مجاز نیست');
	if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') wp_die('روش درخواست نامعتبر است');
	$id = absint($_POST['post_id'] ?? 0);
	check_admin_referer('bhc_ticket_reply_' . $id);
	$text = sanitize_textarea_field(wp_unslash($_POST['reply'] ?? ''));
	$kind = sanitize_key($_POST['kind'] ?? 'reply') === 'note' ? 'note' : 'reply';
	if ($text === '' || mb_strlen($text) > 1500) bhc_ticket_back(['bhc_notice' => 'invalid']);
	if (preg_match('/(?:cvv|otp|رمز پویا|شماره کارت)/iu', $text)) bhc_ticket_back(['bhc_notice' => 'bank']);
	$post = get_post($id);
	if (!$post || $post->post_type !== 'bhc_lead') bhc_ticket_back(['bhc_notice' => 'missing']);
	$lock = bhc_ticket_lock_holder($id);
	if ($lock && $lock !== get_current_user_id() && !current_user_can('manage_options')) bhc_ticket_back(['bhc_notice' => 'locked']);
	$user = wp_get_current_user();
	$delivery = 'none';
	$delivery_note = 'به مشتری ارسال نشد.';
	if ($kind === 'reply') {
		$cid = (string) get_post_meta($id, '_bhc_conversation', true);
		if (preg_match('/^[a-f0-9]{32}$/', $cid)) {
			$delivery = 'chat';
			$delivery_note = 'در گفتگوی چت همان مشتری، بعد از بازگشت به ویجت، نشان داده می‌شود.';
		}
		$contact = bhc_privacy_decrypt((string) get_post_meta($id, '_bhc_contact_enc', true));
		if (is_email($contact)) {
			$ticket = (string) get_post_meta($id, '_bhc_ticket', true);
			$sent = wp_mail($contact, 'پاسخ تیکت ' . $ticket, $text . "\n\nشماره پیگیری: " . $ticket);
			$delivery = $sent ? 'email' : $delivery;
			$delivery_note = $sent ? 'ایمیل شد.' : ($delivery === 'chat' ? $delivery_note . ' ایمیل ارسال نشد.' : 'ایمیل ارسال نشد و گفتگو هم وصل نیست.');
		} elseif ($delivery !== 'chat') {
			$delivery_note = 'راه تماس ایمیل نیست و گفتگو وصل نیست؛ مشتری این پاسخ را نمی‌بیند.';
		}
		$stage = sanitize_key(get_post_meta($id, '_bhc_stage', true) ?: 'new');
		if ($stage === 'new') update_post_meta($id, '_bhc_stage', 'contacted');
		if ((string) get_post_meta($id, '_bhc_first_reply_at', true) === '') update_post_meta($id, '_bhc_first_reply_at', current_time('mysql'));
		if (!(int) get_post_meta($id, '_bhc_assignee', true)) update_post_meta($id, '_bhc_assignee', get_current_user_id());
	}
	$replies = bhc_ticket_rows(get_post_meta($id, '_bhc_replies', true));
	$replies[] = ['at' => current_time('mysql'), 'text' => $text, 'kind' => $kind, 'user' => get_current_user_id(), 'name' => $user->display_name, 'delivery' => $delivery, 'delivery_note' => $delivery_note];
	update_post_meta($id, '_bhc_replies', array_slice($replies, -40));
	bhc_ticket_audit($id, $kind === 'note' ? 'ticket_note' : 'ticket_reply', ['delivery' => $delivery]);
	bhc_ticket_back(['bhc_notice' => $kind === 'note' ? 'noted' : 'saved']);
}
add_action('admin_post_bhc_ticket_reply', 'bhc_ticket_reply');

function bhc_ticket_stage() {
	if (!bhc_ticket_can()) wp_die('دسترسی مجاز نیست');
	if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') wp_die('روش درخواست نامعتبر است');
	$id = absint($_POST['post_id'] ?? 0);
	check_admin_referer('bhc_ticket_stage_' . $id);
	$stage = sanitize_key($_POST['stage'] ?? '');
	$post = get_post($id);
	if (!$post || $post->post_type !== 'bhc_lead' || !isset(bhc_ticket_stages()[$stage])) bhc_ticket_back(['bhc_notice' => 'missing']);
	update_post_meta($id, '_bhc_stage', $stage);
	bhc_ticket_audit($id, 'ticket_stage', ['stage' => $stage]);
	bhc_ticket_back(['bhc_notice' => 'stage']);
}
add_action('admin_post_bhc_ticket_stage', 'bhc_ticket_stage');

function bhc_ticket_claim() {
	if (!bhc_ticket_can()) wp_die('دسترسی مجاز نیست');
	if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') wp_die('روش درخواست نامعتبر است');
	$id = absint($_POST['post_id'] ?? 0);
	check_admin_referer('bhc_ticket_claim_' . $id);
	$post = get_post($id);
	if (!$post || $post->post_type !== 'bhc_lead') bhc_ticket_back(['bhc_notice' => 'missing']);
	$mode = sanitize_key($_POST['mode'] ?? 'claim');
	if ($mode === 'release') {
		delete_post_meta($id, '_bhc_assignee');
		delete_post_meta($id, '_bhc_lock_user');
		delete_post_meta($id, '_bhc_lock_until');
		bhc_ticket_audit($id, 'ticket_release');
		bhc_ticket_back(['bhc_notice' => 'released']);
	}
	update_post_meta($id, '_bhc_assignee', get_current_user_id());
	update_post_meta($id, '_bhc_lock_user', get_current_user_id());
	update_post_meta($id, '_bhc_lock_until', time() + 15 * MINUTE_IN_SECONDS);
	bhc_ticket_audit($id, 'ticket_claim');
	bhc_ticket_back(['bhc_notice' => 'claimed']);
}
add_action('admin_post_bhc_ticket_claim', 'bhc_ticket_claim');

function bhc_ticket_reveal() {
	if (!bhc_ticket_can()) wp_die('دسترسی مجاز نیست');
	if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') wp_die('روش درخواست نامعتبر است');
	$id = absint($_POST['post_id'] ?? 0);
	check_admin_referer('bhc_ticket_reveal_' . $id);
	$reason = sanitize_text_field(wp_unslash($_POST['reason'] ?? ''));
	if (mb_strlen($reason) < 3) bhc_ticket_back(['bhc_notice' => 'reason']);
	$post = get_post($id);
	if (!$post || $post->post_type !== 'bhc_lead') bhc_ticket_back(['bhc_notice' => 'missing']);
	$plain = bhc_privacy_decrypt((string) get_post_meta($id, '_bhc_contact_enc', true));
	if ($plain === '') $plain = 'راه تماسی ذخیره نشده است.';
	set_transient('bhc_reveal_' . get_current_user_id() . '_' . $id, $plain, MINUTE_IN_SECONDS);
	bhc_ticket_audit($id, 'ticket_reveal', ['reason' => $reason]);
	bhc_ticket_back(['bhc_notice' => 'revealed', 'reveal' => $id]);
}
add_action('admin_post_bhc_ticket_reveal', 'bhc_ticket_reveal');

function bhc_chat_thread() {
	if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') wp_send_json_error(['message' => 'روش درخواست نامعتبر است.'], 405);
	check_ajax_referer('bhc_nonce', 'nonce');
	$cid = sanitize_text_field(wp_unslash($_POST['conversation_id'] ?? ''));
	$token = sanitize_text_field(wp_unslash($_POST['privacy_token'] ?? ''));
	if (!preg_match('/^[a-f0-9]{32}$/', $cid) || strlen($token) < 20) wp_send_json_error(['message' => 'گفتگو پیدا نشد.'], 404);
	$hash = hash('sha256', $token);
	global $wpdb;
	$ids = $wpdb->get_col($wpdb->prepare("SELECT p.ID FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} m ON p.ID=m.post_id INNER JOIN {$wpdb->postmeta} t ON p.ID=t.post_id WHERE p.post_type='bhc_lead' AND p.post_status='private' AND m.meta_key='_bhc_conversation' AND m.meta_value=%s AND t.meta_key='_bhc_privacy_token_hash' AND t.meta_value=%s LIMIT 5", $cid, $hash));
	$replies = [];
	foreach ($ids as $id) {
		foreach (bhc_ticket_rows(get_post_meta($id, '_bhc_replies', true)) as $row) {
			if (($row['kind'] ?? 'reply') === 'note') continue;
			$replies[] = ['at' => sanitize_text_field($row['at'] ?? ''), 'text' => sanitize_textarea_field($row['text'])];
		}
	}
	wp_send_json_success(['replies' => $replies]);
}
add_action('wp_ajax_bhc_chat_thread', 'bhc_chat_thread');
add_action('wp_ajax_nopriv_bhc_chat_thread', 'bhc_chat_thread');

function bhc_chat_kb_page() {
	bhc_chat_kb_admin_page();
}

function bhc_chat_log($event, $intent = 'unknown', $extra = []) {
	$events = ['question', 'feedback', 'handoff', 'product_click', 'add_to_cart', 'open', 'fallback', 'ai_error'];
	if (!in_array($event, $events, true)) return;
	$intents = ['purchase', 'budget', 'compare', 'support', 'order', 'complaint', 'payment', 'shipping', 'return', 'warranty', 'unknown'];
	if (!in_array($intent, $intents, true)) $intent = 'unknown';
	$rows = array_values((array) get_option('bhc_audit_log', []));
	$rows[] = array_merge(['action' => 'chat_event', 'event' => $event, 'intent' => $intent, 'at' => current_time('mysql')], $extra);
	update_option('bhc_audit_log', array_slice($rows, -400), false);
}

function bhc_chat_normalize($text) {
	return bhc_chat_letters($text);
}

function bhc_chat_letters($text) {
	$t = mb_strtolower(trim((string) $text), 'UTF-8');
	return strtr($t, ['ي' => 'ی', 'ك' => 'ک', 'ة' => 'ه', '‌' => '']);
}

function bhc_chat_followup($q, $history) {
	$t = bhc_chat_letters($q);
	if (!preg_match('/موجوده|موجود است|چنده|چند است|قیمتش|همون قبلی|همان قبلی|این مدل/u', $t) || mb_strlen($t, 'UTF-8') > 40) return $q;
	foreach (array_reverse((array) $history) as $row) {
		if (!is_array($row) || ($row['role'] ?? '') !== 'user') continue;
		$prev = trim((string) ($row['content'] ?? ''));
		if ($prev === '' || preg_match('/موجوده|چنده|قیمتش|همون|این مدل/u', $prev)) continue;
		return $prev . ' — ' . $q;
	}
	return 'نام مدل را بفرستید؛ در این گفتگو مدل قبلی برای این سؤال پیدا نشد.';
}

function bhc_chat_intent($text) {
	$t = bhc_chat_letters($text);
	if (preg_match('/مرجوع|بازگشت کالا|return/u', $t)) return 'return';
	if (preg_match('/گارانتی|ضمانت|warranty/u', $t)) return 'warranty';
	if (preg_match('/پرداخت|درگاه|payment/u', $t)) return 'payment';
	if (preg_match('/ارسال|حمل|shipping/u', $t)) return 'shipping';
	if (preg_match('/پشتیبان انسانی|اپراتور|انسان/u', $t)) return 'support';
	if (preg_match('/پیگیری|سفارش من|رهگیری/u', $t)) return 'order';
	if (preg_match('/مقایسه|فرق|تفاوت/u', $t)) return 'compare';
	if (preg_match('/شکایت|خراب|ناراضی/u', $t)) return 'complaint';
	if (preg_match('/بودجه|زیر|تومان|ریال/u', $t)) return 'budget';
	return 'purchase';
}

function bhc_chat_hours_note() {
	$hours = (string) (Baharaan_Commerce::opts()['support_hours'] ?? '');
	if ($hours === '') return 'ساعات پاسخ انسانی در تنظیمات فروشگاه ثبت نشده است؛ درخواست در صف می‌ماند.';
	if (preg_match('/(\d{1,2})\s*(?:تا|-|–)\s*(\d{1,2})/u', $hours, $m)) {
		$hour = (int) current_time('G');
		$start = (int) $m[1];
		$end = (int) $m[2];
		$open = $start <= $end ? ($hour >= $start && $hour < $end) : ($hour >= $start || $hour < $end);
		return $open ? 'الان داخل بازه اعلام‌شده پشتیبانی است: ' . $hours . '.' : 'الان خارج از ساعت پشتیبانی است (' . $hours . '). تیکت ثبت می‌شود و در ساعت کاری پیگیری می‌شود.';
	}
	return 'ساعات اعلام‌شده پشتیبانی: ' . $hours . '.';
}

function bhc_chat_presence() {
	$hours = (string) (Baharaan_Commerce::opts()['support_hours'] ?? '');
	if (preg_match('/(\d{1,2})\s*(?:تا|-|–)\s*(\d{1,2})/u', $hours, $m)) {
		$hour = (int) current_time('G');
		$start = (int) $m[1];
		$end = (int) $m[2];
		$open = $start <= $end ? ($hour >= $start && $hour < $end) : ($hour >= $start || $hour < $end);
		if ($open) return ['online' => true, 'label' => 'پشتیبان در ساعت اعلام‌شده در دسترس است'];
	}
	return ['online' => false, 'label' => 'پاسخ در صف است'];
}

function bhc_chat_guard_answer($answer, array $items) {
	$allowed = [];
	foreach ($items as $item) {
		if (!empty($item['amount'])) $allowed[] = (string) (int) $item['amount'];
		foreach ((array) ($item['variations'] ?? []) as $variation) {
			$digits = preg_replace('/\D/', '', (string) ($variation['price'] ?? ''));
			if ($digits !== '') $allowed[] = $digits;
		}
	}
	$normalized = strtr((string) $answer, ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9']);
	if (!preg_match_all('/\d[\d,٬.]{3,}/u', $normalized, $found)) return $answer;
	foreach ($found[0] as $raw) {
		$number = preg_replace('/\D/', '', $raw);
		if (strlen($number) < 4) continue;
		$match = false;
		foreach ($allowed as $price) {
			if ($number === $price) $match = true;
		}
		if (!$match) {
			$lines = array_map(function ($item) {
				return $item['name'] . ' — ' . $item['price'] . ' — ' . $item['stock'];
			}, $items);
			return "این پاسخ قیمت یا موجودی خارج از کارت داشت و کنار گذاشته شد.\n" . implode("\n", $lines);
		}
	}
	return $answer;
}

function bhc_chat_policy_reply($intent) {
	$map = [
		'return' => 'مرجوعی فقط با شماره سفارش و طبق شرایط همان کالا بررسی می‌شود. از دکمه «پیگیری سفارش» شماره سفارش و ایمیل صورتحساب را بفرستید. بدون سفارش، مهلت یا نتیجه مرجوعی اعلام نمی‌شود.',
		'warranty' => 'مدت ضمانت روی مشخصات همان مدل یا برگه ضمانت سفارش است. نام مدل را بفرستید تا مشخصات ثبت‌شده را نشان بدهم. مدت ضمانت را حدس نمی‌زنم.',
		'payment' => 'پرداخت در صفحه تسویه‌حساب و با درگاه فعال فروشگاه انجام می‌شود. اگر پرداخت ناموفق بود، وضعیت همان سفارش را از پیگیری سفارش ببینید. شماره کارت، رمز و رمز پویا را اینجا ننویسید.',
		'shipping' => 'هزینه و زمان ارسال در تسویه‌حساب، بعد از ثبت شهر، قطعی می‌شود. اگر کد رهگیری روی سفارش ذخیره شده باشد همان را در پیگیری می‌بینید. آدرس و تلفن را در چت تکرار نمی‌کنم.',
		'support' => 'نام و راه تماس را در فرم پایین بنویسید تا تیکت با شماره BH ساخته شود. ' . bhc_chat_hours_note(),
		'order' => 'پیگیری فقط با شماره سفارش و ایمیل صورتحساب انجام می‌شود. دکمه «پیگیری سفارش» را بزنید. شماره سفارش به‌تنهایی وضعیت را نشان نمی‌دهد.',
		'complaint' => 'مشکل را کوتاه بنویسید. اگر سفارش دارید، شماره آن را از پیگیری سفارش بگیرید. جزئیات بانکی را نفرستید.',
	];
	return $map[$intent] ?? '';
}

add_action('init', function () {
	remove_action('wp_ajax_bhc_assistant', ['Baharaan_Commerce', 'assistant']);
	remove_action('wp_ajax_nopriv_bhc_assistant', ['Baharaan_Commerce', 'assistant']);
}, 20);
add_action('wp_ajax_bhc_assistant', 'bhc_assistant_live');
add_action('wp_ajax_nopriv_bhc_assistant', 'bhc_assistant_live');

function bhc_chat_smalltalk_reply($text) {
	$t = bhc_chat_letters($text);
	if (preg_match('/^(?:سلام|سلم|سلام علیکم|سلام و عرض ادب|درود|صبح بخیر|عصر بخیر|شب بخیر|hi|hello|salam|salam alaykom|dorood)\b/iu', $t)) {
		return 'سلام و خوش آمدید به بهارآن 🌿 من کنار شما هستم. بگویید دنبال چه وسیله‌ای هستید، برای چند نفر یا چه فضایی می‌خواهید و کم‌صدا بودن، کم‌مصرف بودن یا بودجه برایتان مهم‌تر است؟';
	}
	if (preg_match('/(?:حالت چطوره|خوبی|چطوری|چه خبر|خوب هستی|مرسی|ممنون|merci|chetori)\b/iu', $t)) {
		return 'ممنون که پرسیدید، من آماده‌ام کمکتان کنم 😊 نوع وسیله یا مدل موردنظرتان را بفرستید تا برای انتخاب، مقایسه یا بررسی کاتالوگ راهنمایی‌تان کنم.';
	}
	if (preg_match('/(?:halet\s*chetore|khobi|chetori|merci|salam)\b/iu', $t)) return 'ممنون که پرسیدید 😊 آماده‌ام برای انتخاب لوازم خانه کمکتان کنم؛ نوع وسیله یا مدل موردنظرتان را بفرستید.';
	if (preg_match('/^(?:خداحافظ|فعلا|بای|bye)\b/iu', $t)) return 'خدانگهدار 🌷 هر وقت برای انتخاب لوازم خانه کمک خواستید، به بهارآن برگردید.';
	return '';
}

function bhc_chat_no_match_reply($intent, $question) {
	if ('compare' === $intent) return 'برای مقایسه منصفانه، نام یا مدل دقیق دو یا چند کالا را بفرستید. معیارهایی مثل ظرفیت، مصرف، صدا و امکانات را فقط از مشخصات واقعی کاتالوگ بررسی می‌کنم.';
	if ('budget' === $intent) return 'برای پیشنهاد در محدوده بودجه، نوع وسیله، بودجه تقریبی، ظرفیت و کاربرد را بگویید. قیمت و موجودی فقط از کاتالوگ زنده خوانده می‌شود و عددی از خودم نمی‌سازم.';
	if ('support' === $intent) return 'موضوع مشکل و اگر دارید نام مدل یا شماره سفارش را بفرستید تا راهنمایی اولیه یا ارجاع مناسب انجام شود. رمز، CVV، شماره کارت و کد یک‌بارمصرف لازم نیست.';
	if ('unknown' === $intent) return 'خوشحال می‌شوم کمک کنم 😊 نوع وسیله، کاربرد، تعداد نفرات یا متراژ و ویژگی مهمتان را بگویید؛ اگر مدل مشخصی مدنظر دارید نام دقیق آن را بفرستید.';
	return 'برای راهنمایی دقیق‌تر، نوع وسیله، کاربرد، ظرفیت یا متراژ و ویژگی مهمتان را بگویید. اگر سؤال درباره قیمت یا موجودی است، نام یا مدل دقیق لازم است تا از کاتالوگ زنده بررسی شود.';
}

function bhc_chat_fast_advisor_reply($text, $intent) {
	$t = bhc_chat_letters($text);
	if ( preg_match('/^\s*[0-9۰-۹]+\s*(?:متر|متری)\s*(?:خانگی|خانه|آپارتمان)?\s*$/iu', $t) ) return 'برای متراژ گفته‌شده، اول باید نوع وسیله را مشخص کنیم؛ یخچال، کولر، جارو، لباسشویی یا وسیله‌ای دیگر؟ شهر، تعداد نفرات و ویژگی مهمتان مثل کم‌صدا یا کم‌مصرف بودن را هم بگویید تا پیشنهاد دقیق بدهم.';
	if ( preg_match('/(?:آدرس|ادرس|address|کجا هستید|کجایید)/iu', $t) ) return !empty(Baharaan_Commerce::opts()['store_address']) ? 'آدرس فروشگاه: ' . sanitize_text_field(Baharaan_Commerce::opts()['store_address']) : 'آدرس حضوری در تنظیمات فروشگاه ثبت نشده است. برای راهنمایی خرید، نوع وسیله یا مدل را بفرستید و برای تماس از لینک‌های پایین چت استفاده کنید.';
	if ( preg_match('/(?:پرفروش|پر فروش|پرامتیاز|پر امتیاز|بیشترین امتیاز|محبوب‌ترین|محبوب ترین|best seller|top rated|highest rated)/iu', $t) ) return 'در کاتالوگ فعلی برای محصولات امتیاز کاربران و آمار فروش قابل اتکا ثبت نشده است؛ بنابراین پرفروش‌ترین یا پرامتیازترین مدل را ادعا نمی‌کنم. نام دسته، بودجه و معیارهای مهمتان را بگویید تا بر اساس مشخصات واقعی، گزینه‌ها را منصفانه مقایسه کنم.';
	if ( 'compare' === $intent || preg_match('/(?:کدام بهتر|کدوم بهتر|فرق|تفاوت|مقایسه|بهترین برند)/iu', $t) ) return 'برای مقایسه حرفه‌ای، اول ظرفیت و ابعاد مناسب، سپس مصرف انرژی، صدای کار، کیفیت ساخت، امکانات واقعی و خدمات پس از فروش را بسنجید. نام یا مدل دقیق را بفرستید تا بر اساس کاتالوگ واقعی مقایسه کنم؛ بدون مدل، برند را برنده مطلق اعلام نمی‌کنم.';
	if ( 'budget' === $intent ) return 'برای انتخاب اقتصادی، نوع وسیله، ظرفیت، بودجه تقریبی، فضای نصب و ویژگی‌های ضروری را بگویید. می‌توانم گزینه‌ها را از نظر ارزش خرید مقایسه کنم، اما قیمت و موجودی را فقط از کاتالوگ زنده می‌خوانم.';
	if ( preg_match('/لباسشویی|لباس شویی|ماشین لباس|washer|washing machine/iu', $t) ) return 'برای لباسشویی، ظرفیت را بر اساس تعداد نفرات و حجم شست‌وشو انتخاب کنید و به صدای کار، مصرف، لرزش، نوع موتور، برنامه‌ها و خدمات توجه کنید. تعداد نفرات، فضای نصب و کم‌صدا یا کم‌مصرف بودن را بگویید تا راهنمایی دقیق‌تری بدهم.';
	if ( preg_match('/یخچال|فریزر|ساید|fridge|freezer/iu', $t) ) return 'برای یخچال و فریزر، ابعاد محل، تعداد نفرات، نوع چیدمان، مصرف، صدای کمپرسور و دسترسی به خدمات مهم است. فضای نصب و تعداد اعضای خانواده را بگویید؛ برای قیمت یا موجودی، نام مدل دقیق لازم است.';
	if ( preg_match('/ظرفشویی|ظرف شویی|dishwasher/iu', $t) ) return 'برای ظرفشویی، تعداد نفرات، فضای نصب، رومیزی یا ایستاده بودن، ظرفیت، صدای کار و برنامه‌های شست‌وشو را بررسی کنید. اگر مدل یا ابعاد آشپزخانه را بفرستید، انتخاب دقیق‌تری پیشنهاد می‌دهم.';
	if ( preg_match('/جارو|vacuum/iu', $t) ) return 'برای جاروبرقی یا رباتیک، نوع کف، متراژ، موانع، توان مکش، صدای کار، فیلتر و دسترسی به قطعات مهم است. متراژ و نوع استفاده را بگویید تا بین مدل‌ها معیار درست بدهم.';
	if ( preg_match('/کولر|اسپلیت|ac|air conditioner/iu', $t) ) return 'برای کولر، متراژ، اقلیم، آفتاب‌گیر بودن فضا، برق تک‌فاز یا سه‌فاز و نیاز به گرمایش مهم است. متراژ و شهر را بفرستید؛ ظرفیت و موجودی مدل را حدس نمی‌زنم.';
	if ( preg_match('/اجاق|فر|هود|stove|oven|hood/iu', $t) ) return 'برای اجاق، فر و هود، ابعاد برش، توکار یا رومیزی بودن، برق یا گاز، تهویه، تعداد شعله و خدمات نصب را بررسی کنید. ابعاد محل و نوع سوخت را بگویید تا گزینه مناسب‌تر مشخص شود.';
	if ( preg_match('/مایکرو|microwave|چای|غذاساز|مخلوط|kettle|blender/iu', $t) ) return 'برای لوازم کوچک آشپزخانه، حجم مصرف، فضای روی کابینت، توان، جنس بدنه، شست‌وشوی آسان و خدمات را مقایسه کنید. نوع استفاده و ظرفیت موردنیاز را بفرستید تا راهنمایی دقیق بدهم.';
	return '';
}

function bhc_chat_has_model_signal($text) {
	return (bool) preg_match('/(?:\b(?=[A-Za-z0-9-]*[A-Za-z])(?=[A-Za-z0-9-]*\d)[A-Za-z0-9-]{4,}\b|\bSKU\b|کد\s*مدل|مدل\s+[A-Za-z0-9۰-۹])/iu', (string) $text);
}

function bhc_chat_category_recommendations($text) {
	if (!function_exists('wc_get_products')) return [];
	$t = bhc_chat_letters($text);
	$terms = [];
	$groups = [
		['(?:جارو(?:برقی| ربات| شارژی)?|جاروشارژی|vacuum|robot)', 'جارو'],
		['(?:لباسشویی|لباس شویی|ماشین لباس|washer|washing machine)', 'لباسشویی'],
		['(?:ظرفشویی|ظرف شویی|ماشین ظرف|dishwasher)', 'ظرفشویی'],
		['(?:یخچال|فریزر|ساید|fridge|freezer)', 'یخچال'],
		['(?:کولر|اسپلیت|air conditioner|\bac\b)', 'کولر'],
		['(?:اجاق|فر توکار|گاز|stove|oven)', 'اجاق'],
		['(?:هود|hood)', 'هود'],
		['(?:تلویزیون|تلوزیون|tv|television)', 'تلویزیون'],
		['(?:مایکرو|مایکروویو|microwave)', 'مایکروویو'],
	];
	foreach ($groups as $group) if (preg_match('/' . $group[0] . '/iu', $t)) { $terms[] = $group[1]; break; }
	if (!$terms || bhc_chat_has_model_signal($text)) return [];
	$products = wc_get_products(['status' => 'publish', 'limit' => 8, 's' => $terms[0], 'return' => 'objects']);
	if (!$products) return [];
	$items = [];
	foreach ($products as $p) {
		if (!$p || !is_a($p, 'WC_Product')) continue;
		// Never present an unavailable or unpriced item as a recommendation.
		// The live catalog can still be searched explicitly by exact model.
		if (!$p->is_in_stock() || $p->get_price() === '') continue;
		$meta = get_post_meta($p->get_id(), '_bhc_specs', true);
		$specs = [];
		if (is_array($meta)) foreach ($meta as $k => $v) if (is_scalar($v) && (string) $v !== '') $specs[] = sanitize_text_field($k) . ': ' . sanitize_text_field($v);
		$rating = method_exists($p, 'get_average_rating') ? (float) $p->get_average_rating() : 0.0;
		$rating_count = method_exists($p, 'get_rating_count') ? (int) $p->get_rating_count() : 0;
		$sales = method_exists($p, 'get_total_sales') ? (int) $p->get_total_sales() : (int) get_post_meta($p->get_id(), 'total_sales', true);
		$score = 0.0;
		$reasons = [];
		$searchable = bhc_chat_letters($p->get_name() . ' ' . implode(' ', $specs));
		$needs_quiet = (bool) preg_match('/(?:کم\s*صدا|بی\s*صدا|ساکت|quiet|silent)/iu', $t);
		$needs_efficient = (bool) preg_match('/(?:کم\s*مصرف|مصرف\s*کم|اینورتر|inverter|energy\s*save)/iu', $t);
		$needs_small = (bool) preg_match('/(?:آشپزخانه\s*کوچک|فضای\s*کم|آپارتمان\s*کوچک|small\s*kitchen|compact)/iu', $t);
		$needs_family = preg_match('/([0-9۰-۹]+)\s*(?:نفر|person|people)/iu', $t, $family_match) ? (int) preg_replace('/\D/', '', $family_match[1]) : 0;
		$budget = preg_match('/(?:بودجه|زیر|تا|under|budget)\s*([0-9۰-۹]{3,})/iu', $t, $budget_match) ? (float) preg_replace('/\D/', '', $budget_match[1]) : 0.0;
		if ($p->is_in_stock()) { $score += 25; $reasons[] = 'موجودی فعلی'; }
		if ($rating_count > 0 && $rating > 0) { $score += min(35, ($rating / 5) * 35); $reasons[] = 'امتیاز کاربران (' . number_format_i18n($rating, 1) . ' از ۵)'; }
		if ($sales > 0) { $score += min(25, log(1 + $sales) * 4); $reasons[] = 'سابقه فروش ثبت‌شده'; }
		if ($specs) { $score += min(15, count($specs)); $reasons[] = 'مشخصات فنی ثبت‌شده'; }
		if ($needs_quiet && preg_match('/(?:کم\s*صدا|بی\s*صدا|ساکت|silent|noise)/iu', $searchable)) { $score += 12; $reasons[] = 'هم‌خوان با اولویت کم‌صدا'; }
		if ($needs_efficient && preg_match('/(?:کم\s*مصرف|اینورتر|inverter|energy|رده\s*[آ-ی])/iu', $searchable)) { $score += 12; $reasons[] = 'هم‌خوان با اولویت مصرف'; }
		if ($needs_small && preg_match('/(?:رومیزی|کمپکت|کوچک|compact|slim|باریک)/iu', $searchable)) { $score += 10; $reasons[] = 'مناسب‌تر برای فضای محدود'; }
		if ($needs_family && preg_match('/(?:' . max(1, $needs_family - 1) . '|' . $needs_family . '|' . ($needs_family + 1) . ')\s*(?:نفر|نفره|kg|کیلو)/iu', $searchable)) { $score += 10; $reasons[] = 'ظرفیت نزدیک به تعداد نفرات'; }
		if ($budget > 0 && (float) $p->get_price() > 0 && (float) $p->get_price() <= $budget) { $score += 14; $reasons[] = 'در محدوده بودجه ثبت‌شده'; }
		$items[] = [
			'id' => $p->get_id(), 'name' => $p->get_name(), 'sku' => $p->get_sku(),
			'price' => wp_strip_all_tags($p->get_price_html()), 'price_label' => 'قیمت بهاران', 'stock' => $p->is_in_stock() ? 'موجود' : 'ناموجود', 'stock_label' => 'موجودی بهاران',
			'specs' => implode('؛ ', array_slice($specs, 0, 12)), 'url' => get_permalink($p->get_id()),
			'rating' => $rating, 'rating_count' => $rating_count, 'sales_count' => $sales,
			'recommendation_score' => round($score, 2), 'recommendation_reasons' => implode('، ', $reasons),
		];
	}
	usort($items, function ($a, $b) { return ($b['recommendation_score'] <=> $a['recommendation_score']) ?: strcasecmp($a['name'], $b['name']); });
	$items = array_slice($items, 0, 4);
	if (function_exists('bhc_market_quotes_for_product')) foreach ($items as &$item) { $item['market_quotes'] = bhc_market_quotes_for_product($item['id'], 3); if (function_exists('bhc_market_quote_health')) foreach ($item['market_quotes'] as &$quote) $quote['health'] = bhc_market_quote_health($item['id'], $quote); unset($quote); }
	unset($item);
	return $items;
}

function bhc_assistant_live() {
	check_ajax_referer('bhc_nonce', 'nonce');
	$q = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));
	if (!$q || Baharaan_Commerce::text_length($q) > 700) wp_send_json_error(['message' => 'پرسش معتبر نیست.'], 400);
	if (preg_match('/(?:cvv|cvc|otp|رمز پویا|شماره کارت)/iu', $q)) wp_send_json_error(['message' => 'اطلاعات بانکی یا رمز یک‌بارمصرف را ارسال نکنید.'], 400);
	$o = Baharaan_Commerce::opts();
	$rate = bhc_rate_subject('bhc_ai_');
	$used = (int) get_transient($rate);
	if ($used >= 12) wp_send_json_error(['message' => 'تعداد پرسش‌ها زیاد است؛ کمی بعد دوباره تلاش کنید.'], 429);
	set_transient($rate, $used + 1, MINUTE_IN_SECONDS);
	$cid = sanitize_text_field(wp_unslash($_POST['conversation_id'] ?? ''));
	$history = [];
	$hist_key = '';
	if (preg_match('/^[a-f0-9]{32}$/', $cid)) {
		$hist_key = 'bhc_cv_' . hash('sha256', $cid);
		$history = array_values((array) get_transient($hist_key));
	}
	$expanded = bhc_chat_followup($q, $history);
	if ($expanded !== $q && str_contains($expanded, 'نام مدل را بفرستید')) {
		wp_send_json_success(['message' => $expanded, 'ai' => false, 'intent' => 'purchase', 'source' => 'followup', 'products' => []]);
	}
	$q = $expanded;
	$smalltalk = bhc_chat_smalltalk_reply($q);
	if ($smalltalk !== '') {
		bhc_chat_log('question', 'unknown');
		wp_send_json_success(['message' => $smalltalk, 'ai' => false, 'intent' => 'unknown', 'source' => 'smalltalk', 'products' => []]);
	}
	$intent = bhc_chat_intent($q);
	bhc_chat_log('question', $intent);
	$policy = bhc_chat_policy_reply($intent);
	if ($policy && $intent !== 'purchase' && $intent !== 'budget' && $intent !== 'compare') {
		bhc_chat_log($intent === 'support' ? 'handoff' : 'fallback', $intent);
		wp_send_json_success(['message' => $policy, 'ai' => false, 'intent' => $intent, 'source' => 'policy', 'products' => []]);
	}
	$recommendations = bhc_chat_category_recommendations($q);
	if ($recommendations) {
		$has_rating = false; $has_sales = false;
		foreach ($recommendations as $item) { $has_rating = $has_rating || !empty($item['rating_count']); $has_sales = $has_sales || !empty($item['sales_count']); }
		$basis = 'موجودی و مشخصات فنی ثبت‌شده';
		if ($has_rating) $basis .= ' و امتیاز کاربران';
		if ($has_sales) $basis .= ' و سابقه فروش';
		$personal = [];
		if (preg_match('/(?:کم\s*صدا|بی\s*صدا|quiet|silent)/iu', bhc_chat_letters($q))) $personal[] = 'کم‌صدایی';
		if (preg_match('/(?:کم\s*مصرف|inverter|energy)/iu', bhc_chat_letters($q))) $personal[] = 'مصرف کمتر';
		if (preg_match('/(?:بودجه|زیر|تا|under)/iu', bhc_chat_letters($q))) $personal[] = 'بودجه';
		$personal_note = $personal ? ' اولویت تشخیص‌داده‌شده: ' . implode('، ', $personal) . '.' : '';
		$category_note = 'بر اساس دسته‌ای که گفتید، این گزینه‌ها در کاتالوگ فعلی پیدا شد و بر پایهٔ ' . $basis . ' و نیازهای قابل‌تشخیص از پیام شما مرتب شده‌اند.' . $personal_note . ' این رتبه‌بندی «بهترین مطلق» نیست؛ اگر بودجه، ابعاد یا اولویتتان را بفرستید، رتبه‌بندی دقیق‌تری انجام می‌دهم.';
		wp_send_json_success(['message' => $category_note, 'ai' => false, 'intent' => $intent, 'source' => 'catalog_recommendation', 'products' => $recommendations]);
	}
	$fast_advice = bhc_chat_has_model_signal( $q ) ? '' : bhc_chat_fast_advisor_reply($q, $intent);
	if ($fast_advice !== '') {
		wp_send_json_success(['message' => $fast_advice, 'ai' => false, 'intent' => $intent, 'source' => 'advisor_rules', 'products' => []]);
	}
	if (!bhc_chat_has_model_signal($q) && class_exists('BHC_Chat_Knowledge_Store')) {
		foreach (BHC_Chat_Knowledge_Store::search($q, 1) as $entry) {
			if (!empty($entry['answer'])) {
				wp_send_json_success(['message' => sanitize_textarea_field($entry['answer']), 'ai' => false, 'intent' => $intent, 'source' => 'knowledge', 'products' => []]);
			}
		}
	}
	$items = [];
	if (function_exists('wc_get_products')) {
		$ids = wc_get_products(['status' => 'publish', 'limit' => 4, 's' => $q, 'return' => 'ids']);
		foreach ($ids as $id) {
			$p = wc_get_product($id);
			if (!$p) continue;
			$spec_meta = get_post_meta($p->get_id(), '_bhc_specs', true);
			$spec_text = '';
			if (is_array($spec_meta)) { $spec_parts = []; foreach ($spec_meta as $spec_key => $spec_value) { if (is_scalar($spec_value) && (string) $spec_value !== '') $spec_parts[] = sanitize_text_field($spec_key) . ': ' . sanitize_text_field($spec_value); } $spec_text = implode('؛ ', array_slice($spec_parts, 0, 20)); }
			$variations = [];
			if ($p->is_type('variable')) {
				foreach (array_slice($p->get_available_variations(), 0, 8) as $row) {
					if (empty($row['variation_id'])) continue;
					$label = [];
					foreach ((array) ($row['attributes'] ?? []) as $attr => $value) $label[] = wc_attribute_label(str_replace('attribute_', '', $attr)) . ': ' . $value;
					$variations[] = ['id' => (int) $row['variation_id'], 'label' => implode('، ', $label), 'in_stock' => !empty($row['is_in_stock']), 'price' => wp_strip_all_tags($row['price_html'] ?? '')];
				}
			}
			$items[] = [
				'id' => $p->get_id(),
				'name' => $p->get_name(),
				'sku' => $p->get_sku(),
				'type' => $p->get_type(),
				'price' => wp_strip_all_tags($p->get_price_html()),
				'price_label' => 'قیمت بهاران',
				'amount' => (float) $p->get_price(),
				'currency' => function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : '',
				'stock' => $p->is_in_stock() ? 'موجود' : 'ناموجود',
				'stock_label' => 'موجودی بهاران',
				'purchasable' => $p->is_type('simple') && $p->is_purchasable() && $p->is_in_stock(),
				'variations' => $variations,
				'specs' => $spec_text,
				'url' => get_permalink($p->get_id()),
			];
		}
	}
	if ($items) {
		if (function_exists('bhc_market_quotes_for_product')) foreach ($items as &$item) { $item['market_quotes'] = bhc_market_quotes_for_product($item['id'], 3); if (function_exists('bhc_market_quote_health')) foreach ($item['market_quotes'] as &$quote) $quote['health'] = bhc_market_quote_health($item['id'], $quote); unset($quote); }
		unset($item);
		$lines = array_map(function ($item) {
			$line = $item['name'] . ' — قیمت بهاران: ' . ($item['price'] !== '' ? $item['price'] : 'ثبت نشده') . ' — موجودی بهاران: ' . $item['stock'];
			if (!empty($item['specs'])) $line .= ' — مشخصات ثبت‌شده: ' . $item['specs'];
			if (!empty($item['market_quotes'][0])) { $quote = $item['market_quotes'][0]; $line .= ' — قیمت دیجی‌کالا (مرجع، جدا از قیمت فروش): ' . (($quote['price'] ?? '') !== '' ? $quote['price'] . ' ' . ($quote['currency'] ?? '') : 'هنوز خوانده نشده') . ' — تاریخ خواندن: ' . ($quote['checked_at'] ?? 'نامشخص') . (!empty($quote['health']['stale']) ? ' — این استعلام کهنه است' : ''); }
			else $line .= ' — قیمت دیجی‌کالا هنوز برای این کالا خوانده نشده';
			return $line;
		}, $items);
		wp_send_json_success(['message' => "از کاتالوگ فروشگاه:\n" . implode("\n", $lines) . "\nفقط قیمت بهاران قیمت فروش است. قیمت دیجی‌کالا مرجع بازار است و جدا مانده.", 'ai' => false, 'intent' => $intent, 'source' => 'catalog', 'products' => $items]);
	}
	if ( ! get_option( 'bhc_chat_remote_fallback', false ) ) {
		if (function_exists('bhc_chat_unanswered_add')) bhc_chat_unanswered_add($q, $intent, 'no_catalog_match');
		bhc_chat_log('fallback', $intent);
		wp_send_json_success(['message' => bhc_chat_no_match_reply($intent, $q), 'ai' => false, 'intent' => $intent, 'source' => 'fast_no_match', 'products' => []]);
	}
	if (!function_exists('bhc_openrouter_ready') || !bhc_openrouter_ready()) {
		if (function_exists('bhc_chat_unanswered_add')) bhc_chat_unanswered_add($q, $intent, 'no_catalog_match');
		bhc_chat_log('fallback', $intent);
		wp_send_json_success(['message' => bhc_chat_no_match_reply($intent, $q), 'ai' => false, 'intent' => $intent, 'source' => 'no_match', 'products' => []]);
	}
	if (get_transient('bhc_ai_circuit')) {
		bhc_chat_log('ai_error', $intent);
		wp_send_json_success(['message' => 'سرویس هوشمند موقتاً قطع است. نتیجه کاتالوگ را در کارت‌ها ببینید یا کمی بعد دوباره بپرسید.', 'ai' => false, 'intent' => $intent, 'source' => 'circuit', 'products' => $items]);
	}
	$day = 'bhc_ai_day_' . gmdate('Ymd');
	$day_used = (int) get_transient($day);
	if ($day_used >= 400) {
		wp_send_json_success(['message' => 'سقف روزانه پاسخ هوشمند پر شده است. کارت‌های کاتالوگ همچنان معتبرند.', 'ai' => false, 'intent' => $intent, 'source' => 'budget', 'products' => $items]);
	}
	$messages = [['role' => 'system', 'content' => 'You are Baharaan Home shopping assistant. The store had no prepared answer and no catalog match. Answer in short Persian. Do not invent a product, price, stock, warranty, shipping time, or order status. Say the item was not found and ask for the exact model name. Do not request card data, OTP, or passwords.']];
	foreach (array_slice($history, -4) as $row) {
		if (!is_array($row) || empty($row['content'])) continue;
		$messages[] = ['role' => (($row['role'] ?? '') === 'user' ? 'user' : 'assistant'), 'content' => mb_substr(sanitize_textarea_field($row['content']), 0, 500)];
	}
	$messages[] = ['role' => 'user', 'content' => $q];
	$result = bhc_openrouter_complete($messages, 300, 'chat');
	$resp = $result['response'];
	if (!$result['ok']) {
		$fails = (int) get_transient('bhc_ai_fails');
		set_transient('bhc_ai_fails', $fails + 1, 10 * MINUTE_IN_SECONDS);
		if ($fails + 1 >= 4) set_transient('bhc_ai_circuit', 1, 15 * MINUTE_IN_SECONDS);
		bhc_chat_log('ai_error', $intent);
		wp_send_json_success(['message' => 'پاسخ هوشمند الان برنگشت. نام، قیمت و موجودی کارت‌ها از کاتالوگ زنده است.', 'ai' => false, 'intent' => $intent, 'source' => 'ai_down', 'products' => $items]);
	}
	delete_transient('bhc_ai_fails');
	set_transient($day, $day_used + 1, DAY_IN_SECONDS);
	$json = json_decode(wp_remote_retrieve_body($resp), true);
	$answer = sanitize_textarea_field($json['choices'][0]['message']['content'] ?? '');
	if ($answer === '') $answer = 'برای این مورد پاسخ قابل اتکایی ساخته نشد. کارت محصول را ببینید یا مدل را دقیق‌تر بنویسید.';
	$guarded = bhc_chat_guard_answer($answer, $items);
	$rejected = $guarded !== $answer;
	$answer = $guarded;
	if ($hist_key) {
		$history[] = ['role' => 'user', 'content' => $q];
		$history[] = ['role' => 'assistant', 'content' => $answer];
		set_transient($hist_key, array_slice($history, -8), 2 * HOUR_IN_SECONDS);
	}
	wp_send_json_success(['message' => $answer, 'ai' => !$rejected, 'intent' => $intent, 'source' => $rejected ? 'ai_rejected' : 'ai', 'products' => $items]);
}

function bhc_chat_event() {
	check_ajax_referer('bhc_nonce', 'nonce');
	$event = sanitize_key(wp_unslash($_POST['event'] ?? ''));
	$intent = sanitize_key(wp_unslash($_POST['intent'] ?? 'unknown'));
	$extra = [];
	if ($event === 'feedback') {
		$rating = sanitize_key(wp_unslash($_POST['rating'] ?? ''));
		if (!in_array($rating, ['helpful', 'not_helpful'], true)) wp_send_json_error(['message' => 'رأی نامعتبر است.'], 400);
		$extra['rating'] = $rating;
	}
	bhc_chat_log($event, $intent, $extra);
	wp_send_json_success(['stored' => true]);
}
add_action('wp_ajax_bhc_chat_event', 'bhc_chat_event');
add_action('wp_ajax_nopriv_bhc_chat_event', 'bhc_chat_event');

function bhc_chat_add_to_cart() {
	check_ajax_referer('bhc_nonce', 'nonce');
	$id = absint($_POST['product_id'] ?? 0);
	$variation_id = absint($_POST['variation_id'] ?? 0);
	$product = function_exists('wc_get_product') ? wc_get_product($id) : false;
	if (!$product) wp_send_json_error(['message' => 'این کالا الان قابل افزودن به سبد نیست.'], 400);
	if (!function_exists('WC') || !WC()->cart) wp_send_json_error(['message' => 'سبد خرید در دسترس نیست.'], 500);
	$added = false;
	if ($product->is_type('simple')) {
		if (!$product->is_purchasable() || !$product->is_in_stock()) wp_send_json_error(['message' => 'این کالا ناموجود است.'], 400);
		$added = WC()->cart->add_to_cart($product->get_id(), 1);
	} elseif ($product->is_type('variable')) {
		$variation = wc_get_product($variation_id);
		if (!$variation || (int) $variation->get_parent_id() !== (int) $product->get_id() || !$variation->is_purchasable() || !$variation->is_in_stock()) {
			wp_send_json_error(['message' => 'ابتدا یک گزینه موجود را انتخاب کنید.'], 400);
		}
		$attrs = [];
		foreach (get_post_meta($variation->get_id()) as $key => $values) {
			if (!str_starts_with((string) $key, 'attribute_')) continue;
			$value = is_array($values) ? (string) reset($values) : (string) $values;
			$slug = sanitize_title(substr((string) $key, 10));
			if ($slug === '' || $value === '') continue;
			$attrs['attribute_' . $slug] = $value;
		}
		$added = WC()->cart->add_to_cart($product->get_id(), 1, $variation->get_id(), $attrs);
	} else {
		wp_send_json_error(['message' => 'این نوع کالا را از صفحه محصول به سبد اضافه کنید.'], 400);
	}
	if (!$added) wp_send_json_error(['message' => 'افزودن به سبد انجام نشد.'], 400);
	bhc_chat_log('add_to_cart', 'purchase');
	wp_send_json_success(['message' => 'به سبد خرید اضافه شد.']);
}
add_action('wp_ajax_bhc_chat_add_to_cart', 'bhc_chat_add_to_cart');
add_action('wp_ajax_nopriv_bhc_chat_add_to_cart', 'bhc_chat_add_to_cart');

function bhc_chat_seed_answers() {
	if (get_option('bhc_chat_faq_pack') === '2' || !class_exists('BHC_Chat_Knowledge_Store')) return;
	$rows = [
		['question' => 'هزینه ارسال چقدر است؟', 'answer' => 'هزینه ارسال بعد از انتخاب شهر در تسویه‌حساب محاسبه می‌شود. قبل از آن مبلغ قطعی اعلام نمی‌شود.', 'intent' => 'shipping'],
		['question' => 'چند روزه می‌رسد؟', 'answer' => 'زمان رسیدن را تسویه‌حساب و روش ارسال انتخاب‌شده مشخص می‌کند. در چت تاریخ رسیدن ساخته نمی‌شود.', 'intent' => 'shipping'],
		['question' => 'گارانتی چند ماه است؟', 'answer' => 'مدت ضمانت در مشخصات همان مدل نوشته شده است. نام مدل را بفرستید تا همان متن نشان داده شود.', 'intent' => 'warranty'],
		['question' => 'چطور مرجوع کنم؟', 'answer' => 'از پیگیری سفارش، شماره سفارش و ایمیل صورتحساب را بفرستید. مرجوعی بدون سفارش بررسی نمی‌شود.', 'intent' => 'return'],
		['question' => 'پرداخت ناموفق بود', 'answer' => 'وضعیت همان سفارش را از پیگیری سفارش ببینید. اگر در انتظار پرداخت باشد، پرداخت را از حساب کاربری ادامه دهید. شماره کارت را نفرستید.', 'intent' => 'payment'],
		['question' => 'فاکتور می‌خواهم', 'answer' => 'فاکتور از حساب کاربری و جزئیات همان سفارش در دسترس است. شماره سفارش و ایمیل صورتحساب را در پیگیری وارد کنید.', 'intent' => 'order'],
		['question' => 'نصب هم دارید؟', 'answer' => 'نصب فقط اگر در مشخصات همان کالا یا خدمات تسویه‌حساب آمده باشد انجام می‌شود. نام مدل را بفرستید.', 'intent' => 'purchase'],
		['question' => 'تخفیف دارید؟', 'answer' => 'قیمت معتبر همان قیمت فعلی کارت کالا است. تخفیف جداگانه‌ای در چت ساخته نمی‌شود.', 'intent' => 'budget'],
	];
	foreach ($rows as $row) {
		BHC_Chat_Knowledge_Store::upsert($row + ['lang' => 'fa', 'enabled' => true, 'source' => 'pack']);
	}
	update_option('bhc_chat_faq_pack', '2', false);
}
add_action('init', 'bhc_chat_seed_answers', 30);
