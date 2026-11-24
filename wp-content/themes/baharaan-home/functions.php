<?php
if (!defined('ABSPATH')) exit;
require_once get_template_directory().'/inc/blueprint.php';
function bh_setup(){
 add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('custom-logo',['height'=>60,'width'=>250,'flex-height'=>true,'flex-width'=>true]);
 add_theme_support('html5',['search-form','comment-form','comment-list','gallery','caption','style','script']); add_theme_support('responsive-embeds');
 add_theme_support('woocommerce'); add_theme_support('wc-product-gallery-zoom'); add_theme_support('wc-product-gallery-lightbox'); add_theme_support('wc-product-gallery-slider');
 register_nav_menus(['primary'=>__('منوی اصلی','baharaan-home')]);
}
add_action('after_setup_theme','bh_setup');
/* Visible dates use the Persian calendar; ISO values remain Gregorian for SEO and processing. */
function bh_fa_digits($value){return strtr((string)$value,['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹']);}
function bh_jalali_date($timestamp,$with_time=false){
 $timestamp=absint($timestamp);if(!$timestamp)return '';$tz=function_exists('wp_timezone')?wp_timezone():new DateTimeZone('Asia/Tehran');$dt=(new DateTimeImmutable('@'.$timestamp))->setTimezone($tz);$gy=(int)$dt->format('Y');$gm=(int)$dt->format('n');$gd=(int)$dt->format('j');
 if(function_exists('bhc_gregorian_to_jalali'))[$jy,$jm,$jd]=bhc_gregorian_to_jalali($gy,$gm,$gd);else[$jy,$jm,$jd]=[$gy,$gm,$gd];
 $out=sprintf('%04d/%02d/%02d',$jy,$jm,$jd);if($with_time)$out.=' · '.sprintf('%02d:%02d',(int)$dt->format('H'),(int)$dt->format('i'));return bh_fa_digits($out);
}
function bh_jalali_post_date($post_id=0,$with_time=false){$post_id=$post_id?:get_the_ID();$ts=get_post_timestamp($post_id,'date');return $ts?bh_jalali_date($ts,$with_time):'';}
add_filter('get_the_date',function($date,$format,$post){$format=(string)$format;if(in_array($format,['c','r','U','Y-m-d','Y-m-d H:i:s'],true))return $date;$ts=get_post_timestamp($post,'date');return $ts?bh_jalali_date($ts):$date;},20,3);
add_filter('get_the_modified_date',function($date,$format,$post){$format=(string)$format;if(in_array($format,['c','r','U','Y-m-d','Y-m-d H:i:s'],true))return $date;$ts=get_post_timestamp($post,'modified');return $ts?bh_jalali_date($ts):$date;},20,3);
/* Admin-only presentation adapter for legacy option records that store ISO
 * timestamps. Raw values remain unchanged; only visible chat/audit dates are
 * converted to the Persian calendar on GET requests. */
function bh_admin_persianize_dates($value){
 if(!is_admin()||($_SERVER['REQUEST_METHOD']??'GET')!=='GET')return $value;
 $walk=function($item)use(&$walk){
  if(!is_array($item))return $item;
  foreach($item as $key=>$child){
   if(is_array($child)){$item[$key]=$walk($child);continue;}
   if(!is_string($child)||!preg_match('/(?:^|_)(?:at|date|seen|checked|reviewed|updated|created)(?:$|_)/i',(string)$key))continue;
   if(preg_match('/^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}(?::\d{2})?)?$/',$child)){$ts=strtotime($child);if($ts)$item[$key]=bh_jalali_date($ts,true);}
  }
  return $item;
 };
 return $walk($value);
}
foreach(['bhc_chat_unanswered','bhc_chat_kb','bhc_audit_log','bhc_image_reference_import_last'] as $bh_admin_date_option)add_filter('option_'.$bh_admin_date_option,'bh_admin_persianize_dates',20);
function bh_assets(){
 $ver=(wp_get_theme()->get('Version')?:'1.1.0').'.20261123.1';
 wp_enqueue_style('bh-style',get_stylesheet_uri(),[],$ver);
 wp_enqueue_style('bh-design-system',get_template_directory_uri().'/assets/css/design-system.css',['bh-style'],$ver.'.5');
 wp_enqueue_style('bh-main-layout',get_template_directory_uri().'/assets/css/main.css',['bh-design-system'],$ver.'.7');
 // The blueprint asset loader owns the final shop.css enqueue so it remains
 // after blueprint.css and wireframe.css and can safely normalize their mobile rules.
 wp_enqueue_script('bh-main',get_template_directory_uri().'/assets/js/main.js',[],$ver.'.6',true);
 wp_enqueue_script('bh-navigation',get_template_directory_uri().'/assets/js/navigation.js',['bh-main'],$ver.'.18',true);
 wp_enqueue_script('bh-filter-validation',get_template_directory_uri().'/assets/js/filter-validation.js',[],$ver.'.3',true);
 if(function_exists('is_product')&&is_product())wp_enqueue_script('bh-product',get_template_directory_uri().'/assets/js/product.js',['bh-main'],$ver.'.10',true);
 wp_enqueue_script('bh-search',get_template_directory_uri().'/assets/js/search.js',[],$ver.'.4',true);
 if(function_exists('is_cart')&&is_cart())wp_enqueue_script('bh-cart',get_template_directory_uri().'/assets/js/cart.js',[],$ver.'.11',true);
 wp_localize_script('bh-search','BHSearch',['ajax'=>wp_make_link_relative(admin_url('admin-ajax.php')),'nonce'=>wp_create_nonce('bhc_public'),'minimum'=>2]);
}
add_action('wp_enqueue_scripts','bh_assets');
function bh_register_sizes(){add_image_size('bh-product-card',520,520,false);add_image_size('bh-product-hero',1200,900,false);add_image_size('bh-product-thumb',144,144,true);}
add_action('after_setup_theme','bh_register_sizes');
function bh_register_asset_sizes(){add_image_size('bh-banner-desktop',1600,560,true);add_image_size('bh-banner-mobile',900,900,true);add_image_size('bh-editorial',1200,760,true);}
add_action('after_setup_theme','bh_register_asset_sizes');
function bh_picture($desktop_id,$mobile_id=0,$alt='',$class=''){if(!$desktop_id)return ''; $desktop=wp_get_attachment_image_src(absint($desktop_id),'bh-banner-desktop');$mobile=$mobile_id?wp_get_attachment_image_src(absint($mobile_id),'bh-banner-mobile'):false;if(!$desktop)return ''; $srcset=wp_get_attachment_image_srcset(absint($desktop_id),'bh-banner-desktop');$out='<picture class="'.esc_attr($class).'">';if($mobile)$out.='<source media="(max-width: 640px)" srcset="'.esc_attr($mobile[0]).'">';$out.='<img src="'.esc_url($desktop[0]).'"'.($srcset?' srcset="'.esc_attr($srcset).'" sizes="100vw"':'').' alt="'.esc_attr($alt).'" loading="lazy" decoding="async" width="'.intval($desktop[1]).'" height="'.intval($desktop[2]).'">';return $out.'</picture>';}
function bh_studio_options(){return function_exists('bhc_studio_opts')?bhc_studio_opts():[];}
function bh_cart_options(){return wp_parse_args(get_option('bh_cart_settings',[]),['steps'=>1,'trust'=>1,'coupon'=>1,'sticky'=>1,'recommendations'=>1,'recommendation_count'=>4,'empty_title'=>'سبد خرید شما خالی است','empty_text'=>'محصولات منتخب خود را از فروشگاه پیدا کنید و برای خرید به سبد اضافه کنید.']);}
add_filter('body_class',function($classes){if(function_exists('is_cart')&&is_cart()&&!bh_cart_options()['sticky'])$classes[]='bh-cart-sticky-off';return $classes;});

