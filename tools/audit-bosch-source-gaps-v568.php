<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$n=0;
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$brand=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);if(($brand[0]??'')!=='بوش'||$state!=='reference_needs_verification')continue;$sku=(string)$p->get_sku();
 $note='منابع رسمی Bosch/BSH برای این خانواده نسخه‌های کامل دارای پسوند منطقه‌ای، E-Nr یا رنگ را نشان می‌دهند؛ رکورد فعلی بدون پسوند کامل است و به‌عنوان مدل دقیق تأیید نمی‌شود.';
 if(strpos($sku,'BGS21')!==false)$note='خانواده BGS21 چند زیرمدل رسمی مانند BGS21POW2، BGS21X320، BGS21CAR و BGS21WBAGB دارد؛ suffix، رنگ و فیلتر رکورد فعلی مشخص نیست.';
 elseif(strpos($sku,'WGA142X0')!==false)$note='منابع رسمی نسخه‌های WGA142X0GC، WGA142X0SA و WGA142X0TR را نشان می‌دهند؛ نسخه منطقه‌ای رکورد فعلی مشخص نیست.';
 elseif(strpos($sku,'WGB256A')!==false)$note='منابع رسمی نسخه‌های WGB256A1GB و WGB256A2GB را نشان می‌دهند؛ عدد/پسوند کامل E-Nr رکورد فعلی مشخص نیست.';
 elseif(strpos($sku,'WAW325')!==false)$note='منابع رسمی نسخه‌های WAW325X0، WAW325V0 و WAW325H0 را نشان می‌دهند؛ نسخه منطقه‌ای و پسوند کامل رکورد فعلی مشخص نیست.';
 update_post_meta($id,'_bhc_source_audit_status','reviewed_variant_or_no_exact_source');
 update_post_meta($id,'_bhc_source_review_checked_at','2026-11-21');
 update_post_meta($id,'_bhc_source_review_scope','جست‌وجوی کد کامل مدل در صفحات رسمی Bosch/BSH، برگه‌های فنی و منابع فروشگاهی؛ نسخه‌های منطقه‌ای، E-Nrهای متفاوت و مدل‌های خانواده جدا نگه داشته شدند.');
 update_post_meta($id,'_bhc_source_review_notes',$note);
 update_post_meta($id,'_bhc_source_gap_reason','منبع مدل دقیق/پسوند کامل این SKU هنوز قطعی نیست؛ برای تأیید به E-Nr کامل، پلاک دستگاه یا لینک مستقیم همان نسخه نیاز است.');$n++;
}
echo "AUDITED_BOSCH|$n\n";
