<?php
if (!defined('ABSPATH')) exit;
$studio=bhb_studio();
$products=bhb_product_by_source($studio['featured_source']??'latest',10);
?>
<section class="bhb-section" id="bhb-featured" aria-labelledby="bhb-featured-title" data-bhb-rail data-bhb-auto>
 <div class="bhb-wrap">
  <div class="bhb-heading">
   <h2 id="bhb-featured-title"><?php echo esc_html(bhb_copy('featured_title','انتخاب‌های ویژه خانه')); ?></h2>
   <div class="bhb-heading-tools">
    <a class="bhb-text-link" href="<?php echo esc_url(bhb_shop_url()); ?>">مشاهده همه</a>
    <button type="button" data-rail="prev" aria-label="محصولات قبلی">→</button>
    <button type="button" data-rail="next" aria-label="محصولات بعدی">←</button>
   </div>
  </div>
  <?php if($products): ?>
  <div class="bhb-rail"><div class="bhb-rail-track bhb-card-grid">
   <?php foreach($products as $i=>$product) get_template_part('template-parts/home/card',null,['product'=>$product,'variant'=>'full','tone'=>$i%2]); ?>
  </div></div>
  <?php else: ?>
  <p class="bhb-empty">در حال حاضر محصولی برای نمایش در این بخش منتشر نشده است.</p>
  <?php endif; ?>
 </div>
</section>
