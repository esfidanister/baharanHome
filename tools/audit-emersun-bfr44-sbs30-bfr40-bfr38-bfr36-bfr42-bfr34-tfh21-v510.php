<?php
require '/var/www/html/wp-load.php';
$items=[
 'BHC-EMERSUN-BFR44'=>'برای BFR44 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ مدل‌های نزدیک ۴۶ فوت به این رکورد تعمیم داده نشدند.',
 'BHC-EMERSUN-SBS30'=>'برای SBS30 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ مدل‌های ساید ۳۲ فوت یا دوقلوی ۳۰ فوت به این رکورد تعمیم داده نشدند.',
 'BHC-EMERSUN-BFR40'=>'برای BFR40 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ ظرفیت و ساختار دستگاه قطعی نیست.',
 'BHC-EMERSUN-BFR38'=>'برای BFR38 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ ظرفیت و ساختار دستگاه قطعی نیست.',
 'BHC-EMERSUN-BFR36'=>'برای BFR36 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ ظرفیت و ساختار دستگاه قطعی نیست.',
 'BHC-EMERSUN-BFR42'=>'برای BFR42 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ ظرفیت و ساختار دستگاه قطعی نیست.',
 'BHC-EMERSUN-BFR34'=>'برای BFR34 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ ظرفیت و ساختار دستگاه قطعی نیست.',
 'BHC-EMERSUN-TFH21'=>'برای TFH21 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ مدل‌های TFH17 و TFH18 به این رکورد تعمیم داده نشدند.',
];$checked=0;
foreach($items as $sku=>$note){$id=wc_get_product_id_by_sku($sku);if(!$id){echo "MISSING|$sku\n";continue;}$p=wc_get_product($id);if(!$p){echo "INVALID|$sku|$id\n";continue;}$p->set_status('draft');$p->set_catalog_visibility('hidden');$p->set_stock_status('outofstock');$p->set_description('<p>'.esc_html($p->get_name()).' در کاتالوگ بهاران‌هوم ناموجود و غیرقابل انتشار است.</p><h3>نتیجه ممیزی منبع</h3><p>'.esc_html($note).'</p><h3>اطلاعات فنی</h3><p>ظرفیت، ابعاد، ساختار، کمپرسور، سیستم سرمایش، رده انرژی و امکانات تا زمان یافتن سند دقیق همان مدل ثبت نمی‌شوند.</p><h3>وضعیت انتشار</h3><p>پیش‌نویس، مخفی، ناموجود و release-blocked تا زمان تأیید منبع دقیق.</p>');$p->set_short_description($p->get_name().' · ناموجود · در انتظار منبع دقیق مدل');$p->save();update_post_meta($id,'_bhc_source_url','');update_post_meta($id,'_bhc_source_type','legacy_needs_exact');update_post_meta($id,'_bhc_source_checked_at','2026-11-21');update_post_meta($id,'_bhc_source_review_notes','فاز ۵۱۰: مدل دقیق در منابع وب و فروشگاه‌های بررسی‌شده تأیید نشد.');update_post_meta($id,'_bhc_source_gap_reason',$note);update_post_meta($id,'_bhc_verification_status','pending_source');update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');update_post_meta($id,'_bhc_release_blocked','yes');update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_pending_exact_source');echo "AUDITED|$sku|$id\n";$checked++;}
update_option('bhc_phase_510_emersun_batch_audit',['checked'=>$checked,'at'=>current_time('mysql'),'result'=>'unresolved_exact_sources'],false);echo "CHECKED|$checked\n";
