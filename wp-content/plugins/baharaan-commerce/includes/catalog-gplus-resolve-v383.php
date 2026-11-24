<?php
if (!defined('ABSPATH')) { exit; }

add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_gplus_resolve_v383_done')) return;
    $items = [
        257 => [
            'sku'=>'BHC-GPLUS-GWM-J8250','name'=>'ماشین لباسشویی جی‌پلاس مدل GWM-J8250 ظرفیت ۸ کیلوگرم','file'=>'gplus-gwm-j8250-v383.png',
            'prompt'=>'GPlus GWM-J8250 exact model-specific 8 kg front-load washing machine, silver body, dark circular door, isolated transparent 4K PNG product cutout',
            'source'=>'https://eleganc.ir/product/%D9%85%D8%A7%D8%B4%DB%8C%D9%86-%D9%84%D8%A8%D8%A7%D8%B3%D8%B4%D9%88%DB%8C%DB%8C-%D8%AC%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-8-%DA%A9%DB%8C%D9%84%D9%88%DB%8C%DB%8C-%D9%85%D8%AF%D9%84-gwm-j8250/',
            'sources'=>['https://eleganc.ir/product/%D9%85%D8%A7%D8%B4%DB%8C%D9%86-%D9%84%D8%A8%D8%A7%D8%B3%D8%B4%D9%88%DB%8C%DB%8C-%D8%AC%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-8-%DA%A9%DB%8C%D9%84%D9%88%DB%8C%DB%8C-%D9%85%D8%AF%D9%84-gwm-j8250/','https://110center.ir/%D9%85%D8%AD%D8%B5%D9%88%D9%84%D8%A7%D8%AA/233/%D9%85%D8%A7%D8%B4%DB%8C%D9%86-%D9%84%D8%A8%D8%A7%D8%B3%D8%B4%D9%88%DB%8C%DB%8C-8-%DA%A9%DB%8C%D9%84%D9%88%DB%8C%DB%8C-%D8%AC%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-%D9%85%D8%AF%D9%84-J8250S'],
            'desc'=>'ماشین لباسشویی جی‌پلاس مدل GWM-J8250 با ظرفیت ۸ کیلوگرم، درب از جلو و موتور اینورتر BLDC طراحی شده است. این مدل سرعت خشک‌کن ۱۲۰۰ دور در دقیقه، ۱۵ برنامه اصلی و ۷ برنامه مکمل، قابلیت اضافه‌کردن لباس، شست‌وشوی سریع، برنامه لباس کودک، سنجش وزن و رتبه انرژی +++A دارد. وضعیت کالا در کاتالوگ به‌صورت ناموجود ثبت شده است.',
            'specs'=>['مدل دقیق'=>'GWM-J8250','نوع مخزن'=>'درب از جلو','ظرفیت دیگ'=>'۸ کیلوگرم','حداکثر سرعت خشک‌کن'=>'۱۲۰۰ دور در دقیقه','نوع موتور'=>'اینورتر BLDC','تعداد برنامه'=>'۱۵ برنامه اصلی + ۷ برنامه مکمل','مصرف انرژی'=>'+۳A','ابعاد تقریبی'=>'۶۰۰×۵۹۵×۸۵۰ میلی‌متر','ورودی آب'=>'آب سرد و گرم','افزودن لباس حین شست‌وشو'=>'دارد','برنامه لباس کودک'=>'دارد','شست‌وشوی سریع'=>'دارد','سنجش وزن و تنظیم مصرف آب'=>'دارد','رنگ ثبت‌شده'=>'سیلور','وضعیت'=>'ناموجود'],
        ],
        263 => [
            'sku'=>'BHC-GPLUS-GDW-M1463','name'=>'ماشین ظرفشویی جی‌پلاس مدل GDW-M1463 ظرفیت ۱۴ نفره','file'=>'gplus-gdw-m1463-v383.png',
            'prompt'=>'GPlus GDW-M1463 exact model-specific freestanding 14-place dishwasher, white full-size body with black front LED control strip, isolated transparent 4K PNG product cutout',
            'source'=>'https://radek.ir/product/g-plus-14-person-dishwasher-model-gdw-m1463w-ind/',
            'sources'=>['https://radek.ir/product/g-plus-14-person-dishwasher-model-gdw-m1463w-ind/','https://khaaneyelux.ir/%D9%85%D8%A7%D8%B4%DB%8C%D9%86-%D8%B8%D8%B1%D9%81%D8%B4%D9%88%DB%8C%DB%8C-%D8%AC%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-%D9%85%D8%AF%D9%84-gdw-m1463w/','https://moqayese.com/home-kitchen/gplus-gdw-m1463-vs-snowa-sdw-f353212'],
            'desc'=>'ماشین ظرفشویی جی‌پلاس مدل GDW-M1463 یک دستگاه ایستاده با ظرفیت ۱۴ نفره است. منابع بررسی‌شده برای خانواده‌ی مدل M1463 وجود ۸ برنامه شست‌وشو، نمایشگر LED، رتبه انرژی ++A، صدای ۴۷ دسی‌بل، مصرف آب حدود ۱۱ تا ۱۳ لیتر، قابلیت تنظیم ارتفاع طبقه دوم، شست‌وشوی سریع، شست‌وشوی پرقدرت، خودتمیزشونده، شروع با تأخیر، سنسور نشتی و بازشدن خودکار در را ثبت کرده‌اند. رنگ در این رکورد عمداً تعیین نشده است تا نسخه W یا S با یکدیگر اشتباه نشود. وضعیت کالا ناموجود است.',
            'specs'=>['مدل دقیق'=>'GDW-M1463','نوع دستگاه'=>'ایستاده / مبله','ظرفیت'=>'۱۴ نفره','تعداد برنامه'=>'۸ برنامه','صفحه‌نمایش'=>'LED','رده انرژی'=>'++A','میزان صدا'=>'۴۷ دسی‌بل','مصرف آب'=>'حدود ۱۱ تا ۱۳ لیتر در هر چرخه','تعداد سبد'=>'۳ سبد','تنظیم ارتفاع طبقه دوم'=>'دارد','شست‌وشوی سریع'=>'دارد','شست‌وشوی پرقدرت'=>'دارد','خودتمیزشونده'=>'دارد','شروع با تأخیر'=>'دارد','سنسور نشتی آب'=>'دارد','بازشدن خودکار در'=>'دارد','ابعاد تقریبی'=>'۶۰×۶۰×۸۴٫۵ سانتی‌متر','رنگ'=>'در رکورد پایه تعیین نشده','وضعیت'=>'ناموجود'],
        ],
    ];
    $resolved=[];
    foreach($items as $id=>$item){
        $p=wc_get_product($id); if(!$p) continue;
        $p->set_sku($item['sku']);$p->set_name($item['name']);$p->set_status('publish');$p->set_catalog_visibility('visible');$p->set_stock_status('outofstock');
        $h='<p>'.esc_html($item['desc']).'</p><h3>اطلاعات فنی</h3><ul>';foreach($item['specs'] as $k=>$v)$h.='<li><strong>'.esc_html($k).':</strong> '.esc_html($v).'</li>';$p->set_description($h.'</ul>');$p->set_short_description($item['name'].' · ناموجود · مشخصات مدل‌محور');$p->save();
        wp_set_object_terms($id,'جی‌پلاس','pa_brand',false);wp_set_object_terms($id,'جی‌پلاس','product_brand',false);wp_set_object_terms($id,$id===263?'ظرفشویی':'لباسشویی','product_cat',true);
        update_post_meta($id,'_bhc_source_url',$item['source']);update_post_meta($id,'_bhc_source_urls',$item['sources']);update_post_meta($id,'_bhc_source_type','cross_checked_retailer_and_comparison');update_post_meta($id,'_bhc_source_checked_at','2026-11-20');update_post_meta($id,'_bhc_catalog_quality_state','reference_verified');update_post_meta($id,'_bhc_verification_status','verified');update_post_meta($id,'_bhc_specs',$item['specs']);update_post_meta($id,'_bhc_no_price_message','قیمت نمایش داده نمی‌شود. وضعیت کالا: ناموجود.');update_post_meta($id,'_yoast_wpseo_metadesc',mb_substr($item['desc'],0,155));
        $path=plugin_dir_path(__FILE__).'bhc-image-references/'.$item['file'];if(file_exists($path)){require_once ABSPATH.'wp-admin/includes/image.php';$old=get_post_thumbnail_id($id);if($old)wp_delete_attachment($old,true);$u=wp_upload_bits($item['file'],null,file_get_contents($path));if(empty($u['error'])){$type=wp_check_filetype($u['file'],null);$a=wp_insert_attachment(['post_mime_type'=>$type['type'],'post_title'=>$item['name'].' تصویر اختصاصی','post_status'=>'inherit'],$u['file'],$id);wp_update_attachment_metadata($a,wp_generate_attachment_metadata($a,$u['file']));set_post_thumbnail($id,$a);update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified');update_post_meta($id,'_bhc_image_prompt',$item['prompt'].'؛ پس‌زمینه کاملاً شفاف.');update_post_meta($id,'_bhc_image_checked_at','2026-11-20');}}
        $resolved[]=$id;
    }
    update_option('bhc_gplus_resolve_v383_done',['resolved'=>$resolved,'at'=>current_time('mysql')],false);
});
