<?php
require_once '/var/www/html/wp-load.php';
$id=482;$p=wc_get_product($id);if(!$p){echo "MISSING|$id\n";exit;}
$p->set_name('تلویزیون سونی مدل KD-75X75K');
$p->set_short_description('تلویزیون سونی مدل KD-75X75K · ناموجود · در انتظار تأیید منبع رسمی دقیق');
$p->set_description('<p>این رکورد در کاتالوگ بهاران‌هوم نگهداری شده اما تا زمان تأیید منبع رسمی دقیق مدل، قابل انتشار و استناد نیست.</p><h3>درباره محصول</h3><p>هویت دقیق مدل KD-75X75K با منبع رسمی سازنده به‌طور قطعی تطبیق نشده است. اطلاعات مدل‌های نزدیک یا خانواده‌ی X75K به این رکورد منتقل نمی‌شود.</p><h3>اطلاعات فنی</h3><p>مشخصات فنی تا زمان پیدا شدن صفحه یا دفترچه‌ی رسمی دقیق مدل ثبت نمی‌شود.</p><h3>وضعیت کاتالوگ</h3><p>ناموجود و مسدود تا تأیید مدل دقیق. قیمت نمایش داده نمی‌شود.</p>');$p->save();
delete_post_meta($id,'_bhc_source_url');delete_post_meta($id,'_bhc_source_urls');delete_post_meta($id,'_bhc_structured_specs');delete_post_meta($id,'_bhc_specs');delete_post_meta($id,'_bhc_source_evidence');
update_post_meta($id,'_bhc_catalog_quality_state','reference_invalid');update_post_meta($id,'_bhc_verification_status','invalid_reference');update_post_meta($id,'_bhc_release_blocked','yes');update_post_meta($id,'_bhc_source_type','official_family_contradicts_model');update_post_meta($id,'_bhc_source_checked_at','2026-11-21');update_post_meta($id,'_bhc_source_audit_status','invalid_reference');update_post_meta($id,'_bhc_release_block_reason','منبع رسمی خانواده Sony X75K اندازه ۷۵ اینچ را برای این کد تأیید نمی‌کند؛ مدل دقیق یا پسوند منطقه‌ای معتبر لازم است.');update_post_meta($id,'_bhc_image_generation_status','blocked_until_exact_model');update_post_meta($id,'_bhc_image_block_reason','تا تأیید مدل دقیق، تصویر اختصاصی این رکورد قابل استناد نیست.');
echo 'REQUARANTINED|'.$id."\n";
