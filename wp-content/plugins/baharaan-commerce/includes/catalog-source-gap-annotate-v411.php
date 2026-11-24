<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (get_option('bhc_catalog_source_gap_annotate_v411_done')) return;
    $q = new WP_Query(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
    $annotated = 0;
    foreach ($q->posts as $id) {
        $state = get_post_meta($id, '_bhc_catalog_quality_state', true);
        $source = get_post_meta($id, '_bhc_source_url', true);
        $image = get_post_thumbnail_id($id);
        $reasons = [];
        if (!$source) $reasons[] = 'منبع دقیق همین مدل ثبت نشده است.';
        if (!$image) $reasons[] = 'تصویر اختصاصی محصول ثبت نشده است.';
        if (!$state || $state !== 'reference_verified') $reasons[] = 'تطبیق نهایی مدل/مشخصات هنوز تأیید نشده است.';
        if (!$reasons) continue;
        if ($state !== 'reference_invalid') {
            update_post_meta($id, '_bhc_release_blocked', 'yes');
            update_post_meta($id, '_bhc_source_gap_reason', implode(' ', $reasons));
            update_post_meta($id, '_bhc_source_review_notes', 'فاز ۴۱۱: این رکورد تا ثبت منبع دقیق، مشخصات مدل‌محور و تصویر اختصاصی بررسی‌شده، آماده انتشار نهایی نیست. مشخصات مدل نزدیک یا خانواده نباید به آن منتقل شود.');
            if (!$image) update_post_meta($id, '_bhc_image_generation_status', 'blocked_until_exact_model');
            $annotated++;
        }
    }
    update_option('bhc_catalog_source_gap_annotate_v411_done', ['annotated'=>$annotated,'at'=>current_time('mysql')], false);
});
