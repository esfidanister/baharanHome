<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_catalog_source_gap_v394_done')) return;
    $items=[496=>'منبع موجود درباره خانواده TFH/TFN است اما مدل صفحه‌شده TFN18D است و برای SKU TFH18 تطبیق دقیق ندارد.',482=>'صفحه رسمی Sony X75K مدل‌های 43، 50، 55 و 65 اینچ را فهرست می‌کند و مدل 75X75K در همان منبع تأیید نشده است.'];
    foreach($items as $id=>$reason){$p=wc_get_product($id);if(!$p)continue;$p->set_status('draft');$p->set_catalog_visibility('hidden');$p->set_stock_status('outofstock');$p->save();update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');update_post_meta($id,'_bhc_verification_status','pending_source');update_post_meta($id,'_bhc_release_blocked','yes');update_post_meta($id,'_bhc_source_gap_reason',$reason);update_post_meta($id,'_bhc_source_review_notes','فاز ۳۹۴: مدل دقیق منبع با کد کاتالوگ یکسان نیست؛ تا تطبیق SKU و پسوند، انتشار و تصویر نهایی ممنوع است.');}
    update_option('bhc_catalog_source_gap_v394_done',['quarantined'=>array_keys($items),'at'=>current_time('mysql')],false);
});
