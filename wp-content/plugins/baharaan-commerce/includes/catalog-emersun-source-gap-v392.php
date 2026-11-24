<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_emersun_source_gap_v392_done')) return;
    $items=[532=>'کد EF11 در نتایج موجود با مدل فروشگاهی قابل تطبیق نیست؛ برخی نتایج فقط به خطای پنل فریزر اشاره دارند.',533=>'کد EF12 منبع دقیق محصول، ظرفیت و خانواده مدل در منابع قابل ردیابی پیدا نشد.',531=>'کد EF10 منبع دقیق محصول، ظرفیت و خانواده مدل در منابع قابل ردیابی پیدا نشد.',534=>'کد EF13 در نتایج موجود به کد تعمیراتی/خطای پنل اشاره دارد و صفحه محصول دقیق پیدا نشد.',522=>'کد UN-55 منبع دقیق مدل تلویزیون و مشخصات رسمی قابل ردیابی پیدا نشد.',530=>'کد EF9 منبع دقیق محصول، ظرفیت و خانواده مدل در منابع قابل ردیابی پیدا نشد.',529=>'کد EF8 منبع دقیق محصول، ظرفیت و خانواده مدل در منابع قابل ردیابی پیدا نشد.',524=>'کد UN-75 منبع دقیق مدل تلویزیون و مشخصات رسمی قابل ردیابی پیدا نشد.'];
    foreach($items as $id=>$reason){$p=wc_get_product($id);if(!$p)continue;$p->set_status('draft');$p->set_catalog_visibility('hidden');$p->set_stock_status('outofstock');$p->save();update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');update_post_meta($id,'_bhc_verification_status','pending_source');update_post_meta($id,'_bhc_release_blocked','yes');update_post_meta($id,'_bhc_source_gap_reason',$reason);update_post_meta($id,'_bhc_source_review_notes','فاز ۳۹۲: تا زمان یافتن صفحه رسمی یا صفحه فروشگاهی دقیقِ همان کد، مشخصات و تصویر مدل‌محور قابل انتشار نیست.');}
    update_option('bhc_emersun_source_gap_v392_done',['quarantined'=>array_keys($items),'at'=>current_time('mysql')],false);
});
