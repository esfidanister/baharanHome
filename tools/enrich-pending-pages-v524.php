<?php
require '/var/www/html/wp-load.php';

$brandMap = ['امرسان'=>'امرسان','بوش'=>'بوش','ایکس‌ویژن'=>'ایکس‌ویژن','ال‌جی'=>'ال‌جی','ال جی'=>'ال جی','سونی'=>'سونی','سامسونگ'=>'سامسونگ','اسنوا'=>'اسنوا','پاکشوما'=>'پاکشوما','جی‌پلاس'=>'جی‌پلاس','دوو'=>'دوو','سام'=>'سام'];
$count = 0; $seo = 0; $brand = 0; $content = 0;
$q = new WP_Query(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
foreach ($q->posts as $id) {
    if (get_post_meta($id, '_bhc_catalog_quality_state', true) !== 'reference_needs_verification') continue;
    $count++;
    $title = wp_strip_all_tags(get_the_title($id));
    $model = get_post_meta($id, '_bhc_model', true);
    if (!$model) $model = $title;

    if (!get_post_meta($id, '_yoast_wpseo_metadesc', true)) {
        $d = $title . '؛ صفحه بررسی کاتالوگ و مشخصات فنی در حال تکمیل است. این کالا فعلاً ناموجود و تا تأیید منبع دقیق غیرقابل انتشار است.';
        update_post_meta($id, '_yoast_wpseo_metadesc', mb_substr($d, 0, 155));
        update_post_meta($id, '_bhc_seo_pending_enriched_at', '2026-11-21');
        $seo++;
    }

    $terms = wp_get_post_terms($id, 'product_brand');
    if (!$terms || is_wp_error($terms) || !count($terms)) {
        foreach ($brandMap as $needle => $termName) {
            if (mb_strpos($title, $needle) === false) continue;
            $term = get_term_by('name', $termName, 'product_brand');
            if (!$term && $needle !== $termName) $term = get_term_by('name', $needle, 'product_brand');
            if ($term && !is_wp_error($term)) { wp_set_post_terms($id, [(int)$term->term_id], 'product_brand', false); $brand++; }
            break;
        }
    }

    $raw = get_post_meta($id, '_bhc_structured_specs', true);
    $data = json_decode($raw, true);
    $rows = '';
    if (is_array($data)) {
        foreach ($data as $k => $v) {
            if (!is_scalar($v) || trim((string)$v) === '' || mb_strtolower((string)$k) === 'وضعیت') continue;
            $rows .= '<tr><th>' . esc_html($k) . '</th><td>' . esc_html($v) . '</td></tr>';
            if (substr_count($rows, '<tr>') >= 18) break;
        }
    }
    $specBlock = $rows ? '<table><tbody>' . $rows . '</tbody></table>' : '<p>اطلاعات فنی این مدل پس از یافتن منبع مستقیم و قابل استناد تکمیل می‌شود.</p>';
    $html = '<div class="bhc-pending-catalog">'
      . '<h2>درباره محصول</h2><p>' . esc_html($title) . ' در کاتالوگ به‌صورت ناموجود نگهداری می‌شود. مدل کامل، پسوند و منبع دقیق آن هنوز در مرحله تطبیق است؛ بنابراین از نسبت‌دادن مشخصات یک مدل مشابه به این کالا خودداری شده است.</p>'
      . '<h2>اطلاعات فنی</h2>' . $specBlock
      . '<p><strong>وضعیت صحت اطلاعات:</strong> موارد بالا داده‌های ثبت‌شده برای بررسی هستند و تا تأیید منبع مستقیم مدل، نهایی محسوب نمی‌شوند.</p>'
      . '<h2>وضعیت موجودی و انتشار</h2><p>ناموجود؛ این صفحه تا تأیید مدل، برند، دسته‌بندی و مشخصات فنی منتشر نمی‌شود.</p>'
      . '<h2>راهنمای خرید</h2><p>برای جلوگیری از خرید مدل اشتباه، پس از تأیید نهایی، شماره مدل کامل، پسوند منطقه‌ای، رنگ، ظرفیت و اقلام همراه جداگانه اعلام خواهد شد.</p>'
      . '</div>';
    if (get_post_field('post_content', $id) !== $html) { wp_update_post(['ID'=>$id, 'post_content'=>$html]); $content++; }
    update_post_meta($id, '_bhc_pending_page_enriched_at', '2026-11-21');
}
echo "PENDING_V524|products=$count|seo_added=$seo|brands_added=$brand|pages_updated=$content\n";
