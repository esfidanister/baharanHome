<?php
if (!defined('ABSPATH')) exit;
function bh_setup(){
 add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('custom-logo',['height'=>60,'width'=>250,'flex-height'=>true,'flex-width'=>true]);
 add_theme_support('html5',['search-form','comment-form','comment-list','gallery','caption','style','script']); add_theme_support('responsive-embeds');
 add_theme_support('woocommerce'); add_theme_support('wc-product-gallery-zoom'); add_theme_support('wc-product-gallery-lightbox'); add_theme_support('wc-product-gallery-slider');
 register_nav_menus(['primary'=>__('منوی اصلی','baharaan-home')]);
}
add_action('after_setup_theme','bh_setup');
function bh_assets(){
 $ver=wp_get_theme()->get('Version')?:'1.1.0';
 wp_enqueue_style('bh-style',get_stylesheet_uri(),[],$ver);
 wp_enqueue_style('bh-main',get_template_directory_uri().'/assets/css/main.css',['bh-style'],$ver);
 wp_enqueue_script('bh-main',get_template_directory_uri().'/assets/js/main.js',[],$ver,true);
 wp_enqueue_script('bh-search',get_template_directory_uri().'/assets/js/search.js',[],$ver,true);
 wp_localize_script('bh-search','BHSearch',['ajax'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('bhc_public'),'minimum'=>2]);
}
add_action('wp_enqueue_scripts','bh_assets');
function bh_register_sizes(){add_image_size('bh-product-card',520,520,false);add_image_size('bh-product-hero',1200,900,false);add_image_size('bh-product-thumb',144,144,true);}
add_action('after_setup_theme','bh_register_sizes');
function bh_favicon(){if(!has_site_icon())echo '<link rel="icon" href="'.esc_url(get_template_directory_uri().'/assets/images/favicon.svg').'" type="image/svg+xml">';}
add_action('wp_head','bh_favicon',2);
function bh_shop_loop_columns(){return 3;} add_filter('loop_shop_columns','bh_shop_loop_columns');
function bh_product_badges(){global $product;if(!$product)return;if($product->is_on_sale())echo '<span class="bh-badge bh-sale">تخفیف ثبت‌شده</span>';if(!$product->is_in_stock())echo '<span class="bh-badge bh-out">ناموجود</span>';}
add_action('woocommerce_before_shop_loop_item_title','bh_product_badges',8);
function bh_cart_count_fragment($fragments){if(function_exists('WC')&&WC()->cart)$fragments['.bh-cart b']='<b>'.intval(WC()->cart->get_cart_contents_count()).'</b>';return $fragments;}
add_filter('woocommerce_add_to_cart_fragments','bh_cart_count_fragment');
function bh_cart_fragments_script(){if(function_exists('WC'))wp_enqueue_script('wc-cart-fragments');}
add_action('wp_enqueue_scripts','bh_cart_fragments_script',30);
function bh_category_icon($name){
 $plain=wp_strip_all_tags((string)$name);$text=function_exists('mb_strtolower')?mb_strtolower($plain,'UTF-8'):strtolower($plain);
 $map=[
  'fridge'=>['یخچال','refrigerator','fridge'], 'laundry'=>['لباسشویی','washing'],
  'dishwasher'=>['ظرفشویی','dishwasher'], 'tv'=>['تلویزیون','tv','television'],
  'vacuum'=>['جاروبرقی','vacuum'], 'kitchen'=>['آشپز','cook','kitchen'],
  'small-appliance'=>['ساندویچ','اتو','سرخ‌کن','سرخ کن','مخلوط‌کن','چای‌ساز','خردکن','small appliance']
 ];$icon='small-appliance';foreach($map as $file=>$terms){foreach($terms as $term)if((function_exists('mb_strpos')?mb_strpos($text,(function_exists('mb_strtolower')?mb_strtolower($term,'UTF-8'):strtolower($term))):strpos($text,strtolower($term)))!==false){$icon=$file;break 2;}}
 return '<img class="bhx-cat-svg" src="'.esc_url(get_template_directory_uri().'/assets/icons/'.$icon.'.svg').'" alt="" loading="lazy" decoding="async">';
}

function bh_icon($name){$allowed=['delivery','shield','support','payment','compare','heart','cart','search','account','home'];if(!in_array($name,$allowed,true))return '';return '<img class="bh-icon bh-icon-'.esc_attr($name).'" src="'.esc_url(get_template_directory_uri().'/assets/icons/'.$name.'.svg').'" width="20" height="20" alt="" loading="lazy" decoding="async">';}
function bh_primary_menu(){
 wp_nav_menu(['theme_location'=>'primary','container'=>false,'fallback_cb'=>false,'menu_id'=>'bh-primary-menu','menu_class'=>'bh-primary-menu','depth'=>2]);
}
function bh_block_patterns(){
 if(!function_exists('register_block_pattern'))return;
 register_block_pattern_category('baharaan',['label'=>__('بهاران هوم','baharaan-home')]);
 register_block_pattern('baharaan/trust-note',['title'=>__('یادداشت اعتماد فروشگاه','baharaan-home'),'categories'=>['baharaan'],'content'=>'<!-- wp:group {"className":"bh-editorial-note","layout":{"type":"constrained"}} --><div class="wp-block-group bh-editorial-note"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">اطلاعات روشن، انتخاب مطمئن</h2><!-- /wp:heading --><!-- wp:paragraph --><p>شرایط ارسال، ضمانت و موجودی را فقط پس از تأیید فروشگاه در این بخش بنویسید.</p><!-- /wp:paragraph --></div><!-- /wp:group -->']);
 register_block_pattern('baharaan/contact-cta',['title'=>__('دعوت به ارتباط با فروشگاه','baharaan-home'),'categories'=>['baharaan'],'content'=>'<!-- wp:group {"className":"bh-contact-cta","layout":{"type":"constrained"}} --><div class="wp-block-group bh-contact-cta"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">برای انتخاب محصول راهنمایی می‌خواهید؟</h2><!-- /wp:heading --><!-- wp:paragraph --><p>راه ارتباطی و ساعت پاسخ‌گویی واقعی فروشگاه را در تنظیمات بهاران وارد کنید.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"url":"/contact/"} --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">تماس با فروشگاه</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->']);
}
add_action('init','bh_block_patterns');
