<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__.'/brand-directory.php';

function bhb_section_catalog(){
 return [
  'hero'=>['label'=>'هیرو ارگانیک','part'=>'hero'],
  'categories'=>['label'=>'دسته‌بندی تصویری','part'=>'categories'],
  'bento'=>['label'=>'بنرهای نامتقارن','part'=>'bento'],
  'featured'=>['label'=>'کارت‌های محصول','part'=>'featured'],
  'offers'=>['label'=>'پیشنهادهای ویژه','part'=>'offers'],
  'rooms'=>['label'=>'خرید بر اساس فضا','part'=>'rooms'],
  'brands'=>['label'=>'ویترین برندها','part'=>'brands'],
  'best-sellers'=>['label'=>'پربازدیدها','part'=>'bestsellers'],
  'journal'=>['label'=>'مجله و راهنما','part'=>'journal'],
  'instagram'=>['label'=>'ریل اینستاگرام','part'=>'instagram'],
  'trust'=>['label'=>'خدمات اعتماد','part'=>'trust'],
  'newsletter'=>['label'=>'خبرنامه','part'=>'newsletter'],
 ];
}

function bhb_fallback_studio(){
 $order=array_keys(bhb_section_catalog());
 return [
  'order'=>$order,
  'enabled'=>$order,
  'container_width'=>1320,
  'hero_overflow'=>96,
  'motion'=>true,
  'hero_title'=>'',
  'hero_subtitle'=>'',
  'hero_product_id'=>0,
  'featured_source'=>'latest',
  'copy'=>[
   'utility'=>'',
   'hero_kicker'=>'فروشگاه لوازم خانگی بهاران',
   'hero_title'=>'برای خانه‌ای',
   'hero_accent'=>'زیباتر و هوشمندتر',
   'hero_subtitle'=>'لوازم خانگی را با بررسی مشخصات، قیمت و وضعیت موجودی انتخاب کنید.',
   'hero_cta'=>'مشاهده دسته‌بندی‌ها',
   'categories_title'=>'خرید لوازم خانگی را از کجا شروع کنیم؟',
   'categories_lead'=>'دسته‌بندی‌های فروشگاه را با تصاویر واقعی محصولات و اطلاعات به‌روز ببینید.',
   'featured_title'=>'انتخاب‌های ویژه خانه',
   'offers_title'=>'پیشنهادهای منتخب بهاران',
   'offers_text'=>'محصولات منتخب با شرایط ویژه و موجودی مشخص در فروشگاه.',
   'offers_cta'=>'مشاهده پیشنهادها',
   'rooms_title'=>'',
   'brands_title'=>'برندهای معتبر لوازم خانگی','brands_lead'=>'برندهای معتبر لوازم خانگی را همراه با محصولات و دسته‌بندی‌های مرتبط ببینید.',
   'journal_title'=>'مجله و راهنمای خرید','journal_lead'=>'راهنماهای خرید و مطالب کاربردی برای انتخاب آگاهانه لوازم خانگی.',
   'instagram_title'=>'ایده‌های بهاران برای خانه','instagram_lead'=>'ویدیوها و ایده‌های بهاران را در اینستاگرام ببینید.',
   'newsletter_title'=>'با خبرهای تازه خانه همراه باشید',
   'newsletter_text'=>'با عضویت اختیاری، از راهنماها و خبرهای تازه بهاران‌هوم باخبر شوید.',
   'trust_1_title'=>'جزئیات کالا','trust_1_text'=>'پیش از خرید، مشخصات را در صفحه همان کالا بخوانید',
   'trust_2_title'=>'قیمت روشن','trust_2_text'=>'مبلغ و موجودی، همان چیزی است که کنار کالا می‌بینید',
   'trust_3_title'=>'ایده برای خانه','trust_3_text'=>'راهنماهای خرید بهاران را برای انتخاب بهتر بخوانید',
   'trust_4_title'=>'گفتگو با بهاران','trust_4_text'=>'سؤال دارید؟ از راه‌های تماس فروشگاه بپرسید',
   'footer_about'=>'انتخاب لوازم خانگی با مشخصات، قیمت و وضعیت موجودی به‌روز.',
   'footer_address'=>'','footer_phone'=>'','footer_hours'=>'',
  ],
  'colors'=>['navy'=>'#0B2039','blue'=>'#1769D2','ice'=>'#EAF3FF','bg'=>'#F7F9FC','accent'=>'#FF8A3D'],
  'banners'=>[
   1=>['enabled'=>1,'title'=>'شست‌وشوی دقیق، خانه‌ای آرام','text'=>'ماشین‌های لباسشویی برای شست‌وشوی بهتر و مصرف بهینه.','cta'=>'مشاهده ماشین‌های لباسشویی','url'=>'','product_id'=>0,'product_id_2'=>0,'tone'=>'navy','image_id'=>0,'image_mobile_id'=>0],
   2=>['enabled'=>1,'title'=>'صدای سینمایی برای خانه','text'=>'ساندبارهای حرفه‌ای برای فیلم، موسیقی و بازی.','cta'=>'مشاهده محصولات صوتی','url'=>'','product_id'=>0,'product_id_2'=>0,'tone'=>'orange','image_id'=>0,'image_mobile_id'=>0],
   3=>['enabled'=>1,'title'=>'آشپزخانه‌ای روشن و کاربردی','text'=>'ابزارهای کاربردی برای آماده‌سازی سریع‌تر غذا.','cta'=>'مشاهده لوازم آشپزخانه','url'=>'','product_id'=>0,'product_id_2'=>0,'tone'=>'ice','image_id'=>0,'image_mobile_id'=>0],
  ],
  'rooms'=>[0,0,0],
 ];
}

