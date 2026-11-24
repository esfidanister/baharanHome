<?php
if(!defined('ABSPATH'))exit;
get_header();
$skip=[];
foreach(get_posts(['post_type'=>'post','post_status'=>'publish','posts_per_page'=>-1,'ignore_sticky_posts'=>true]) as $candidate){
 if(in_array($candidate->post_name,['hello-world'],true)||in_array(trim(wp_strip_all_tags($candidate->post_title)),['سلام دنیا','سلام دنیا!'],true))$skip[]=(int)$candidate->ID;
}
$query=new WP_Query(['post_type'=>'post','post_status'=>'publish','posts_per_page'=>20,'paged'=>max(1,(int)get_query_var('paged')),'post__not_in'=>$skip,'ignore_sticky_posts'=>true]);
$count=(int)$query->found_posts;
?>
<main class="bhb-magazine-archive bhb-section" aria-labelledby="bhb-magazine-title">
 <div class="bhb-wrap">
  <header class="bhb-heading bhb-magazine-head">
   <div><span class="bhx-overline">BAHARAN HOME / MAGAZINE</span><h1 id="bhb-magazine-title">مجله خانه و راهنمای خرید</h1><p>راهنماهای تخصصی و کاربردی برای انتخاب، استفاده و نگهداری لوازم خانگی. <b><?php echo esc_html(number_format_i18n($count)); ?> مقاله</b></p></div>
   <a class="bhb-btn" href="<?php echo esc_url(home_url('/')); ?>">بازگشت به فروشگاه</a>
  </header>
  <?php if($query->have_posts()): ?><div class="bhb-magazine-grid">
   <?php while($query->have_posts()):$query->the_post();$cats=get_the_category();$cat=$cats?$cats[0]->name:'راهنمای خرید'; ?>
   <article class="bhb-magazine-card">
    <a class="bhb-magazine-card-media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>"><?php if(has_post_thumbnail()):the_post_thumbnail('large',['loading'=>'lazy','alt'=>esc_attr(get_the_title())]);else:?><img src="<?php echo esc_url(get_template_directory_uri().'/assets/images/generated/journal-interior.webp'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy"><?php endif; ?></a>
    <div class="bhb-magazine-card-body"><span class="bhb-story-cat"><?php echo esc_html($cat); ?></span><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html(wp_trim_words(get_the_excerpt()?:wp_strip_all_tags(get_the_content()),28)); ?></p><div class="bhb-story-meta"><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(bh_jalali_post_date(get_the_ID())); ?></time><a href="<?php the_permalink(); ?>">مطالعه راهنما ←</a></div></div>
   </article>
   <?php endwhile;wp_reset_postdata(); ?>
  </div><?php if($query->max_num_pages>1): ?><nav class="bhb-magazine-pagination" aria-label="صفحه‌های مجله"><?php echo wp_kses_post(paginate_links(['total'=>$query->max_num_pages,'current'=>max(1,get_query_var('paged')),'type'=>'list','prev_text'=>'← قبلی','next_text'=>'بعدی →'])); ?></nav><?php endif; ?><?php else:?><p class="bhb-empty">مقاله‌ای منتشر نشده است.</p><?php endif; ?>
 </div>
</main>
<?php get_footer();
