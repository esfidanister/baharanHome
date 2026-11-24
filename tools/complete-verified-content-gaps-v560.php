<?php
require_once '/var/www/html/wp-load.php';
if(get_option('bhc_verified_content_gaps_v560')){echo "ALREADY_DONE\n";exit;}
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$changed=0;
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$content=(string)get_post_field('post_content',$id);$state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);$spec=get_post_meta($id,'_bhc_specs',true);$source=(string)get_post_meta($id,'_bhc_source_url',true);$missing=mb_stripos($content,'درباره محصول')===false||mb_stripos($content,'اطلاعات فنی')===false||mb_stripos($content,'وضعیت کاتالوگ')===false;if(!$missing||$state!=='reference_verified')continue;
 $name=$p->get_name();$about='<h3>درباره محصول</h3><p>'.esc_html($name).' بر اساس رکورد مدل‌محور و منبع ثبت‌شده در کاتالوگ بهاران‌هوم نگهداری می‌شود. قیمت نمایش داده نمی‌شود و وضعیت کالا ناموجود است.</p>';
 $technical='<h3>اطلاعات فنی</h3>';
 if(is_array($spec)&&$spec){$technical.='<ul>';foreach($spec as $k=>$v){if(is_array($v))$v=implode('، ',$v);$technical.='<li><strong>'.esc_html((string)$k).':</strong> '.esc_html((string)$v).'</li>';} $technical.='</ul>';}else $technical.='<p>مشخصات فنی ساختاریافته برای این رکورد هنوز ثبت نشده است.</p>';
 $status='<h3>وضعیت کاتالوگ</h3><p>مدل و منبع این رکورد در کنترل کاتالوگ بررسی و تأیید شده است. کالا فعلاً ناموجود است و قیمت نمایش داده نمی‌شود.</p>';
 if(mb_stripos($content,'درباره محصول')===false)$content=$about.$content;if(mb_stripos($content,'اطلاعات فنی')===false)$content.=$technical;if(mb_stripos($content,'وضعیت کاتالوگ')===false)$content.=$status;if($source&&mb_stripos($content,'منبع بررسی‌شده')===false)$content.='<p>منبع بررسی‌شده: <a href="'.esc_url($source).'" target="_blank" rel="nofollow noopener">صفحه مرجع مدل</a></p>';
 wp_update_post(['ID'=>$id,'post_content'=>$content]);$changed++;
}
update_option('bhc_verified_content_gaps_v560',['changed'=>$changed,'at'=>current_time('mysql')],false);echo "UPDATED|$changed|TOTAL|".count($ids)."\n";
