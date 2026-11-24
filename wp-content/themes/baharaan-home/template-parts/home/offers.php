<?php
if (!defined('ABSPATH')) exit;
$products=array_slice(array_values(array_filter(bhb_products(['limit'=>12,'on_sale'=>true]),static function($product){return $product instanceof WC_Product&&$product->is_on_sale();})),0,5);
$hero=$products[0]??null;
$cards=array_slice($products,0,4);
$until=$hero?bhb_sale_until($hero):'';
?>
<section class="bhb-section bhb-offers<?php echo $cards?'':' is-quiet'; ?>" id="bhb-offers" aria-labelledby="bhb-offers-title">
 <div class="bhb-wrap">
  <div class="bhb-offers-panel">
   <div class="bhb-offers-copy">
    <h2 id="bhb-offers-title"><?php echo esc_html(bhb_copy('offers_title','پیشنهادهای منتخب بهاران')); ?></h2>
    <p><?php echo esc_html(bhb_copy('offers_text','فقط محصولات دارای قیمت ویژه ثبت‌شده')); ?></p>
    <?php if($until): ?><p class="bhb-until">پایان قیمت ویژه این کالا: <?php echo esc_html($until); ?></p><?php endif; ?>
    <a class="bhb-btn is-light" href="<?php echo esc_url(add_query_arg('on_sale','1',bhb_shop_url())); ?>"><?php echo esc_html(bhb_copy('offers_cta','مشاهده پیشنهادها')); ?></a>
   </div>
   <?php if($hero): ?>
   <a class="bhb-offers-hero" href="<?php echo esc_url(get_permalink($hero->get_id())); ?>"><?php echo bhb_img($hero,'bh-product-hero',['loading'=>'lazy']); ?></a>
   <?php endif; ?>
   <svg class="bhb-offers-wave" viewBox="0 0 1480 200" preserveAspectRatio="none" aria-hidden="true"><path d="M0 110Q230 0 440 115T920 85T1480 120V200H0Z" fill="#fff"/></svg>
   <?php if($cards): ?>
   <div class="bhb-offers-grid">
    <?php foreach($cards as $product) get_template_part('template-parts/home/card',null,['product'=>$product,'variant'=>'compact']); ?>
   </div>
   <?php else: ?>
   <p class="bhb-empty is-on-color">در حال حاضر قیمت ویژه‌ای در فروشگاه ثبت نشده است.</p>
   <?php endif; ?>
  </div>
 </div>
</section>