function bhb_studio(){
 if(function_exists('bhc_studio_opts')) return bhc_studio_opts();
 return bhb_fallback_studio();
}

function bhb_copy($key,$fallback=''){
 $studio=bhb_studio();
 $value=trim((string)($studio['copy'][$key]??''));
 return $value!==''?$value:$fallback;
}

add_filter('woocommerce_get_breadcrumb',function($crumbs){
 foreach($crumbs as &$crumb){
  if(!isset($crumb[0])) continue;
  if(strtolower((string)$crumb[0])==='shop') $crumb[0]='فروشگاه';
 }
 return $crumbs;
});

function bhb_shop_url(){
 return function_exists('wc_get_page_permalink')?wc_get_page_permalink('shop'):home_url('/');
}

function bhb_page_url($path){
 $page=get_page_by_path($path);
 return ($page&&$page->post_status==='publish')?get_permalink($page):'';
}

function bhb_hex($value,$fallback){
 $color=sanitize_hex_color($value);
 return $color?:$fallback;
}

function bhb_products($args=[]){
 if(!function_exists('wc_get_products')) return [];
 $query=wp_parse_args($args,['status'=>'publish','limit'=>8,'orderby'=>'date','order'=>'DESC','return'=>'objects']);
 $policy='in_stock_first';
 $commerce=(array)get_option('bhc_options',[]);
 if(isset($commerce['stock_display_policy'])&&in_array($commerce['stock_display_policy'],['in_stock_first','in_stock_only','all'],true)) $policy=$commerce['stock_display_policy'];
 if($policy==='all'||isset($query['stock_status'])||!empty($query['return'])&&$query['return']!=='objects'){
  $items=wc_get_products($query);
  return is_array($items)?$items:[];
 }
 $limit=(int)($query['limit']??8);
 $first=$query; $first['stock_status']='instock';
 $in=wc_get_products($first);
 if($policy==='in_stock_only') return is_array($in)?$in:[];
 $in=is_array($in)?$in:[];
 if($limit<0||count($in)>=$limit) return $limit<0?$in:array_slice($in,0,$limit);
 $rest=$query; $rest['stock_status']='outofstock'; $rest['limit']=$limit<0?-1:max(0,$limit-count($in));
 $out=wc_get_products($rest); $out=is_array($out)?$out:[];
 return $limit<0?array_merge($in,$out):array_slice(array_merge($in,$out),0,$limit);
}

function bhb_product_by_source($source,$limit){
 $source=in_array($source,['latest','featured','sale','popularity'],true)?$source:'latest';
 $args=['limit'=>$limit,'status'=>'publish'];
 if($source==='featured'){$args['featured']=true;$args['orderby']='date';}
 elseif($source==='sale'){$args['on_sale']=true;}
 elseif($source==='popularity'){$args['orderby']='popularity';$args['order']='DESC';}
 $items=bhb_products($args);
 if(!$items&&$source!=='latest') $items=bhb_products(['limit'=>$limit]);
 return $items;
}

