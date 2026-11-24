<?php
if (!defined('ABSPATH')) exit;

function bhc_openrouter_models() {
	return [
		'nvidia/nemotron-3-ultra-550b-a55b' => 'NVIDIA Nemotron 3 Ultra',
		'baidu/ernie-5.5-flash-preview' => 'Baidu ERNIE 5.5 Flash Preview',
		'nvidia/nemotron-3.5-super-vl' => 'NVIDIA Nemotron 3.5 Super VL',
		'nvidia/nemotron-3.5-lightning' => 'NVIDIA Nemotron 3.5 Lightning',
		'recursive/flint-v1.0' => 'Recursive Flint',
		'nvidia/nemotron-3-nano-omni-30b-a3b-reasoning' => 'NVIDIA Nemotron 3 Nano Omni',
		'google/gemma-4-26b-a4b-it' => 'Google Gemma 4 26B',
		'google/gemma-4-31b-it' => 'Google Gemma 4 31B',
	];
}

function bhc_openrouter_model($slot = 'chat') {
	$models = bhc_openrouter_models();
	$o = Baharaan_Commerce::opts();
	$picked = $slot === 'article' ? (string) ($o['ai_article_model'] ?? '') : (string) ($o['ai_model'] ?? '');
	if (!isset($models[$picked])) $picked = 'google/gemma-4-26b-a4b-it';
	$order = array_values(array_unique(array_merge([$picked], array_keys($models))));
	return $order;
}

function bhc_openrouter_key() {
	$stored = trim((string) (Baharaan_Commerce::opts()['ai_key'] ?? ''));
	if ($stored !== '') return $stored;
	$env = getenv('OPENROUTER_API_KEY');
	return is_string($env) ? trim($env) : '';
}

function bhc_openrouter_ready() {
	$o = Baharaan_Commerce::opts();
	return !empty($o['ai_enabled']) && bhc_openrouter_key() !== '';
}

function bhc_openrouter_complete(array $messages, $max_tokens, $slot = 'chat') {
	$key = bhc_openrouter_key();
	$endpoint = Baharaan_Commerce::api_endpoint(Baharaan_Commerce::opts()['ai_endpoint'] ?? '');
	$last = new WP_Error('bhc_ai_empty', 'پاسخی از OpenRouter نرسید.');
	foreach (bhc_openrouter_model($slot) as $model) {
		$resp = wp_safe_remote_post($endpoint, [
			'timeout' => $slot === 'article' ? 45 : 22,
			'headers' => [
				'Authorization' => 'Bearer ' . $key,
				'Content-Type' => 'application/json',
				'HTTP-Referer' => home_url('/'),
				'X-Title' => get_bloginfo('name'),
			],
			'body' => wp_json_encode([
				'model' => $model,
				'messages' => $messages,
				'temperature' => $slot === 'article' ? 0.55 : 0.2,
				'max_tokens' => (int) $max_tokens,
			]),
		]);
		$code = is_wp_error($resp) ? 0 : (int) wp_remote_retrieve_response_code($resp);
		$body = is_wp_error($resp) ? [] : json_decode(wp_remote_retrieve_body($resp), true);
		$text = $body['choices'][0]['message']['content'] ?? '';
		if ($code >= 200 && $code < 300 && is_string($text) && trim($text) !== '') {
			return ['ok' => true, 'text' => $text, 'model' => $model, 'response' => $resp];
		}
		$last = $resp;
		if ($code === 401 || $code === 403) break;
	}
	return ['ok' => false, 'text' => '', 'model' => '', 'response' => $last];
}

function bhc_openrouter_article($system, $prompt, $max = 1800) {
	if (!bhc_openrouter_ready()) return new WP_Error('bhc_ai_disabled', 'ابتدا OpenRouter را فعال کنید و کلید را در تنظیمات یا متغیر OPENROUTER_API_KEY بگذارید.');
	$uid = get_current_user_id();
	$rk = 'bhc_ai_gen_' . ($uid ?: 'admin');
	$used = (int) get_transient($rk);
	if ($used >= 10) return new WP_Error('bhc_ai_rate', 'سقف ۱۰ درخواست تولید محتوا در یک ساعت پر شده است.');
	set_transient($rk, $used + 1, HOUR_IN_SECONDS);
	$result = bhc_openrouter_complete([
		['role' => 'system', 'content' => $system],
		['role' => 'user', 'content' => $prompt],
	], $max, 'article');
	if (!$result['ok']) return new WP_Error('bhc_ai_http', 'تولید مقاله از مدل‌های انتخاب‌شده برنگشت.');
	return $result['text'];
}

function bhc_openrouter_select($name, $selected) {
	echo '<select class="bhc-model-select" name="' . esc_attr($name) . '">';
	foreach (bhc_openrouter_models() as $id => $label) {
		echo '<option value="' . esc_attr($id) . '" ' . selected($selected, $id, false) . '>' . esc_html($label . ' — ' . $id) . '</option>';
	}
	echo '</select>';
}

function bhc_openrouter_admin_card($o) {
	$key = bhc_openrouter_key();
	$ready = bhc_openrouter_ready();
	echo '<div class="bhc-ai-card">';
	echo '<strong>' . ($ready ? 'پاسخ‌گویی روشن است' : 'پاسخ‌گویی هنوز آماده نیست') . '</strong>';
	echo '<p>کلید در دیتابیس می‌ماند و در خروجی تنظیمات نمی‌آید. چهار رقم آخر: ' . esc_html($key !== '' ? substr($key, -4) : 'ندارد') . '</p>';
	echo '<label>مدل چت ';
	bhc_openrouter_select('bhc_options[ai_model]', $o['ai_model'] ?? '');
	echo '</label><label>مدل مقاله ';
	bhc_openrouter_select('bhc_options[ai_article_model]', $o['ai_article_model'] ?? 'google/gemma-4-31b-it');
	echo '</label>';
	echo '<p>اگر مدل چت یا مقاله ۴۲۹، ۴۰۴ یا ۵۰۲ بدهد، مدل بعدی همین فهرست امتحان می‌شود. خطای کلید همان‌جا می‌ایستد.</p>';
	echo '</div>';
	echo '<style>.bhc-ai-card,.bhc-set-card{display:grid;gap:10px;max-width:760px;padding:16px;border:1px solid #d7e4f1;border-radius:16px;background:linear-gradient(180deg,#f7fbff,#fff)}.bhc-ai-card strong,.bhc-set-card strong{color:#0b2039}.bhc-ai-card label,.bhc-set-card label{display:grid;gap:6px;font-weight:700;color:#24364a}.bhc-set-card input.regular-text,.bhc-set-card input[type=number]{width:100%;max-width:420px;height:40px;border:1px solid #d7e4f1;border-radius:10px;padding:0 12px}.bhc-model-select{width:100%;max-width:100%;height:42px;border:1px solid #d7e4f1;border-radius:10px;background:#fff;font:inherit}.bhc-ai-card p,.bhc-set-card p{margin:0;color:#61758b}</style>';
}
