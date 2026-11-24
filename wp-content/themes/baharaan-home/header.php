<?php
if (!defined('ABSPATH')) exit;
$opts=get_option('bhc_options',[]);
$menu_opts=class_exists('Baharaan_Commerce')?Baharaan_Commerce::opts():$opts;
$mega_menu_enabled=array_key_exists('mega_menu_enabled',$menu_opts)?(bool)$menu_opts['mega_menu_enabled']:true;
$mega_menu_limit=max(4,min(10,absint($menu_opts['mega_menu_limit']??8)));
$mega_feature_id=absint($menu_opts['mega_menu_feature_product_id']??0);
$notice=trim((string)($opts['home_notice']??''));
$utility=$notice!==''?$notice:bhb_copy('utility','بهاران هوم، فروشگاه لوازم خانگی');
$shop=bhb_shop_url();
$account=function_exists('wc_get_page_permalink')?wc_get_page_permalink('myaccount'):wp_login_url();
$cart=function_exists('wc_get_cart_url')?wc_get_cart_url():$shop;
$count=function_exists('WC')&&WC()->cart?intval(WC()->cart->get_cart_contents_count()):0;
$parents=bhb_categories($mega_menu_limit);
$sale_ids=function_exists('wc_get_product_ids_on_sale')?wc_get_product_ids_on_sale():[];
$links=[];
foreach(['buying-guide'=>'راهنمای خرید','order-tracking'=>'پیگیری سفارش','contact'=>'پشتیبانی'] as $path=>$label){
 $url=bhb_page_url($path);
 if($url) $links[]='<a href="'.esc_url($url).'">'.esc_html($label).'</a>';
}
?><!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="bh-skip-link" href="#main-content">رفتن به محتوای اصلی</a>
<div class="bhb-top">
 <div class="bh-topline">
  <p><?php echo esc_html($utility); ?></p>
  <?php if($links): ?><nav class="bh-topline-links" aria-label="پیوندهای راهنما"><?php echo implode('<span aria-hidden="true">·</span>',$links); ?></nav><?php endif; ?>
 </div>
 <header class="bh-header">
  <a class="bh-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="صفحه اصلی بهاران هوم">
   <?php if(has_custom_logo()): the_custom_logo(); else: ?>
   <span class="bh-mark" aria-hidden="true"><svg viewBox="0 0 48 48" width="26" height="26"><path d="M8 21.5 24 8l16 13.5V38a3 3 0 0 1-3 3H11a3 3 0 0 1-3-3V21.5z" fill="none" stroke="#fff" stroke-width="2.6" stroke-linejoin="round"/><path d="M20 41V27h8v14" fill="none" stroke="#fff" stroke-width="2.6" stroke-linejoin="round"/></svg></span>
   <span class="bh-brand-name"><?php echo esc_html($menu_opts['brand_first']??'بهاران'); ?> <em><?php echo esc_html($menu_opts['brand_second']??'هوم'); ?></em></span>
   <?php endif; ?>
  </a>
  <form role="search" method="get" class="bh-search" action="<?php echo esc_url(home_url('/')); ?>" autocomplete="off">
   <label class="screen-reader-text" for="bh-product-search">جستجوی محصولات</label>
   <input id="bh-product-search" type="search" role="combobox" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="جستجو در محصولات و دسته‌ها" aria-label="جستجوی محصولات" aria-autocomplete="list" aria-controls="bh-search-results" aria-expanded="false" autocomplete="off">
   <input type="hidden" name="post_type" value="product">
   <button type="submit" aria-label="جستجو"><?php echo bh_icon('search'); ?></button>
   <div id="bh-search-results" class="bh-search-results" role="listbox" aria-label="نتایج جستجوی زنده" hidden></div>
  </form>
  <div class="bh-head-actions">
   <a class="bhb-icon-btn" href="<?php echo esc_url($account); ?>" aria-label="حساب کاربری"><?php echo bh_icon('account'); ?></a>
   <?php if(!empty($opts['show_wishlist'])): ?><button class="bhb-icon-btn" type="button" data-open-saved aria-label="علاقه‌مندی‌ها و مقایسه"><?php echo bh_icon('heart'); ?><b data-wish-count>۰</b></button><?php endif; ?>
   <a class="bh-cart bhb-icon-btn" href="<?php echo esc_url($cart); ?>" aria-label="سبد خرید"><?php echo bh_icon('cart'); ?><b><?php echo esc_html($count); ?></b></a>
  </div>
  <button class="bh-menu-toggle" type="button" aria-controls="bhb-drawer" aria-expanded="false" aria-label="باز کردن منو"><span class="bhb-burger-open" aria-hidden="true"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg></span><span class="bhb-burger-close" aria-hidden="true"><svg viewBox="0 0 24 24" width="20" height="20"><path d="M6 6l12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg></span><span class="bhb-burger-label">منو</span></button>
 </header>
 <nav class="bh-nav" aria-label="ناوبری اصلی">
  <div class="bh-nav-bar" id="bh-nav-bar">
   <details class="bhp-mega <?php echo $mega_menu_enabled?'':'is-disabled'; ?>">
    <summary><span aria-hidden="true">☷</span> دسته‌بندی محصولات</summary>
    <div class="bhp-mega-panel">
     <?php if($parents): ?>
     <div class="bhb-mega-cats">
      <?php foreach($parents as $i=>$term): ?>
      <a href="<?php echo esc_url(get_term_link($term)); ?>" data-mega="<?php echo esc_attr($term->term_id); ?>" class="bhb-mega-cat <?php echo $i===0?'is-current':''; ?>"><span class="bhb-mega-thumb"><?php echo bhb_term_visual($term); ?></span><span><b><?php echo esc_html($term->name); ?></b><small class="bhb-count"><?php echo esc_html(number_format_i18n($term->count)); ?> کالا</small></span></a>
      <?php endforeach; ?>
     </div>
     <div class="bhb-mega-links">
      <?php foreach($parents as $i=>$term):
       $children=get_terms(['taxonomy'=>'product_cat','hide_empty'=>true,'parent'=>$term->term_id,'number'=>9]);
       if(is_wp_error($children)) $children=[];
      ?>
      <div data-mega-panel="<?php echo esc_attr($term->term_id); ?>" <?php echo $i?'hidden':''; ?>>
       <a class="bhb-mega-all" href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a>
       <?php if($children): foreach($children as $child): ?><a href="<?php echo esc_url(get_term_link($child)); ?>"><?php echo esc_html($child->name); ?></a><?php endforeach; else:
        $samples=function_exists('wc_get_products')?wc_get_products(['status'=>'publish','limit'=>4,'category'=>[$term->slug],'orderby'=>'date','order'=>'DESC']):[];
        if($samples): foreach($samples as $sample): ?>
        <a class="bhb-mega-product" href="<?php echo esc_url(get_permalink($sample->get_id())); ?>"><?php echo bhb_img($sample,'thumbnail'); ?><span><?php echo esc_html($sample->get_name()); ?></span></a>
        <?php endforeach; else: ?><p class="bhb-mega-empty">در این دسته هنوز محصولی برای نمایش منتشر نشده است.</p><?php endif; ?>
        <a class="bhb-mega-all" href="<?php echo esc_url(get_term_link($term)); ?>">دیدن کالاهای <?php echo esc_html($term->name); ?></a>
       <?php endif; ?>
      </div>
      <?php endforeach; ?>
     </div>
     <div class="bhb-mega-feature">
      <?php $feature=$mega_feature_id&&function_exists('wc_get_product')?[wc_get_product($mega_feature_id)]:bhb_products(['limit'=>1]); $feature=array_values(array_filter((array)$feature)); if($feature): ?>
      <a href="<?php echo esc_url(get_permalink($feature[0]->get_id())); ?>"><?php echo bhb_img($feature[0],'bh-product-card'); ?><b>انتخابی از کاتالوگ</b><span><?php echo esc_html($feature[0]->get_name()); ?></span></a>
      <?php else: ?><p>برای این دسته هنوز محصولی برای نمایش منتشر نشده است.</p><?php endif; ?>
     </div>
     <?php else: ?>
     <p class="bhb-empty">دسته‌ای برای منو ثبت نشده است.</p>
     <?php endif; ?>
    </div>
   </details>
   <div class="bh-primary-menu" id="bh-primary-menu">
    <?php
    $menu_done=false;
    if(has_nav_menu('primary')){
     wp_nav_menu(['theme_location'=>'primary','container'=>false,'fallback_cb'=>false,'menu_class'=>'bhb-menu','depth'=>2]);
     $menu_done=true;
    }
    if(!$menu_done){
     echo '<ul class="bhb-menu">';
     foreach(array_slice($parents,0,5) as $term) echo '<li><a href="'.esc_url(get_term_link($term)).'">'.esc_html($term->name).'</a></li>';
     $journal=bhb_page_url('blog')?:(get_option('page_for_posts')?get_permalink(get_option('page_for_posts')):'');
     if($journal) echo '<li><a href="'.esc_url($journal).'">مجله بهاران</a></li>';
     echo '</ul>';
    }
    ?>
   </div>
   <?php if($sale_ids): ?><a class="bhb-nav-chip" href="<?php echo esc_url(add_query_arg('on_sale','1',$shop)); ?>">فروش ویژه</a><?php endif; ?>
  </div>
 </nav>