// Keep WooCommerce archive/category/search grids aligned with homepage rails.
// The admin policy is intentionally conservative: only stock ordering/filtering
// changes; category, brand, price and other merchant filters remain untouched.
add_action('woocommerce_product_query',function($query){
 if(is_admin()||!is_object($query)||!function_exists('is_shop')) return;
 $is_catalog=is_shop()||is_product_category()||is_product_taxonomy()||is_search();
 if(!$is_catalog) return;
 $policy='in_stock_first'; $opts=(array)get_option('bhc_options',[]);
 if(isset($opts['stock_display_policy'])&&in_array($opts['stock_display_policy'],['in_stock_first','in_stock_only','all'],true)) $policy=$opts['stock_display_policy'];
 if($policy==='all') return;
 $meta=(array)$query->get('meta_query');
 if($policy==='in_stock_only'){
  $meta[]=['key'=>'_stock_status','value'=>'instock','compare'=>'='];
  $query->set('meta_query',$meta);
  return;
 }
 $query->set('meta_key','_stock_status');
 $query->set('orderby',['meta_value'=>'ASC','date'=>'DESC']);
},30);

function bhb_product_image_is_verified($product){
 if(!$product||!is_object($product)) return false;
 $id=$product->get_id();
 $quality=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);
 $image_status=(string)get_post_meta($id,'_bhc_image_generation_status',true);
 if($quality==='reference_invalid'||in_array($image_status,['blocked_until_exact_model','blocked_until_valid_model'],true)) return false;
 if(!get_post_meta($id,'_bhc_source_url',true)) return true;
 // The catalog pipeline stores the approved, product-specific transparent
 // render as generated_4k_transparent_verified. Keep the legacy status too
 // so older approved records remain visible with their own image.
 $status=$image_status;
 return in_array($status,['generated_4k_transparent_verified','product_specific_generated','verified'],true);
}

function bhb_demo_product_image($product,$size='bh-product-card',$attrs=[]){
 if(!$product) return '';
 $terms=wp_get_post_terms($product->get_id(),'product_cat',['orderby'=>'parent','order'=>'ASC']);
 $term=(!is_wp_error($terms)&&$terms)?$terms[0]:null;
 $thumb=$term?absint(get_term_meta($term->term_id,'thumbnail_id',true)):0;
 $label=$term?'تصویر نمایشی دسته '.$term->name:'تصویر نمایشی محصول';
 $attrs=wp_parse_args($attrs,['class'=>'bhb-photo bhb-photo-demo','alt'=>$label,'decoding'=>'async','loading'=>'lazy']);
 $attrs['class']=trim(($attrs['class']??'').' bhb-photo-demo-media');
 $image=$thumb?wp_get_attachment_image($thumb,$size,false,$attrs):'';
 if(!$image) $image='<span class="bhb-photo-empty" role="img" aria-label="'.esc_attr($label).'">تصویر محصول در حال تکمیل</span>';
 return '<span class="bhb-demo-image" role="group" aria-label="'.esc_attr($label).'">'.$image.'<span class="bhb-demo-image-label">تصویر نمایشی · تصویر دقیق مدل در حال تکمیل</span></span>';
}

function bhb_img($product,$size='bh-product-card',$attrs=[]){
 if(!$product) return '';
 $attrs=wp_parse_args($attrs,['class'=>'bhb-photo','alt'=>$product->get_name(),'decoding'=>'async']);
 if(empty($attrs['loading'])) $attrs['loading']='lazy';
 if(!bhb_product_image_is_verified($product)) return bhb_demo_product_image($product,$size,$attrs);
 $home_id=absint(get_post_meta($product->get_id(),'_bhb_home_image',true));
 if($home_id) return wp_get_attachment_image($home_id,$size,false,$attrs);
 if(!$product->get_image_id()) return '<span class="bhb-photo bhb-photo-empty" role="img" aria-label="'.esc_attr($product->get_name()).'">بدون تصویر ثبت‌شده</span>';
 return $product->get_image($size,$attrs);
}

