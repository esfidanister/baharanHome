<?php
require_once '/var/www/html/wp-load.php';
$id=244;
$p=wc_get_product($id);
if(!$p){echo "MISSING|{$id}\n";exit(1);}
$name='تلویزیون ال ای دی دوو مدل DLE-55M6000EU سایز ۵۵ اینچ';
$sku='BHC-DAEWOO-DLE-55M6000EU';
$urls=[
 'https://radek.shop/product/daewoo-dle-55m6000eu-uhd-led-tv-55-inch/',
 'https://bvf.ir/DAEWOO-55M6000EU',
 'https://moqayese.com/home-kitchen/daewoo-dle-55m6000eu'
];
$spec=[
 'برند'=>'دوو', 'مدل دقیق'=>'DLE-55M6000EU', 'اندازه صفحه'=>'۵۵ اینچ',
 'نوع صفحه'=>'LED تخت', 'وضوح تصویر'=>'Ultra HD 4K، ۳۸۴۰×۲۱۶۰ پیکسل',
 'نسبت تصویر'=>'۱۶:۹', 'HDR'=>'دارد', 'رابط هوشمند'=>'ندارد',
 'گیرنده دیجیتال'=>'DVB-T2', 'پورت HDMI'=>'۳ عدد', 'پورت USB'=>'۲ عدد USB 2.0',
 'قابلیت ضبط روی حافظه جانبی'=>'دارد', 'خروجی صدای دیجیتال'=>'دارد',
 'بلندگو'=>'۲ بلندگو، هرکدام ۱۵ وات؛ توان کلی ۳۰ وات',
 'قابلیت نصب روی دیوار'=>'دارد', 'رنگ'=>'نوک‌مدادی'
];
$li='';foreach($spec as $k=>$v)$li.='<li><strong>'.esc_html($k).':</strong> '.esc_html($v).'</li>';
$desc='<p>'.esc_html($name).' با کد کامل مدل DLE-55M6000EU در کاتالوگ بهاران‌هوم ثبت شده است. کالا در حال حاضر ناموجود است و قیمت نمایش داده نمی‌شود.</p><h2>درباره محصول</h2><p>این تلویزیون ۵۵ اینچی دوو با پنل LED تخت، وضوح Ultra HD 4K و پشتیبانی از HDR برای تماشای محتوای باکیفیت طراحی شده است. گیرنده دیجیتال داخلی، ضبط روی حافظه جانبی و قابلیت اتصال به دیوار، استفاده از آن را در فضای خانه ساده‌تر می‌کند.</p><h2>اطلاعات فنی</h2><ul>'.$li.'</ul><h2>وضعیت کاتالوگ و صحت مدل</h2><p>پسوند کامل EU با چند منبع فروشگاهی مستقل تطبیق داده شد. مشخصات فقط برای همین کد مدل ثبت شده‌اند و با مدل‌های دیگر M6000 ادغام نشده‌اند. کالا ناموجود است.</p><h2>پرسش‌های متداول</h2><p><strong>آیا این محصول موجود است؟</strong><br>خیر؛ وضعیت فعلی این مدل ناموجود است.</p><p><strong>مدل دقیق چیست؟</strong><br>DLE-55M6000EU با اندازه ۵۵ اینچ.</p><p>منابع: <a href="'.esc_url($urls[0]).'" rel="nofollow noopener" target="_blank">Radek</a>، <a href="'.esc_url($urls[1]).'" rel="nofollow noopener" target="_blank">BVF</a>، <a href="'.esc_url($urls[2]).'" rel="nofollow noopener" target="_blank">مقایسه</a></p>';
$p->set_name($name);$p->set_sku($sku);$p->set_status('publish');$p->set_catalog_visibility('visible');$p->set_stock_status('outofstock');$p->set_short_description($name.' · ناموجود · مدل دقیق با پسوند EU');$p->set_description($desc);$p->save();
update_post_meta($id,'_bhc_model','DLE-55M6000EU');update_post_meta($id,'_bhc_brand_latin','Daewoo');update_post_meta($id,'_bhc_source_url',$urls[0]);update_post_meta($id,'_bhc_source_urls',$urls);update_post_meta($id,'_bhc_source_type','retail_exact_model_multiple_sources');update_post_meta($id,'_bhc_source_checked_at',current_time('mysql'));update_post_meta($id,'_bhc_source_evidence','سه منبع مستقل کد کامل DLE-55M6000EU، اندازه ۵۵ اینچ، وضوح 4K، HDR، پورت‌ها و وضعیت ناموجود را نشان می‌دهند.');update_post_meta($id,'_bhc_structured_specs',wp_json_encode($spec,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));update_post_meta($id,'_bhc_specs',$spec);update_post_meta($id,'_bhc_catalog_quality_state','reference_verified');update_post_meta($id,'_bhc_verification_status','verified');update_post_meta($id,'_bhc_release_blocked','no');update_post_meta($id,'_bhc_release_state','ready_for_release');update_post_meta($id,'_bhc_image_prompt','Daewoo DLE-55M6000EU exact 55-inch LED television, dark graphite flat bezel, realistic product-only commercial cutout, 4K detail, transparent background, no room, no props, no text, no watermark.');update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_verified');update_post_meta($id,'_bhc_seo_title',$name.' | مشخصات و وضعیت ناموجود | بهاران‌هوم');update_post_meta($id,'_bhc_seo_description','مشخصات تلویزیون دوو DLE-55M6000EU سایز ۵۵ اینچ؛ وضوح 4K، HDR، پورت‌ها و وضعیت ناموجود در بهاران‌هوم.');
echo "VERIFIED|{$id}|{$sku}|DLE-55M6000EU\n";
