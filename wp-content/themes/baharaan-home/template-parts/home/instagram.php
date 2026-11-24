<?php
if (!defined('ABSPATH')) exit;
$studio=bhb_studio();
$reels=is_array($studio['reels']??null)?$studio['reels']:[];
$reels=array_values(array_filter($reels,static function($reel){
 return is_array($reel)&&!empty($reel['url']);
}));
$commerce=get_option('bhc_options',[]);
$profile=esc_url_raw($commerce['instagram']??'');
if($profile===''||strpos($profile,'instagram.com')===false) $profile='https://www.instagram.com/baharaan.home/';
$display=wp_parse_args(is_array($studio['reels_display']??null)?$studio['reels_display']:[],['layout'=>'rail','columns'=>3,'copy'=>'overlay','show_index'=>1,'show_play'=>1,'show_title'=>1,'show_caption'=>1,'button'=>'مشاهده در اینستاگرام','radius'=>28]);
$layout=in_array($display['layout'],['rail','center','grid'],true)?$display['layout']:'rail';
$columns=in_array((int)$display['columns'],[2,3,4],true)?(int)$display['columns']:3;
$copy=in_array($display['copy'],['overlay','below','hidden'],true)?$display['copy']:'overlay';
$is_rail=$layout==='rail';
?>
<section class="bhb-section is-layout-<?php echo esc_attr($layout); ?> is-copy-<?php echo esc_attr($copy); ?>" id="bhb-instagram" aria-labelledby="bhb-instagram-title" style="--bhb-reel-cols:<?php echo esc_attr($columns); ?>;--bhb-reel-radius:<?php echo esc_attr(max(12,min(36,absint($display['radius'])))); ?>px" <?php echo ($reels&&$is_rail)?'data-bhb-rail data-bhb-auto':''; ?>>
 <div class="bhb-wrap">
  <div class="bhb-heading">
   <div>
    <h2 id="bhb-instagram-title"><?php echo esc_html(bhb_copy('instagram_title','لحظه‌های بهاران')); ?></h2>
    <p><?php echo esc_html(bhb_copy('instagram_lead','ویدیوهای کوتاه با کاور و لینک واقعی. برای تماشا وارد اینستاگرام شوید.')); ?></p>
   </div>
   <?php if($reels&&$is_rail): ?>
   <div class="bhb-heading-tools">
    <button type="button" data-rail="prev" aria-label="ریل قبلی">→</button>
    <button type="button" data-rail="next" aria-label="ریل بعدی">←</button>
   </div>
   <?php endif; ?>
  </div>
  <?php if($reels): ?>
  <div class="bhb-rail"><div class="bhb-rail-track bhb-reels">
   <?php foreach($reels as $i=>$reel): ?>
   <a class="bhb-reel" href="<?php echo esc_url($reel['url']); ?>" target="_blank" rel="noopener noreferrer">
    <?php if(!empty($reel['image_id'])) echo wp_get_attachment_image(absint($reel['image_id']),'large',false,['alt'=>esc_attr($reel['alt']??($reel['title']?:'کاور ریل اینستاگرام')),'loading'=>'lazy']); ?>
    <?php if(!empty($display['show_play'])): ?><span class="bhb-reel-play" aria-hidden="true">▶</span><?php endif; ?>
    <?php if(!empty($display['show_index'])): ?><span class="bhb-reel-index">ریل <?php echo esc_html(number_format_i18n($i+1)); ?></span><?php endif; ?>
    <?php if($copy!=='hidden'): ?>
    <span class="bhb-reel-copy">
     <?php if(!empty($display['show_title'])): ?><b><?php echo esc_html($reel['title']?:'تماشا در اینستاگرام'); ?></b><?php endif; ?>
     <?php if(!empty($display['show_caption'])&&!empty($reel['caption'])): ?><small><?php echo esc_html($reel['caption']); ?></small><?php endif; ?>
     <em><?php echo esc_html($display['button']?:'مشاهده در اینستاگرام'); ?></em>
    </span>
    <?php endif; ?>
   </a>
   <?php endforeach; ?>
  </div></div>
  <?php else: ?>
  <a class="bhb-profile-bar" href="<?php echo esc_url($profile); ?>" target="_blank" rel="noopener noreferrer"><span><b>baharaan.home</b><small>صفحه اینستاگرام فروشگاه. با کاور هر ریل، اینجا کارت ویدیو ساخته می‌شود.</small></span><em>مشاهده صفحه</em></a>
  <?php endif; ?>
 </div>
</section>
