<?php
require_once '/var/www/html/wp-load.php';

$sku = 'BHC-GPLUS-GTV-50LU722S';
$id = wc_get_product_id_by_sku($sku);
$url1 = 'https://www.radhome.co/ProductDetail/70778/%D8%AA%D9%84%D9%88%DB%8C%D8%B2%DB%8C%D9%88%D9%86-50-%D8%A7%DB%8C%D9%86%DA%86-%D8%AC%DB%8C-%D9%BE%D9%84%D8%A7%D8%B3-%D9%85%D8%AF%D9%84-GTV-50LU722S';
$url2 = 'https://agnesh.blogfa.com/tag/%D9%BE%D9%84%D8%A7%D8%B3%D9%85%D8%A7';
if (!$id) { echo "MISSING|$sku\n"; exit; }

$spec = [
  'برند' => 'GPlus / جی‌پلاس',
  'مدل دقیق' => 'GTV-50LU722S',
  'اندازه صفحه' => '۵۰ اینچ',
  'نوع نمایشگر' => 'LED تخت',
  'رزولوشن' => '۳۸۴۰×۲۱۶۰ پیکسل، 4K UHD',
  'HDR' => 'دارد؛ HDR10 در منبع دوم ثبت شده است',
  'پردازنده' => 'چهارهسته‌ای',
  'سیستم عامل' => 'Android 9',
  'حافظه' => '۱۶ گیگابایت داخلی و ۲ گیگابایت RAM',
  'توان صوتی' => '۲۰ وات، دو بلندگوی ۱۰ وات',
  'اتصالات' => 'Wi‑Fi، Bluetooth، LAN، ورودی کامپوزیت، خروجی هدفون، ۳ HDMI و ۲ USB',
  'گیرنده دیجیتال' => 'DVB-T2 و پشتیبانی از شبکه‌های HD صداوسیما',
  'قابلیت‌ها' => 'Time Shift، ضبط دیجیتال، Air Mouse، Mirroring، مرورگر وب، صرفه‌جویی انرژی و اتصال به دیوار',
  'ابعاد بدون پایه' => 'عرض ۱۱۱۷، ارتفاع ۶۸۲ و عمق ۹۴ میلی‌متر',
  'خروجی صدا' => 'اپتیکال',
  'استانداردهای صوتی' => 'Dolby طبق منبع دوم',
  'رنگ' => 'مشکی و نقره‌ای طبق منبع اول',
  'کشور سازنده' => 'ایران طبق منبع اول',
  'وضعیت' => 'ناموجود'
];
$li = '';
foreach ($spec as $k => $v) $li .= '<li><strong>'.esc_html($k).':</strong> '.esc_html($v).'</li>';
$name = 'تلویزیون هوشمند جی‌پلاس مدل GTV-50LU722S سایز ۵۰ اینچ';
$description = '<p>'.esc_html($name).' بر اساس دو منبع مدل‌محور مستقل بررسی شده است. قیمت نمایش داده نمی‌شود و کالا در بهاران‌هوم ناموجود است.</p>'
  .'<h3>درباره محصول</h3><p>این مدل تلویزیون LED تخت ۵۰ اینچی جی‌پلاس با رزولوشن 4K UHD، سیستم‌عامل Android 9، پردازنده چهارهسته‌ای و امکانات هوشمند برای پخش محتوای خانگی طراحی شده است. مشخصات زیر فقط به مدل GTV-50LU722S نسبت داده شده‌اند.</p>'
  .'<h3>اطلاعات فنی</h3><ul>'.$li.'</ul>'
  .'<h3>راهنمای انتخاب و استفاده</h3><p>پیش از نصب، ابعاد محل، فاصله تماشای مناسب و امکان تهویه پشت دستگاه را بررسی کنید. برای پخش فایل یا ضبط برنامه، سازگاری حافظه خارجی و فرمت فایل را کنترل نمایید.</p>'
  .'<h3>گارانتی و ارسال</h3><p>سایز، مدل کامل، سلامت پنل، پایه‌ها، ریموت و کارت گارانتی هنگام تأمین کنترل می‌شود. ارسال یا نصب فقط پس از تأیید موجودی واقعی انجام خواهد شد.</p>'
  .'<h3>پرسش‌های متداول</h3><p><strong>آیا کالا موجود است؟</strong><br>خیر، فعلاً ناموجود است.</p><p><strong>آیا مشخصات برای همین مدل است؟</strong><br>بله، هر دو منبع نام GTV-50LU722S را صریحاً ثبت کرده‌اند.</p><p><strong>آیا قیمت درج شده است؟</strong><br>خیر.</p>'
  .'<h3>منابع دقیق مدل</h3><p><a href="'.esc_url($url1).'" target="_blank" rel="nofollow noopener">صفحه مدل در رادهوم</a> · <a href="'.esc_url($url2).'" target="_blank" rel="nofollow noopener">صفحه مشخصات مدل در اگنش</a></p>';