add_filter('woocommerce_product_get_image',function($image,$product,$size,$attr,$placeholder,$original){
 if($product&&!bhb_product_image_is_verified($product)) return bhb_demo_product_image($product,$size,(array)$attr);
 return $image;
},20,6);

function bhb_term_visual($term){
 $thumb=absint(get_term_meta($term->term_id,'thumbnail_id',true));
 if($thumb) return wp_get_attachment_image($thumb,'bh-product-card',false,['class'=>'bhb-photo','alt'=>esc_attr($term->name),'loading'=>'lazy','decoding'=>'async']);
 if(function_exists('wc_get_products')){
  $products=wc_get_products(['status'=>'publish','limit'=>1,'category'=>[$term->slug],'orderby'=>'date','order'=>'DESC']);
  if($products&&$products[0]->get_image_id()) return bhb_img($products[0],'bh-product-card');
 }
 return function_exists('bh_category_icon')?bh_category_icon($term->name):'';
}

function bhb_categories($limit=8){
 if(!taxonomy_exists('product_cat')) return [];
 $terms=get_terms(['taxonomy'=>'product_cat','hide_empty'=>true,'parent'=>0,'number'=>$limit,'exclude'=>[absint(get_option('default_product_cat'))]]);
 return is_wp_error($terms)?[]:$terms;
}

function bhb_brand_tax(){
 foreach(['product_brand','pa_brand','pwb-brand'] as $tax) if(taxonomy_exists($tax)) return $tax;
 return '';
}

function bhb_payload($product){
 if(class_exists('Baharaan_Commerce')) return Baharaan_Commerce::product_payload($product);
 $id=$product->get_id();
 return ['id'=>$id,'title'=>$product->get_name(),'url'=>get_permalink($id),'image'=>get_the_post_thumbnail_url($id,'woocommerce_thumbnail')?:'','price'=>wp_strip_all_tags($product->get_price_html()),'sku'=>$product->get_sku(),'specs'=>[]];
}

function bhb_sale_badge($product){
 if(!$product->is_on_sale()) return '';
 if($product->is_type('variable')||$product->is_type('grouped')) return '<span class="bhb-badge">قیمت ویژه</span>';
 $regular=(float)$product->get_regular_price();
 $sale=(float)$product->get_sale_price();
 if($regular>0&&$sale>0&&$sale<$regular){
  $percent=(int)round((1-($sale/$regular))*100);
  if($percent>0&&$percent<100) return '<span class="bhb-badge">'.esc_html($percent).'٪</span>';
 }
 return '<span class="bhb-badge">قیمت ویژه</span>';
}

function bhb_sale_until($product){
 if(!method_exists($product,'get_date_on_sale_to')) return '';
 $date=$product->get_date_on_sale_to();
 return $date&&function_exists('bh_jalali_date')?bh_jalali_date($date->getTimestamp(),true):'';
}

function bhb_render_section($key){
 $catalog=bhb_section_catalog();
 if(empty($catalog[$key]['part'])) return;
 get_template_part('template-parts/home/'.$catalog[$key]['part']);
}

function bhb_css_vars(){
 $studio=bhb_studio();
 $colors=wp_parse_args($studio['colors']??[],bhb_fallback_studio()['colors']);
 $navy=bhb_hex($colors['navy'],'#0B2039');
 $blue=bhb_hex($colors['blue'],'#1769D2');
 $ice=bhb_hex($colors['ice'],'#EAF3FF');
 $bg=bhb_hex($colors['bg'],'#F7F9FC');
 $accent=bhb_hex($colors['accent'],'#FF8A3D');
 $width=max(1100,min(1440,absint($studio['container_width']??1320)));
 $overflow=max(0,min(120,absint($studio['hero_overflow']??96)));
 echo '<style id="bhb-vars">:root{--bhb-navy:'.$navy.';--bhb-blue:'.$blue.';--bhb-ice:'.$ice.';--bhb-bg:'.$bg.';--bhb-accent:'.$accent.';--bhb-container:'.$width.'px;--bhb-hero-out:'.$overflow.'px;--bh-color-ink:'.$navy.';--bh-color-primary:'.$blue.';--bh-color-accent:'.$accent.';--bh-color-surface-blue:'.$ice.';}</style>';
}
add_action('wp_head','bhb_css_vars',20);

