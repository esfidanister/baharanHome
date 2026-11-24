<?php
if (!defined('ABSPATH')) exit;
$skip=[];
foreach(get_posts(['post_type'=>'post','post_status'=>'publish','posts_per_page'=>20,'ignore_sticky_posts'=>true]) as $candidate){
 $title=trim(wp_strip_all_tags($candidate->post_title));
 if(in_array($candidate->post_name,['hello-world'],true)||in_array($title,['سلام دنیا','سلام دنیا!'],true)) $skip[]=(int)$candidate->ID;
}
$query=new WP_Query([
 'post_type'=>'post',
 'post_status'=>'publish',
 'posts_per_page'=>8,
 'post__not_in'=>$skip,
 'ignore_sticky_posts'=>true,
 'no_found_rows'=>true,
]);
$archive=get_permalink((int)get_option('page_for_posts'));
if(!$archive){$archive=home_url('/magazine/');}
$editorial_count=new WP_Query(['post_type'=>'post','post_status'=>'publish','posts_per_page'=>1,'post__not_in'=>$skip,'fields'=>'ids']);
$published_count=(int)$editorial_count->found_posts;
?>
<section class="bhb-section" id="bhb-journal" aria-labelledby="bhb-journal-title" data-bhb-rail data-bhb-auto>
 <div class="bhb-wrap">
  <div class="bhb-heading">
   <div>
    <h2 id="bhb-journal-title"><?php echo esc_html(bhb_copy('journal_title','مجله خانه')); ?></h2>
    <p><?php echo esc_html(bhb_copy('journal_lead','راهنماهای خرید و مطالب کاربردی برای انتخاب آگاهانه لوازم خانگی.')); ?> <span class="bhb-journal-count"><?php echo esc_html(number_format_i18n($published_count)); ?> مقاله منتشرشده</span></p>
   </div>
   <div class="bhb-heading-tools">
    <a class="bhb-text-link" href="<?php echo esc_url($archive); ?>">تمام مقاله‌ها</a>
    <button type="button" data-rail="prev" aria-label="مقاله‌های قبلی">→</button>
    <button type="button" data-rail="next" aria-label="مقاله‌های بعدی">←</button>
   </div>
  </div>
  <?php if($query->have_posts()): ?>
  <div class="bhb-rail"><div class="bhb-journal-grid bhb-rail-track" tabindex="0" aria-label="مقاله‌های مجله خانه">
   <?php $index=0; while($query->have_posts()): $query->the_post(); $index++;
    $cats=get_the_category();
    $cat=$cats?$cats[0]->name:'مجله';
   ?>
   <article class="bhb-story">
    <a class="bhb-story-media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
     <?php if(has_post_thumbnail()): the_post_thumbnail('bh-editorial',['loading'=>'lazy','alt'=>esc_attr(get_the_title())]); else: ?><img class="bhb-story-fallback-image" src="<?php echo esc_url(get_template_directory_uri().'/assets/images/generated/journal-interior.webp'); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy"><?php endif; ?>
    </a>
    <div class="bhb-story-body">
     <span class="bhb-story-cat"><?php echo esc_html($cat); ?></span>
     <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
     <p><?php echo esc_html(wp_trim_words(get_the_excerpt()?:wp_strip_all_tags(get_the_content()),22)); ?></p>
     <div class="bhb-story-meta">
      <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(bh_jalali_post_date(get_the_ID())); ?></time>
      <a href="<?php the_permalink(); ?>">مطالعه مقاله</a>
     </div>
    </div>
   </article>
   <?php endwhile; wp_reset_postdata(); ?>
  </div></div>
  <?php else: ?>
  <p class="bhb-empty">مقاله منتشرشده‌ای برای مجله وجود ندارد. نوشته پیش‌فرض وردپرس در این ویترین نشان داده نمی‌شود.</p>
  <?php endif; ?>
 </div>
</section>
