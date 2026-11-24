<?php
if (!defined('ABSPATH')) exit;
$studio=bhb_studio();
$banners=$studio['banners']??bhb_fallback_studio()['banners'];
$pool=bhb_products(['limit'=>6]);
$fallback_images=[
 1=>'washing.webp',
 2=>'audio.webp',
 3=>'kitchen.webp',
];
$preset_images=[
 1=>get_template_directory_uri().'/assets/images/generated/bento-washer.webp',
 2=>get_template_directory_uri().'/assets/images/generated/bento-soundbar.webp',
 3=>get_template_directory_uri().'/assets/images/generated/bento-airfryer.webp',
];
$slots=[];
foreach([1,2,3] as $i){
 $banner=wp_parse_args($banners[$i]??[],bhb_fallback_studio()['banners'][$i]);
 if(array_key_exists('enabled',$banner)&&!$banner['enabled']) continue;
 $first=!empty($banner['product_id'])&&function_exists('wc_get_product')?wc_get_product(absint($banner['product_id'])):($pool[$i-1]??null);
 if($first&&$first->get_status()!=='publish') $first=null;
 $second=!empty($banner['product_id_2'])&&function_exists('wc_get_product')?wc_get_product(absint($banner['product_id_2'])):($i===1?($pool[$i]??null):null);
 if($second&&($second->get_status()!=='publish'||($first&&$second->get_id()===$first->get_id()))) $second=null;
 $default_url=$i===1?bhb_category_url('شست‌وشو'):($i===3?bhb_category_url('لوازم آشپزخانه'):bhb_shop_url());
 $url=$banner['url']?:($i===1||$i===3?$default_url:($first?get_permalink($first->get_id()):$default_url));
 $slots[]=['banner'=>$banner,'first'=>$first,'second'=>$second,'url'=>$url];
}
?>
<section class="bhb-section" id="bhb-bento" aria-label="بنرهای فروشگاه">
 <div class="bhb-wrap bhb-bento">
  <?php foreach($slots as $index=>$slot):
   $banner=$slot['banner'];
   $tone=in_array($banner['tone'],['navy','orange','ice'],true)?$banner['tone']:'navy';
   $class=$index===0?'bhb-banner bhb-banner-main is-'.$tone:'bhb-banner is-'.$tone;
   $preset_image=esc_url($preset_images[$index+1]??'');
   $desktop_id=absint($banner['image_id']??0);
   $mobile_id=absint($banner['image_mobile_id']??0);
   $desktop_src=$desktop_id?wp_get_attachment_image_url($desktop_id,'bh-banner-desktop'):($preset_image?:esc_url(get_template_directory_uri().'/assets/images/catalog/'.($fallback_images[$index+1]??'kitchen.webp')));
   $mobile_src=$mobile_id?wp_get_attachment_image_url($mobile_id,'bh-banner-mobile'):$desktop_src;
  ?>
  <article class="<?php echo esc_attr($class); ?>" data-bhb-bento-card>
   <span class="bhb-banner-photo bhb-generated-photo">
    <picture>
     <?php if($mobile_src&&$mobile_src!==$desktop_src): ?><source media="(max-width: 980px)" srcset="<?php echo esc_url($mobile_src); ?>"><?php endif; ?>
     <img class="bhb-banner-image" src="<?php echo esc_url($desktop_src); ?>" alt="" loading="eager" decoding="async">
    </picture>
   </span>
   <div class="bhb-banner-copy">
    <?php if($index===0): ?><span class="bhb-pill is-accent">ویترین فروشگاه</span><?php endif; ?>
    <h2><?php echo esc_html($banner['title']?:'ویترین فروشگاه'); ?></h2>
    <?php if(!empty($banner['text'])): ?><p><?php echo esc_html($banner['text']); ?></p><?php endif; ?>
    <a class="bhb-btn <?php echo $tone==='navy'?'is-light':''; ?>" href="<?php echo esc_url($slot['url']); ?>"><?php echo esc_html($banner['cta']?:'مشاهده'); ?></a>
   </div>
   <?php /* Generated backgrounds are presentation assets; real product cutouts are not composited on top of them. */ ?>
  </article>
  <?php endforeach; ?>
 </div>
</section>
