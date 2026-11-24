<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$n=0;
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$brand=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);if(($brand[0]??'')!=='امرسان'||$state!=='reference_needs_verification')continue;
 update_post_meta($id,'_bhc_source_audit_status','reviewed_no_exact_source');
 update_post_meta($id,'_bhc_source_review_checked_at','2026-11-21');
 update_post_meta($id,'_bhc_source_review_scope','جست‌وجوی کد کامل مدل در وب‌سایت/فروشگاه نمایندگی امرسان و فروشگاه‌های ایرانی؛ نتایج مدل‌های هم‌خانواده به این SKU تعمیم داده نشد.');
 update_post_meta($id,'_bhc_source_review_notes','برای این کد دقیق، صفحه رسمی یا صفحه فروشگاهی معتبرِ همان مدل پیدا نشد؛ مدل‌های نزدیک یا کدهای متفاوت عمداً به‌عنوان منبع این رکورد ثبت نشدند.');
 update_post_meta($id,'_bhc_source_gap_reason','کد مدل در بررسی منابع موجود، صفحه دقیق قابل استناد ندارد؛ نیازمند پلاک دستگاه، دفترچه یا لینک مدل کامل است.');
 $n++;
}
echo "AUDITED_EMERSUN|$n\n";
