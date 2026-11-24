<?php
if (!defined('ABSPATH')) exit;
$brands=function_exists('bhb_brand_rows')?bhb_brand_rows():[];
$index=function_exists('bhb_brand_index')?bhb_brand_index():[];
$theme_uri=get_template_directory_uri();
$logo_assets=[
 'lg'=>'lg.webp','samsung'=>'samsung.webp','sony'=>'sony.webp','bosch'=>'bosch-v2.webp',
 'panasonic'=>'panasonic.webp','delonghi'=>'delonghi.webp',
];
$brand_logo_file=function($brand) use ($logo_assets){
 if(!empty($brand['logo'])) return $brand['logo'];
 $keys=[strtolower(trim((string)($brand['latin']??''))),strtolower(trim((string)($brand['name']??'')))];
 foreach($keys as $key){
  $key=preg_replace('/[^a-z0-9]+/i','',str_replace('+','',$key));
  if(isset($logo_assets[$key])) return $logo_assets[$key];
 }
 return '';
};
?>
<section class="bhb-section" id="bhb-brands" aria-labelledby="bhb-brands-title" data-bhb-rail data-bhb-auto>
 <div class="bhb-wrap">
  <div class="bhb-heading">
   <div>
    <h2 id="bhb-brands-title"><?php echo esc_html(bhb_copy('brands_title','برندهای معتبر لوازم خانگی')); ?></h2>
    <p><?php echo esc_html(bhb_copy('brands_lead','برندهای معتبر لوازم خانگی را همراه با محصولات و دسته‌بندی‌های مرتبط ببینید.')); ?></p>
   </div>
   <div class="bhb-heading-tools">
    <button type="button" data-rail="prev" aria-label="برندهای قبلی">→</button>
    <button type="button" data-rail="next" aria-label="برندهای بعدی">←</button>
   </div>
  </div>
  <div class="bhb-rail"><div class="bhb-rail-track bhb-brand-board">
   <?php
   $ranked=[];
   foreach($brands as $i=>$brand) $ranked[]=['brand'=>$brand,'row'=>$index[$i]??['count'=>0,'product'=>null]];
   usort($ranked,static function($a,$b){ return ((int)$b['row']['count'])<=>((int)$a['row']['count']); });
   foreach($ranked as $item):
    $brand=$item['brand'];
    $name=$brand['name'];
    $latin=$brand['latin'];
    $row=$item['row'];
    $count=(int)$row['count'];
    $href=function_exists('bhb_brand_url')?bhb_brand_url($brand):($brand['url']?:add_query_arg(['s'=>$name,'post_type'=>'product'],home_url('/')));
    $cats=array_values(array_filter(array_map('trim',preg_split('/[،,]/u',(string)$brand['cats']))));
    $product=$row['product']??null;
    $logo_file=$brand_logo_file($brand);
   ?>
   <article class="bhb-brand-card<?php echo $count?' has-product':''; ?>">
    <a class="bhb-brand-top" href="<?php echo esc_url($href); ?>" aria-label="مشاهده محصولات برند <?php echo esc_attr($name); ?>">
     <?php $logo_id=absint($brand['image_id']??0); if($logo_id&&wp_attachment_is_image($logo_id)): echo wp_get_attachment_image($logo_id,'medium',false,['class'=>'bhb-brand-logo','alt'=>esc_attr('لوگوی '.$name),'loading'=>'lazy','decoding'=>'async']); elseif($logo_file): ?>
     <img class="bhb-brand-logo bhb-brand-logo-preset" src="<?php echo esc_url($theme_uri.'/assets/images/brands/'.$logo_file); ?>" alt="<?php echo esc_attr('لوگوی '.$name); ?>" loading="lazy" decoding="async" width="1600" height="840">
     <?php else: ?>
     <span class="bhb-brand-logo-fallback" aria-label="نام برند <?php echo esc_attr($name); ?>" lang="en"><?php echo esc_html($latin?:$name); ?></span>
     <?php endif; ?>
     <span class="bhb-brand-name"><b><?php echo esc_html($name); ?></b><?php if($latin): ?><small><?php echo esc_html($latin); ?></small><?php endif; ?></span>
    </a>
    <p><?php echo esc_html(wp_trim_words($brand['line'],12)); ?></p>
    <?php if($cats): ?><div class="bhb-brand-cats"><?php foreach(array_slice($cats,0,3) as $cat): ?><a href="<?php echo esc_url(bhb_category_url($cat)); ?>"><?php echo esc_html($cat); ?></a><?php endforeach; ?></div><?php endif; ?>
    <?php if($product): ?>
    <a class="bhb-brand-shot" href="<?php echo esc_url(get_permalink($product->get_id())); ?>"><?php echo bhb_img($product,'bh-product-card'); ?><span><?php echo esc_html($product->get_name()); ?></span></a>
    <?php else: ?>
    <div class="bhb-brand-shot bhb-brand-shot-empty" role="note"><span>هنوز محصولی با این برند ثبت نشده</span></div>
    <?php endif; ?>
    <a class="bhb-brand-cta" href="<?php echo esc_url($href); ?>"><?php echo $count? 'مشاهده محصولات':'جستجوی این نام'; ?><?php if($count): ?><b><?php echo esc_html(number_format_i18n($count)); ?></b><?php endif; ?></a>
   </article>
   <?php endforeach; ?>
  </div></div>
  <?php if($brands): ?>
  <div class="bhb-brand-strip" aria-label="برندهای فروشگاه" data-bhb-brand-strip>
   <?php foreach($brands as $brand): $href=function_exists('bhb_brand_url')?bhb_brand_url($brand):($brand['url']?:add_query_arg(['s'=>$brand['name'],'post_type'=>'product'],home_url('/'))); $logo_file=$brand_logo_file($brand); $logo_id=absint($brand['image_id']??0); ?>
   <a href="<?php echo esc_url($href); ?>" aria-label="مشاهده محصولات برند <?php echo esc_attr($brand['name']); ?>">
    <?php if($logo_id&&wp_attachment_is_image($logo_id)): echo wp_get_attachment_image($logo_id,'medium',false,['class'=>'bhb-brand-logo','alt'=>esc_attr('لوگوی '.$brand['name']),'loading'=>'lazy','decoding'=>'async']); elseif($logo_file): ?><img src="<?php echo esc_url($theme_uri.'/assets/images/brands/'.$logo_file); ?>" alt="<?php echo esc_attr('لوگوی '.$brand['name']); ?>" loading="lazy" decoding="async" width="1600" height="840">
    <?php else: ?><span><?php echo esc_html($brand['latin']?:$brand['name']); ?></span><?php endif; ?>
   </a>
   <?php endforeach; ?>
  </div>
  <?php endif; ?>
 </div>
</section>
