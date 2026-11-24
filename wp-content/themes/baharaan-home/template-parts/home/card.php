<?php
if (!defined('ABSPATH')) exit;
$product=$args['product']??null;
if(!$product) return;
$variant=$args['variant']??'full';
$tone=!empty($args['tone'])?'is-warm':'';
$payload=bhb_payload($product);
$opts=get_option('bhc_options',[]);
$url=get_permalink($product->get_id());
?>
<article class="bhb-card <?php echo esc_attr(trim($variant.' '.$tone)); ?>">
 <?php echo bhb_sale_badge($product); ?>
 <?php if($variant==='full'&&!empty($opts['show_wishlist'])): ?>
 <button class="bhx-fav bhb-wish" type="button" data-item="<?php echo esc_attr(wp_json_encode($payload)); ?>" aria-label="افزودن به علاقه‌مندی‌ها" aria-pressed="false">♡</button>
 <?php endif; ?>
 <a class="bhb-card-media" href="<?php echo esc_url($url); ?>">
  <?php echo bhb_img($product,$variant==='compact'?'woocommerce_thumbnail':'bh-product-card'); ?>
 </a>
 <h3><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
 <div class="bhb-card-price bhb-price-hidden" aria-hidden="true"></div>
 <?php if($variant==='full'): ?>
 <div class="bhb-card-foot">
  <?php if($product->is_in_stock()&&$product->is_purchasable()&&$product->is_type('simple')): ?>
  <a class="bhb-btn bhb-btn-small add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-quantity="1" data-product_id="<?php echo esc_attr($product->get_id()); ?>" rel="nofollow">افزودن به سبد</a>
  <?php elseif(!$product->is_in_stock()): ?>
  <span class="bhb-stock">ناموجود</span>
  <?php else: ?>
  <a class="bhb-btn bhb-btn-small" href="<?php echo esc_url($url); ?>">مشاهده محصول</a>
  <?php endif; ?>
 </div>
 <?php else: ?>
 <p class="bhb-card-note"><?php echo $product->is_on_sale()?'قیمت ویژه این کالا':''; ?></p>
 <?php endif; ?>
</article>