$p = wc_get_product($id);
$p->set_name($name); $p->set_status('publish'); $p->set_catalog_visibility('visible'); $p->set_stock_status('outofstock');
$p->set_description($description); $p->set_short_description($name.' · ناموجود · مدل‌محور'); $p->save();
wp_set_object_terms($id, 'تلویزیون', 'product_cat', false);
wp_set_object_terms($id, 'جی‌پلاس', 'pa_brand', false);
$meta = [
  '_bhc_model'=>'GTV-50LU722S', '_bhc_brand_latin'=>'GPlus', '_bhc_source_url'=>$url1,
  '_bhc_source_urls'=>wp_json_encode([$url1,$url2], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
  '_bhc_source_type'=>'cross_checked_exact_model_sources', '_bhc_source_checked_at'=>'2026-11-21',
  '_bhc_source_audit_status'=>'exact_model_verified', '_bhc_source_review_checked_at'=>'2026-11-21',
  '_bhc_source_review_scope'=>'دو منبع مدل‌محور برای GTV-50LU722S بررسی شد؛ اندازه، رزولوشن، نمایشگر، سیستم‌عامل، پردازنده، صدا، درگاه‌ها و قابلیت‌ها تطبیق شد.',
  '_bhc_source_review_notes'=>'مدل دقیق تأیید شد؛ فقط مواردی که یکی از منابع صریحاً ثبت کرده با برچسب منبع وارد شده است.',
  '_bhc_source_gap_reason'=>'', '_bhc_source_evidence'=>$url1.' | '.$url2.' | کد مدل، سایز ۵۰ اینچ، 4K، Android 9، حافظه، درگاه‌ها و توان صوتی تطبیق شد.',
  '_bhc_structured_specs'=>wp_json_encode($spec, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
  '_bhc_highlights'=>implode("\n", array_slice(array_values($spec), 0, 12)),
  '_bhc_faq'=>'آیا کالا موجود است؟ | خیر، ناموجود است.\nآیا مدل دقیق است؟ | بله، دو منبع مدل‌محور ثبت شده است.\nآیا قیمت دارد؟ | خیر.',
  '_bhc_verification_status'=>'verified', '_bhc_catalog_quality_state'=>'reference_verified',
  '_bhc_release_state'=>'ready_for_release', '_bhc_release_blocked'=>'no', '_bhc_release_gate_checked_at'=>'2026-11-21'
];
foreach ($meta as $k=>$v) update_post_meta($id, $k, $v);
$title = mb_substr($name.' | بهاران‌هوم', 0, 60);
$md = mb_substr($name.'؛ تلویزیون 4K با Android 9، HDR10، Wi‑Fi، Bluetooth و صدای ۲۰ وات؛ ناموجود.', 0, 155);
foreach (['_bhc_seo_title'=>$title, '_bhc_seo_description'=>$md, '_yoast_wpseo_title'=>$title, '_yoast_wpseo_metadesc'=>$md] as $k=>$v) update_post_meta($id, $k, $v);
echo "VERIFIED|$sku|$id\n";
