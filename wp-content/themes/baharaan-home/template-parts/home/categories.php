<?php
if (!defined('ABSPATH')) exit;
$terms=bhb_categories(8);
$tones=['is-peach','is-sky','is-mint','is-sand','is-ice'];
$feature=null;
foreach($terms as $term){ if(!$feature||(int)$term->count>(int)$feature->count) $feature=$term; }
?>
<section class="bhb-section" id="bhb-categories" aria-labelledby="bhb-categories-title" data-bhb-rail data-bhb-auto>
 <div class="bhb-wrap">
  <div class="bhb-heading">
   <div>
    <h2 id="bhb-categories-title"><?php echo esc_html(bhb_copy('categories_title','خرید لوازم خانگی را از کجا شروع کنیم؟')); ?></h2>
    <p><?php echo esc_html(bhb_copy('categories_lead','دسته‌بندی‌های فروشگاه را با تصاویر واقعی محصولات و اطلاعات به‌روز ببینید.')); ?></p>
   </div>
   <div class="bhb-heading-tools">
    <button type="button" data-rail="prev" aria-label="دسته‌های قبلی">→</button>
    <button type="button" data-rail="next" aria-label="دسته‌های بعدی">←</button>
   </div>
  </div>
  <?php if($terms): ?>
  <div class="bhb-rail"><div class="bhb-rail-track bhb-cats" tabindex="0" aria-label="دسته‌های فروشگاه">
   <?php foreach($terms as $i=>$term): $link=get_term_link($term); if(is_wp_error($link)) continue; ?>
   <a class="bhb-cat <?php echo esc_attr($tones[$i%5]); ?>" href="<?php echo esc_url($link); ?>">
    <span class="bhb-cat-visual"><?php echo bhb_term_visual($term); ?></span>
    <b><?php echo esc_html($term->name); ?></b>
    <small><?php echo esc_html(number_format_i18n($term->count)); ?> محصول</small>
    <span class="bhb-cat-go" aria-hidden="true">←</span>
   </a>
   <?php endforeach; ?>
  </div></div>
  <?php if($feature): $feature_link=get_term_link($feature); ?>
  <a class="bhb-spot" href="<?php echo esc_url($feature_link); ?>">
   <span class="bhb-spot-copy">
    <small>پربازدیدترین دسته</small>
    <b><?php echo esc_html($feature->name); ?></b>
    <span><?php echo esc_html(number_format_i18n($feature->count)); ?> محصول در این دسته</span>
    <em>مشاهده محصولات</em>
   </span>
   <span class="bhb-spot-photo"><?php echo bhb_term_visual($feature); ?></span>
  </a>
  <?php endif; ?>
  <?php else: ?>
  <p class="bhb-empty">در حال حاضر دسته‌ای با محصول منتشرشده برای نمایش وجود ندارد.</p>
  <?php endif; ?>
 </div>
</section>
