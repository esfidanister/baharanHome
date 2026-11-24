<?php if (!defined('ABSPATH')) exit; ?>
<section class="bhb-section" id="bhb-trust" aria-label="اطلاعات فروشگاه">
 <div class="bhb-wrap bhb-trust" tabindex="0" aria-label="نوار اطلاعات فروشگاه">
  <?php for($i=1;$i<=4;$i++): ?>
  <article>
   <span><?php echo esc_html(sprintf('%02d',$i)); ?></span>
   <b><?php echo esc_html(bhb_copy('trust_'.$i.'_title')); ?></b>
   <small><?php echo esc_html(bhb_copy('trust_'.$i.'_text')); ?></small>
  </article>
  <?php endfor; ?>
 </div>
</section>
