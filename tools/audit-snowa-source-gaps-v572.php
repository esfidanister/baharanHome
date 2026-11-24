<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$n=0;
foreach($ids as $id){
 $p=wc_get_product($id);if(!$p)continue;$brand=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);
 if(($brand[0]??'')!=='اسنوا'||$state!=='reference_needs_verification')continue;
 $sku=(string)$p->get_sku();$model=(string)get_post_meta($id,'_bhc_model',true);
 $note='برای مدل '.$model.' جست‌وجوی کد کامل در سایت رسمی اسنوا، منابع فروشگاهی ایرانی و صفحات مقایسه‌ای انجام شد، اما منبعی که همین SKU را همراه با نسخه، ظرفیت/سایز و مشخصات فنی قطعی تأیید کند ثبت نشد. اطلاعات خانواده یا سایز دیگر به این رکورد منتقل نمی‌شود.';
 if(strpos($sku,'SAC-')!==false)$note.=' کد ظرفیت کولر گازی به‌تنهایی نوع کمپرسور، سرمایش/گرمایش و رده انرژی نسخه را قطعی نمی‌کند.';
 elseif(strpos($sku,'SGC-')!==false)$note.=' برای اجاق و فر، تعداد شعله، نوع سوخت، ابعاد برش و امکانات فر باید از صفحه همان کد یا پلاک دستگاه تأیید شود.';
 elseif(strpos($sku,'SMO-')!==false)$note.=' برای مایکروویو، ظرفیت، توان، نوع پخت و ابعاد نسخه بدون منبع مستقیم قابل انتقال از خانواده نیست.';
 elseif(strpos($sku,'SDW-')!==false)$note.=' برای ظرفشویی، ظرفیت نفره، تعداد برنامه، مصرف آب و سطح صدا بین سری‌ها متغیر است و نیازمند تطبیق کد کامل است.';
 elseif(strpos($sku,'SRD-')!==false||strpos($sku,'SBD-')!==false||strpos($sku,'SBS-')!==false||strpos($sku,'SB100')!==false)$note.=' برای یخچال، گنجایش، چیدمان، نوع کمپرسور و رنگ نسخه باید با پلاک یا صفحه مستقیم مدل کنترل شود.';
 update_post_meta($id,'_bhc_source_audit_status','reviewed_variant_or_no_exact_source');
 update_post_meta($id,'_bhc_source_review_checked_at','2026-11-21');
 update_post_meta($id,'_bhc_source_review_scope','جست‌وجوی SKU کامل در Snowa.ir، فروشگاه‌های ایرانی و منابع مقایسه‌ای؛ سایز، ظرفیت، رنگ، پسوند و نسخه بازار جداگانه کنترل شد و منابع خانواده‌ای به‌عنوان منبع دقیق پذیرفته نشدند.');
 update_post_meta($id,'_bhc_source_review_notes',$note);
 update_post_meta($id,'_bhc_source_gap_reason','منبع مدل دقیق و قابل ردیابی برای این SKU هنوز قطعی نیست؛ برای تأیید نهایی به کد کامل روی پلاک/کارت گارانتی یا صفحه مستقیم همان نسخه نیاز است.');$n++;
}
echo "AUDITED_SNOWA|$n\n";
