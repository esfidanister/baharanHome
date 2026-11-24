<?php
if (!defined('ABSPATH')) exit;
$studio=bhb_studio();
$selected=absint($studio['hero_product_id']??0);
$slides=[];
$seen=[];
$push=function($product) use (&$slides,&$seen){
 if(!$product||count($slides)>=3||$product->get_status()!=='publish'||isset($seen[$product->get_id()])) return;
 $seen[$product->get_id()]=true;
 $slides[]=$product;
};
if($selected&&function_exists('wc_get_product')) $push(wc_get_product($selected));
foreach(bhb_products(['featured'=>true,'limit'=>3]) as $product) $push($product);
foreach(bhb_products(['limit'=>3]) as $product) $push($product);
$commerce=get_option('bhc_options',[]);
$kicker=bhb_copy('hero_kicker','فروشگاه لوازم خانگی بهاران');
$override=trim((string)($studio['hero_title']??''));
$accent=bhb_copy('hero_accent','زیباتر و هوشمندتر');
$subtitle=trim((string)($studio['hero_subtitle']??''));
if($subtitle==='') $subtitle=bhb_copy('hero_subtitle','محصولات کاربردی، با قیمت و موجودی ثبت‌شده در فروشگاه.');
$cta=trim((string)($commerce['hero_cta_label']??''));
if($cta===''||$cta==='شروع خرید') $cta=bhb_copy('hero_cta','مشاهده دسته‌بندی‌ها');
$cta_url=trim((string)($commerce['hero_cta_url']??''));
if($cta_url==='') $cta_url='#bhb-categories';
$hero_bg_id=absint($studio['hero_bg_id']??0);
$hero_bg_mobile_id=absint($studio['hero_bg_mobile_id']??0);
$hero_bg=$hero_bg_id?wp_get_attachment_image_url($hero_bg_id,'full'):'';
$hero_bg_mobile=$hero_bg_mobile_id?wp_get_attachment_image_url($hero_bg_mobile_id,'large'):'';
if(!$hero_bg) $hero_bg=get_template_directory_uri().'/assets/images/generated/hero-studio-background-v2.webp';
if(!$hero_bg_mobile) $hero_bg_mobile=get_template_directory_uri().'/assets/images/generated/hero-studio-background-v2-mobile.webp';
?>
<section class="bhb-hero" id="bhb-hero" aria-label="معرفی فروشگاه" data-bhb-hero>
 <div class="bhb-wrap bhb-hero-stage has-studio-bg" style="--bhb-hero-bg:url('<?php echo esc_url($hero_bg); ?>');--bhb-hero-bg-mobile:url('<?php echo esc_url($hero_bg_mobile); ?>');">
  <div class="bhb-hero-blob" aria-hidden="true"></div>
  <div class="bhb-hero-copy">
   <span class="bhb-pill"><?php echo esc_html($kicker); ?></span>
   <h1><?php if($override!==''): ?><?php echo esc_html($override); ?><?php else: ?><?php echo esc_html(bhb_copy('hero_title','برای خانه‌ای')); ?><?php if($accent!==''): ?><br><span><?php echo esc_html($accent); ?></span><?php endif; ?><?php endif; ?></h1>
   <p><?php echo esc_html($subtitle); ?></p>
   <a class="bhb-btn is-light" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta); ?> <span aria-hidden="true">◀</span></a>
  </div>
  <div class="bhb-hero-visual">
   <?php if($slides): foreach($slides as $index=>$product):
    $next=$slides[($index+1)%count($slides)];
    if($next->get_id()===$product->get_id()) $next=null;
   ?>
   <div class="bhb-hero-slide" data-slide <?php echo $index?'hidden':''; ?>>
    <a class="bhb-hero-primary" href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
     <?php echo bhb_img($product,'bh-product-hero',['loading'=>$index?'lazy':'eager','fetchpriority'=>$index?'auto':'high']); ?>
     <span><?php echo esc_html($product->get_name()); ?></span>
    </a>
    <?php if($next): ?>
    <a class="bhb-hero-secondary" href="<?php echo esc_url(get_permalink($next->get_id())); ?>">
     <?php echo bhb_img($next,'bh-product-card'); ?>
    </a>
    <?php endif; ?>
   </div>
   <?php endforeach; else: ?>
   <div class="bhb-hero-fallback"><img src="<?php echo esc_url(get_template_directory_uri().'/assets/images/generated/hero-appliance.webp'); ?>" alt="تصویر تزئینی هیرو" loading="eager"><span>محصول واقعی برای هیرو انتخاب نشده است</span></div>
   <?php endif; ?>
  </div>
  <svg class="bhb-hero-wave" viewBox="0 0 1500 160" preserveAspectRatio="none" aria-hidden="true"><path d="M0 80Q210-35 440 73T870 68T1500 30V160H0Z" fill="#fff"/></svg>
  <?php if(count($slides)>1): ?>
  <div class="bhb-hero-controls">
   <div class="bhb-dots" role="tablist" aria-label="نمونه‌های هیرو">
    <?php foreach($slides as $index=>$product): ?>
    <button type="button" role="tab" data-slide-to="<?php echo esc_attr($index); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>" <?php echo $index?'aria-selected="false"':'aria-selected="true" aria-current="true"'; ?>></button>
    <?php endforeach; ?>
   </div>
   <button class="bhb-chip" type="button" data-slide-next>اسلاید بعدی</button>
  </div>
  <?php endif; ?>
 </div>
</section>