</div>
<div class="bhb-drawer" id="bhb-drawer" hidden>
 <div class="bhb-drawer-head">
  <strong><?php echo esc_html($menu_opts['menu_label']??'دسته‌بندی محصولات'); ?></strong>
  <button type="button" class="bhb-drawer-close" aria-label="بستن منو">×</button>
 </div>
 <nav aria-label="منوی موبایل">
  <?php if($parents): foreach($parents as $term): ?>
  <a class="bhb-drawer-cat" href="<?php echo esc_url(get_term_link($term)); ?>"><span class="bhb-mega-thumb"><?php echo bhb_term_visual($term); ?></span><span><b><?php echo esc_html($term->name); ?></b><small class="bhb-count"><?php echo esc_html(number_format_i18n($term->count)); ?> کالا</small></span></a>
  <?php endforeach; else: ?><p>دسته‌ای ثبت نشده است.</p><?php endif; ?>
  <a href="<?php echo esc_url($shop); ?>">فروشگاه</a>
  <a href="<?php echo esc_url($account); ?>">حساب کاربری</a>
  <a href="<?php echo esc_url($cart); ?>">سبد خرید<?php if($count): ?> (<?php echo esc_html(number_format_i18n($count)); ?>)<?php endif; ?></a>
 </nav>
</div>
<main id="main-content">