function bhb_body_class($classes){
 $classes[]='bhb-theme';
 if(empty(bhb_studio()['motion'])) $classes[]='bhb-motion-off';
 return $classes;
}
add_filter('body_class','bhb_body_class');

function bhb_assets(){
 $ver='1.3.0.blueprint.51';
 wp_enqueue_style('bh-vazirmatn','https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800;900&display=swap',[],null);
 wp_enqueue_style('bh-blueprint',get_template_directory_uri().'/assets/css/blueprint.css',['bh-design-system','bh-vazirmatn'],$ver);
 wp_enqueue_style('bh-wire',get_template_directory_uri().'/assets/css/wireframe.css',['bh-blueprint'],$ver);
 wp_enqueue_style('bh-shop',get_template_directory_uri().'/assets/css/shop.css',['bh-wire'],$ver.'.46');
 wp_enqueue_style('bh-premium-polish',get_template_directory_uri().'/assets/css/premium-polish.css',['bh-shop'],$ver.'.21');
 wp_enqueue_style('bh-brand-refresh',get_template_directory_uri().'/assets/css/brand-refresh.css',['bh-premium-polish'],$ver.'.59');
 if (is_shop() || is_product_category()) {
  wp_enqueue_style('bh-shop-layout',get_template_directory_uri().'/assets/css/shop-layout.css',['bh-brand-refresh'],$ver.'.3');
 }
    wp_enqueue_style('bh-mobile-header-lock',get_template_directory_uri().'/assets/css/mobile-header-lock.css',[(is_shop()||is_product_category())?'bh-shop-layout':'bh-brand-refresh'],$ver.'.8');
 wp_enqueue_style('bh-mobile-nav',get_template_directory_uri().'/assets/css/mobile-nav.css',['bh-mobile-header-lock'],$ver.'.2');
 wp_enqueue_style('bh-footer-pro',get_template_directory_uri().'/assets/css/footer-pro.css',['bh-mobile-nav'],$ver.'.13');
 wp_add_inline_style('bh-footer-pro','@media(max-width:640px){.bhp-card-media,.bhp-card-photo{height:168px!important;min-height:168px!important;max-height:168px!important;overflow:hidden!important;display:grid!important;place-items:center!important}.bhp-card-photo img,.bhp-card-media img{position:static!important;display:block!important;width:auto!important;height:108px!important;max-width:62%!important;max-height:108px!important;padding:0!important;margin:0!important;object-fit:contain!important;object-position:center!important;transform:none!important}}');
 if(function_exists('is_cart')&&is_cart()) wp_enqueue_style('bh-cart-final',get_template_directory_uri().'/assets/css/cart-final.css',['bh-mobile-header-lock'],$ver.'.11');
 if(!function_exists('is_cart')||(!is_cart()&&!function_exists('is_checkout')||!is_checkout()))wp_enqueue_script('bh-blueprint',get_template_directory_uri().'/assets/js/blueprint.js',[],$ver,true);
 if((is_front_page()||(function_exists('is_woocommerce')&&(is_woocommerce()||is_cart()||is_checkout())))&&wp_script_is('wc-add-to-cart','registered')) wp_enqueue_script('wc-add-to-cart');
}
add_action('wp_enqueue_scripts','bhb_assets',30);

function bhb_newsletter(){
 if(!isset($_POST['bhb_newsletter_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bhb_newsletter_nonce'])),'bhb_newsletter')){
  wp_safe_redirect(add_query_arg('bhb_news','invalid',home_url('/#bhb-newsletter')));
  exit;
 }
 $email=sanitize_email(wp_unslash($_POST['bhb_email']??''));
 $status='invalid';
 if($email&&is_email($email)){
  $list=get_option('bhb_newsletter_emails',[]);
  if(!is_array($list)) $list=[];
  $list=array_values(array_unique(array_merge($list,[$email])));
  update_option('bhb_newsletter_emails',array_slice($list,-500),false);
  $status='ok';
 }
 wp_safe_redirect(add_query_arg('bhb_news',$status,home_url('/#bhb-newsletter')));
 exit;
}
add_action('admin_post_nopriv_bhb_newsletter','bhb_newsletter');
add_action('admin_post_bhb_newsletter','bhb_newsletter');

