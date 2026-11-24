<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$n=0;
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$brand=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);if(($brand[0]??'')!=='جی‌پلاس'||$state!=='reference_needs_verification')continue;$sku=(string)$p->get_sku();
 $note='کد کامل مدل در منابع فروشگاهی/کاتالوگی بررسی شد؛ نتیجه مدل دقیق یا نسخه‌ای با پسوند متفاوت بود و به این SKU تعمیم داده نشد.';
 if(strpos($sku,'GRF-P5320')!==false)$note='منابع برای خانواده GRF-P5320 نسخه‌های دارای پسوند مانند W، S و BS را نشان می‌دهند؛ پسوند رکورد فعلی مشخص نیست و مشخصات نسخه‌ای به آن تعمیم داده نمی‌شود.';
 elseif(strpos($sku,'GDW-K4623')!==false)$note='منابع برای خانواده GDW-K462 و نسخه‌های رنگی K462W/K462S/K462NS یافت شد؛ کد K4623 رکورد فعلی با نسخه دقیق منبع تطبیق نشده است.';
 elseif(strpos($sku,'GWM-KD106')!==false)$note='کاتالوگ‌های موجود مدل‌های GWM-KD1059 و GWM-KD1069 را نشان می‌دهند؛ کد GWM-KD106 بدون پسوند کامل قابل تأیید نیست.';
 update_post_meta($id,'_bhc_source_audit_status','reviewed_variant_or_no_exact_source');
 update_post_meta($id,'_bhc_source_review_checked_at','2026-11-21');
 update_post_meta($id,'_bhc_source_review_scope','جست‌وجوی کد کامل مدل در منابع سازنده، کاتالوگ‌های جی‌پلاس و فروشگاه‌های ایرانی؛ نسخه‌های نزدیک و پسوندهای متفاوت جدا نگه داشته شدند.');
 update_post_meta($id,'_bhc_source_review_notes',$note);
 update_post_meta($id,'_bhc_source_gap_reason','منبع مدل دقیق یا پسوند کامل این SKU هنوز قطعی نیست؛ برای تأیید به پلاک دستگاه، دفترچه یا لینک مدل کامل نیاز است.');$n++;
}
echo "AUDITED_GPLUS|$n\n";