function bh_cart_admin_page(){if(class_exists('Baharan_Home_Dashboard_Settings')){Baharan_Home_Dashboard_Settings::render_cart_settings();return;}if(!current_user_can('manage_options'))return;}
add_action('admin_menu',function(){add_submenu_page('bh-dashboard','تنظیمات سبد خرید','تنظیمات سبد خرید','manage_options','bh-cart-settings','bh_cart_admin_page');},40);
function bh_studio_sections($fallback){$s=bh_studio_options();if(empty($s['order']))return $fallback;$map=['hero'=>'hero','categories'=>'categories','quick-entry'=>'quick-entry','bento'=>'bento','featured'=>'featured','best-sellers'=>'best-sellers','brands'=>'brands','editorial'=>'editorial','journal'=>'journal','service'=>'service','campaign'=>'campaign'];$out=[];foreach((array)$s['order'] as $key){if(isset($map[$key])&&in_array($key,(array)($s['enabled']??[]),true))$out[]=$map[$key];}return $out?:$fallback;}
function bh_favicon(){if(!has_site_icon())echo '<link rel="icon" href="'.esc_url(get_template_directory_uri().'/assets/images/favicon.svg').'" type="image/svg+xml">';}
add_action('wp_head','bh_favicon',2);
function bh_has_seo_plugin(){return defined('WPSEO_VERSION')||defined('RANK_MATH_VERSION')||class_exists('AIOSEO\\Plugin\\Common\\Main');}
function bh_public_catalog_copy($text){
 if(!is_string($text)||$text==='')return $text;
 $text=preg_replace('~<h[2-6][^>]*>\\s*(?:منبع|منابع|منبع بررسی|منبع دقیق مدل|منبع مدل)[^<]*</h[2-6]>\\s*(?:<p[^>]*>[\\s\\S]*?</p>)?~iu','',$text);
 // Editorial evidence is for internal QA only; never expose source notes or
 // external reference links in the public product/catalog copy.
 $text=preg_replace('~<p[^>]*>(?:(?!</p>)[\s\S])*(?:منبع بررسی‌شده|منبع بررسی|منبع دقیق مدل|صفحه رسمی(?: سازنده| مدل)?|لینک منبع|منبع مدل)(?:(?!</p>)[\s\S])*</p>~iu','',$text);
 $text=preg_replace('~<a[^>]+href=["\'][^"\']*(?:https?://|www\.)[^"\']*["\'][^>]*>(?:(?!</a>)[\s\S])*(?:منبع|صفحه رسمی|مشخصات مدل|مدل دقیق)(?:(?!</a>)[\s\S])*</a>~iu','',$text);
 return strtr($text,[
  'مشخصات فنی از منبع مدل استخراج شده'=>'مشخصات فنی ثبت‌شده در کاتالوگ فروشگاه',
  'از اطلاعات ثبت‌شده برای مدل استخراج شده'=>'با اطلاعات ثبت‌شده برای این مدل تنظیم شده',
  'از منبع مدل استخراج شده'=>'بر اساس اطلاعات ثبت‌شده در کاتالوگ فروشگاه تنظیم شده',
 ]);
}
add_filter('the_content','bh_public_catalog_copy',25);
add_filter('woocommerce_short_description','bh_public_catalog_copy',25);
function bh_meta_description(){
 if(function_exists('is_shop')&&is_shop())return 'فروشگاه بهاران هوم؛ جست‌وجو و مقایسه لوازم خانگی از برندهای منتخب با مشخصات، تصاویر و وضعیت موجودی قابل بررسی.';
 if(is_singular('post')){$custom=bh_public_catalog_copy(get_post_meta(get_queried_object_id(),'_bhc_seo_description',true));if($custom)return wp_trim_words(wp_strip_all_tags($custom),32,'…');$post_title=get_the_title(get_queried_object_id());if(mb_stripos($post_title,'یخچال')!==false||mb_stripos($post_title,'فریزر')!==false)return 'راهنمای کامل نگهداری یخچال و فریزر؛ تنظیم دما، چیدمان مواد غذایی، نظافت لاستیک درب و کندانسور، کاهش مصرف برق و تشخیص زمان مناسب تعمیر.';}
 if(function_exists('is_product')&&is_product()&&function_exists('wc_get_product')){$custom=bh_public_catalog_copy(get_post_meta(get_queried_object_id(),'_bhc_seo_description',true));if($custom)return wp_trim_words(wp_strip_all_tags($custom),32,'…');$p=wc_get_product(get_queried_object_id());if($p){$text=bh_public_catalog_copy($p->get_short_description()?:$p->get_description());return wp_trim_words(wp_strip_all_tags($text),30,'…');}}
 if(function_exists('is_product_category')&&is_product_category()){$term=get_queried_object();$raw=term_description();if(!$raw&&!is_wp_error($term)){$count=number_format_i18n((int)$term->count);$raw='خرید و بررسی '.$term->name.' از کاتالوگ بهاران هوم؛ مشاهده مدل‌ها، برندها، مشخصات فنی و وضعیت موجودی '.$count.' محصول این دسته.';}return wp_trim_words(wp_strip_all_tags($raw),30,'…');}
 if(function_exists('is_account_page')&&is_account_page())return 'مدیریت سفارش‌ها، پیگیری خرید، ویرایش آدرس‌ها و تنظیمات حساب کاربری در بهاران هوم.';
 if(function_exists('is_cart')&&is_cart())return 'سبد خرید بهاران هوم؛ بررسی کالاهای انتخاب‌شده، تعداد، مبلغ و ادامه فرایند خرید.';
 if(function_exists('is_checkout')&&is_checkout())return 'تکمیل سفارش در بهاران هوم؛ ثبت اطلاعات ارسال و انتخاب روش پرداخت امن.';
 if(is_front_page()){$studio=function_exists('bhc_studio_opts')?bhc_studio_opts():[];$seo=(array)($studio['seo']??[]);if(trim((string)($seo['site_meta_description']??''))!=='')return wp_trim_words(wp_strip_all_tags($seo['site_meta_description']),30,'…');$o=get_option('bhc_options',[]);return wp_trim_words(wp_strip_all_tags($o['hero_subtitle']??get_bloginfo('description')),30,'…');}
 if(is_singular())return wp_trim_words(wp_strip_all_tags(get_the_excerpt()),30,'…');
 return wp_trim_words(wp_strip_all_tags(get_bloginfo('description')),30,'…');
}
function bh_seo_document_title($title){if(is_singular('post')){$custom=get_post_meta(get_queried_object_id(),'_bhc_seo_title',true);if($custom)return sanitize_text_field($custom);}return $title;}
add_filter('pre_get_document_title','bh_seo_document_title',20);
function bh_seo_head(){
 if(bh_has_seo_plugin())return;
 $description=bh_meta_description();$title=wp_get_document_title();$url=home_url('/');
 if(is_singular())$url=get_permalink(get_queried_object_id());elseif(function_exists('is_shop')&&is_shop())$url=wc_get_page_permalink('shop');elseif(function_exists('is_product_category')&&is_product_category()){$term=get_queried_object();$url=is_wp_error($term)?$url:get_term_link($term);}
 $image=is_singular()?get_the_post_thumbnail_url(get_queried_object_id(),'large'):'';
 if($description)echo '<meta name="description" content="'.esc_attr($description).'" />'.PHP_EOL;
 echo '<meta property="og:title" content="'.esc_attr($title).'" />'.PHP_EOL.'<meta property="og:url" content="'.esc_url($url).'" />'.PHP_EOL.'<meta property="og:type" content="'.esc_attr(function_exists('is_product')&&is_product()?'product':'website').'" />'.PHP_EOL;
 if($description)echo '<meta property="og:description" content="'.esc_attr($description).'" />'.PHP_EOL;
 if($image)echo '<meta property="og:image" content="'.esc_url($image).'" />'.PHP_EOL;
 echo '<meta name="twitter:card" content="summary_large_image" />'.PHP_EOL;
}
add_action('wp_head','bh_seo_head',3);
function bh_product_structured_data(){if(!function_exists('is_product')||!is_product()||bh_has_seo_plugin()||!function_exists('wc_get_product'))return;$p=wc_get_product(get_queried_object_id());if(!$p)return;$brand='';if(function_exists('bhb_brand_rows'))foreach(bhb_brand_rows() as $row){foreach(array_filter([$row['name']??'',$row['latin']??'']) as $needle){if($needle!==''&&function_exists('mb_stripos')&&mb_stripos($p->get_name(),$needle)!==false){$brand=$row['latin']?:$row['name'];break 2;}}}$data=['@context'=>'https://schema.org','@type'=>'Product','name'=>$p->get_name(),'url'=>get_permalink($p->get_id()),'description'=>wp_trim_words(wp_strip_all_tags(bh_public_catalog_copy($p->get_short_description()?:$p->get_description())),55,'…'),'sku'=>$p->get_sku()?:null,'image'=>array_values(array_filter([$p->get_image_id()?wp_get_attachment_image_url($p->get_image_id(),'large'):null])), 'brand'=>$brand?['@type'=>'Brand','name'=>$brand]:null];if(!$data['sku'])unset($data['sku']);if(!$data['brand'])unset($data['brand']);if($p->is_purchasable()&&$p->get_price()!=='')$data['offers']=['@type'=>'Offer','url'=>get_permalink($p->get_id()),'priceCurrency'=>get_woocommerce_currency(),'price'=>(string)$p->get_price(),'availability'=>$p->is_in_stock()?'https://schema.org/InStock':'https://schema.org/OutOfStock','itemCondition'=>'https://schema.org/NewCondition'];if($p->get_review_count()>0)$data['aggregateRating']=['@type'=>'AggregateRating','ratingValue'=>(string)$p->get_average_rating(),'reviewCount'=>(string)$p->get_review_count()];echo '<script type="application/ld+json">'.wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'.PHP_EOL;$faq=get_post_meta($p->get_id(),'_bhc_faq',true);if($faq){$items=[];foreach(preg_split('/\r\n|\r|\n/',$faq) as $line){$pair=array_map('trim',explode('|',$line,2));if(!empty($pair[0])&&!empty($pair[1]))$items[]=['@type'=>'Question','name'=>$pair[0],'acceptedAnswer'=>['@type'=>'Answer','text'=>wp_strip_all_tags($pair[1])]];}if($items)echo '<script type="application/ld+json">'.wp_json_encode(['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$items],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'.PHP_EOL;}}
add_action('wp_head','bh_product_structured_data',5);
function bh_studio_schema_gate(){if(function_exists('is_product')&&is_product()&&function_exists('bhc_studio_opts')){$studio=bhc_studio_opts();if(isset($studio['seo']['product_schema_enabled'])&&!$studio['seo']['product_schema_enabled'])remove_action('wp_head','bh_product_structured_data',5);}}
add_action('wp_head','bh_studio_schema_gate',4);
function bh_editorial_structured_data(){
 if(!is_singular('post')||bh_has_seo_plugin())return;
 $id=get_queried_object_id();$description=get_post_meta($id,'_bhc_seo_description',true)?:wp_trim_words(wp_strip_all_tags(get_the_excerpt()),35,'…');
 $data=['@context'=>'https://schema.org','@type'=>'Article','headline'=>get_the_title($id),'description'=>wp_strip_all_tags($description),'mainEntityOfPage'=>get_permalink($id),'datePublished'=>get_the_date('c',$id),'dateModified'=>get_the_modified_date('c',$id),'author'=>['@type'=>'Organization','name'=>'بهاران هوم'],'publisher'=>['@type'=>'Organization','name'=>'بهاران هوم']];
 $image=get_post_thumbnail_id($id)?wp_get_attachment_image_url(get_post_thumbnail_id($id),'large'):'';if($image)$data['image']=[$image];
 echo '<script type="application/ld+json">'.wp_json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'.PHP_EOL;
}
add_action('wp_head','bh_editorial_structured_data',6);
function bh_preload_lcp(){if(!is_front_page()||!class_exists('WooCommerce')||!function_exists('wc_get_products'))return;$opts=class_exists('Baharaan_Commerce')?Baharaan_Commerce::opts():get_option('bhc_options',[]);$id=absint($opts['hero_product_id']??0);$product=$id?wc_get_product($id):null;if(!$product){$pick=wc_get_products(['status'=>'publish','featured'=>true,'limit'=>1]);$product=$pick[0]??null;}if(!$product){$pick=wc_get_products(['status'=>'publish','limit'=>1,'orderby'=>'date','order'=>'DESC']);$product=$pick[0]??null;}if(!$product)return;$src=wp_get_attachment_image_src($product->get_image_id(),'bh-product-hero');if($src&&$src[0])echo '<link rel="preload" as="image" href="'.esc_url($src[0]).'" fetchpriority="high">'.PHP_EOL;}
add_action('wp_head','bh_preload_lcp',1);
function bh_product_context(){if(!function_exists('is_product')||!is_product()||!function_exists('wc_get_product'))return;$product=wc_get_product(get_queried_object_id());if(!$product)return;$brand='';$latin='';if(function_exists('bhb_brand_rows'))foreach(bhb_brand_rows() as $row){foreach(array_filter([$row['name']??'',$row['latin']??'']) as $needle){if($needle!==''&&function_exists('mb_stripos')&&mb_stripos($product->get_name(),$needle)!==false){$brand=$row['name'];$latin=$row['latin'];break 2;}}}echo '<div class="bhp-context-bar">';if($brand)echo '<div class="bhp-brand-context"><span><b>'.esc_html($brand).'</b>'.($latin?' <small><bdi>'.esc_html($latin).'</bdi></small>':'').'</span></div>';echo '<div class="bhp-trust-strip"><span>✓ اصالت و مشخصات قابل بررسی</span><span>↗ ارسال با هماهنگی فروشگاه</span><span>✦ پشتیبانی قبل از خرید</span></div></div>';}
add_action('woocommerce_before_single_product','bh_product_context',12);
function bh_product_purchase_facts(){if(!function_exists('is_product')||!is_product()||!function_exists('wc_get_product'))return;$p=wc_get_product(get_queried_object_id());if(!$p)return;$facts=[];if($p->get_sku())$facts[]=['عنوان'=>'مدل / شناسه','مقدار'=>$p->get_sku()];if($p->has_weight())$facts[]=['عنوان'=>'وزن','مقدار'=>wc_format_weight($p->get_weight())];if($p->has_dimensions())$facts[]=['عنوان'=>'ابعاد','مقدار'=>wc_format_dimensions($p->get_dimensions(false))];$attrs=$p->get_attributes();foreach($attrs as $a){$values=$a->is_taxonomy()?wc_get_product_terms($p->get_id(),$a->get_name(),['fields'=>'names']):$a->get_options();if($values){$facts[]=['عنوان'=>wc_attribute_label($a->get_name()),'مقدار'=>implode('، ',array_map('sanitize_text_field',$values))];if(count($facts)>=6)break;}}$extra=array_values(array_filter($facts,function($fact){return ($fact['عنوان']??'')!=='مدل / شناسه';}));if(!$extra)return;echo '<div class="bhp-buy-facts" aria-label="خلاصه اطلاعات خرید"><h2>اطلاعات مهم پیش از خرید</h2><div class="bhp-buy-facts-grid">';foreach($extra as $fact)echo '<div><small>'.esc_html($fact['عنوان']).'</small><b>'.esc_html($fact['مقدار']).'</b></div>';echo '</div></div>';}
add_action('woocommerce_before_add_to_cart_form','bh_product_purchase_facts',8);
function bh_shop_loop_columns(){return 3;} add_filter('loop_shop_columns','bh_shop_loop_columns');
function bh_shop_products_per_page($count){if(function_exists('is_woocommerce')&&is_woocommerce()&&function_exists('bhc_studio_opts')){$o=bhc_studio_opts();return absint($o['archive']['products_per_page']??$count)?:$count;}return $count;} add_filter('loop_shop_per_page','bh_shop_products_per_page');
function bh_shop_archive_filters($query){
 if(is_admin()||!$query->is_main_query()||!function_exists('is_shop')||(!is_shop()&&!is_product_category()))return;
 if(sanitize_key($_GET['filter_stock_status']??'')==='instock'){
  $meta=(array)$query->get('meta_query');$meta[]=['key'=>'_stock_status','value'=>'instock','compare'=>'='];$query->set('meta_query',$meta);
 }
 if((string)($_GET['on_sale']??'')==='1'||(string)($_GET['filter_sale']??'')==='1'){
  $meta=(array)$query->get('meta_query');$meta[]=['key'=>'_sale_price','value'=>'','compare'=>'!='];$query->set('meta_query',$meta);
 }
 if(taxonomy_exists('pa_brand')&&!empty($_GET['filter_pa_brand'])){
  $raw_brands=(array)wp_unslash($_GET['filter_pa_brand']);
  // Persian term slugs can arrive once more URL-encoded from older forms.
  // Normalize both forms before building the taxonomy query.
  $brands=array_values(array_filter(array_map(function($brand){
   $brand=(string)$brand;
   for($i=0;$i<2&&strpos($brand,'%')!==false;$i++){$decoded=rawurldecode($brand);if($decoded===$brand)break;$brand=$decoded;}
   return sanitize_title($brand);
  },$raw_brands)));
  if($brands){$tax=(array)$query->get('tax_query');$tax[]=['taxonomy'=>'pa_brand','field'=>'slug','terms'=>$brands,'operator'=>'IN'];$query->set('tax_query',$tax);}
 }
}
add_action('pre_get_posts','bh_shop_archive_filters',20);
function bh_translate_shop_title($title){return $title==='Shop'?'فروشگاه':$title;} add_filter('woocommerce_page_title','bh_translate_shop_title');
function bh_product_badges(){global $product;if(!$product)return;if($product->is_on_sale())echo '<span class="bh-badge bh-sale">تخفیف ثبت‌شده</span>';if(!$product->is_in_stock())echo '<span class="bh-badge bh-out">ناموجود</span>';}
add_action('woocommerce_before_shop_loop_item_title','bh_product_badges',8);
function bh_cart_count_fragment($fragments){if(function_exists('WC')&&WC()->cart)$fragments['.bh-cart b']='<b>'.intval(WC()->cart->get_cart_contents_count()).'</b>';return $fragments;}
add_filter('woocommerce_add_to_cart_fragments','bh_cart_count_fragment');
function bh_cart_fragments_script(){if(function_exists('WC')&&(!function_exists('is_cart')||(!is_cart()&&!function_exists('is_checkout')||!is_checkout())))wp_enqueue_script('wc-cart-fragments');}
add_action('wp_enqueue_scripts','bh_cart_fragments_script',30);
function bh_category_icon($name){
 $plain=wp_strip_all_tags((string)$name);$text=function_exists('mb_strtolower')?mb_strtolower($plain,'UTF-8'):strtolower($plain);
 $map=[
  'fridge'=>['یخچال','refrigerator','fridge'],
  'laundry'=>['لباسشویی','washing','شست'],
  'dishwasher'=>['ظرفشویی','dishwasher'],
  'tv'=>['تلویزیون','tv','television','تصویر','صوتی'],
  'vacuum'=>['جاروبرقی','vacuum','نظافت'],
  'kitchen'=>['آشپز','cook','kitchen'],
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
/* Resolve WooCommerce product query links safely when the site uses plain permalinks. */
function bh_product_id_from_request($value){
 global $wpdb;
 $raw=trim((string)wp_unslash($value));
 if($raw==='')return 0;
 $decoded=urldecode(rawurldecode(str_replace('+',' ',$raw)));
 $candidates=array_values(array_unique([$raw,$decoded,rawurlencode($decoded),sanitize_title($decoded)]));
 $placeholders=implode(',',array_fill(0,count($candidates),'%s'));
 $args=array_merge(['publish','product'], $candidates, $candidates);
 $sql="SELECT ID FROM {$wpdb->posts} WHERE post_status=%s AND post_type=%s AND (post_name IN ($placeholders) OR post_title IN ($placeholders)) ORDER BY ID DESC LIMIT 1";
 return (int)$wpdb->get_var($wpdb->prepare($sql,$args));
}
function bh_resolve_plain_product_link($vars){
 if(empty($vars['product'])||!empty($vars['p']))return $vars;
 $id=bh_product_id_from_request($vars['product']);
 if($id){unset($vars['product']);$vars['p']=$id;$vars['post_type']='product';}
 return $vars;
}
add_filter('request','bh_resolve_plain_product_link',5);
function bh_product_parse_request($wp){
 if(is_admin()||empty($wp->query_vars['product'])||!empty($wp->query_vars['p']))return;
 $id=bh_product_id_from_request($wp->query_vars['product']);
 if($id){unset($wp->query_vars['product']);$wp->query_vars['p']=$id;$wp->query_vars['post_type']='product';}
}
add_action('parse_request','bh_product_parse_request',1);
function bh_redirect_legacy_product_query(){
 if(empty($_GET['product'])||is_admin())return;
 $id=bh_product_id_from_request($_GET['product']);
 if(!$id)return;
 $permalink=get_permalink($id);
 /* With plain permalinks the query URL is the canonical URL; redirecting would loop. */
 if(get_option('permalink_structure') && $permalink && false===strpos($permalink,'?product=')){wp_safe_redirect($permalink,301);exit;}
}
add_action('template_redirect','bh_redirect_legacy_product_query',1);
function bh_product_query_compat($query){
 if(is_admin()||!$query->is_main_query()||empty($_GET['product']))return;
 $id=bh_product_id_from_request($_GET['product']);
 if($id){$query->set('p',$id);$query->set('post_type','product');$query->is_single=true;$query->is_404=false;}
}
add_action('pre_get_posts','bh_product_query_compat',1);
function bh_canonical(){
 if(bh_has_seo_plugin()||is_404()||is_search()||is_singular())return;
 $url='';
 if(is_front_page())$url=home_url('/');
 elseif(function_exists('is_product')&&is_product())$url=get_permalink(get_queried_object_id());
 elseif(function_exists('is_shop')&&is_shop())$url=wc_get_page_permalink('shop');
 elseif(function_exists('is_product_category')&&is_product_category()){$term=get_queried_object();$url=is_wp_error($term)?'':get_term_link($term);}
 elseif(function_exists('is_cart')&&is_cart())$url=wc_get_cart_url();
 elseif(is_singular())$url=get_permalink(get_queried_object_id());
 if($url&&!is_wp_error($url))echo '<link rel="canonical" href="'.esc_url($url).'" />'.PHP_EOL;
}
add_action('wp_head','bh_canonical',4);
function bh_cart_meta(){
 if(!function_exists('is_cart')||!is_cart()||bh_has_seo_plugin())return;
 $url=wc_get_cart_url();
 $title='سبد خرید – '.get_bloginfo('name');
 $description='سبد خرید بهاران هوم؛ محصولات انتخاب‌شده را بررسی کنید و برای تکمیل خرید به مرحله پرداخت بروید.';
 echo '<meta name="description" content="'.esc_attr($description).'" />'.PHP_EOL;
 echo '<meta property="og:site_name" content="'.esc_attr(get_bloginfo('name')).'" />'.PHP_EOL;
 echo '<meta property="og:title" content="'.esc_attr($title).'" />'.PHP_EOL;
 echo '<meta property="og:description" content="'.esc_attr($description).'" />'.PHP_EOL;
 echo '<meta property="og:url" content="'.esc_url($url).'" />'.PHP_EOL;
 echo '<meta property="og:type" content="website" />'.PHP_EOL;
 echo '<meta name="twitter:title" content="'.esc_attr($title).'" />'.PHP_EOL;
 echo '<meta name="twitter:description" content="'.esc_attr($description).'" />'.PHP_EOL;
}
add_action('wp_head','bh_cart_meta',5);
function bh_robots($robots){
 if(is_search()||is_404()||(!empty($_GET)&&((function_exists('is_shop')&&is_shop())||(function_exists('is_product_category')&&is_product_category())))){$robots['noindex']=true;$robots['follow']=true;}
 return $robots;
}
add_filter('wp_robots','bh_robots');
function bh_cart_item_thumbnail($thumbnail,$cart_item,$cart_item_key){
 if(!function_exists('wc_get_product'))return $thumbnail;
 $product=$cart_item['data']??null;
 if(!$product||!is_a($product,'WC_Product'))return $thumbnail;
 $image_id=$product->get_image_id();
 if($image_id&&wp_attachment_is_image($image_id)&&($file=get_attached_file($image_id))&&is_readable($file))return $thumbnail;
 $placeholder=function_exists('wc_placeholder_img_src')?wc_placeholder_img_src():'';
 return $placeholder?'<img src="'.esc_url($placeholder).'" alt="'.esc_attr($product->get_name()).'" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" loading="lazy" decoding="async">':'';
}
add_filter('woocommerce_cart_item_thumbnail','bh_cart_item_thumbnail',20,3);
add_filter('woocommerce_cart_is_empty_message',function(){return '';},20);
add_action('woocommerce_before_cart',function(){if(!bh_cart_options()['steps'])return;echo '<nav class="bh-cart-steps" aria-label="مراحل خرید"><span class="is-current"><b aria-hidden="true">۱</b> سبد خرید</span><i aria-hidden="true"></i><span><b aria-hidden="true">۲</b> تسویه حساب</span><i aria-hidden="true"></i><span><b aria-hidden="true">۳</b> تکمیل سفارش</span></nav>';},5);
add_action('woocommerce_after_cart',function(){if(!bh_cart_options()['trust'])return;echo '<section class="bh-cart-trust" aria-label="خدمات فروشگاه"><div><b aria-hidden="true">▣</b><strong>ارسال سراسری</strong><small>پیگیری وضعیت سفارش</small></div><div><b aria-hidden="true">✓</b><strong>پرداخت امن</strong><small>با درگاه معتبر</small></div><div><b aria-hidden="true">◇</b><strong>اصالت کالا</strong><small>اطلاعات واقعی محصول</small></div><div><b aria-hidden="true">☎</b><strong>پشتیبانی</strong><small>راهنمایی قبل از خرید</small></div></section>';},20);
add_filter('render_block_woocommerce/cart',function($content,$block){
 if(!is_cart())return $content;
 $o=bh_cart_options();
 if(!empty($o['steps']))$steps='<nav class="bh-cart-steps" aria-label="مراحل خرید"><span class="is-current"><b aria-hidden="true">۱</b> سبد خرید</span><i aria-hidden="true"></i><span><b aria-hidden="true">۲</b> تسویه حساب</span><i aria-hidden="true"></i><span><b aria-hidden="true">۳</b> تکمیل سفارش</span></nav>'; else $steps='';
 $trust=!empty($o['trust'])?'<section class="bh-cart-trust bh-cart-trust--block" aria-label="خدمات فروشگاه"><div><b aria-hidden="true">▣</b><strong>ارسال سراسری</strong><small>پیگیری وضعیت سفارش</small></div><div><b aria-hidden="true">✓</b><strong>پرداخت امن</strong><small>با درگاه معتبر</small></div><div><b aria-hidden="true">◇</b><strong>اصالت کالا</strong><small>اطلاعات واقعی محصول</small></div><div><b aria-hidden="true">☎</b><strong>پشتیبانی</strong><small>راهنمایی قبل از خرید</small></div></section>':'';
 if(!empty(WC()->cart)&&WC()->cart->is_empty()){$empty='<section class="bh-cart-empty-guide bh-cart-empty-guide--block" aria-labelledby="bh-cart-empty-title"><div class="bh-cart-empty-guide__art" aria-hidden="true"><svg viewBox="0 0 160 120"><path d="M20 28h18l12 58h72l15-42H47" fill="none" stroke="currentColor" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="65" cy="103" r="7" fill="currentColor"/><circle cx="115" cy="103" r="7" fill="currentColor"/><path d="M79 16h28M93 2v28" stroke="currentColor" stroke-width="7" stroke-linecap="round"/></svg></div><h2 id="bh-cart-empty-title">'.esc_html($o['empty_title']).'</h2><p class="bh-cart-empty-guide__intro">'.esc_html($o['empty_text']).'</p><a class="bh-cart-empty-guide__primary" href="'.esc_url(wc_get_page_permalink('shop')).'">بازگشت به فروشگاه <span aria-hidden="true">←</span></a></section>';}else $empty='';
 return $steps.$empty.$content.$trust;
},20,2);
add_action('woocommerce_cart_is_empty',function(){
 if(!function_exists('wc_get_cart_url'))return;
 echo '<section class="bh-cart-empty-guide" aria-labelledby="bh-cart-empty-guide-title"><div class="bh-cart-empty-guide__art" aria-hidden="true"><svg viewBox="0 0 160 120"><path d="M20 28h18l12 58h72l15-42H47" fill="none" stroke="currentColor" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="65" cy="103" r="7" fill="currentColor"/><circle cx="115" cy="103" r="7" fill="currentColor"/><path d="M79 16h28M93 2v28" stroke="currentColor" stroke-width="7" stroke-linecap="round"/></svg></div>';
 echo '<h2 id="bh-cart-empty-guide-title">سبد خرید شما خالی است</h2>';
 echo '<p class="bh-cart-empty-guide__intro">محصولات را بر اساس دسته‌بندی یا مشخصات واقعی بررسی کنید و بعد به سبد برگردید.</p>';
 echo '<div class="bh-cart-empty-guide__actions">';
 echo '<a class="bh-cart-empty-guide__primary" href="'.esc_url(wc_get_page_permalink('shop')).'">مشاهده فروشگاه <span aria-hidden="true">←</span></a>';
 echo '<a class="bh-cart-empty-guide__secondary" href="'.esc_url(home_url('/my-account/')).'">ورود به حساب کاربری</a>';
 echo '</div>';
 echo '<ul class="bh-cart-empty-guide__benefits" aria-label="راهنمای ادامه خرید">';
 echo '<li><span aria-hidden="true">✓</span><b>مقایسه قبل از خرید</b><small>مشخصات و قیمت محصولات را بررسی کنید.</small></li>';
 echo '<li><span aria-hidden="true">⌕</span><b>جست‌وجوی سریع</b><small>از جست‌وجوی بالای صفحه برای پیدا کردن کالا استفاده کنید.</small></li>';
 echo '<li><span aria-hidden="true">?</span><b>نیاز به راهنمایی دارید؟</b><small>راهنمای خرید پایین صفحه در دسترس است.</small></li>';
 echo '</ul></section>';
},20);
add_action('login_enqueue_scripts',function(){wp_enqueue_style('bh-login-font',get_template_directory_uri().'/assets/css/design-system.css',[], '1.0.0');wp_add_inline_style('bh-login-font','body.login{background:radial-gradient(circle at 15% 15%,#dceeff 0 12%,transparent 32%),linear-gradient(145deg,#f5f8fc,#eaf3ff);font-family:inherit;direction:rtl}body.login:before{content:"";position:fixed;width:360px;height:360px;border:1px solid #1769d21c;border-radius:50%;left:-130px;bottom:-160px;box-shadow:0 0 0 28px #1769d20a,0 0 0 56px #1769d206;animation:bhLoginFloat 8s ease-in-out infinite;pointer-events:none}body.login #login{position:relative;z-index:1;width:min(420px,calc(100% - 32px));padding:7vh 0 30px}body.login h1 a{width:230px;height:72px;margin:0 auto 22px;background-image:none!important;display:grid;place-items:center;color:#0b2039!important;font-size:30px;font-weight:850;text-indent:0;text-decoration:none}body.login h1 a:after{content:"بهاران هوم"}body.login form{margin-top:0;padding:30px;border:1px solid #ffffffaa;border-radius:24px;background:#fffffff2;box-shadow:0 18px 48px #0b20391a;backdrop-filter:blur(14px);animation:bhLoginIn .45s ease both}body.login label{color:#315473;font-size:12px;font-weight:700}body.login input[type=text],body.login input[type=password],body.login input[type=number]{min-height:46px;margin:7px 0 18px;border:1px solid #cbddec;border-radius:10px;background:#fbfdff;box-shadow:none;font-size:15px;direction:ltr;text-align:left}body.login input:focus{border-color:#1769d2;box-shadow:0 0 0 3px #1769d21c}body.login .button-primary{min-height:44px;padding:0 20px;border:0;border-radius:10px;background:linear-gradient(135deg,#1769d2,#0b55b8);box-shadow:0 8px 18px #1769d22b;font-weight:750;transition:transform .18s ease,box-shadow .18s ease}body.login .button-primary:hover{transform:translateY(-2px);box-shadow:0 12px 24px #1769d238}.bh-login-challenge{margin:3px 0 15px;padding:13px;border:1px solid #d5e5f4;border-radius:13px;background:#f1f7fd}.bh-login-challenge span{display:block;color:#1769d2;font-size:10px;font-weight:800;margin-bottom:5px}.bh-login-challenge label{display:block;color:#0b2039!important;font-size:14px!important}.bh-login-challenge input{width:100%;box-sizing:border-box;margin:7px 0 0!important}.bh-login-challenge input[type=hidden]{display:none}body.login .forgetmenot{margin-top:12px}body.login #nav,body.login #backtoblog{text-align:center;margin-top:14px}body.login #nav a,body.login #backtoblog a{color:#1769d2;text-decoration:none;font-size:12px}body.login .language-switcher{margin-top:22px}body.login .language-switcher select{border:1px solid #cbddec;border-radius:9px;min-height:38px}body.login .message,body.login #login_error{border:0;border-radius:12px;box-shadow:none;font-size:12px;line-height:1.8}body.login #login_error{border-right:4px solid #d85b52;background:#fff0ed;color:#8f382f}body.login .message{border-right:4px solid #1769d2;background:#eef5ff;color:#315473}@keyframes bhLoginIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}@keyframes bhLoginFloat{50%{transform:translate(18px,-12px)}}@media(prefers-reduced-motion:reduce){body.login:before,body.login form{animation:none}}@media(max-width:480px){body.login #login{padding-top:28px}body.login h1 a{font-size:26px}body.login form{padding:22px}}');});
add_filter('login_headerurl',function(){return home_url('/');});add_filter('login_headertext',function(){return 'بهاران هوم';});add_action('login_init',function(){if(function_exists('nocache_headers'))nocache_headers();});add_action('login_enqueue_scripts',function(){wp_enqueue_style('bh-login-cache-bust',get_template_directory_uri().'/assets/css/design-system.css',[], '1.0.1');});
add_action('login_form',function(){if(!isset($_GET['action'])||sanitize_key($_GET['action'])!=='register'){$a=random_int(2,9);$b=random_int(1,8);$token=wp_generate_password(20,false,false);set_transient('bh_login_challenge_'.hash('sha256',$token),$a+$b,10*MINUTE_IN_SECONDS);echo '<div class="bh-login-challenge"><span>سؤال کوتاه برای ورود</span><label for="bh-login-answer">'.$a.' + '.$b.' = ؟</label><input id="bh-login-answer" name="bh_login_answer" type="number" inputmode="numeric" autocomplete="off" required><input type="hidden" name="bh_login_token" value="'.esc_attr($token).'" /></div>';}});
add_filter('authenticate',function($user,$username,$password){if(!isset($_POST['bh_login_token']))return $user;$token=sanitize_text_field(wp_unslash($_POST['bh_login_token']));$answer=absint($_POST['bh_login_answer']??-1);$key='bh_login_challenge_'.hash('sha256',$token);$expected=get_transient($key);delete_transient($key);if($expected===false||$answer!==(int)$expected)return new WP_Error('bh_login_challenge','پاسخ سؤال ورود درست نیست یا منقضی شده است.');return $user;},30,3);
add_action('lostpassword_form',function(){echo '<div class="bh-recovery-intro"><span>بازیابی امن حساب</span><p>ایمیل حساب خود را وارد کنید تا لینک ساخت رمز جدید برای شما ارسال شود.</p></div>';});
add_action('login_head',function(){echo '<style>.bh-recovery-intro{margin:0 0 16px;padding:13px 15px;border:1px solid #d5e5f4;border-radius:13px;background:#f1f7fd}.bh-recovery-intro span{display:block;color:#1769d2;font-size:11px;font-weight:800}.bh-recovery-intro p{margin:5px 0 0;color:#607692;font-size:12px;line-height:1.8}</style>';});
add_action('wp_enqueue_scripts',function(){if(function_exists('is_account_page')&&is_account_page()){wp_enqueue_script('bh-account-auth',get_template_directory_uri().'/assets/js/account-auth.js',[], '1.0.4',true);}});
/* Keep the account surface isolated and deterministic: this is intentionally
 * enqueued after every shared shop/polish stylesheet. */
add_action('wp_enqueue_scripts',function(){
 if(function_exists('is_account_page')&&is_account_page()){
  wp_enqueue_style('bh-account-final',get_template_directory_uri().'/assets/css/account-final.css',[],'1.0.4');
 }
},100);
add_action('woocommerce_order_details_after_order_table',function($order){if(!$order||!is_a($order,'WC_Order'))return;$status=$order->get_status();$steps=['pending'=>'در انتظار بررسی','processing'=>'در حال آماده‌سازی','completed'=>'تحویل‌شده'];$keys=array_keys($steps);$current=array_search($status,$keys,true);if($current===false)$current=$status==='cancelled'?-1:1;echo '<section class="bh-order-timeline" aria-label="وضعیت سفارش"><h3>پیگیری وضعیت سفارش</h3><ol>';foreach($steps as $key=>$label){$index=array_search($key,$keys,true);$class=$index<$current?'is-done':($index===$current?'is-current':'');echo '<li class="'.esc_attr($class).'"><span>'.($index<$current?'✓':($index===$current?'•':'')).'</span><b>'.esc_html($label).'</b></li>';}echo '</ol>';if($status==='cancelled')echo '<p class="bh-order-note is-danger">این سفارش لغو شده است. برای راهنمایی با پشتیبانی تماس بگیرید.</p>';elseif($status==='on-hold')echo '<p class="bh-order-note">سفارش شما ثبت شده و پس از تأیید پرداخت آماده‌سازی می‌شود.</p>';echo '</section>';},10,1);
add_action('woocommerce_account_dashboard',function(){
 if(!is_user_logged_in())return;
 $orders=function_exists('wc_get_orders')?wc_get_orders(['customer_id'=>get_current_user_id(),'limit'=>5,'orderby'=>'date','order'=>'DESC','return'=>'objects']):[];$processing=0;$spent=0;
 foreach($orders as $order){$spent+=(float)$order->get_total();if(in_array($order->get_status(),['processing','on-hold'],true))$processing++;}
 echo '<section class="bh-account-welcome"><div><span class="bh-account-eyebrow">BAHARAN HOME / ACCOUNT</span><h2>خوش آمدید، '.esc_html(wp_get_current_user()->display_name?:'همراه بهاران').'</h2><p>سفارش‌ها، آدرس‌ها و اطلاعات حساب خود را سریع و ساده مدیریت کنید.</p></div><a class="bh-account-primary" href="'.esc_url(wc_get_endpoint_url('shop')).'">ادامه خرید <span>←</span></a></section><section class="bh-account-metrics" aria-label="خلاصه حساب"><article><b>'.esc_html(number_format_i18n(count($orders))).'</b><span>سفارش‌های اخیر</span></article><article><b>'.esc_html(number_format_i18n($processing)).'</b><span>در حال پیگیری</span></article><article><b>'.esc_html(function_exists('wc_price')?wp_strip_all_tags(wc_price($spent)):'—').'</b><span>جمع خریدهای اخیر</span></article></section><section class="bh-account-shortcuts"><a href="'.esc_url(wc_get_endpoint_url('orders')).'"><b>▣</b><span>سفارش‌های من</span><small>مشاهده و پیگیری</small></a><a href="'.esc_url(wc_get_endpoint_url('edit-address')).'"><b>⌖</b><span>آدرس‌ها</span><small>مدیریت ارسال و صورتحساب</small></a><a href="'.esc_url(wc_get_endpoint_url('edit-account')).'"><b>◌</b><span>اطلاعات حساب</span><small>ویرایش مشخصات ورود</small></a></section>';
});

/* Convert user-facing ISO timestamps (including WooCommerce account dates)
 * to Persian calendar text without changing the machine-readable datetime. */
add_action('wp_footer',function(){ ?>
<script>
(function(){
  if(!window.Intl||!Intl.DateTimeFormat)return;
  var formatter=new Intl.DateTimeFormat('fa-IR-u-ca-persian',{year:'numeric',month:'2-digit',day:'2-digit',hour:'2-digit',minute:'2-digit'});
  document.querySelectorAll('time[datetime]').forEach(function(node){
    var value=node.getAttribute('datetime');
    if(!value||node.dataset.persianRendered==='1')return;
    var date=new Date(value);
    if(isNaN(date.getTime()))return;
    node.textContent=formatter.format(date).replace('،',' ·');
    node.dataset.persianRendered='1';
  });
})();
</script>
<?php });
add_action('woocommerce_before_customer_login_form',function(){echo '<section class="bh-account-guest"><span class="bh-account-eyebrow">BAHARAN HOME / SECURE ACCOUNT</span><h2>برای مدیریت سفارش‌ها وارد حساب خود شوید</h2><p>سفارش‌ها، آدرس‌های ارسال و اطلاعات حساب شما در یک فضای امن و ساده در دسترس است.</p><div class="bh-account-guest-points"><span>✓ پیگیری سفارش</span><span>✓ مدیریت آدرس‌ها</span><span>✓ خرید سریع‌تر</span></div></section>';});
// Legacy product-category alias: «شستشو» was merged into the canonical «شست‌وشو» term.
add_action('template_redirect',function(){
 $path=rawurldecode((string)parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH));
 if(str_contains($path,'/product-category/شستشو')){
  $target=get_term_link(18,'product_cat');
  if(!is_wp_error($target)){wp_safe_redirect($target,301);exit;}
 }
});
// Recoverable redirects for duplicate product URLs consolidated in catalog audit v617.
add_action('template_redirect',function(){
 $path=rawurldecode((string)parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH));
 $redirects=[
  '/product/%d8%aa%d9%84%d9%88%db%8c%d8%b2%db%8c%d9%88%d9%86-%d8%b3%d9%88%d9%86%db%8c-%d9%85%d8%af%d9%84-k-85s30-%d8%b3%d8%a7%db%8c%d8%b2-%db%b8%db%b5-%d8%a7%db%8c%d9%86%da%86'=>477,
  '/product/%d8%aa%d9%84%d9%88%db%8c%d8%b2%db%8c%d9%88%d9%86-%d8%b3%d9%88%d9%86%db%8c-%d9%85%d8%af%d9%84-k-75s30-%d8%b3%d8%a7%db%8c%d8%b2-%db%b7%db%b5-%d8%a7%db%8c%d9%86%da%86'=>476,
  '/product/%d8%aa%d9%84%d9%88%db%8c%d8%b2%db%8c%d9%88%d9%86-%d8%b3%d9%88%d9%86%db%8c-%d9%85%d8%af%d9%84-k-55s30-%d8%b3%d8%a7%db%8c%d8%b2-%db%b5%b5-%d8%a7%db%8c%d9%86%da%86'=>474,
  '/product/%db%8c%d8%ae%da%86%d8%a7%d9%84%d9%81%d8%b1%db%8c%d8%b2%d8%b1-%d8%af%d9%88%d9%82%d9%84%d9%88-%d8%a7%d9%85%d8%b1%d8%b3%d8%a7%d9%86-%d9%85%d8%af%d9%84-rh20d-%d9%88-fn20d-%d8%b8%d8%b1%d9%81%db%8c'=>1480,
  '/product/%d9%85%d8%a7%d8%b4%db%8c%d9%86-%d8%b8%d8%b1%d9%81%d8%b4%d9%88%db%8c%db%8c-%d9%be%d8%a7%da%a9%d8%b4%d9%88%d9%85%d8%a7-%d9%85%d8%af%d9%84-j52-x-%d8%b8%d8%b1%d9%81%db%8c%d8%aa-%db%b1%db%b5-%d9%86%d9%81'=>145,
  '/product/%d9%85%d8%a7%d8%b4%db%8c%d9%86-%d8%b8%d8%b1%d9%81%d8%b4%d9%88%db%8c%db%8c-%d9%be%d8%a7%da%a9%d8%b4%d9%88%d9%85%d8%a7-%d9%85%d8%af%d9%84-e53-w-%d8%b8%d8%b1%d9%81%db%8c%d8%aa-%db%b1%db%b5-%d9%86%d9%81'=>976,
  '/product/%d8%b8%d8%b1%d9%81%d8%b4%d9%88%db%8c%db%8c-%d9%be%d8%a7%da%a9%d8%b4%d9%88%d9%85%d8%a7-%d9%85%d8%af%d9%84-e53w'=>976,
  '/product/%d9%85%d8%a7%d8%b4%db%8c%d9%86-%d9%84%d8%a8%d8%a7%d8%b3%d8%b4%d9%88%db%8c%db%8c-%d9%be%d8%a7%da%a9%d8%b4%d9%88%d9%85%d8%a7-%d9%85%d8%af%d9%84-q9-w-%d8%b8%d8%b1%d9%81%db%8c%d8%aa-%db%b9-%da%a9%db%8c'=>138,
  '/product/%d8%b5%d9%88%d8%aa%db%8c-%d9%88-%d8%aa%d8%b5%d9%88%db%8c%d8%b1%db%8c-%d8%b3%d9%88%d9%86%db%8c-%d9%85%d8%af%d9%84-ht-a5000'=>1032,
  '/product/%d8%b5%d9%88%d8%aa%db%8c-%d9%88-%d8%aa%d8%b5%d9%88%db%8c%d8%b1%db%8c-%d8%b3%d9%88%d9%86%db%8c-%d9%85%d8%af%d9%84-ht-s40r'=>1028,
  '/product/%d9%84%d8%a8%d8%a7%d8%b3%d8%b4%d9%88%db%8c%db%8c-%d9%be%d8%a7%da%a9%d8%b4%d9%88%d9%85%d8%a7-%d9%85%d8%af%d9%84-s9w'=>973,
 ];
 $path=urldecode($path);
 foreach($redirects as $encoded=>$id){
  if(urldecode($encoded)===$path){$target=get_permalink($id);if($target){wp_safe_redirect($target,301);exit;}}
 }
});
