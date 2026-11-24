<?php
require_once '/var/www/html/wp-load.php';
if(get_option('bhc_catalog_status_normalized_v556')){echo "ALREADY_DONE\n";exit;}
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$changed=0;
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$content=(string)get_post_field('post_content',$id);if(mb_stripos($content,'وضعیت کاتالوگ')!==false)continue;
 $state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);$blocked=get_post_meta($id,'_bhc_release_blocked',true)==='yes';
 if($state==='reference_verified')$text='مدل و منبع این رکورد در کنترل کاتالوگ بررسی و تأیید شده است. کالا فعلاً ناموجود است و قیمت نمایش داده نمی‌شود.';
 elseif($state==='reference_invalid')$text='این رکورد به‌دلیل نبود تطبیق معتبر مدل قرنطینه شده است. مشخصات مدل‌های مشابه به آن منتقل نمی‌شود و تا اصلاح منبع قابل انتشار نیست.';
 else $text='این رکورد فعلاً ناموجود و مسدود انتشار است؛ مدل کامل، پسوند منطقه‌ای و منبع مستقیم آن هنوز نیازمند تطبیق است. تا زمان تأیید، مشخصات مدل‌های مشابه به آن نسبت داده نمی‌شود.';
 $content.='<h3>وضعیت کاتالوگ</h3><p>'.esc_html($text).'</p>';
 wp_update_post(['ID'=>$id,'post_content'=>$content]);$changed++;
}
update_option('bhc_catalog_status_normalized_v556',['changed'=>$changed,'at'=>current_time('mysql')],false);echo "UPDATED|$changed|TOTAL|".count($ids)."\n";
