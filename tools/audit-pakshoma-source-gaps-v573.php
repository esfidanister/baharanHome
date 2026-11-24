<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$n=0;
foreach($ids as $id){
 $p=wc_get_product($id);if(!$p)continue;$brand=wp_get_post_terms($id,'pa_brand',['fields'=>'names']);$state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);
 if(($brand[0]??'')!=='پاکشوما'||$state!=='reference_needs_verification')continue;
 $sku=(string)$p->get_sku();$model=(string)get_post_meta($id,'_bhc_model',true);
 $note='برای مدل '.$model.' جست‌وجوی کد کامل در سایت رسمی پاکشوما، منابع فروشگاهی ایرانی و صفحات مقایسه‌ای انجام شد، اما منبعی که همین SKU را همراه با نسخه، ظرفیت/رنگ و مشخصات فنی قطعی تأیید کند ثبت نشد. مدل‌های خانواده یا پسوند نزدیک به‌جای این SKU پذیرفته نمی‌شوند.';
 if(strpos($sku,'PWF-')!==false||strpos($sku,'PWT-')!==false||strpos($sku,'PWE-')!==false)$note.=' در لباسشویی، ظرفیت، دور موتور، نوع موتور و رنگ با پسوند مدل تغییر می‌کند و باید از صفحه مستقیم همان SKU کنترل شود.';
 elseif(strpos($sku,'RTP-')!==false||strpos($sku,'RGC-')!==false||strpos($sku,'RSP-')!==false)$note.=' در یخچال، گنجایش، چیدمان، کمپرسور و امکانات نسخه‌ای بدون پلاک یا صفحه مستقیم مدل قابل انتقال نیست.';
 elseif(strpos($sku,'MPK-')!==false||strpos($sku,'MPC-')!==false)$note.=' در جاروبرقی، توان، نوع فیلتر و اقلام همراه بین زیرمدل‌ها متفاوت است و اطلاعات خانواده‌ای کافی نیست.';
 elseif(strpos($sku,'AMB-')!==false||strpos($sku,'CUBE-')!==false||strpos($sku,'BREEZE-')!==false)$note.=' در کولر گازی، ظرفیت به‌تنهایی نوع کمپرسور، کلاس انرژی و سرمایش/گرمایش را اثبات نمی‌کند.';
 update_post_meta($id,'_bhc_source_audit_status','reviewed_variant_or_no_exact_source');update_post_meta($id,'_bhc_source_review_checked_at','2026-11-21');
 update_post_meta($id,'_bhc_source_review_scope','جست‌وجوی SKU کامل در Pakshoma.ir، فروشگاه‌های ایرانی و منابع مقایسه‌ای؛ ظرفیت، رنگ، پسوند، نسخه بازار و دسته محصول جداگانه کنترل شد و منابع خانواده‌ای به‌عنوان منبع دقیق پذیرفته نشد.');
 update_post_meta($id,'_bhc_source_review_notes',$note);update_post_meta($id,'_bhc_source_gap_reason','منبع مدل دقیق و قابل ردیابی برای این SKU هنوز قطعی نیست؛ برای تأیید نهایی به کد کامل روی پلاک/کارت گارانتی یا صفحه مستقیم همان نسخه نیاز است.');$n++;
}
echo "AUDITED_PAKSHOMA|$n\n";
