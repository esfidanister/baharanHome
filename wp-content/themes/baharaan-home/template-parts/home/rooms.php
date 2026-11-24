<?php
if (!defined('ABSPATH')) exit;
$studio=bhb_studio();
$terms=bhb_categories(12);
$picked=[];
foreach(array_slice((array)($studio['rooms']??[]),0,3) as $id){
 $term=$id?get_term(absint($id),'product_cat'):null;
 if($term&&!is_wp_error($term)) $picked[]=$term;
}
if(count($picked)<3){
 foreach($terms as $term){
  $have=false;
  foreach($picked as $item) if($item->term_id===$term->term_id) $have=true;
  if(!$have) $picked[]=$term;
  if(count($picked)===3) break;
 }
}
if(!$picked){
 echo '<section class="bhb-section" id="bhb-rooms"><div class="bhb-wrap"><p class="bhb-empty">برای این بخش هنوز دسته‌ای با محصول منتشرشده ثبت نشده است.</p></div></section>';
 return;
}
$main=array_shift($picked);
?>
<section class="bhb-section" id="bhb-rooms" aria-label="خرید بر اساس دسته">
 <div class="bhb-wrap bhb-rooms">
  <a class="bhb-room bhb-room-main" href="<?php echo esc_url(get_term_link($main)); ?>">
   <span class="bhb-room-copy"><small>دسته فروشگاه</small><b><?php echo esc_html($main->name); ?></b></span>
   <span class="bhb-room-visual"><?php echo bhb_term_visual($main); ?></span>
   <span class="bhb-btn">مشاهده <?php echo esc_html($main->name); ?></span>
  </a>
  <?php if($picked): ?><div class="bhb-room-side"><?php foreach($picked as $term): ?>
   <a class="bhb-room" href="<?php echo esc_url(get_term_link($term)); ?>">
    <span class="bhb-room-copy"><b><?php echo esc_html($term->name); ?></b><small><?php echo esc_html(number_format_i18n($term->count)); ?> محصول</small></span>
    <span class="bhb-room-visual"><?php echo bhb_term_visual($term); ?></span>
   </a>
  <?php endforeach; ?></div><?php endif; ?>
 </div>
</section>
