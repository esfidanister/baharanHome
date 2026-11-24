<?php
if (!defined('ABSPATH')) exit;
$products=bhb_products(['limit'=>5,'orderby'=>'popularity','order'=>'DESC']);
$has_sales=false;
foreach($products as $product) if((int)$product->get_total_sales()>0) $has_sales=true;
$title=$has_sales?'پرفروش‌های فروشگاه':'محصولات پربازدید';
?>
<section class="bhb-section" id="bhb-best" aria-labelledby="bhb-best-title" data-bhb-rail>
 <div class="bhb-wrap">
  <div class="bhb-heading">
   <h2 id="bhb-best-title"><?php echo esc_html($title); ?></h2>
   <div class="bhb-heading-tools">
    <button type="button" data-rail="prev" aria-label="قبلی">→</button>
    <button type="button" data-rail="next" aria-label="بعدی">←</button>
   </div>
  </div>
  <?php if($products): ?>
  <div class="bhb-rail"><div class="bhb-rail-track bhb-card-grid is-five">
   <?php foreach($products as $i=>$product) get_template_part('template-parts/home/card',null,['product'=>$product,'variant'=>'full','tone'=>$i%2]); ?>
  </div></div>
  <?php else: ?><p class="bhb-empty">محصولی برای این فهرست نیست.</p><?php endif; ?>
 </div>
</section>
