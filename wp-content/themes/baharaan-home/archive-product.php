<?php get_header();
$shop=function_exists('wc_get_page_permalink')?wc_get_page_permalink('shop'):home_url('/');
$cats=taxonomy_exists('product_cat')?get_terms(['taxonomy'=>'product_cat','hide_empty'=>true,'parent'=>0]):[];
if(is_wp_error($cats)) $cats=[];
$current=is_product_category()?get_queried_object_id():0;
$term=$current?get_term($current,'product_cat'):null;
$children=$current?get_terms(['taxonomy'=>'product_cat','hide_empty'=>true,'parent'=>$current,'number'=>12]):[];
if(is_wp_error($children)) $children=[];
$term_image=$term&&!is_wp_error($term)?absint(get_term_meta($term->term_id,'thumbnail_id',true)):0;
$term_description=$term&&!is_wp_error($term)?term_description($term->term_id,'product_cat'):'';
$term_count=$term&&!is_wp_error($term)?(int)$term->count:0;
$hero_product_image_id=0;
if(!$term_image&&$term&&!is_wp_error($term)){
 $hero_ids=get_posts(['post_type'=>'product','post_status'=>'publish','posts_per_page'=>1,'orderby'=>'date','order'=>'DESC','fields'=>'ids','no_found_rows'=>true,'tax_query'=>[['taxonomy'=>'product_cat','field'=>'term_id','terms'=>[$term->term_id]]]]);
 $hero_product_image_id=!empty($hero_ids[0])?absint(get_post_thumbnail_id($hero_ids[0])):0;
}
$archive_opts=function_exists('bhc_studio_opts')?bhc_studio_opts()['archive']:['show_banner'=>1,'show_filters'=>1,'show_sale_filter'=>1,'show_rating'=>1,'show_stock'=>1,'columns'=>3,'card_radius'=>28];
?>
<div class="bhp-shop wrap">
 <?php if($archive_opts['show_banner']&&$term&&!is_wp_error($term)): ?>
 <nav class="bhp-mobile-breadcrumb" aria-label="مسیر صفحه"><?php woocommerce_breadcrumb(); ?></nav>
 <section class="bhp-category-hero" aria-labelledby="bhp-category-title">
  <div class="bhp-category-hero__copy">
   <nav class="bhp-breadcrumb" aria-label="مسیر صفحه"><?php woocommerce_breadcrumb(); ?></nav>
   <span class="bhx-overline">دسته‌بندی محصولات</span>
   <h1 id="bhp-category-title"><?php echo esc_html($term->name); ?></h1>
   <?php if($term_description): ?><div class="bhp-category-hero__description"><?php echo wp_kses_post(wpautop($term_description)); ?></div><?php else: ?><p class="bhp-category-hero__description">انتخابی از محصولات واقعی این دسته با قیمت و موجودی به‌روز فروشگاه.</p><?php endif; ?>
   <strong class="bhp-category-hero__count"><?php echo esc_html(number_format_i18n($term_count)); ?> محصول</strong>
  </div>
  <div class="bhp-category-hero__media">
   <?php if($term_image): echo wp_get_attachment_image($term_image,'large',false,['loading'=>'eager','fetchpriority'=>'high','class'=>'bhp-category-hero__image','alt'=>$term->name]); elseif($hero_product_image_id): echo wp_get_attachment_image($hero_product_image_id,'bh-product-hero',false,['loading'=>'eager','fetchpriority'=>'high','class'=>'bhp-category-hero__image','alt'=>$term->name]); else: ?><div class="bhp-category-hero__fallback" aria-hidden="true"><?php echo bh_category_icon($term->name); ?></div><?php endif; ?>
  </div>
 </section>
 <?php endif; ?>
 <header class="bhp-shop-head">
  <div>
   <?php if(!$term): ?><nav class="bhp-breadcrumb" aria-label="مسیر صفحه"><?php woocommerce_breadcrumb(); ?></nav><h1><?php woocommerce_page_title(); ?></h1><?php endif; ?>
   <?php /* Category copy is rendered in the hero above. The Shop page content
      is intentionally not printed here: legacy product-specific notes must
      never leak into the storefront archive. */ ?>
  </div>
  <div class="bhp-shop-count"><?php woocommerce_result_count(); ?></div>
 </header>
 <?php if (!$term && !is_paged() && $archive_opts['show_banner']): ?>
 <div class="bh-shop-showcase">
  <div class="bh-shop-cover"><h2>لوازم خانگی برای خانه شما</h2><p>مدل‌ها، مشخصات و موجودی را کنار هم ببینید.</p><a href="#shop-products">مشاهده محصولات</a></div>
  <nav aria-label="دسته‌های منتخب"><?php foreach (['ماشین لباسشویی'=>'washing','یخچال و فریزر'=>'refrigerator','تلویزیون'=>'television'] as $name=>$image):
   $group=get_term_by('name',$name,'product_cat'); if (!$group) continue;
   ?><a href="<?php echo esc_url(get_term_link($group)); ?>"><img alt="" src="<?php echo esc_url(get_template_directory_uri().'/assets/images/catalog/'.$image.'.webp'); ?>"><b><?php echo esc_html($name); ?></b></a><?php endforeach; ?></nav>
 </div>
 <?php endif; ?>
 <?php $rail_cats=$children?:$cats; if($rail_cats): ?>
 <div class="bhp-cat-rail" aria-label="دسته‌های فروشگاه">
  <a class="<?php echo $current?'':'is-current'; ?>" href="<?php echo esc_url($shop); ?>">همه دسته‌ها</a>
  <?php foreach($rail_cats as $cat): $link=get_term_link($cat); if(is_wp_error($link)) continue; $keep_query=[]; foreach(['orderby','s','min_price','max_price','filter_stock_status','filter_sale','on_sale','filter_pa_brand'] as $query_key) if(isset($_GET[$query_key])) $keep_query[$query_key]=wp_unslash($_GET[$query_key]); if($keep_query)$link=add_query_arg($keep_query,$link); ?>
  <a class="<?php echo (int)$cat->term_id===$current?'is-current':''; ?>" href="<?php echo esc_url($link); ?>">
   <span><?php echo bhb_term_visual($cat); ?></span>
   <?php echo esc_html($cat->name); ?>
   <small><?php echo esc_html(number_format_i18n($cat->count)); ?></small>
  </a>
  <?php endforeach; ?>
 </div>
 <?php endif; ?>
 <?php
 $price_bounds=function_exists('wc_get_min_max_price')?wc_get_min_max_price():['min_price'=>0,'max_price'=>0];
 $raw_price_min=array_key_exists('min',$price_bounds)?$price_bounds['min']:($price_bounds['min_price']??0);
 $raw_price_max=array_key_exists('max',$price_bounds)?$price_bounds['max']:($price_bounds['max_price']??0);
 $price_min=max(0,(int)floor((float)$raw_price_min));
 $price_max=max($price_min,(int)ceil((float)$raw_price_max));
 $price_has_data=$price_max>$price_min&&$price_max>0;
 $active=[];
 if($price_has_data&&isset($_GET['min_price'])&&$_GET['min_price']!=='') $active['min_price']='از '.number_format_i18n(absint($_GET['min_price']));
 if($price_has_data&&isset($_GET['max_price'])&&$_GET['max_price']!=='') $active['max_price']='تا '.number_format_i18n(absint($_GET['max_price']));
 if(($_GET['filter_stock_status']??'')==='instock') $active['filter_stock_status']='فقط موجود';
 if((string)($_GET['on_sale']??'')==='1'||(string)($_GET['filter_sale']??'')==='1') $active['on_sale']='تخفیف‌دار';
 if(taxonomy_exists('pa_brand')&&!empty($_GET['filter_pa_brand'])){
  $active_brand_slugs=array_values(array_filter(array_map('sanitize_title',(array)wp_unslash($_GET['filter_pa_brand']))));
  if($active_brand_slugs){$active_brand_terms=get_terms(['taxonomy'=>'pa_brand','slug'=>$active_brand_slugs,'hide_empty'=>false]);if(!is_wp_error($active_brand_terms)&&$active_brand_terms){$active['filter_pa_brand']='برند: '.implode('، ',wp_list_pluck($active_brand_terms,'name'));}}
 }
 $price_max_for_input=max($price_min+1,$price_max);
 $price_step=$price_max>1000000?1000:1;
 $query_min=$price_has_data&&isset($_GET['min_price'])&&$_GET['min_price']!==''?max($price_min,min($price_max,absint(wp_unslash($_GET['min_price'])))):null;
 $query_max=$price_has_data&&isset($_GET['max_price'])&&$_GET['max_price']!==''?max($price_min,min($price_max,absint(wp_unslash($_GET['max_price'])))):null;
 if($query_min!==null&&$query_max!==null&&$query_min>$query_max){$swap=$query_min;$query_min=$query_max;$query_max=$swap;}
 ?>
 <?php if($archive_opts['show_filters']): ?><button class="bhp-filter-open" type="button" aria-controls="bhp-filters" aria-expanded="false"><span aria-hidden="true">☷</span> فیلتر محصولات</button><?php endif; ?>
 <div class="bhp-shop-layout <?php echo $archive_opts['show_filters']?'has-filters':'no-filters'; ?>">
  <?php if($archive_opts['show_filters']): ?>
   <aside id="bhp-filters" class="bhp-filters <?php echo empty($archive_opts['filter_sticky'])?'is-not-sticky':''; ?>">
   <form method="get" action="<?php echo esc_url(get_pagenum_link(1)); ?>">
   <?php $filter_order=(array)($archive_opts['filter_order']??['price','brand','category','availability','sale']);$filter_labels=(array)($archive_opts['filter_labels']??[]);$filter_label=function($key,$fallback)use($filter_labels){return esc_html($filter_labels[$key]??$fallback);}; ?>
   <div class="bhp-filter-title"><b>فیلتر محصولات</b><button class="bhp-filter-close" type="button" aria-label="بستن فیلتر">×</button><a href="<?php echo esc_url($term ? get_term_link($term) : $shop); ?>">پاک‌کردن</a></div>
    <?php if(in_array('price',$filter_order,true)): ?><details open><summary><?php echo $filter_label('price','محدوده قیمت'); ?></summary>
     <?php if(!$price_has_data): ?><p class="bhp-filter-unavailable">قیمت کالاها هنوز در کاتالوگ ثبت نشده است.</p><?php else: ?>
     <div class="bhp-price-filter">
      <label>از<input name="min_price" type="number" min="<?php echo esc_attr($price_min); ?>" max="<?php echo esc_attr($price_max_for_input); ?>" step="<?php echo esc_attr($price_step); ?>" value="<?php echo $query_min!==null?esc_attr($query_min):''; ?>" placeholder="حداقل" inputmode="numeric"></label>
      <label>تا<input name="max_price" type="number" min="<?php echo esc_attr($price_min); ?>" max="<?php echo esc_attr($price_max_for_input); ?>" step="<?php echo esc_attr($price_step); ?>" value="<?php echo $query_max!==null?esc_attr($query_max):''; ?>" placeholder="حداکثر" inputmode="numeric"></label>
     </div>
     <div class="bhp-price-slider" data-price-range data-bound-min="<?php echo esc_attr($price_min); ?>" data-bound-max="<?php echo esc_attr($price_max); ?>" data-price-step="<?php echo esc_attr($price_step); ?>" aria-label="انتخاب بازه قیمت">
      <input class="bhp-price-range bhp-price-range-min" type="range" min="<?php echo esc_attr($price_min); ?>" max="<?php echo esc_attr($price_max); ?>" step="<?php echo esc_attr($price_step); ?>" value="<?php echo esc_attr($query_min??$price_min); ?>" data-range="min" aria-label="حداقل قیمت">
      <input class="bhp-price-range bhp-price-range-max" type="range" min="<?php echo esc_attr($price_min); ?>" max="<?php echo esc_attr($price_max); ?>" step="<?php echo esc_attr($price_step); ?>" value="<?php echo esc_attr($query_max??$price_max); ?>" data-range="max" aria-label="حداکثر قیمت">
     </div>
     <div class="bhp-price-range-values"><output data-range-output="min"><?php echo esc_html(number_format_i18n($query_min??$price_min)); ?></output><span>تا</span><output data-range-output="max"><?php echo esc_html(number_format_i18n($query_max??$price_max)); ?></output></div>
     <?php endif; ?>
    </details><?php endif; ?>
    <?php if(in_array('brand',$filter_order,true)&&taxonomy_exists('pa_brand')):$brands=get_terms(['taxonomy'=>'pa_brand','hide_empty'=>true]); $selected_brands=array_map(function($brand){$brand=(string)$brand;for($i=0;$i<2&&strpos($brand,'%')!==false;$i++){$decoded=rawurldecode($brand);if($decoded===$brand)break;$brand=$decoded;}return sanitize_title($brand);},(array)($_GET['filter_pa_brand']??[])); if($brands&&!is_wp_error($brands)): ?>
    <details open><summary><?php echo $filter_label('brand','برند'); ?></summary>
     <label class="bhp-filter-search-wrap"><span class="screen-reader-text">جستجوی برند</span><input type="search" class="bhp-filter-search" data-filter-search="brand" placeholder="جستجوی برند" autocomplete="off"></label>
     <div class="bhp-brand-list" role="group" aria-label="انتخاب برند">
      <?php $visible_brands=array_slice($brands,0,max(4,min(40,absint($archive_opts['filter_initial_limit']??12)))); foreach($brands as $brand)if(in_array($brand->slug,$selected_brands,true)&&!in_array($brand,$visible_brands,true))$visible_brands[]=$brand; foreach($visible_brands as $brand): ?><label><input type="checkbox" name="filter_pa_brand[]" value="<?php echo esc_attr($brand->slug); ?>" <?php checked(in_array($brand->slug,$selected_brands,true)); ?>><span><?php echo esc_html($brand->name); ?></span><small><?php echo esc_html(number_format_i18n($brand->count)); ?></small></label><?php endforeach; ?>
     </div>
    </details>
    <?php endif; endif; ?>
    <?php if(in_array('category',$filter_order,true)): ?><details><summary><?php echo $filter_label('category','دسته‌بندی'); ?></summary>
     <label class="bhp-filter-search-wrap"><span class="screen-reader-text">جستجوی دسته‌بندی</span><input type="search" class="bhp-filter-search" data-filter-search="category" placeholder="جستجوی دسته‌بندی" autocomplete="off"></label>
     <div class="bhp-check-list">
      <?php foreach(array_slice($cats,0,max(4,min(40,absint($archive_opts['filter_initial_limit']??12)))) as $cat): $link=get_term_link($cat); if(is_wp_error($link)) continue; $keep_query=[]; foreach(['orderby','s','min_price','max_price','filter_stock_status','filter_sale','on_sale','filter_pa_brand'] as $query_key) if(isset($_GET[$query_key])) $keep_query[$query_key]=wp_unslash($_GET[$query_key]); if($keep_query)$link=add_query_arg($keep_query,$link); ?>
      <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($cat->name); ?><small><?php echo intval($cat->count); ?></small></a>
      <?php endforeach; ?>
     </div>
    </details><?php endif; ?>
   <?php if(in_array('availability',$filter_order,true)&&!empty($archive_opts['show_stock'])): ?><label class="bhp-stock-filter"><input type="checkbox" name="filter_stock_status" value="instock" <?php checked(isset($_GET['filter_stock_status'])&&$_GET['filter_stock_status']==='instock'); ?>> <?php echo $filter_label('availability','فقط کالاهای موجود'); ?></label><?php endif; ?>
   <?php if(in_array('sale',$filter_order,true)&&!empty($archive_opts['show_sale_filter'])): ?><label class="bhp-stock-filter"><input type="checkbox" name="filter_sale" value="1" <?php checked(isset($_GET['filter_sale'])&&$_GET['filter_sale']==='1'); ?>> <?php echo $filter_label('sale','فقط کالاهای تخفیف‌دار'); ?></label><?php endif; ?>
    <label>جستجو در محصولات<input type="search" name="s" value="<?php echo esc_attr(get_search_query(false)); ?>"></label>
    <input type="hidden" name="post_type" value="product">
    <?php foreach(wc_get_attribute_taxonomies() as $attribute):
     $slug=$attribute->attribute_name;
     if($slug==='brand') continue;
     $terms=get_terms(['taxonomy'=>wc_attribute_taxonomy_name($slug),'hide_empty'=>true]);
     if(!$terms || is_wp_error($terms)) continue;
     $key='filter_'.$slug;
     ?><label><?php echo esc_html($attribute->attribute_label); ?><select name="<?php echo esc_attr($key); ?>"><option value="">همه</option><?php foreach($terms as $option): ?><option value="<?php echo esc_attr($option->slug); ?>" <?php selected(wc_clean(wp_unslash($_GET[$key]??'')),$option->slug); ?>><?php echo esc_html($option->name); ?></option><?php endforeach; ?></select></label><?php endforeach; ?>
    <?php foreach($_GET as $k=>$v) if(is_scalar($v)&&in_array($k,['orderby','on_sale'],true)) echo '<input type="hidden" name="'.esc_attr($k).'" value="'.esc_attr(sanitize_text_field(wp_unslash($v))).'">'; ?>
    <button class="bhp-filter-submit" type="submit">اعمال فیلترها</button>
   </form>
  </aside><?php endif; ?>
  <section class="bhp-shop-results" id="shop-products">
   <?php if($active): ?><div class="bhp-active-filters" aria-label="فیلترهای فعال"><?php foreach($active as $key=>$label): $remove_args=[$key]; if($key==='on_sale')$remove_args[]='filter_sale'; foreach(['min_price','max_price'] as $empty_price_key) if(isset($_GET[$empty_price_key])&&$_GET[$empty_price_key]==='') $remove_args[]=$empty_price_key; ?><a href="<?php echo esc_url(remove_query_arg($remove_args)); ?>"><?php echo esc_html($label); ?> ×</a><?php endforeach; ?></div><?php endif; ?>
   <div class="bhp-toolbar"><span><?php woocommerce_result_count(); ?></span><?php woocommerce_catalog_ordering(); ?></div>
   <?php if(woocommerce_product_loop()): ?>
   <ul class="products columns-<?php echo esc_attr($archive_opts['columns']); ?>" style="--bhp-columns:<?php echo esc_attr($archive_opts['columns']); ?>;--bhp-card-radius:<?php echo esc_attr($archive_opts['card_radius']); ?>px;">
    <?php while(have_posts()): the_post(); wc_get_template_part('content','product'); endwhile; ?>
   </ul>
   <?php do_action('woocommerce_after_shop_loop');
   $pages=(int)wc_get_loop_prop('total_pages');
   $page=max(1,(int)get_query_var('paged'));
   if($pages>1): ?>
   <nav class="bh-shop-pages" aria-label="صفحه‌بندی محصولات">
    <span><?php if($page>1): ?><a href="<?php echo esc_url(get_pagenum_link($page-1)); ?>">قبلی</a><?php endif; ?></span>
    <b aria-current="page"><?php echo esc_html(sprintf('صفحه %s از %s',number_format_i18n($page),number_format_i18n($pages))); ?></b>
    <span><?php if($page<$pages): ?><a href="<?php echo esc_url(get_pagenum_link($page+1)); ?>">بعدی</a><?php endif; ?></span>
   </nav><?php endif; else: ?>
   <div class="bhp-empty"><b>هیچ محصولی با این فیلتر پیدا نشد.</b><p>عبارت یا محدوده را عوض کنید، یا فیلترها را بردارید.</p><a class="button" href="<?php echo esc_url($shop); ?>">مشاهده همه محصولات</a></div>
   <?php endif; ?>
  </section>
 </div>
 <section class="bh-shop-services" aria-label="خدمات مشتریان">
  <h2>راهنمای خرید از بهاران</h2>
  <?php foreach(['shipping'=>'روش‌های ارسال','returns'=>'شرایط ضمانت و بازگشت','contact'=>'تماس با فروشگاه'] as $slug=>$label):
   $url=bhb_page_url($slug); if($url): ?><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a><?php endif; endforeach; ?>
  <button type="button" hidden data-shop-support>گفت‌وگو با پشتیبانی</button>
 </section>
</div>
<?php get_footer(); ?>
