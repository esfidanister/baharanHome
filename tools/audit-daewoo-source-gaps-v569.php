<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$n=0;
foreach($ids as $id){
 $p=wc_get_product($id);if(!$p)continue;
 $brand=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);
 $state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);
 if(($brand[0]??'')!=='دوو'||$state!=='reference_needs_verification')continue;
 $sku=(string)$p->get_sku();$model=(string)get_post_meta($id,'_bhc_model',true);
 $note='برای مدل '.$model.' جست‌وجوی کد کامل در منابع رسمی دوو/دوو‌شاپ و فروشگاه‌های ایرانی انجام شد، اما صفحه‌ای که همین کد کامل را با ظرفیت/رنگ/نسخه بازار تأیید کند پیدا نشد. مدل‌های نزدیک یا خانواده‌ای به‌جای این SKU ثبت نمی‌شوند.';
 if(strpos($sku,'DW-1412')!==false)$note='منبع DDW-M1412S در دوو‌شاپ مربوط به کد کامل دیگری است و با DW-1412 یکسان فرض نشد؛ به همین دلیل به‌عنوان منبع دقیق این محصول قابل استفاده نیست.';
 if(strpos($sku,'DLE-')!==false)$note.=' کدهای سری DLE-M6000 با اندازه صفحه و پسوند نسخه بازار تفکیک می‌شوند و بدون صفحه همان کد کامل، مشخصات خانواده به این رکورد منتقل نمی‌شود.';
 if(strpos($sku,'DMA-')!==false)$note.=' کد ظرفیت کولر گازی به‌تنهایی برای تأیید نوع کمپرسور، کلاس انرژی و نسخه سرمایش/گرمایش کافی نیست.';
 update_post_meta($id,'_bhc_source_audit_status','reviewed_variant_or_no_exact_source');
 update_post_meta($id,'_bhc_source_review_checked_at','2026-11-21');
 update_post_meta($id,'_bhc_source_review_scope','جست‌وجوی SKU کامل در منابع رسمی Daewoo/DaewooShop، صفحات فروشگاهی ایرانی و برگه‌های مشخصات؛ ظرفیت، رنگ، پسوند و نسخه بازار جداگانه کنترل شد و منبع خانواده به‌عنوان مدل دقیق پذیرفته نشد.');
 update_post_meta($id,'_bhc_source_review_notes',$note);
 update_post_meta($id,'_bhc_source_gap_reason','منبع مدل دقیق و قابل ردیابی برای این SKU هنوز قطعی نیست؛ برای تأیید نهایی به کد کامل روی پلاک/کارت گارانتی یا لینک مستقیم همان نسخه نیاز است.');
 $n++;
}
echo "AUDITED_DAEWOO|$n\n";
