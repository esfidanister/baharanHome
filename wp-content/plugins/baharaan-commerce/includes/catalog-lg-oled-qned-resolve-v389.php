<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_lg_oled_qned_resolve_v389_done')) return;
    $items = [
        408 => [
            'sku'=>'BHC-LG-OLED77C4','name'=>'تلویزیون هوشمند ال‌جی مدل OLED77C4 سایز ۷۷ اینچ','file'=>'lg-oled77c4-v389.png',
            'prompt'=>'LG OLED77C4 exact model-specific 77-inch OLED evo 4K smart television product cutout, slim black bezel, dark graphite central pedestal stand, transparent 4K PNG',
            'source'=>'https://www.lg.com/ca_en/tv-soundbars/oled/oled77c4pua/',
            'sources'=>['https://www.lg.com/ca_en/tv-soundbars/oled/oled77c4pua/','https://www.lg.com/uk/tvs-soundbars/oled-evo/oled77c44la/','https://www.energystar.gov/productfinder/product/certified-televisions/details/2740387/export/pdf'],
            'desc'=>'تلویزیون ال‌جی OLED77C4 از خانواده OLED evo C4، یک نمایشگر ۷۷ اینچی 4K با پنل OLED، پردازنده α9 AI Processor 4K Gen7 و webOS 24 است. منابع رسمی ال‌جی و Energy Star، وضوح 3840×2160، نرخ ۱۲۰ هرتز، پشتیبانی Dolby Vision و HDR10/HLG، صدای Dolby Atmos، قابلیت‌های گیمینگ VRR، G-Sync، FreeSync و ALLM و درگاه‌های HDMI 2.1 را برای این خانواده تأیید می‌کنند. این رکورد بدون قیمت و با وضعیت ناموجود نگهداری می‌شود.',
            'specs'=>['مدل دقیق'=>'OLED77C4','سری'=>'OLED evo C4','اندازه نمایشگر'=>'۷۷ اینچ','نوع پنل'=>'OLED evo','وضوح تصویر'=>'۳۸۴۰×۲۱۶۰ پیکسل، 4K UHD','نرخ تازه‌سازی'=>'۱۲۰ هرتز؛ حالت بازی تا ۱۴۴ هرتز طبق پیکربندی سازگار','پردازنده'=>'α9 AI Processor 4K Gen7','HDR'=>'Dolby Vision / HDR10 / HLG','سیستم‌عامل'=>'webOS 24','صدا'=>'Dolby Atmos، پیکربندی ۲.۲ کانال، توان ۴۰ وات','گیمینگ'=>'VRR، G-Sync، FreeSync، ALLM و HGiG','درگاه‌ها'=>'۴ HDMI با پشتیبانی HDMI 2.1 و eARC؛ USB','اتصال هوشمند'=>'Wi‑Fi، بلوتوث، AirPlay 2، ThinQ AI، Google Assistant و Alexa','وضعیت'=>'ناموجود']
        ],
        403 => [
            'sku'=>'BHC-LG-55QNED80','name'=>'تلویزیون هوشمند ال‌جی مدل 55QNED80 سایز ۵۵ اینچ','file'=>'lg-55qned80-v389.png',
            'prompt'=>'LG 55QNED80 exact model-specific 55-inch QNED 4K smart television product cutout, slim black bezel, narrow central stand, transparent 4K PNG',
            'source'=>'https://www.lg.com/us/support/product/lg-55QNED80TUC.AUS',
            'sources'=>['https://www.lg.com/us/support/product/lg-55QNED80TUC.AUS','https://www.lg.com/jp/tvs-soundbars/lcd-tvs/55qned80tja/','https://www.pmcav.com/wp-content/uploads/2025/06/LG-2024-Product-Guide-Master-TV.pdf','https://www.lg.com/content/dam/lge/in/migration/catalogue-2023/pdf/357-QNED-Nanocell_Digital-Catalogue-2024.pdf'],
            'desc'=>'تلویزیون ال‌جی 55QNED80 از خانواده QNED80T، نمایشگری ۵۵ اینچی با وضوح 4K و ترکیب فناوری Quantum Dot و NanoCell است. منابع رسمی ال‌جی و راهنمای محصول، پردازنده α5 AI Processor 4K Gen7، webOS 24، HDR10/HLG، امکانات هوشمند ThinQ، AirPlay و حالت‌های ALLM/VRR را برای این خانواده ثبت کرده‌اند. به دلیل تفاوت پسوندهای منطقه‌ای، این رکورد با نام پایه 55QNED80 نگهداری شده و مشخصات متغیر پسوندی با برچسب منبع‌محور نمایش داده می‌شود. وضعیت کالا ناموجود است.',
            'specs'=>['مدل پایه'=>'55QNED80','خانواده مدل'=>'QNED80T؛ پسوند منطقه‌ای در منبع رسمی 55QNED80TUC / 55QNED80TJA','اندازه نمایشگر'=>'۵۵ اینچ','نوع نمایشگر'=>'QNED 4K UHD، Quantum Dot و NanoCell','وضوح تصویر'=>'۳۸۴۰×۲۱۶۰ پیکسل','نرخ تازه‌سازی'=>'۱۲۰ هرتز در پیکربندی QNED80T ثبت‌شده در راهنمای محصول؛ وابسته به منطقه','پردازنده'=>'α5 AI Processor 4K Gen7','HDR'=>'HDR10 / HLG','سیستم‌عامل'=>'webOS 24','صدا'=>'دو کانال، توان نامی ۲۰ وات در راهنمای خانواده','گیمینگ'=>'ALLM، VRR، HGiG و Game Optimizer','درگاه‌ها'=>'HDMI با eARC، USB، LAN و خروجی دیجیتال؛ تعداد دقیق وابسته به پسوند منطقه‌ای','اتصال هوشمند'=>'Wi‑Fi، بلوتوث، ThinQ AI، AirPlay 2، HomeKit و Alexa','گیرنده'=>'گیرنده دیجیتال تلویزیون؛ استاندارد و تعداد تیونر وابسته به منطقه','وضعیت'=>'ناموجود']
        ],
    ];
    foreach ($items as $id=>$item) {
        $p=wc_get_product($id); if(!$p) continue;
        $p->set_sku($item['sku']); $p->set_name($item['name']); $p->set_status('publish'); $p->set_catalog_visibility('visible'); $p->set_stock_status('outofstock');
        $html='<p>'.esc_html($item['desc']).'</p><h3>اطلاعات فنی</h3><ul>'; foreach($item['specs'] as $k=>$v) $html.='<li><strong>'.esc_html($k).':</strong> '.esc_html($v).'</li>'; $p->set_description($html.'</ul>'); $p->set_short_description($item['name'].' · ناموجود · مشخصات مدل‌محور'); $p->save();
        wp_set_object_terms($id,'ال‌جی','pa_brand',false); wp_set_object_terms($id,'ال‌جی','product_brand',false); wp_set_object_terms($id,'تلویزیون','product_cat',true);
        update_post_meta($id,'_bhc_source_url',$item['source']); update_post_meta($id,'_bhc_source_urls',$item['sources']); update_post_meta($id,'_bhc_source_type','cross_checked_official_and_catalog'); update_post_meta($id,'_bhc_source_checked_at','2026-11-20'); update_post_meta($id,'_bhc_catalog_quality_state','reference_verified'); update_post_meta($id,'_bhc_verification_status','verified'); update_post_meta($id,'_bhc_specs',$item['specs']); update_post_meta($id,'_bhc_no_price_message','قیمت نمایش داده نمی‌شود. وضعیت کالا: ناموجود.'); update_post_meta($id,'_yoast_wpseo_metadesc',mb_substr($item['desc'],0,155));
        $path=plugin_dir_path(__FILE__).'bhc-image-references/'.$item['file']; if(file_exists($path)){ require_once ABSPATH.'wp-admin/includes/image.php'; $old=get_post_thumbnail_id($id); if($old) wp_delete_attachment($old,true); $u=wp_upload_bits($item['file'],null,file_get_contents($path)); if(empty($u['error'])){ $type=wp_check_filetype($u['file'],null); $a=wp_insert_attachment(['post_mime_type'=>$type['type'],'post_title'=>$item['name'].' تصویر اختصاصی','post_status'=>'inherit'],$u['file'],$id); wp_update_attachment_metadata($a,wp_generate_attachment_metadata($a,$u['file'])); set_post_thumbnail($id,$a); update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified'); update_post_meta($id,'_bhc_image_prompt',$item['prompt'].'؛ پس‌زمینه کاملاً شفاف.'); update_post_meta($id,'_bhc_image_checked_at','2026-11-20'); }}
    }
    update_option('bhc_lg_oled_qned_resolve_v389_done',['resolved'=>array_keys($items),'at'=>current_time('mysql')],false);
});
