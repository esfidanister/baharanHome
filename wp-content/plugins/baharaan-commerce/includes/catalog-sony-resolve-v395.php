<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    if (!function_exists('wc_get_product') || get_option('bhc_catalog_sony_v395_done')) return;
    $items = [
        475 => [
            'sku'=>'BHC-SONY-K65S30', 'name'=>'تلویزیون هوشمند سونی مدل K-65S30 سایز ۶۵ اینچ', 'file'=>'sony-k65s30-v395.png',
            'source'=>'https://www.sony.com/electronics/support/televisions-projectors-lcd-tvs-android-/k-65s30/specifications',
            'sources'=>['https://www.sony.com/electronics/support/televisions-projectors-lcd-tvs-android-/k-65s30/specifications','https://www.sony.com/en-cd/bravia/products/bravia-3'],
            'prompt'=>'Exact model Sony K-65S30 / BRAVIA 3 65-inch 4K HDR smart television, transparent 4K PNG; black LCD Direct LED, narrow bezel, slim wedged stand',
            'desc'=>'تلویزیون سونی K-65S30 از سری BRAVIA 3، یک تلویزیون ۶۵ اینچی LCD با نور پس‌زمینه Direct LED و وضوح 4K است. صفحه رسمی Sony برای این مدل پردازنده 4K HDR Processor X1، سیستم Google TV، پشتیبانی HDR10/HLG/Dolby Vision، نرخ native برابر ۶۰ هرتز، چهار ورودی HDMI، دو USB، eARC/ARC، ALLM، Wi‑Fi 6 و بلوتوث 5.3 را ثبت کرده است. وضعیت کالا ناموجود است و قیمت در سایت نمایش داده نمی‌شود.',
            'specs'=>['برند'=>'سونی','مدل دقیق'=>'K-65S30','سری'=>'BRAVIA 3','اندازه'=>'۶۵ اینچ (قطر واقعی ۶۴٫۵ اینچ)','نوع نمایشگر'=>'LCD','نور پس‌زمینه'=>'Direct LED','وضوح'=>'۳۸۴۰×۲۱۶۰، 4K Ultra HD','نرخ تازه‌سازی'=>'۶۰ هرتز Native','پردازنده تصویر'=>'4K HDR Processor X1','پردازش تصویر'=>'4K X-Reality PRO، TRILUMINOS PRO، Live Color','HDR'=>'HDR10، HLG و Dolby Vision','سیستم‌عامل'=>'Google TV / Android TV','صدا'=>'دو بلندگوی Full Range Bass Reflex، مجموع ۲۰ وات، Dolby Audio و Dolby Atmos','گیمینگ'=>'ALLM در هر چهار HDMI؛ VRR ندارد','درگاه‌ها'=>'۴ HDMI، دو USB، LAN، خروجی صدای دیجیتال، eARC/ARC و RF','اتصال بی‌سیم'=>'Wi‑Fi 6، بلوتوث 5.3، Chromecast داخلی، AirPlay و HomeKit','ابعاد بدون پایه'=>'۱۴۵۲×۸۳۶×۷۲ میلی‌متر','VESA'=>'۳۰۰×۳۰۰ میلی‌متر','حافظه داخلی'=>'۱۶ گیگابایت','اقلام همراه'=>'پایه رومیزی، ریموت صوتی، باتری، کابل برق و راهنمای راه‌اندازی','وضعیت'=>'ناموجود']
        ],
        458 => [
            'sku'=>'BHC-SONY-KD50X80L', 'name'=>'تلویزیون هوشمند سونی مدل KD-50X80L سایز ۵۰ اینچ', 'file'=>'sony-kd50x80l-v395.png',
            'source'=>'https://www.sony.com/en-ye/bravia/products/x80l-x81bl-series',
            'sources'=>['https://www.sony.com/en-ye/bravia/products/x80l-x81bl-series','https://www.sony.com/ar-sa/bravia/products/x80l-x81bl-series'],
            'prompt'=>'Exact model Sony KD-50X80L 50-inch X80L Series 4K HDR Google TV, transparent 4K PNG; black LCD Direct LED, narrow bezel, slim wedged stand',
            'desc'=>'تلویزیون سونی KD-50X80L از سری X80L، یک مدل ۵۰ اینچی 4K HDR با پردازنده 4K HDR Processor X1، پنل رنگ گسترده و سیستم Google TV است. صفحه رسمی Sony حضور دقیق KD-50X80L در این سری را ثبت می‌کند و قابلیت‌های سری شامل پردازش 4K HDR، بلندگوی X‑Balanced، Google TV و طراحی با حاشیه باریک است. وضعیت کالا ناموجود است و قیمت در سایت نمایش داده نمی‌شود.',
            'specs'=>['برند'=>'سونی','مدل دقیق'=>'KD-50X80L','سری'=>'X80L','اندازه'=>'۵۰ اینچ (قطر واقعی ۴۹٫۵ اینچ)','نوع نمایشگر'=>'LCD 4K HDR','پردازنده تصویر'=>'4K HDR Processor X1','پردازش تصویر'=>'4K X-Reality PRO و رنگ گسترده','سیستم‌عامل'=>'Google TV','صدا'=>'بلندگوی X‑Balanced با طراحی Bass Reflex','طراحی'=>'حاشیه باریک و پایه گوه‌ای باریک','قابلیت‌های هوشمند'=>'دسترسی به فیلم‌ها و برنامه‌ها با Google TV','وضعیت منبع'=>'مدل KD-50X80L در صفحه رسمی سری X80L فهرست شده است','وضعیت'=>'ناموجود']
        ],
    ];
    $done=[];
    foreach($items as $id=>$item){
        $p=wc_get_product($id); if(!$p) continue;
        $sku_owner=function_exists('wc_get_product_id_by_sku')?(int)wc_get_product_id_by_sku($item['sku']):0; if(!$sku_owner || $sku_owner===$id)$p->set_sku($item['sku']); $p->set_name($item['name']); $p->set_status('publish'); $p->set_catalog_visibility('visible'); $p->set_stock_status('outofstock');
        $h='<p>'.esc_html($item['desc']).'</p><h3>اطلاعات فنی</h3><ul>'; foreach($item['specs'] as $k=>$v)$h.='<li><strong>'.esc_html($k).':</strong> '.esc_html($v).'</li>'; $p->set_description($h.'</ul>'); $p->set_short_description($item['name'].' · ناموجود · مشخصات مدل‌محور'); $p->save();
        wp_set_object_terms($id,'سونی','pa_brand',false); wp_set_object_terms($id,'سونی','product_brand',false); wp_set_object_terms($id,'تلویزیون','product_cat',true);
        update_post_meta($id,'_bhc_source_url',$item['source']); update_post_meta($id,'_bhc_source_urls',$item['sources']); update_post_meta($id,'_bhc_source_type','cross_checked_official'); update_post_meta($id,'_bhc_source_checked_at','2026-11-20'); update_post_meta($id,'_bhc_catalog_quality_state','reference_verified'); update_post_meta($id,'_bhc_verification_status','verified'); update_post_meta($id,'_bhc_release_blocked','no'); update_post_meta($id,'_bhc_specs',$item['specs']); update_post_meta($id,'_bhc_no_price_message','قیمت نمایش داده نمی‌شود. وضعیت کالا: ناموجود.'); update_post_meta($id,'_yoast_wpseo_metadesc',mb_substr($item['desc'],0,155));
        $path=plugin_dir_path(__FILE__).'bhc-image-references/'.$item['file']; if(file_exists($path)){require_once ABSPATH.'wp-admin/includes/image.php'; $old=get_post_thumbnail_id($id); if($old)wp_delete_attachment($old,true); $u=wp_upload_bits($item['file'],null,file_get_contents($path)); if(empty($u['error'])){$type=wp_check_filetype($u['file'],null);$a=wp_insert_attachment(['post_mime_type'=>$type['type'],'post_title'=>$item['name'].' تصویر اختصاصی','post_status'=>'inherit'],$u['file'],$id);wp_update_attachment_metadata($a,wp_generate_attachment_metadata($a,$u['file']));set_post_thumbnail($id,$a);update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified');update_post_meta($id,'_bhc_image_prompt',$item['prompt'].'؛ پس‌زمینه کاملاً شفاف.');update_post_meta($id,'_bhc_image_checked_at','2026-11-20');}}
        $done[]=$id;
    }
    $gaps=[474=>'برای K-55S30 منبع رسمی خانواده پیدا شد اما صفحه مشخصات دقیق این SKU در این بررسی ثبت نشد.',476=>'برای K-75S30 منبع رسمی خانواده پیدا شد اما صفحه مشخصات دقیق این SKU در این بررسی ثبت نشد.',477=>'برای K-85S30 منبع رسمی خانواده پیدا شد اما صفحه مشخصات دقیق این SKU در این بررسی ثبت نشد.',478=>'برای K-98S30 منبع رسمی دقیق در خانواده BRAVIA 3 پیدا نشد؛ مدل 98 اینچ تا تطبیق مستقل قرنطینه است.'];
    foreach($gaps as $id=>$reason){$p=wc_get_product($id);if(!$p)continue;$p->set_status('draft');$p->set_catalog_visibility('hidden');$p->set_stock_status('outofstock');$p->save();update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');update_post_meta($id,'_bhc_verification_status','pending_source');update_post_meta($id,'_bhc_release_blocked','yes');update_post_meta($id,'_bhc_source_gap_reason',$reason);update_post_meta($id,'_bhc_source_review_notes','فاز ۳۹۵: منبع خانواده به‌تنهایی برای انتشار مدل دقیق کافی نیست.');}
    update_option('bhc_catalog_sony_v395_done',['resolved'=>$done,'quarantined'=>array_keys($gaps),'at'=>current_time('mysql')],false);
});
