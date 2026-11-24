<?php
require '/var/www/html/wp-load.php';
$items = [
 'BHC-EMERSUN-EF8' => 'برای EF8 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ نوع دستگاه و مشخصات فنی قطعی نیست.',
 'BHC-EMERSUN-EF9' => 'برای EF9 منبع رسمی یا صفحه فروش معتبرِ دقیق پیدا نشد؛ نوع دستگاه و مشخصات فنی قطعی نیست.',
 'BHC-EMERSUN-UN-55' => 'برای تلویزیون UN-55 صفحه رسمی یا سند فنی دقیق امرسان پیدا نشد؛ اندازه ۵۵ اینچ از نام رکورد برداشت شده و تا منبع تأیید نشده قطعی اعلام نمی‌شود.',
 'BHC-EMERSUN-UN-65' => 'برای تلویزیون UN-65 صفحه رسمی یا سند فنی دقیق امرسان پیدا نشد؛ اندازه ۶۵ اینچ از نام رکورد برداشت شده و تا منبع تأیید نشده قطعی اعلام نمی‌شود.',
 'BHC-EMERSUN-UN-75' => 'برای تلویزیون UN-75 صفحه رسمی یا سند فنی دقیق امرسان پیدا نشد؛ اندازه ۷۵ اینچ از نام رکورد برداشت شده و تا منبع تأیید نشده قطعی اعلام نمی‌شود.',
];
$checked=0;
foreach($items as $sku=>$note){
 $id=wc_get_product_id_by_sku($sku); if(!$id){echo "MISSING|$sku\n";continue;} $p=wc_get_product($id); if(!$p){echo "INVALID|$sku|$id\n";continue;}
 $p->set_status('draft'); $p->set_catalog_visibility('hidden'); $p->set_stock_status('outofstock');
 $p->set_description('<p>'.esc_html($p->get_name()).' در کاتالوگ بهاران‌هوم ناموجود و غیرقابل انتشار است.</p><h3>درباره محصول</h3><p>این رکورد از داده‌های قدیمی کاتالوگ نگهداری شده است، اما مدل دقیق با منبع سازنده یا سند فنی معتبر تطبیق نشده است. از تکمیل مشخصات بر اساس شباهت نام، اندازه یا تصاویر عمومی خودداری شده است.</p><h3>اطلاعات فنی</h3><p>ظرفیت، ابعاد، رزولوشن، سیستم‌عامل، توان، امکانات، اقلام همراه و سایر ویژگی‌ها فقط پس از یافتن سند دقیق همان مدل ثبت خواهند شد.</p><h3>نتیجه ممیزی منبع</h3><p>'.esc_html($note).'</p><h3>وضعیت انتشار</h3><p>ناموجود، پیش‌نویس، مخفی و release-blocked تا زمان ثبت منبع دقیق.</p>');
 $p->set_short_description($p->get_name().' · ناموجود · در انتظار منبع دقیق مدل'); $p->save();
 update_post_meta($id,'_bhc_source_url',''); update_post_meta($id,'_bhc_source_type','legacy_needs_exact'); update_post_meta($id,'_bhc_source_checked_at','2026-11-21'); update_post_meta($id,'_bhc_source_review_notes','فاز ۵۰۳: مدل دقیق و نوع محصول در نتایج وب و فروشگاه‌های بررسی‌شده با منبع معتبر تطبیق نشد.'); update_post_meta($id,'_bhc_source_gap_reason',$note); update_post_meta($id,'_bhc_verification_status','pending_source'); update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification'); update_post_meta($id,'_bhc_release_blocked','yes'); update_post_meta($id,'_bhc_image_generation_status','generated_4k_transparent_pending_exact_source');
 echo "AUDITED|$sku|$id|source_unresolved\n"; $checked++;
}
update_option('bhc_phase_503_emersun_exact_source_audit',['checked'=>$checked,'at'=>current_time('mysql'),'result'=>'unresolved_exact_sources'],false); echo "CHECKED|$checked\n";
