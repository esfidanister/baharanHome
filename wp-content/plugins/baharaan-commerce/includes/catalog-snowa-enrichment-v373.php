<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
 if (!function_exists('wc_get_product') || get_option('bhc_snowa_enrichment_v373_done')) return;
 $id=wc_get_product_id_by_sku('SN8-3323GW');
 if($id){$specs=['مدل'=>'SN8-3323GW','سری'=>'Gallery','نوع'=>'سایدبای‌ساید','اندازه'=>'متوسط','رنگ'=>'سفید متالیک','درب سوم'=>'دسترسی سریع به مواد غذایی پراستفاده بدون بازکردن در اصلی','یخ‌ساز و آب‌سردکن'=>'دارد','بار خانگی'=>'دارد','محفظه نگهداری گوشت'=>'دمای ثابت ۴ درجه سانتی‌گراد','اقلام همراه'=>'برچسب انرژی، کارت ضمانت، دفترچه راهنما، فیلتر تصفیه و لوله‌های اتصال','گارانتی اعلام‌شده'=>'۱۲۴ ماه ضمانت کمپرسور','وضعیت'=>'ناموجود'];$desc='یخچال سایدبای‌ساید اسنوا مدل SN8-3323GW از سری Gallery، با رنگ سفید متالیک و اندازه متوسط، به درِ سوم برای دسترسی سریع، یخ‌ساز و آب‌سردکن و بار خانگی مجهز است. صفحه رسمی سازنده همچنین محفظه نگهداری گوشت با دمای ثابت ۴ درجه سانتی‌گراد، اقلام همراه نصب و ۱۲۴ ماه ضمانت کمپرسور را ذکر می‌کند. وضعیت کالا ناموجود است.';$h='<p>'.esc_html($desc).'</p><h3>اطلاعات فنی</h3><ul>';foreach($specs as $k=>$v)$h.='<li><strong>'.esc_html($k).':</strong> '.esc_html($v).'</li>';$p=wc_get_product($id);$p->set_description($h.'</ul>');$p->set_short_description('یخچال سایدبای‌ساید اسنوا مدل SN8-3323GW · ناموجود · منبع رسمی سازنده');$p->save();update_post_meta($id,'_bhc_specs',$specs);update_post_meta($id,'_yoast_wpseo_metadesc',mb_substr($desc,0,155));}
 update_option('bhc_snowa_enrichment_v373_done',['at'=>current_time('mysql')],false);
});
