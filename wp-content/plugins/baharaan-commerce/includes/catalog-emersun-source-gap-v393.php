<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_emersun_source_gap_v393_done')) return;
    $items=[525=>'کد EF4 در منابع قابل ردیابی به صفحه محصول دقیق امرسان تطبیق نشد.',527=>'کد EF6 در منابع قابل ردیابی به صفحه محصول دقیق امرسان تطبیق نشد.',528=>'کد EF7 در منابع قابل ردیابی به صفحه محصول دقیق امرسان تطبیق نشد.',523=>'کد UN-65 در منابع عمومی صفحه رسمی یا فروشگاهی دقیق و قابل تطبیق ندارد.'];
    foreach($items as $id=>$reason){$p=wc_get_product($id);if(!$p)continue;$p->set_status('draft');$p->set_catalog_visibility('hidden');$p->set_stock_status('outofstock');$p->save();update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');update_post_meta($id,'_bhc_verification_status','pending_source');update_post_meta($id,'_bhc_release_blocked','yes');update_post_meta($id,'_bhc_source_gap_reason',$reason);update_post_meta($id,'_bhc_source_review_notes','فاز ۳۹۳: تا یافتن صفحه دقیق همان کد، مشخصات و تصویر مدل‌محور منتشر نمی‌شود.');}
    update_option('bhc_emersun_source_gap_v393_done',['quarantined'=>array_keys($items),'at'=>current_time('mysql')],false);
});
