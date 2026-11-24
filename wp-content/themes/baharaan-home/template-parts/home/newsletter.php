<?php
if (!defined('ABSPATH')) exit;
$status=sanitize_key(wp_unslash($_GET['bhb_news']??''));
?>
<section class="bhb-section" id="bhb-newsletter" aria-labelledby="bhb-news-title">
 <div class="bhb-wrap">
  <div class="bhb-news">
   <div>
    <h2 id="bhb-news-title"><?php echo esc_html(bhb_copy('newsletter_title','به دنیای خانه مدرن نزدیک‌تر شوید')); ?></h2>
    <p><?php echo esc_html(bhb_copy('newsletter_text')); ?></p>
   </div>
   <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="bhb_newsletter">
    <?php wp_nonce_field('bhb_newsletter','bhb_newsletter_nonce'); ?>
    <label class="screen-reader-text" for="bhb-email">ایمیل</label>
    <input id="bhb-email" type="email" name="bhb_email" required autocomplete="email" placeholder="آدرس ایمیل">
    <button class="bhb-btn is-accent" type="submit">عضویت</button>
   </form>
   <?php if($status==='ok'): ?><p class="bhb-news-note" role="status">ایمیل ذخیره شد.</p><?php elseif($status==='invalid'): ?><p class="bhb-news-note" role="status">ایمیل معتبر نیست.</p><?php endif; ?>
  </div>
 </div>
</section>
