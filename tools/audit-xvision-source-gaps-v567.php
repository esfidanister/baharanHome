<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$n=0;
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$brand=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);if(($brand[0]??'')!=='ایکس‌ویژن'||$state!=='reference_needs_verification')continue;$sku=(string)$p->get_sku();
 $note='کد کامل مدل در منابع فروشگاهی و مقایسه‌ای بررسی شد؛ نتیجه مستقیم همان SKU پیدا نشد و مدل‌های نزدیک یا سایزهای دیگر به این رکورد تعمیم داده نشدند.';
 if(strpos($sku,'XCU-')!==false||strpos($sku,'XEN-')!==false||strpos($sku,'XYU-')!==false||strpos($sku,'XK-')!==false)$note='برای خانواده تلویزیون، منابع مدل‌های نزدیک یا سایزهای دیگر را نشان می‌دهند؛ قالب کد این رکورد با مدل دقیق منبع تطبیق نشده و مشخصات سایز/نسخه دیگر منتقل نمی‌شود.';
 update_post_meta($id,'_bhc_source_audit_status','reviewed_variant_or_no_exact_source');
 update_post_meta($id,'_bhc_source_review_checked_at','2026-11-21');
 update_post_meta($id,'_bhc_source_review_scope','جست‌وجوی کد کامل مدل در منابع ایکس‌ویژن، کاتالوگ‌های بازار و فروشگاه‌های ایرانی؛ مدل‌های مشابه، سایزهای دیگر و خانواده‌های نزدیک جدا نگه داشته شدند.');
 update_post_meta($id,'_bhc_source_review_notes',$note);
 update_post_meta($id,'_bhc_source_gap_reason','منبع مدل دقیق این SKU هنوز قطعی نیست؛ برای تأیید به پلاک دستگاه، دفترچه یا لینک مستقیم همان مدل نیاز است.');$n++;
}
echo "AUDITED_XVISION|$n\n";
