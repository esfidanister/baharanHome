<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_catalog_bosch_image_quarantine_v399_done')) return;
    $ids=[455,454,453,452,451,450,448,449,444,442,441,443,446,447]; $changed=[];
    foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$thumb=get_post_thumbnail_id($id);if($thumb){delete_post_thumbnail($id);$changed[]=$id;}update_post_meta($id,'_bhc_image_generation_status','blocked_until_exact_model');update_post_meta($id,'_bhc_image_review_notes','فاز ۳۹۹: تصویر قبلی مدل‌محور و قابل تطبیق نبود؛ برای جلوگیری از نمایش تصویر محصول اشتباه، تصویر از صفحه عمومی حذف شد. تصویر جدید فقط پس از اثبات E-Nr دقیق تولید می‌شود.');update_post_meta($id,'_bhc_release_blocked','yes');}
    update_option('bhc_catalog_bosch_image_quarantine_v399_done',['cleared_thumbnails'=>$changed,'checked'=>$ids,'at'=>current_time('mysql')],false);
});
