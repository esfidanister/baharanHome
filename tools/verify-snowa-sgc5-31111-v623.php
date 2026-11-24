<?php
require_once '/var/www/html/wp-load.php';
$oldSku='BHC-SNOWA-SGC-31111';
$newSku='BHC-SNOWA-SGC5-31111';
$id=wc_get_product_id_by_sku($oldSku);
if(!$id){echo "MISSING|$oldSku\n";exit;}
$p=wc_get_product($id);
$url='https://radek.ir/product/%D8%A7%D8%AC%D8%A7%D9%82-%DA%AF%D8%A7%D8%B2-5-%D8%B4%D8%B9%D9%84%D9%87-%D8%A7%D8%B3%D9%86%D9%88%D8%A7-%D9%85%D8%AF%D9%84-sgc5-31111/';
$url2='https://bvf.ir/brand/snowa/';
$spec=[
 'برند'=>'اسنوا / Snowa',
 'مدل دقیق'=>'SGC5-31111',
 'سری'=>'آدرینا پرو / Adrina Pro',
 'نوع'=>'اجاق گاز مبله فردار، ۵ شعله',
 'رنگ'=>'استیل',
 'ابعاد'=>'ارتفاع ۸۶۰ × عرض ۹۰۰ × عمق ۶۰۰ میلی‌متر',
 'گنجایش محفظه فر'=>'۹۰ لیتر',
 'جنس بدنه'=>'استیل؛ دیواره‌های محفظه فر با لعاب مشکی',
 'تعداد شعله'=>'۵ شعله',
 'شعله پلوپز'=>'دارد',
 'ترموکوپل'=>'ترموکوپل صفر ثانیه',
 'زمان‌سنج فر'=>'تایمر لمسی',
 'وضعیت'=>'ناموجود'
];
$li='';foreach($spec as $k=>$v)$li.='<li><strong>'.esc_html($k).':</strong> '.esc_html($v).'</li>';
$name='اجاق گاز مبله اسنوا سری آدرینا پرو مدل SGC5-31111';
$content='<p>'.esc_html($name).' با کد کامل مدل از منابع فروشگاهی دارای نام مدل تطبیق داده شد. قیمت نمایش داده نمی‌شود و وضعیت فعلی کالا در بهاران‌هوم ناموجود است.</p>'
 .'<h2>درباره محصول</h2><p>مدل SGC5-31111 از سری آدرینا پرو اسنوا، یک اجاق گاز مبله فردار پنج‌شعله با بدنه استیل و محفظه فر ۹۰ لیتری است. وجود ترموکوپل صفرثانیه، شعله پلوپز و تایمر لمسی، کنترل و استفاده روزمره را ایمن‌تر و ساده‌تر می‌کند.</p>'
 .'<h2>اطلاعات فنی</h2><ul>'.$li.'</ul>'
 .'<h2>راهنمای نصب و بررسی پیش از تأمین</h2><p>پیش از نصب، ابعاد محل، مسیر ورود دستگاه، اتصال گاز، تهویه، نوع دوشاخه و سلامت شیشه و درِ فر بررسی شود. کد کامل SGC5-31111، رنگ استیل و شماره سریال باید روی پلاک و بسته‌بندی با سفارش تطبیق داده شود.</p>'
 .'<h2>پرسش‌های متداول</h2><p><strong>آیا کالا موجود است؟</strong><br>خیر؛ کالا فعلاً ناموجود است.</p><p><strong>آیا مدل دقیق تأیید شده است؟</strong><br>بله؛ کد کامل SGC5-31111 در منبع محصول درج شده است.</p><p><strong>آیا قیمت نمایش داده می‌شود؟</strong><br>خیر.</p>'
 .'<h2>منبع دقیق مدل</h2><p><a href="'.esc_url($url).'" target="_blank" rel="nofollow noopener">صفحه محصول اسنوا SGC5-31111</a> و <a href="'.esc_url($url2).'" target="_blank" rel="nofollow noopener">فهرست محصولات اسنوا</a></p>';
$p->set_name($name);$p->set_sku($newSku);$p->set_status('publish');$p->set_catalog_visibility('visible');$p->set_stock_status('outofstock');$p->set_short_description($name.' · ناموجود · مشخصات مدل دقیق');$p->set_description($content);$p->save();
wp_set_object_terms($id,'اسنوا','pa_brand',false);wp_set_object_terms($id,'اسنوا','product_brand',false);wp_set_object_terms($id,'اجاق و فر','product_cat',false);
foreach([
 '_bhc_model'=>'SGC5-31111','_bhc_brand_latin'=>'Snowa','_bhc_source_url'=>$url,'_bhc_source_urls'=>[$url,$url2],
 '_bhc_source_type'=>'exact_retailer_page_and_brand_catalog','_bhc_source_checked_at'=>'2026-11-21',
 '_bhc_source_audit_status'=>'exact_model_verified','_bhc_source_review_checked_at'=>'2026-11-21',
 '_bhc_source_review_scope'=>'عنوان و کد کامل SGC5-31111، سری آدرینا پرو، نوع اجاق، ابعاد، رنگ، ظرفیت فر، تعداد شعله، ترموکوپل و تایمر از منابع مدل‌محور بررسی شد.',
 '_bhc_source_review_notes'=>'رکورد قبلی SGC-31111 ناقص بود؛ کد صحیح SGC5-31111 است. مدل‌های نزدیک با این رکورد ادغام نشده‌اند.',
 '_bhc_source_gap_reason'=>'','_bhc_source_evidence'=>$url.' | کد کامل مدل SGC5-31111 و مشخصات همان محصول تطبیق شد.',
 '_bhc_specs'=>$spec,'_bhc_structured_specs'=>wp_json_encode($spec,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
 '_bhc_highlights'=>implode("\n",array_slice(array_values($spec),0,10)),
 '_bhc_faq'=>"آیا کالا موجود است؟ | خیر، ناموجود است.\nآیا مدل دقیق است؟ | بله، SGC5-31111 از منبع مدل‌محور تطبیق شد.\nآیا قیمت دارد؟ | خیر.",
 '_bhc_verification_status'=>'verified','_bhc_catalog_quality_state'=>'reference_verified','_bhc_release_state'=>'ready_for_release','_bhc_release_blocked'=>'no',
 '_bhc_no_price_message'=>'قیمت نمایش داده نمی‌شود. وضعیت کالا: ناموجود.',
 '_bhc_image_prompt'=>'Snowa SGC5-31111 Adrina Pro five-burner freestanding gas range with oven, stainless steel body, exact product-only commercial cutout, front three-quarter view, studio lighting, 4K PNG, fully transparent background, no props, no text, no watermark.',
 '_bhc_release_gate_checked_at'=>'2026-11-21'
] as $k=>$v)update_post_meta($id,$k,$v);
$title=mb_substr($name.' | بهاران‌هوم',0,60);$md=mb_substr($name.'؛ مشخصات فنی مدل دقیق، فر ۹۰ لیتری، پنج شعله و ترموکوپل صفرثانیه؛ وضعیت ناموجود.',0,155);
foreach(['_bhc_seo_title'=>$title,'_bhc_seo_description'=>$md,'_yoast_wpseo_title'=>$title,'_yoast_wpseo_metadesc'=>$md] as $k=>$v)update_post_meta($id,$k,$v);
echo "VERIFIED|$id|$oldSku=>$newSku|SGC5-31111\n";
