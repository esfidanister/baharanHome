<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_catalog_lg_dishwasher_v402_done')) return;
    $items = [
        387 => [
            'sku'=>'BHC-LG-DFB425FP', 'name'=>'ماشین ظرفشویی ال‌جی مدل DFB425FP ظرفیت ۱۴ نفره', 'file'=>'lg-dfb425fp-v402.png',
            'source'=>'https://www.lg.com/eastafrica/dishwashers/lg-DFB425FP',
            'sources'=>['https://www.lg.com/eastafrica/dishwashers/lg-DFB425FP','https://www.lg.com/sg/support/product-support/cs-DFB425FP.APZQESL/','https://www.lg.com/za/support/product-support/cs-DFB425FP.ASTQESL/'],
            'desc'=>'ماشین ظرفشویی ال‌جی DFB425FP یک مدل ایستاده ۱۴ نفره با پنل کنترل جلویی و رنگ Platinum Silver است. منابع رسمی LG وجود QuadWash، TrueSteam، موتور Inverter Direct Drive، سبد EasyRack Plus/SmartRack+ و اتصال هوشمند ThinQ را برای مدل دقیق DFB425FP تأیید می‌کنند. این کالا در کاتالوگ به‌صورت ناموجود ثبت شده و قیمت نمایش داده نمی‌شود.',
            'specs'=>['برند'=>'ال‌جی','مدل دقیق'=>'DFB425FP','نوع نصب'=>'ایستاده','نوع کنترل'=>'پنل کنترل جلویی','ظرفیت'=>'۱۴ نفره','رنگ'=>'Platinum Silver','فناوری شست‌وشو'=>'QuadWash با بازوهای چندجهته','بخار'=>'TrueSteam','موتور'=>'Inverter Direct Drive','سبد'=>'EasyRack Plus / SmartRack+','سبد سوم'=>'دارد','اتصال هوشمند'=>'ThinQ و Wi‑Fi','عیب‌یابی'=>'Smart Diagnosis','مصرف آب'=>'۹٫۶ لیتر','ابعاد'=>'۵۹۸×۸۴۵×۶۰۰ میلی‌متر','جنس محفظه'=>'فولاد ضدزنگ','وضعیت'=>'ناموجود']
        ],
        388 => [
            'sku'=>'BHC-LG-DFB227HM', 'name'=>'ماشین ظرفشویی ال‌جی مدل DFB227HM ظرفیت ۱۴ نفره', 'file'=>'lg-dfb227hm-v402.png',
            'source'=>'https://www.lg.com/sg/support/product-support/cs-DFB227HM.ABMQESL/',
            'sources'=>['https://www.lg.com/sg/support/product-support/cs-DFB227HM.ABMQESL/','https://www.lg.com/sg/dishwashers/lg-DFB227HM','https://www.lg.com/sg/content/dam/lge/sg/products/dishwashers/DFB227HM/DFB227HM_Brochure.pdf'],
            'desc'=>'ماشین ظرفشویی ال‌جی DFB227HM یک مدل ایستاده ۱۴ نفره مشکی با کنترل بالایی است. مستندات رسمی LG برای مدل دقیق DFB227HM فناوری‌های QuadWash و TrueSteam، موتور Inverter Direct Drive، سبد SmartRack Plus، سبد سوم و اتصال ThinQ را ثبت می‌کنند. این کالا در کاتالوگ به‌صورت ناموجود ثبت شده و قیمت نمایش داده نمی‌شود.',
            'specs'=>['برند'=>'ال‌جی','مدل دقیق'=>'DFB227HM','نوع نصب'=>'ایستاده','نوع کنترل'=>'کنترل بالایی (Top Control)','ظرفیت'=>'۱۴ نفره','رنگ'=>'مشکی','فناوری شست‌وشو'=>'QuadWash با بازوهای چندجهته','بخار'=>'TrueSteam','موتور'=>'Inverter Direct Drive','سبد'=>'SmartRack Plus','سبد سوم'=>'دارد','اتصال هوشمند'=>'ThinQ','عیب‌یابی'=>'Smart Diagnosis','سطح صدا'=>'۴۴ دسی‌بل','ابعاد'=>'۵۹۸×۸۴۵×۶۰۰ میلی‌متر','جنس محفظه'=>'فولاد ضدزنگ','وضعیت'=>'ناموجود']
        ]
    ];
    $done=[];
    foreach ($items as $id=>$item) {
        $p=wc_get_product($id); if(!$p) continue;
        $owner=function_exists('wc_get_product_id_by_sku')?(int)wc_get_product_id_by_sku($item['sku']):0;
        if(!$owner || $owner===$id) $p->set_sku($item['sku']);
        $p->set_name($item['name']); $p->set_status('publish'); $p->set_catalog_visibility('visible'); $p->set_stock_status('outofstock');
        $h='<p>'.esc_html($item['desc']).'</p><h3>اطلاعات فنی</h3><ul>'; foreach($item['specs'] as $k=>$v) $h.='<li><strong>'.esc_html($k).':</strong> '.esc_html($v).'</li>'; $p->set_description($h.'</ul>');
        $p->set_short_description($item['name'].' · ناموجود · مشخصات مدل‌محور'); $p->save();
        wp_set_object_terms($id,'ال‌جی','pa_brand',false); wp_set_object_terms($id,'ال‌جی','product_brand',false); wp_set_object_terms($id,'شستشو','product_cat',true);
        update_post_meta($id,'_bhc_source_url',$item['source']); update_post_meta($id,'_bhc_source_urls',$item['sources']); update_post_meta($id,'_bhc_source_type','cross_checked_official_exact_model'); update_post_meta($id,'_bhc_source_checked_at','2026-11-20'); update_post_meta($id,'_bhc_catalog_quality_state','reference_verified'); update_post_meta($id,'_bhc_verification_status','verified'); update_post_meta($id,'_bhc_release_blocked','no'); update_post_meta($id,'_bhc_specs',$item['specs']); update_post_meta($id,'_bhc_no_price_message','قیمت نمایش داده نمی‌شود. وضعیت کالا: ناموجود.'); update_post_meta($id,'_yoast_wpseo_metadesc',mb_substr($item['desc'],0,155));
        $path=plugin_dir_path(__FILE__).'bhc-image-references/'.$item['file'];
        if(file_exists($path)){ require_once ABSPATH.'wp-admin/includes/image.php'; $old=get_post_thumbnail_id($id); if($old) wp_delete_attachment($old,true); $u=wp_upload_bits($item['file'],null,file_get_contents($path)); if(empty($u['error'])){ $type=wp_check_filetype($u['file'],null); $a=wp_insert_attachment(['post_mime_type'=>$type['type'],'post_title'=>$item['name'].' تصویر اختصاصی','post_status'=>'inherit'],$u['file'],$id); wp_update_attachment_metadata($a,wp_generate_attachment_metadata($a,$u['file'])); set_post_thumbnail($id,$a); update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified'); update_post_meta($id,'_bhc_image_prompt','Exact LG '.$item['sku'].' dishwasher, product-specific photorealistic 4K PNG, transparent background, no text or props.'); update_post_meta($id,'_bhc_image_checked_at','2026-11-20'); }}
        $done[]=$id;
    }
    update_option('bhc_catalog_lg_dishwasher_v402_done',['resolved'=>$done,'at'=>current_time('mysql')],false);
});