function bhb_on_sale_query($query){
 if(is_admin()||!$query->is_main_query()||empty($_GET['on_sale'])) return;
 if(!function_exists('wc_get_product_ids_on_sale')) return;
 if(!(function_exists('is_shop')&&(is_shop()||is_product_taxonomy()))) return;
 $ids=wc_get_product_ids_on_sale();
 $query->set('post__in',$ids?:[0]);
}
add_action('pre_get_posts','bhb_on_sale_query',20);

function bhb_product_media_box(){
 add_meta_box('bhb-home-image','تصویر صفحه اول بهاران','bhb_product_media_box_html','product','normal','high');
}
add_action('add_meta_boxes','bhb_product_media_box');

function bhb_product_media_assets($hook){
 if(!in_array($hook,['post.php','post-new.php'],true)) return;
 $screen=function_exists('get_current_screen')?get_current_screen():null;
 if(!$screen||$screen->post_type!=='product') return;
 wp_enqueue_media();
}
add_action('admin_enqueue_scripts','bhb_product_media_assets');

function bhb_product_media_box_html($post){
 wp_nonce_field('bhb_home_image','bhb_home_image_nonce');
 $image_id=absint(get_post_meta($post->ID,'_bhb_home_image',true));
 $stored=trim((string)get_post_meta($post->ID,'_bhb_image_prompt',true));
 $prompt=$stored!==''?$stored:bhb_product_prompt($post->post_title);
 $preview=$image_id?wp_get_attachment_image($image_id,'medium',false,['style'=>'max-width:180px;height:auto;display:block;margin-top:8px']):'';
 ?>
 <p>این تصویر روی هیرو، بنرها و کارت‌های صفحه اول استفاده می‌شود. اگر خالی باشد، تصویر شاخص ووکامرس نشان داده می‌شود. بهتر است PNG شفاف و بدون جعبه باشد.</p>
 <input type="hidden" id="bhb-home-image" name="bhb_home_image" value="<?php echo esc_attr($image_id); ?>">
 <button type="button" class="button" id="bhb-home-image-pick">انتخاب تصویر صفحه اول</button>
 <button type="button" class="button" id="bhb-home-image-clear">حذف</button>
 <span id="bhb-home-image-preview"><?php echo $preview; ?></span>
 <p style="margin-top:14px"><strong>پرامپت ساخت عکس همین کالا</strong></p>
 <textarea id="bhb-image-prompt" name="bhb_image_prompt" rows="7" style="width:100%"><?php echo esc_textarea($prompt); ?></textarea>
 <p><button type="button" class="button" id="bhb-copy-prompt">کپی پرامپت</button> <span id="bhb-copy-state"></span></p>
 <script>
 document.getElementById('bhb-home-image-pick')?.addEventListener('click',function(){
  if(!wp.media) return;
  const frame=wp.media({title:'تصویر صفحه اول',multiple:false,library:{type:'image'}});
  frame.on('select',function(){
   const file=frame.state().get('selection').first().toJSON();
   document.getElementById('bhb-home-image').value=file.id||0;
   document.getElementById('bhb-home-image-preview').innerHTML=file.sizes&&file.sizes.medium?'<img src="'+file.sizes.medium.url+'" style="max-width:180px;height:auto" alt="">':'';
  });
  frame.open();
 });
 document.getElementById('bhb-home-image-clear')?.addEventListener('click',function(){
  document.getElementById('bhb-home-image').value='0';
  document.getElementById('bhb-home-image-preview').innerHTML='';
 });
 document.getElementById('bhb-copy-prompt')?.addEventListener('click',function(){
  const text=document.getElementById('bhb-image-prompt').value;
  navigator.clipboard.writeText(text).then(function(){document.getElementById('bhb-copy-state').textContent='کپی شد';});
 });
 </script>
 <?php
}

function bhb_save_product_media($post_id){
 if(!isset($_POST['bhb_home_image_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['bhb_home_image_nonce'])),'bhb_home_image')) return;
 if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) return;
 if(!current_user_can('edit_post',$post_id)) return;
 update_post_meta($post_id,'_bhb_home_image',absint($_POST['bhb_home_image']??0));
 update_post_meta($post_id,'_bhb_image_prompt',sanitize_textarea_field(wp_unslash($_POST['bhb_image_prompt']??'')));
}
add_action('save_post_product','bhb_save_product_media');

