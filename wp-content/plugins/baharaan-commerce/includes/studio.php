<?php
if (!defined('ABSPATH')) exit;

function bhc_studio_defaults(){
 $order=['hero','categories','bento','featured','offers','rooms','brands','best-sellers','journal','instagram','trust','newsletter'];
 $banner=function($title,$text,$cta,$tone){return ['enabled'=>1,'title'=>$title,'text'=>$text,'cta'=>$cta,'url'=>'','product_id'=>0,'product_id_2'=>0,'tone'=>$tone,'image_id'=>0,'image_mobile_id'=>0];};
 return [
  'order'=>$order,
  'enabled'=>$order,
  'hero_title'=>'',
  'hero_subtitle'=>'',
  'hero_product_id'=>0,
  'hero_bg_id'=>0,
  'hero_bg_mobile_id'=>0,
  'featured_source'=>'latest',
  'campaign_banner_id'=>0,
  'campaign_banner_mobile_id'=>0,
  'campaign_banner_alt'=>'',
  'campaign_banner_url'=>'',
  'container_width'=>1320,
  'hero_overflow'=>96,
  'motion'=>true,
  'reels_display'=>['layout'=>'rail','columns'=>3,'copy'=>'overlay','show_index'=>1,'show_play'=>1,'show_title'=>1,'show_caption'=>1,'button'=>'مشاهده در اینستاگرام','radius'=>28],
  'commerce'=>['hero_cta_label'=>'مشاهده دسته‌بندی‌ها','hero_cta_url'=>'','home_notice'=>'','phone'=>'','phone_2'=>'','store_name'=>'','store_address'=>'','email'=>'','instagram'=>'','whatsapp'=>'','telegram'=>'','telegram_admin'=>'','bale'=>'','eitaa'=>'','support_hours'=>'','show_wishlist'=>1,'show_compare'=>1,'sticky_buy'=>1,'stock_display_policy'=>'in_stock_first','mega_menu_enabled'=>1,'mega_menu_limit'=>8,'mega_menu_feature_product_id'=>0,'chat_greeting'=>'سلام! چطور می‌توانیم کمکتان کنیم؟','chat_avatar'=>'','ai_enabled'=>0,'ai_endpoint'=>'','ai_model'=>'','ai_key'=>'','webp_enabled'=>1,'image_quality'=>82,'brand_first'=>'بهاران','brand_second'=>'هوم'],
  'seo'=>['product_schema_enabled'=>1,'site_meta_description'=>'','organization_name'=>'','organization_url'=>'','organization_logo_id'=>0],
  'archive'=>['show_banner'=>1,'show_filters'=>1,'show_sale_filter'=>1,'columns'=>3,'products_per_page'=>12,'show_rating'=>1,'show_stock'=>1,'card_radius'=>28,'filter_order'=>['price','brand','category','availability','sale'],'filter_initial_limit'=>12,'filter_sticky'=>1,'filter_labels'=>['price'=>'محدوده قیمت','brand'=>'برند','category'=>'دسته‌بندی','availability'=>'موجودی','sale'=>'تخفیف']],
  'colors'=>['navy'=>'#0B2039','blue'=>'#1769D2','ice'=>'#EAF3FF','bg'=>'#F7F9FC','accent'=>'#FF8A3D'],
  'rooms'=>[0,0,0],
  'copy'=>[
   'utility'=>'','hero_kicker'=>'فروشگاه لوازم خانگی بهاران','hero_title'=>'برای خانه‌ای','hero_accent'=>'زیباتر و هوشمندتر',   'hero_subtitle'=>'لوازم خانگی را با بررسی مشخصات، قیمت و وضعیت موجودی انتخاب کنید.','hero_cta'=>'مشاهده دسته‌بندی‌ها',
   'categories_title'=>'خرید لوازم خانگی را از کجا شروع کنیم؟','categories_lead'=>'دسته‌بندی‌های فروشگاه را با تصاویر واقعی محصولات و اطلاعات به‌روز ببینید.','featured_title'=>'انتخاب‌های ویژه خانه','offers_title'=>'پیشنهادهای منتخب بهاران','offers_text'=>'محصولات منتخب با شرایط ویژه و موجودی مشخص در فروشگاه.','offers_cta'=>'مشاهده پیشنهادها',
   'brands_title'=>'برندهای معتبر لوازم خانگی','brands_lead'=>'برندهای معتبر لوازم خانگی را همراه با محصولات و دسته‌بندی‌های مرتبط ببینید.',
   'journal_title'=>'مجله و راهنمای خرید','journal_lead'=>'راهنماهای خرید و مطالب کاربردی برای انتخاب آگاهانه لوازم خانگی.',
   'instagram_title'=>'ایده‌های بهاران برای خانه','instagram_lead'=>'ویدیوها و ایده‌های بهاران را در اینستاگرام ببینید.',
   'newsletter_title'=>'با خبرهای تازه خانه همراه باشید','newsletter_text'=>'با عضویت اختیاری، از راهنماها و خبرهای تازه بهاران‌هوم باخبر شوید.',
   'trust_1_title'=>'جزئیات کالا','trust_1_text'=>'پیش از خرید، مشخصات را در صفحه همان کالا بخوانید','trust_2_title'=>'قیمت روشن','trust_2_text'=>'مبلغ و موجودی، همان چیزی است که کنار کالا می‌بینید','trust_3_title'=>'ایده برای خانه','trust_3_text'=>'راهنماهای خرید بهاران را برای انتخاب بهتر بخوانید','trust_4_title'=>'گفتگو با بهاران','trust_4_text'=>'سؤال دارید؟ از راه‌های تماس فروشگاه بپرسید',
   'footer_about'=>'انتخاب لوازم خانگی با مشخصات، قیمت و وضعیت موجودی به‌روز.','footer_address'=>'','footer_phone'=>'','footer_hours'=>'',
  ],
  'banners'=>[
   1=>$banner('شست‌وشوی دقیق، خانه‌ای آرام','ماشین‌های لباسشویی برای شست‌وشوی بهتر و مصرف بهینه.','مشاهده ماشین‌های لباسشویی','navy'),
   2=>$banner('صدای سینمایی برای خانه','ساندبارهای حرفه‌ای برای فیلم، موسیقی و بازی.','مشاهده محصولات صوتی','orange'),
   3=>$banner('آشپزخانه‌ای روشن و کاربردی','ابزارهای کاربردی برای آماده‌سازی سریع‌تر غذا.','مشاهده لوازم آشپزخانه','ice'),
  ],
 ];
}

function bhc_studio_opts(){
 $saved=get_option('bhc_studio_options',[]);
 if(!is_array($saved)) $saved=[];
 $defaults=bhc_studio_defaults();
 $opts=wp_parse_args($saved,$defaults);
 $opts['copy']=wp_parse_args(is_array($saved['copy']??null)?$saved['copy']:[],$defaults['copy']);
 $opts['commerce']=wp_parse_args(is_array($saved['commerce']??null)?$saved['commerce']:[],$defaults['commerce']);
 $opts['seo']=wp_parse_args(is_array($saved['seo']??null)?$saved['seo']:[],$defaults['seo']);
 $opts['colors']=wp_parse_args(is_array($saved['colors']??null)?$saved['colors']:[],$defaults['colors']);
 $opts['banners']=$defaults['banners'];
 if(is_array($saved['banners']??null)){
  foreach($defaults['banners'] as $i=>$banner){
   $opts['banners'][$i]=wp_parse_args(is_array($saved['banners'][$i]??null)?$saved['banners'][$i]:[],$banner);
   if(($opts['banners'][$i]['text']??'')==='بنر قابل ویرایش در بهاران استودیو') $opts['banners'][$i]['text']='وسایل این بخش را در فروشگاه ببینید';
  }
 if(($opts['banners'][2]['title']??'')==='شست‌وشوی قدرتمند، مصرف بهینه'){
  $opts['banners'][2]['title']='صدای سینمایی برای خانه';
  $opts['banners'][2]['text']='ساندبارهای حرفه‌ای برای فیلم، موسیقی و بازی.';
  $opts['banners'][2]['cta']='مشاهده محصولات صوتی';
  $opts['banners'][2]['url']='';
 }
 }
 $legacy_titles=[1=>'زیبایی را به آشپزخانه بیاورید',2=>'یک فنجان انرژی روزانه',3=>'نظافت آسان، خانه آرام'];
 $aligned_titles=[1=>'شست‌وشوی دقیق، خانه‌ای آرام',2=>'شست‌وشوی قدرتمند، مصرف بهینه',3=>'آشپزخانه‌ای روشن و کاربردی'];
 foreach($legacy_titles as $key=>$legacy) if(($opts['banners'][$key]['title']??'')===$legacy) $opts['banners'][$key]['title']=$aligned_titles[$key];
 $known=$defaults['order'];
 $saved_order=array_values(array_filter((array)($saved['order']??[])));
 $legacy=$saved_order&&!in_array('offers',$saved_order,true)&&!in_array('trust',$saved_order,true);
 if($legacy||!$saved_order){
  $opts['order']=$known;
  $opts['enabled']=$defaults['enabled'];
 }else{
  $order=array_values(array_intersect($saved_order,$known));
  foreach($known as $key) if(!in_array($key,$order,true)) $order[]=$key;
  $opts['order']=$order;
  $opts['enabled']=array_values(array_intersect((array)($saved['enabled']??$defaults['enabled']),$known));
 }
 $rooms=array_values((array)($saved['rooms']??[]));
 $opts['rooms']=[absint($rooms[0]??0),absint($rooms[1]??0),absint($rooms[2]??0)];
 $opts['container_width']=max(1100,min(1440,absint($opts['container_width']??1320)));
 $opts['hero_overflow']=max(0,min(120,absint($opts['hero_overflow']??96)));
 $opts['featured_source']=in_array($opts['featured_source']??'',['latest','featured','sale','popularity'],true)?$opts['featured_source']:'latest';
 $opts['commerce']['stock_display_policy']=in_array($opts['commerce']['stock_display_policy']??'in_stock_first',['in_stock_first','hide_out_of_stock','show_all'],true)?$opts['commerce']['stock_display_policy']:'in_stock_first';
 $opts['commerce']['mega_menu_limit']=max(4,min(12,absint($opts['commerce']['mega_menu_limit']??8)));
 $opts['commerce']['image_quality']=max(50,min(100,absint($opts['commerce']['image_quality']??82)));
 $archive=wp_parse_args(is_array($saved['archive']??null)?$saved['archive']:[],$defaults['archive']);
 $filter_keys=['price','brand','category','availability','sale'];
 $filter_order=array_values(array_intersect(array_map('sanitize_key',(array)($archive['filter_order']??[])),$filter_keys));
 foreach($filter_keys as $filter_key)if(!in_array($filter_key,$filter_order,true))$filter_order[]=$filter_key;
 $default_labels=$defaults['archive']['filter_labels'];$raw_labels=is_array($archive['filter_labels']??null)?$archive['filter_labels']:[];$filter_labels=[];
 foreach($filter_keys as $filter_key){$label=sanitize_text_field($raw_labels[$filter_key]??'');$filter_labels[$filter_key]=$label!==''?$label:$default_labels[$filter_key];}
 $opts['archive']=['show_banner'=>empty($archive['show_banner'])?0:1,'show_filters'=>empty($archive['show_filters'])?0:1,'show_sale_filter'=>empty($archive['show_sale_filter'])?0:1,'columns'=>in_array((int)$archive['columns'],[2,3,4],true)?(int)$archive['columns']:3,'products_per_page'=>max(6,min(48,absint($archive['products_per_page']))),'show_rating'=>empty($archive['show_rating'])?0:1,'show_stock'=>empty($archive['show_stock'])?0:1,'card_radius'=>max(12,min(40,absint($archive['card_radius']))),'filter_order'=>$filter_order,'filter_initial_limit'=>max(4,min(40,absint($archive['filter_initial_limit']??12))),'filter_sticky'=>empty($archive['filter_sticky'])?0:1,'filter_labels'=>$filter_labels];
 $opts['brands']=bhc_studio_brands(is_array($saved['brands']??null)?$saved['brands']:[]);
 $opts['reels']=bhc_studio_reels(is_array($saved['reels']??null)?$saved['reels']:[]);
 $rd=wp_parse_args(is_array($saved['reels_display']??null)?$saved['reels_display']:[],$defaults['reels_display']);
 $opts['reels_display']=['layout'=>in_array($rd['layout']??'',['rail','center','grid'],true)?$rd['layout']:'rail','columns'=>in_array((int)($rd['columns']??3),[2,3,4],true)?(int)$rd['columns']:3,'copy'=>in_array($rd['copy']??'',['overlay','below','hidden'],true)?$rd['copy']:'overlay','show_index'=>empty($rd['show_index'])?0:1,'show_play'=>empty($rd['show_play'])?0:1,'show_title'=>empty($rd['show_title'])?0:1,'show_caption'=>empty($rd['show_caption'])?0:1,'button'=>sanitize_text_field($rd['button']??'')?:'مشاهده در اینستاگرام','radius'=>max(12,min(36,absint($rd['radius']??28)))];
 $fresh=[
  'hero_subtitle'=>'لوازم خانگی را با بررسی مشخصات، قیمت و وضعیت موجودی انتخاب کنید.',
  'offers_text'=>'محصولات منتخب با شرایط ویژه و موجودی مشخص در فروشگاه.',
  'trust_1_title'=>'جزئیات کالا','trust_1_text'=>'پیش از خرید، مشخصات را در صفحه همان کالا بخوانید',
  'trust_2_title'=>'قیمت روشن','trust_2_text'=>'مبلغ و موجودی، همان چیزی است که کنار کالا می‌بینید',
  'trust_3_title'=>'ایده برای خانه','trust_3_text'=>'راهنماهای خرید بهاران را برای انتخاب بهتر بخوانید',
  'trust_4_title'=>'گفتگو با بهاران','trust_4_text'=>'سؤال دارید؟ از راه‌های تماس فروشگاه بپرسید',
 ];
 $stale=[
  'hero_subtitle'=>['محصولات کاربردی، با قیمت و موجودی ثبت‌شده در فروشگاه.',''],
  'offers_text'=>['فقط محصولات دارای قیمت ویژه ثبت‌شده',''],
  'trust_1_title'=>['مشخصات محصول'],'trust_1_text'=>['از داده ثبت‌شده همان کالا'],
  'trust_2_title'=>['قیمت و موجودی'],'trust_2_text'=>['همان عددی که ووکامرس نشان می‌دهد'],
  'trust_3_title'=>['راهنمای خرید'],'trust_3_text'=>['مقاله‌های منتشرشده فروشگاه'],
  'trust_4_title'=>['پشتیبانی'],'trust_4_text'=>['راه‌های تماسی که در تنظیمات وارد شده'],
 ];
 foreach($fresh as $key=>$value){
  $current=trim((string)($opts['copy'][$key]??''));
  if(in_array($current,$stale[$key],true)) $opts['copy'][$key]=$value;
 }
 $copy_updates=[
  'categories_title'=>['از کدام بخش خانه شروع کنیم؟','خرید لوازم خانگی را از کجا شروع کنیم؟'],
  'categories_lead'=>['دسته‌های واقعی فروشگاه، با تصویر کالای همان گروه.','دسته‌بندی‌های فروشگاه را با تصاویر واقعی محصولات و اطلاعات به‌روز ببینید.'],
  'brands_lead'=>['هر برند با دسته‌های مرتبط و کالاهای واقعی همان نام.','برندهای معتبر لوازم خانگی را همراه با محصولات و دسته‌بندی‌های مرتبط ببینید.'],
  'journal_lead'=>['ایده‌ها و راهنماهای منتشرشده فروشگاه، برای انتخاب دقیق‌تر.','راهنماهای خرید و مطالب کاربردی برای انتخاب آگاهانه لوازم خانگی.'],
  'instagram_lead'=>['کاور را اینجا ببینید. برای تماشای ویدیو وارد اینستاگرام شوید.','ویدیوها و ایده‌های بهاران را در اینستاگرام ببینید.'],
  'newsletter_text'=>['اطلاع‌رسانی با عضویت اختیاری. تخفیف یا هدیه‌ای از این فرم اعلام نمی‌شود.','با عضویت اختیاری، از راهنماها و خبرهای تازه بهاران‌هوم باخبر شوید.'],
  'footer_about'=>['انتخاب لوازم خانه با اطلاعات همان کالا.','انتخاب لوازم خانگی با مشخصات، قیمت و وضعیت موجودی به‌روز.'],
 ];
 foreach($copy_updates as $key=>$pair){
  if(trim((string)($opts['copy'][$key]??''))===$pair[0]) $opts['copy'][$key]=$pair[1];
 }
 return $opts;
}

function bhc_studio_revision_label($timestamp){
  return function_exists('bh_jalali_date')?bh_jalali_date(absint($timestamp),true):'—';
}

function bhc_studio_store_revision($options,$reason='save'){
 $history=get_option('bhc_studio_revisions',[]);
 $draft=get_option('bhc_studio_draft',[]);
 if(!is_array($history)) $history=[];
 array_unshift($history,['id'=>wp_generate_uuid4(),'created_at'=>time(),'reason'=>sanitize_key($reason),'user'=>get_current_user_id(),'options'=>$options]);
 update_option('bhc_studio_revisions',array_slice($history,0,20),false);
}

function bhc_studio_brands($rows){
 $clean=[];$seen=[];
 foreach($rows as $row){
  if(!is_array($row)) continue;
  $name=sanitize_text_field($row['name']??'');
  if($name==='') continue;
   $key=mb_strtolower(preg_replace('/[\s\-_‌‍]+/u','',str_replace(['ي','ك','ۀ'],['ی','ک','ه'],$name)),'UTF-8');
   if($key===''||isset($seen[$key])) continue;
   $seen[$key]=true;
  $image_id=absint($row['image_id']??0); if($image_id&&!wp_attachment_is_image($image_id)) $image_id=0;
  $clean[]=['name'=>$name,'latin'=>sanitize_text_field($row['latin']??''),'line'=>sanitize_text_field($row['line']??''),'cats'=>sanitize_text_field($row['cats']??''),'alias'=>sanitize_text_field($row['alias']??''),'url'=>esc_url_raw($row['url']??''),'image_id'=>$image_id,'logo'=>sanitize_file_name($row['logo']??'')];
 }
 if($clean) return $clean;
 return function_exists('bhb_brand_seed')?bhb_brand_seed():[];
}

function bhc_studio_reels($rows){
 $clean=[];
 foreach($rows as $row){
  if(!is_array($row)) continue;
  $url=esc_url_raw($row['url']??'');
  $image=absint($row['image_id']??0);
  $title=sanitize_text_field($row['title']??'');
  if($url===''&&!$image&&$title==='') continue;
  $clean[]=['title'=>$title,'url'=>$url,'image_id'=>$image,'caption'=>sanitize_text_field($row['caption']??''),'alt'=>sanitize_text_field($row['alt']??'')];
 }
 return array_slice($clean,0,8);
}

function bhc_studio_merge_home_sections($value){
 $studio=bhc_studio_opts();
 if(!is_array($value)) return $value;
 if($studio['hero_title']!=='') $value['hero_title']=$studio['hero_title'];
 if($studio['hero_subtitle']!=='') $value['hero_subtitle']=$studio['hero_subtitle'];
 if(!empty($studio['hero_product_id'])) $value['hero_product_id']=absint($studio['hero_product_id']);
 foreach(['campaign_banner_id','campaign_banner_mobile_id','campaign_banner_alt','campaign_banner_url'] as $key) if(isset($studio[$key])) $value[$key]=$studio[$key];
 $saved=get_option('bhc_studio_options',[]); $commerce=is_array($saved['commerce']??null)?$saved['commerce']:[];
 $map=['hero_cta_label'=>'hero_cta_label','hero_cta_url'=>'hero_cta_url','home_notice'=>'home_notice','phone'=>'phone','phone_2'=>'phone_2','store_name'=>'store_name','store_address'=>'store_address','email'=>'email','instagram'=>'instagram','whatsapp'=>'whatsapp','telegram'=>'telegram','telegram_admin'=>'telegram_admin','bale'=>'bale','eitaa'=>'eitaa','support_hours'=>'support_hours','show_wishlist'=>'show_wishlist','show_compare'=>'show_compare','sticky_buy'=>'sticky_buy','stock_display_policy'=>'stock_display_policy','mega_menu_enabled'=>'mega_menu_enabled','mega_menu_limit'=>'mega_menu_limit','mega_menu_feature_product_id'=>'mega_menu_feature_product_id','chat_greeting'=>'chat_greeting','chat_avatar'=>'chat_avatar','ai_enabled'=>'ai_enabled','ai_endpoint'=>'ai_endpoint','ai_model'=>'ai_model','webp_enabled'=>'webp_enabled','image_quality'=>'image_quality','brand_first'=>'brand_first','brand_second'=>'brand_second'];
 foreach($map as $from=>$to) if(array_key_exists($from,$commerce)) $value[$to]=$commerce[$from];
 if(array_key_exists('ai_key',$commerce) && trim((string)$commerce['ai_key'])!=='') $value['ai_key']=$commerce['ai_key'];
 return $value;
}
add_filter('option_bhc_options','bhc_studio_merge_home_sections',20);

function bhc_studio_menu(){add_submenu_page('baharaan-commerce','بهاران استودیو','بهاران استودیو','manage_options','bhc-studio','bhc_studio_page');}
add_action('admin_menu','bhc_studio_menu',20);

function bhc_studio_assets($hook){
 if(($hook!=='baharaan-commerce_page_bhc-studio') && (sanitize_key(wp_unslash($_GET['page']??''))!=='bhc-studio')) return;
 wp_enqueue_media();
 $studio_base=dirname(__DIR__).'/baharaan-commerce.php';
 wp_enqueue_style('bhc-studio',plugins_url('assets/studio.css',$studio_base),[],'1.6.8');
 wp_enqueue_script('bhc-studio',plugins_url('assets/studio.js',$studio_base),['jquery','jquery-ui-sortable'],'1.6.6',true);
 wp_enqueue_script('bhc-studio-tabs',plugins_url('assets/studio-tabs.js',$studio_base),[],'1.6.6',true);
 wp_enqueue_script('bhc-studio-live',plugins_url('assets/studio-live.js',$studio_base),[],'1.6.1',true);
 wp_enqueue_script('bhc-brands-manager',plugins_url('assets/brands-manager.js',$studio_base),[],'1.7.2',true);
}
add_action('admin_enqueue_scripts','bhc_studio_assets');

function bhc_studio_text($value){return sanitize_text_field(wp_unslash($value));}
function bhc_studio_filter_order($raw){
 $keys=['price','brand','category','availability','sale'];
 $parts=is_array($raw)?$raw:preg_split('/\s*,\s*/',sanitize_text_field(wp_unslash((string)$raw)));
 $order=[];
 foreach(array_map('sanitize_key',(array)$parts) as $key){ if(in_array($key,$keys,true)&&!in_array($key,$order,true)) $order[]=$key; }
 foreach($keys as $key) if(!in_array($key,$order,true)) $order[]=$key;
 return $order;
}
function bhc_studio_hex($value,$fallback){$color=sanitize_hex_color(wp_unslash($value));return $color?:$fallback;}

function bhc_studio_save(){
 if(!current_user_can('manage_options')) wp_die('دسترسی مجاز نیست');
 check_admin_referer('bhc_studio_save');
 $mode=sanitize_key($_POST['bhc_save_mode']??'publish');
 $defaults=bhc_studio_defaults();
 $known=$defaults['order'];
 $order=array_values(array_intersect(array_map('sanitize_key',(array)($_POST['bhc_order']??[])),$known));
 foreach($known as $key) if(!in_array($key,$order,true)) $order[]=$key;
 $enabled=array_values(array_intersect(array_map('sanitize_key',(array)($_POST['bhc_enabled']??[])),$known));
 $copy=[];
 foreach($defaults['copy'] as $key=>$fallback) $copy[$key]=bhc_studio_text($_POST['copy'][$key]??$fallback);
 $colors=[];
 foreach($defaults['colors'] as $key=>$fallback) $colors[$key]=bhc_studio_hex($_POST['colors'][$key]??$fallback,$fallback);
 $banners=[];
 foreach($defaults['banners'] as $i=>$banner){
  $tone=sanitize_key($_POST['banner_tone'][$i]??$banner['tone']);
  $banners[$i]=[
   'enabled'=>isset($_POST['banner_enabled'][$i])?1:0,
   'title'=>bhc_studio_text($_POST['banner_title'][$i]??''),
   'text'=>bhc_studio_text($_POST['banner_text'][$i]??''),
   'cta'=>bhc_studio_text($_POST['banner_cta'][$i]??''),
   'url'=>esc_url_raw(wp_unslash($_POST['banner_url'][$i]??'')),
   'product_id'=>absint($_POST['banner_product'][$i]??0),
   'product_id_2'=>absint($_POST['banner_product_2'][$i]??0),
   'tone'=>in_array($tone,['navy','orange','ice'],true)?$tone:$banner['tone'],
   'image_id'=>absint($_POST['banner_image'][$i]??0),
   'image_mobile_id'=>absint($_POST['banner_image_mobile'][$i]??0),
  ];
 }
 $source=sanitize_key($_POST['featured_source']??'latest');
 $old=(array)get_option('bhc_studio_options',[]); $oldc=(array)($old['commerce']??[]); $pc=(array)($_POST['commerce']??[]); $ps=(array)($_POST['seo']??[]);
 $commerce=['hero_cta_label'=>bhc_studio_text($pc['hero_cta_label']??'مشاهده دسته‌بندی‌ها'),'hero_cta_url'=>esc_url_raw(wp_unslash($pc['hero_cta_url']??'')),'home_notice'=>bhc_studio_text($pc['home_notice']??''),'phone'=>bhc_studio_text($pc['phone']??''),'phone_2'=>bhc_studio_text($pc['phone_2']??''),'store_name'=>bhc_studio_text($pc['store_name']??''),'store_address'=>sanitize_textarea_field(wp_unslash($pc['store_address']??'')),'email'=>sanitize_email($pc['email']??''),'instagram'=>esc_url_raw(wp_unslash($pc['instagram']??'')),'whatsapp'=>bhc_studio_text($pc['whatsapp']??''),'telegram'=>bhc_studio_text($pc['telegram']??''),'telegram_admin'=>esc_url_raw(wp_unslash($pc['telegram_admin']??'')),'bale'=>bhc_studio_text($pc['bale']??''),'eitaa'=>bhc_studio_text($pc['eitaa']??''),'support_hours'=>bhc_studio_text($pc['support_hours']??''),'show_wishlist'=>!empty($pc['show_wishlist']),'show_compare'=>!empty($pc['show_compare']),'sticky_buy'=>!empty($pc['sticky_buy']),'stock_display_policy'=>in_array($pc['stock_display_policy']??'', ['in_stock_first','hide_out_of_stock','show_all'],true)?$pc['stock_display_policy']:'in_stock_first','mega_menu_enabled'=>!empty($pc['mega_menu_enabled']),'mega_menu_limit'=>max(4,min(12,absint($pc['mega_menu_limit']??8))),'mega_menu_feature_product_id'=>absint($pc['mega_menu_feature_product_id']??0),'chat_greeting'=>sanitize_textarea_field(wp_unslash($pc['chat_greeting']??'')),'chat_avatar'=>esc_url_raw(wp_unslash($pc['chat_avatar']??'')),'ai_enabled'=>!empty($pc['ai_enabled']),'ai_endpoint'=>esc_url_raw(wp_unslash($pc['ai_endpoint']??'')),'ai_model'=>bhc_studio_text($pc['ai_model']??''),'ai_key'=>trim((string)($pc['ai_key']??''))!==''?sanitize_text_field($pc['ai_key']):($oldc['ai_key']??''),'webp_enabled'=>!empty($pc['webp_enabled']),'image_quality'=>max(50,min(100,absint($pc['image_quality']??82))),'brand_first'=>bhc_studio_text($pc['brand_first']??'بهاران')?:'بهاران','brand_second'=>bhc_studio_text($pc['brand_second']??'هوم')?:'هوم'];
 $seo=['product_schema_enabled'=>!empty($ps['product_schema_enabled']),'site_meta_description'=>sanitize_textarea_field(wp_unslash($ps['site_meta_description']??'')),'organization_name'=>bhc_studio_text($ps['organization_name']??''),'organization_url'=>esc_url_raw(wp_unslash($ps['organization_url']??'')),'organization_logo_id'=>absint($ps['organization_logo_id']??0)];
 $saved=[
  'order'=>$order,
  'enabled'=>$enabled,
  'hero_title'=>bhc_studio_text($_POST['hero_title']??''),
  'hero_subtitle'=>sanitize_textarea_field(wp_unslash($_POST['hero_subtitle']??'')),
  'hero_product_id'=>absint($_POST['hero_product_id']??0),
  'hero_bg_id'=>absint($_POST['hero_bg_id']??0),
  'hero_bg_mobile_id'=>absint($_POST['hero_bg_mobile_id']??0),
  'featured_source'=>in_array($source,['latest','featured','sale','popularity'],true)?$source:'latest',
  'campaign_banner_id'=>absint($_POST['campaign_banner_id']??0),
  'campaign_banner_mobile_id'=>absint($_POST['campaign_banner_mobile_id']??0),
  'campaign_banner_alt'=>bhc_studio_text($_POST['campaign_banner_alt']??''),
  'campaign_banner_url'=>esc_url_raw(wp_unslash($_POST['campaign_banner_url']??'')),
  'container_width'=>max(1100,min(1440,absint($_POST['container_width']??1320))),
  'hero_overflow'=>max(0,min(120,absint($_POST['hero_overflow']??96))),
  'motion'=>!empty($_POST['motion']),
   'archive'=>['show_banner'=>!empty($_POST['archive']['show_banner']),'show_filters'=>!empty($_POST['archive']['show_filters']),'show_sale_filter'=>!empty($_POST['archive']['show_sale_filter']),'columns'=>in_array(absint($_POST['archive']['columns']??3),[2,3,4],true)?absint($_POST['archive']['columns']):3,'products_per_page'=>max(6,min(48,absint($_POST['archive']['products_per_page']??12))),'show_rating'=>!empty($_POST['archive']['show_rating']),'show_stock'=>!empty($_POST['archive']['show_stock']),'card_radius'=>max(12,min(40,absint($_POST['archive']['card_radius']??28))),'filter_order'=>bhc_studio_filter_order($_POST['archive']['filter_order']??[]),'filter_initial_limit'=>max(4,min(40,absint($_POST['archive']['filter_initial_limit']??12))),'filter_sticky'=>!empty($_POST['archive']['filter_sticky']),'filter_labels'=>['price'=>sanitize_text_field(wp_unslash($_POST['archive']['filter_label_price']??'محدوده قیمت')),'brand'=>sanitize_text_field(wp_unslash($_POST['archive']['filter_label_brand']??'برند')),'category'=>sanitize_text_field(wp_unslash($_POST['archive']['filter_label_category']??'دسته‌بندی')),'availability'=>sanitize_text_field(wp_unslash($_POST['archive']['filter_label_availability']??'موجودی')),'sale'=>sanitize_text_field(wp_unslash($_POST['archive']['filter_label_sale']??'تخفیف'))]],
  'colors'=>$colors,
  'copy'=>$copy,
  'banners'=>$banners,
  'reels_display'=>['layout'=>in_array($_POST['reels_display']['layout']??'',['rail','center','grid'],true)?sanitize_key($_POST['reels_display']['layout']):'rail','columns'=>in_array(absint($_POST['reels_display']['columns']??3),[2,3,4],true)?absint($_POST['reels_display']['columns']):3,'copy'=>in_array($_POST['reels_display']['copy']??'',['overlay','below','hidden'],true)?sanitize_key($_POST['reels_display']['copy']):'overlay','show_index'=>!empty($_POST['reels_display']['show_index']),'show_play'=>!empty($_POST['reels_display']['show_play']),'show_title'=>!empty($_POST['reels_display']['show_title']),'show_caption'=>!empty($_POST['reels_display']['show_caption']),'button'=>bhc_studio_text($_POST['reels_display']['button']??'')?:'مشاهده در اینستاگرام','radius'=>max(12,min(36,absint($_POST['reels_display']['radius']??28)))],
  'rooms'=>[absint($_POST['rooms'][0]??0),absint($_POST['rooms'][1]??0),absint($_POST['rooms'][2]??0)],
  'commerce'=>$commerce,'seo'=>$seo,
 ];
 if(isset($_POST['brand_name'])){
  $rows=[];
  foreach((array)$_POST['brand_name'] as $i=>$name){
   $image_id=absint($_POST['brand_image'][$i]??0); if($image_id&&!wp_attachment_is_image($image_id)) $image_id=0;
   $rows[]=['name'=>bhc_studio_text($name),'latin'=>bhc_studio_text($_POST['brand_latin'][$i]??''),'line'=>bhc_studio_text($_POST['brand_line'][$i]??''),'cats'=>bhc_studio_text($_POST['brand_cats'][$i]??''),'alias'=>bhc_studio_text($_POST['brand_alias'][$i]??''),'url'=>esc_url_raw(wp_unslash($_POST['brand_url'][$i]??'')),'image_id'=>$image_id,'logo'=>sanitize_file_name(wp_unslash($_POST['brand_logo'][$i]??''))];
  }
  $saved['brands']=bhc_studio_brands($rows);
 }
 if(isset($_POST['reel_url'])){
  $reels=[];
  foreach((array)$_POST['reel_url'] as $i=>$url){
   $reels[]=['title'=>bhc_studio_text($_POST['reel_title'][$i]??''),'url'=>esc_url_raw(wp_unslash($url)),'image_id'=>absint($_POST['reel_image'][$i]??0),'caption'=>bhc_studio_text($_POST['reel_caption'][$i]??''),'alt'=>bhc_studio_text($_POST['reel_alt'][$i]??'')];
  }
  $saved['reels']=bhc_studio_reels($reels);
 }
 $previous=get_option('bhc_studio_options',[]);
 if(is_array($previous)&&$previous) bhc_studio_store_revision($previous,'before-save');
 $saved['_schema_version']=2;
 $saved['_updated_at']=time();
 $saved['_updated_by']=get_current_user_id();
 if($mode==='draft'){
  update_option('bhc_studio_draft',$saved,false);
  wp_safe_redirect(add_query_arg(['page'=>'bhc-studio','draft_saved'=>'1'],admin_url('admin.php')));
 }else{
  update_option('bhc_studio_options',$saved,false);
  delete_transient('bhb_brand_index_v3');
  delete_option('bhc_studio_draft');
  $site_name=bhc_studio_text($_POST['identity_blogname']??'');
  if($site_name!=='') update_option('blogname',$site_name);
  $logo_id=absint($_POST['identity_logo_id']??0);
  if($logo_id&&wp_attachment_is_image($logo_id)) set_theme_mod('custom_logo',$logo_id);
  elseif(!empty($_POST['identity_logo_clear'])) remove_theme_mod('custom_logo');
  $icon_id=absint($_POST['identity_icon_id']??0);
  if($icon_id&&wp_attachment_is_image($icon_id)) update_option('site_icon',$icon_id);
  elseif(!empty($_POST['identity_icon_clear'])) delete_option('site_icon');
  wp_safe_redirect(add_query_arg(['page'=>'bhc-studio','updated'=>'1'],admin_url('admin.php')));
 }
 exit;
}
add_action('admin_post_bhc_studio_save','bhc_studio_save');

function bhc_studio_publish_draft(){
 if(!current_user_can('manage_options')) wp_die('دسترسی مجاز نیست');
 check_admin_referer('bhc_studio_publish_draft');
 $draft=get_option('bhc_studio_draft',[]);
 if(!is_array($draft)||empty($draft)) wp_die('پیش‌نویسی برای انتشار وجود ندارد.');
 $current=get_option('bhc_studio_options',[]);
 if(is_array($current)&&$current) bhc_studio_store_revision($current,'before-draft-publish');
 update_option('bhc_studio_options',$draft,false);
 delete_option('bhc_studio_draft');
 wp_safe_redirect(add_query_arg(['page'=>'bhc-studio','updated'=>'1'],admin_url('admin.php')));
 exit;
}
add_action('admin_post_bhc_studio_publish_draft','bhc_studio_publish_draft');

function bhc_studio_restore(){
 if(!current_user_can('manage_options')) wp_die('دسترسی مجاز نیست');
 check_admin_referer('bhc_studio_restore');
 $id=sanitize_text_field(wp_unslash($_POST['revision_id']??''));
 $history=get_option('bhc_studio_revisions',[]);
 foreach((array)$history as $revision){
  if(($revision['id']??'')!==$id||!is_array($revision['options']??null)) continue;
  $current=get_option('bhc_studio_options',[]);
  if(is_array($current)&&$current) bhc_studio_store_revision($current,'before-restore');
  update_option('bhc_studio_options',$revision['options'],false);
  wp_safe_redirect(add_query_arg(['page'=>'bhc-studio','restored'=>'1'],admin_url('admin.php')));
  exit;
 }
 wp_die('نسخه انتخاب‌شده پیدا نشد.');
}
add_action('admin_post_bhc_studio_restore','bhc_studio_restore');

function bhc_studio_product_select($name,$selected){
 $preview_id='bhc-product-preview-'.sanitize_title($name);
 echo '<select class="bhc-product-select" name="'.esc_attr($name).'" data-product-preview="'.esc_attr($preview_id).'"><option value="0" data-image="">انتخاب خودکار از کاتالوگ</option>';
 if(function_exists('wc_get_products')){
  foreach(wc_get_products(['status'=>'publish','limit'=>200,'orderby'=>'title','order'=>'ASC']) as $product){
   $image=$product->get_image_id()?wp_get_attachment_image_url($product->get_image_id(),'thumbnail'):'';
   printf('<option value="%d" data-image="%s" %s>%s</option>',$product->get_id(),esc_url($image),selected($selected,$product->get_id(),false),esc_html($product->get_name()));
  }
 }
 echo '</select><span id="'.esc_attr($preview_id).'" class="bhc-product-preview" aria-live="polite"></span>';
}

function bhc_studio_brand_file($brand){
 $dir=get_theme_file_path('assets/images/brands');
 $candidates=[];
 if(function_exists('bhb_brand_identity')&&function_exists('bhb_brand_key')){
  $key=bhb_brand_key($brand['name']??'');
  $latin_key=bhb_brand_key($brand['latin']??'');
  foreach(bhb_brand_identity() as $id_key=>$meta){
   $needles=array_merge([$id_key,$meta['name']??'',$meta['latin']??''],preg_split('/\|/u',(string)($meta['aliases']??'')));
   foreach($needles as $needle){
    $needle_key=bhb_brand_key($needle);
    if($needle_key!==''&&($needle_key===$key||$needle_key===$latin_key)){ $candidates[]=$meta['logo']??''; break 2; }
   }
  }
 }
 $candidates[]=$brand['logo']??'';
 $latin=strtolower((string)preg_replace('/[^a-z0-9]+/i','',str_replace('+','',$brand['latin']??'')));
 foreach([$latin.'.svg',$latin.'.webp',$latin.'.png',$latin.'-v2.png'] as $file) $candidates[]=$file;
 foreach($candidates as $file){
  $file=sanitize_file_name((string)$file);
  if($file!==''&&is_file($dir.'/'.$file)) return $file;
 }
 return '';
}
function bhc_studio_file_preview($relative,$note){
 $path=get_theme_file_path($relative);
 if(!is_file($path)) return '<span class="bhc-media-empty">فایل روی قالب پیدا نشد</span>';
 return '<img class="bhc-media-thumb" src="'.esc_url(get_theme_file_uri($relative)).'" alt=""><small class="description">'.esc_html($note).'</small>';
}
function bhc_studio_media_preview($id,$size='thumbnail'){
 $id=absint($id);
 if(!$id) return '<span class="bhc-media-empty">رسانه‌ای انتخاب نشده</span>';
 $image=wp_get_attachment_image($id,$size,false,['class'=>'bhc-media-thumb','alt'=>'']);
 return $image?:'<span class="bhc-media-empty">رسانه معتبر نیست</span>';
}

function bhc_studio_page(){
 if(!current_user_can('manage_options')) return;
 $opts=bhc_studio_opts();
 $catalog=function_exists('bhb_section_catalog')?bhb_section_catalog():[];
 $labels=['hero'=>'هیرو ارگانیک','categories'=>'دسته‌بندی تصویری','bento'=>'بنرهای نامتقارن','featured'=>'کارت‌های محصول','offers'=>'پیشنهادهای ویژه','rooms'=>'خرید بر اساس فضا','brands'=>'ویترین برندها','best-sellers'=>'پربازدیدها','journal'=>'مجله و راهنما','instagram'=>'ریل اینستاگرام','trust'=>'خدمات اعتماد','newsletter'=>'خبرنامه'];
 $emails=get_option('bhb_newsletter_emails',[]);
 $history=get_option('bhc_studio_revisions',[]);
 $draft=get_option('bhc_studio_draft',[]);
 $terms=taxonomy_exists('product_cat')?get_terms(['taxonomy'=>'product_cat','hide_empty'=>false,'number'=>100]):[];
 if(is_wp_error($terms)) $terms=[];
 $product_ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'numberposts'=>-1,'fields'=>'ids']);$missing_product_images=0;foreach($product_ids as $pid)if(!has_post_thumbnail($pid))$missing_product_images++;$category_images=0;foreach($terms as $term)if((int)get_term_meta($term->term_id,'thumbnail_id',true))$category_images++;$brand_rows=(array)($opts['brands']??[]);$reel_rows=(array)($opts['reels']??[]);$enabled_sections=count((array)$opts['enabled']);
 $audit_items=[];if($missing_product_images)$audit_items[]=['type'=>'warn','title'=>'محصول بدون تصویر شاخص','count'=>$missing_product_images,'link'=>admin_url('edit.php?post_type=product'),'label'=>'مدیریت محصولات'];$missing_category_images=max(0,count($terms)-$category_images);if($missing_category_images)$audit_items[]=['type'=>'warn','title'=>'دسته بدون تصویر','count'=>$missing_category_images,'link'=>admin_url('edit-tags.php?taxonomy=product_cat&post_type=product'),'label'=>'مدیریت دسته‌ها'];$missing_brand_logos=0;foreach($brand_rows as $brand)if(empty($brand['image_id'])&&bhc_studio_brand_file($brand)==='')$missing_brand_logos++;if($missing_brand_logos)$audit_items[]=['type'=>'info','title'=>'برند بدون لوگوی مجاز','count'=>$missing_brand_logos,'link'=>'#studio-brands','label'=>'تکمیل برندها'];$incomplete_reels=0;foreach($reel_rows as $reel)if(empty($reel['url'])||empty($reel['image_id']))$incomplete_reels++;if($incomplete_reels)$audit_items[]=['type'=>'info','title'=>'ریل ناقص (لینک یا کاور)','count'=>$incomplete_reels,'link'=>'#studio-reels','label'=>'تکمیل ریل‌ها'];$disabled_sections=max(0,count($labels)-$enabled_sections);if($disabled_sections)$audit_items[]=['type'=>'neutral','title'=>'سکشن غیرفعال','count'=>$disabled_sections,'link'=>'#studio-order','label'=>'تنظیم نمایش'];$audit_score=$audit_items?max(0,100-min(90,count($audit_items)*15)):100;
 ?>
 <div class="wrap bhc-studio-wrap" dir="rtl">
  <div class="bhc-studio-head"><div><span>BAHARAN HOME / STUDIO</span><h1>بهاران استودیو</h1><p>مرکز مدیریت سکشن‌ها، متن‌ها، رنگ‌ها، رسانه‌ها و برندها؛ همه شاخص‌ها از داده واقعی فروشگاه محاسبه می‌شوند.</p></div><div class="bhc-studio-head-actions"><a class="bhc-studio-badge" href="<?php echo esc_url(home_url('/')); ?>" target="_blank" rel="noopener">پیش‌نمایش صفحه اصلی</a><a class="button" href="#studio-order" data-studio-jump="studio-order">ممیزی ترتیب و نمایش</a></div></div>
  <nav class="bhc-studio-tabs" aria-label="بخش‌های استودیو">
   <button type="button" class="is-active" data-group="overview" aria-selected="true">نمای کلی</button>
   <button type="button" data-group="banners" aria-selected="false">بنرها و رسانه</button>
   <button type="button" data-group="content" aria-selected="false">متن و فضاها</button>
   <button type="button" data-group="catalog" aria-selected="false">برندها و کاتالوگ</button>
   <button type="button" data-group="shop" aria-selected="false">فروشگاه و آرشیو</button>
   <button type="button" data-group="tools" aria-selected="false">ابزارها</button>
  </nav>
  <div class="bhc-studio-kpis"><article><b><?php echo esc_html(number_format_i18n($enabled_sections)); ?></b><span>سکشن فعال</span><small>از <?php echo esc_html(number_format_i18n(count($labels))); ?> بخش استاندارد</small></article><article><b><?php echo esc_html(number_format_i18n(count($product_ids))); ?></b><span>محصول قابل استفاده</span><small><?php echo esc_html(number_format_i18n($missing_product_images)); ?> بدون تصویر شاخص</small></article><article><b><?php echo esc_html(number_format_i18n(count($terms))); ?></b><span>دسته واقعی</span><small><?php echo esc_html(number_format_i18n($category_images)); ?> دارای تصویر</small></article><article><b><?php echo esc_html(number_format_i18n(count($brand_rows))); ?></b><span>برند قابل مدیریت</span><small><?php echo esc_html(number_format_i18n(count($reel_rows))); ?> ریل ثبت‌شده</small></article></div>
  <section class="bhc-studio-audit"><div class="bhc-studio-audit-head"><div><span>ممیزی آماده‌سازی انتشار</span><h2><?php echo $audit_items?'چند مورد نیازمند تکمیل است':'استودیو آماده انتشار است'; ?></h2><p>امتیاز آمادگی بر اساس رسانه‌ها و سکشن‌های واقعی همین فروشگاه محاسبه شده است.</p></div><strong class="<?php echo $audit_score>=85?'is-good':($audit_score>=60?'is-warn':'is-risk'); ?>"><?php echo esc_html($audit_score); ?>٪</strong></div><?php if($audit_items): ?><div class="bhc-studio-audit-grid"><?php foreach($audit_items as $issue): ?><a class="bhc-studio-audit-item <?php echo esc_attr($issue['type']); ?>" href="<?php echo esc_url($issue['link']); ?>"><span><?php echo esc_html($issue['title']); ?></span><b><?php echo esc_html(number_format_i18n($issue['count'])); ?></b><small><?php echo esc_html($issue['label']); ?> ←</small></a><?php endforeach; ?></div><?php else: ?><div class="bhc-studio-audit-ok">هیچ نقص قابل تشخیصی در داده‌های فعلی پیدا نشد. قبل از انتشار، پیش‌نمایش صفحه اصلی را بررسی کنید.</div><?php endif; ?></section>
  <?php if(isset($_GET['updated'])): ?><div class="notice notice-success is-dismissible"><p>تنظیمات استودیو ذخیره شد.</p></div><?php endif; ?>
  <?php if(isset($_GET['draft_saved'])): ?><div class="notice notice-info is-dismissible"><p>پیش‌نویس ذخیره شد و هنوز روی سایت منتشر نشده است.</p></div><?php endif; ?>
  <?php if(is_array($draft)&&!empty($draft)): ?><div class="bhc-studio-draft-bar"><b>پیش‌نویس آماده انتشار است.</b><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="bhc_studio_publish_draft"><?php wp_nonce_field('bhc_studio_publish_draft'); ?><button class="button button-primary" type="submit">انتشار پیش‌نویس</button></form></div><?php endif; ?>
  <?php if(isset($_GET['restored'])): ?><div class="notice notice-success is-dismissible"><p>نسخه قبلی تنظیمات با موفقیت بازیابی شد.</p></div><?php endif; ?>
  <?php if(is_array($history)&&$history): ?><details class="bhc-studio-history"><summary>تاریخچه و بازگردانی نسخه‌ها (<?php echo esc_html(number_format_i18n(count($history))); ?>)</summary><div class="bhc-history-list"><?php foreach(array_slice($history,0,10) as $revision): ?><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="bhc_studio_restore"><input type="hidden" name="revision_id" value="<?php echo esc_attr($revision['id']??''); ?>"><?php wp_nonce_field('bhc_studio_restore'); ?><span><?php echo esc_html(bhc_studio_revision_label($revision['created_at']??0)); ?></span><button type="submit" class="button" onclick="return confirm('این نسخه جایگزین تنظیمات فعلی شود؟');">بازگردانی</button></form><?php endforeach; ?></div></details><?php endif; ?>
  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
   <input type="hidden" name="action" value="bhc_studio_save">
   <?php wp_nonce_field('bhc_studio_save'); ?>
   <div class="bhc-studio-layout">
    <aside class="bhc-studio-side">
     <strong>بخش‌ها</strong>
     <nav>
      <a href="#studio-reels" data-studio-jump="studio-reels">ریل اینستاگرام</a>
      <a href="#studio-order" data-studio-jump="studio-order">ترتیب صفحه</a>
      <a href="#studio-hero" data-studio-jump="studio-hero">هیرو و رنگ</a>
      <a href="#studio-banners" data-studio-jump="studio-banners">بنرها</a>
      <a href="#studio-archive" data-studio-jump="studio-archive">صفحه دسته‌بندی</a>
      <a href="#studio-identity" data-studio-jump="studio-identity">نام، لوگو و نشان</a>
      <a href="#studio-commerce" data-studio-jump="studio-commerce">تنظیمات فروشگاه، چت و SEO</a>
      <a href="#studio-copy" data-studio-jump="studio-copy">متن‌ها</a>
      <a href="#studio-brands" data-studio-jump="studio-brands">برندها</a>
     </nav>
    </aside>
    <div class="bhc-studio-main">
     <section class="bhc-studio-panel" id="studio-needed" data-studio-group="overview">
      <header><h2>برای چسبیدن ظاهر به وایرفریم</h2><p>این‌ها را همین‌جا بگذارید. تا فایل نرسد، فروشگاه تصویر یا تخفیف نمی‌سازد.</p></header>
      <ol class="bhc-needed">
       <li>بنر ۱ و ۲ و ۳: تصویر دسکتاپ و موبایل در بخش بنرها.</li>
       <li>دسته: تصویر شاخص هر دسته در محصولات ووکامرس.</li>
       <li>ریل: نشانی هر پست اینستاگرام به‌علاوه کاور جدا. لینک پروفایل کافی نیست.</li>
       <li>برند فروشگاه: ویژگی pa_brand روی کالا، اگر فیلتر برند لازم است.</li>
       <li>گالری محصول: تصویرهای اضافه در گالری همان کالا، تا بندانگشتی صفحه محصول پر شود.</li>
      </ol>
     </section>
     <section class="bhc-studio-panel" id="studio-reels" data-studio-group="banners" hidden><header><h2>ریل اینستاگرام</h2><p>چیدمان، متن و کاور همین‌جا کنترل می‌شود. بدون آدرس، کارت روی سایت نمی‌آید.</p></header>
      <?php $rd=$opts['reels_display']; ?>
      <div class="bhc-fields">
       <label>چیدمان<select name="reels_display[layout]"><option value="rail" <?php selected($rd['layout'],'rail'); ?>>ردیف کشیدنی</option><option value="center" <?php selected($rd['layout'],'center'); ?>>چند کارت وسط صفحه</option><option value="grid" <?php selected($rd['layout'],'grid'); ?>>شبکه تمام‌عرض</option></select></label>
       <label>تعداد ستون<select name="reels_display[columns]"><option value="2" <?php selected($rd['columns'],2); ?>>۲</option><option value="3" <?php selected($rd['columns'],3); ?>>۳</option><option value="4" <?php selected($rd['columns'],4); ?>>۴</option></select></label>
       <label>جای متن<select name="reels_display[copy]"><option value="overlay" <?php selected($rd['copy'],'overlay'); ?>>روی تصویر، پایین کارت</option><option value="below" <?php selected($rd['copy'],'below'); ?>>زیر تصویر، جدا از عکس</option><option value="hidden" <?php selected($rd['copy'],'hidden'); ?>>فقط تصویر و دکمه پخش</option></select></label>
       <label>متن دکمه<input name="reels_display[button]" value="<?php echo esc_attr($rd['button']); ?>"></label>
       <label>شعاع گوشه کارت<input type="number" min="12" max="36" name="reels_display[radius]" value="<?php echo esc_attr($rd['radius']); ?>"></label>
       <label class="bhc-studio-check"><input type="checkbox" name="reels_display[show_title]" value="1" <?php checked($rd['show_title'],1); ?>> عنوان روی کارت</label>
       <label class="bhc-studio-check"><input type="checkbox" name="reels_display[show_caption]" value="1" <?php checked($rd['show_caption'],1); ?>> توضیح کوتاه</label>
       <label class="bhc-studio-check"><input type="checkbox" name="reels_display[show_play]" value="1" <?php checked($rd['show_play'],1); ?>> دکمه پخش</label>
       <label class="bhc-studio-check"><input type="checkbox" name="reels_display[show_index]" value="1" <?php checked($rd['show_index'],1); ?>> شماره ریل</label>
      </div>
      <div class="bhc-repeat" data-repeat="reel">
      <?php foreach($opts['reels'] as $i=>$reel): ?>
      <article class="bhc-card">
       <div class="bhc-card-head"><b>ریل</b><button class="button-link-delete bhc-remove" type="button">حذف</button></div>
       <div class="bhc-fields">
        <label>عنوان کارت<input name="reel_title[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($reel['title']); ?>"></label>
        <label>توضیح کوتاه<input name="reel_caption[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($reel['caption']); ?>"></label>
        <label>متن جایگزین تصویر<input name="reel_alt[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($reel['alt']??''); ?>" placeholder="برای سئو؛ خالی = عنوان کارت"></label>
        <label class="is-wide">آدرس پست یا ریل<input type="url" name="reel_url[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($reel['url']); ?>" placeholder="https://www.instagram.com/reel/..."></label>
        <label class="is-wide bhc-reel-shot">تصویر کاور، عمودی ۹:۱۶<input id="reel-image-<?php echo esc_attr($i); ?>" type="number" min="0" name="reel_image[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($reel['image_id']); ?>"> <button class="button bhc-media" type="button" data-target="reel-image-<?php echo esc_attr($i); ?>">انتخاب از رسانه</button><span class="bhc-media-preview" data-preview-for="reel-image-<?php echo esc_attr($i); ?>"><?php echo bhc_studio_media_preview($reel['image_id']); ?></span></label>
       </div>
      </article>
      <?php endforeach; ?>
      </div>
      <button class="button bhc-add" type="button" data-template="bhc-reel-template" data-repeat="reel">افزودن ریل</button>
      <template id="bhc-reel-template">
       <article class="bhc-card">
        <div class="bhc-card-head"><b>ریل تازه</b><button class="button-link-delete bhc-remove" type="button">حذف</button></div>
        <div class="bhc-fields">
         <label>عنوان<input name="reel_title[__i__]" value=""></label>
         <label>توضیح کوتاه<input name="reel_caption[__i__]" value=""></label>
         <label class="is-wide">آدرس پست یا ریل<input type="url" name="reel_url[__i__]" value="" placeholder="https://www.instagram.com/reel/..."></label>
         <label class="is-wide">شناسه کاور<input id="reel-image-__i__" type="number" min="0" name="reel_image[__i__]" value="0"> <button class="button bhc-media" type="button" data-target="reel-image-__i__">انتخاب از رسانه</button></label>
        </div>
       </article>
      </template>
     </section>
     <section class="bhc-studio-panel" id="studio-order" data-studio-group="overview"><header><h2>ترتیب سکشن‌ها</h2><p>ردیف را بکشید. تیک، نمایش همان بخش در صفحه اصلی است.</p></header><ol id="bhc-studio-sortable"><?php foreach($opts['order'] as $i=>$key): ?><li><input type="hidden" name="bhc_order[]" value="<?php echo esc_attr($key); ?>"><span class="bhc-drag" aria-hidden="true">☷</span><span class="bhc-num"><?php echo esc_html($i+1); ?></span><b><?php echo esc_html($labels[$key]??($catalog[$key]['label']??$key)); ?></b><label><input type="checkbox" name="bhc_enabled[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key,$opts['enabled'],true)); ?>> فعال</label></li><?php endforeach; ?></ol></section>
     <div class="bhc-studio-grid" id="studio-hero" data-studio-group="overview">
      <section class="bhc-studio-panel"><header><h2>هیرو</h2><p>عنوان خالی یعنی عنوان دوخطی طرح. پس‌زمینه جدا از عکس محصول است.</p></header>
       <label>عنوان جایگزین<input name="hero_title" value="<?php echo esc_attr($opts['hero_title']); ?>" placeholder="خالی بماند تا عنوان طرح استفاده شود"></label>
       <label>زیرعنوان جایگزین<textarea name="hero_subtitle" rows="3"><?php echo esc_textarea($opts['hero_subtitle']); ?></textarea></label>
       <label>محصول اصلی هیرو<?php bhc_studio_product_select('hero_product_id',$opts['hero_product_id']); ?></label>
       <label>منبع کارت‌های محصول<select name="featured_source"><?php foreach(['latest'=>'جدیدترین','featured'=>'ویژه ووکامرس','sale'=>'قیمت ویژه واقعی','popularity'=>'پربازدید'] as $value=>$label): ?><option value="<?php echo esc_attr($value); ?>" <?php selected($opts['featured_source'],$value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
       <label class="is-wide">پس‌زمینه دسکتاپ <small class="description">۱۶:۹ افقی. اگر از رسانه چیزی انتخاب نشود، همین فایل قالب روی سایت می‌ماند.</small><input id="hero-bg" type="number" min="0" name="hero_bg_id" value="<?php echo esc_attr($opts['hero_bg_id']); ?>"> <button class="button bhc-media" type="button" data-target="hero-bg">انتخاب از رسانه</button><span class="bhc-media-preview" data-preview-for="hero-bg"><?php echo $opts['hero_bg_id']?bhc_studio_media_preview($opts['hero_bg_id']):bhc_studio_file_preview('assets/images/generated/hero-studio-background-v2.webp','پس‌زمینه فعلی صفحه اصلی'); ?></span></label>
       <label class="is-wide">پس‌زمینه موبایل <small class="description">۴:۵ عمودی. خالی یعنی نسخه موبایل همین قالب.</small><input id="hero-bg-mobile" type="number" min="0" name="hero_bg_mobile_id" value="<?php echo esc_attr($opts['hero_bg_mobile_id']); ?>"> <button class="button bhc-media" type="button" data-target="hero-bg-mobile">انتخاب از رسانه</button><span class="bhc-media-preview" data-preview-for="hero-bg-mobile"><?php echo $opts['hero_bg_mobile_id']?bhc_studio_media_preview($opts['hero_bg_mobile_id']):bhc_studio_file_preview('assets/images/generated/hero-studio-background-v2-mobile.webp','پس‌زمینه فعلی موبایل'); ?></span></label>
       <div class="bhc-prompt"><b>پرامپت پس‌زمینه هیرو</b><textarea readonly rows="5">پس‌زمینه افقی پریمیوم برای هیرو فروشگاه لوازم خانگی بهاران، نسبت ۱۶:۹ برای دسکتاپ و نسخه جدا ۴:۵ برای موبایل. فضای داخلی مدرن و روشن، دیوار و کف روشن، نور نرم صبحگاهی از چپ، عمق میدان ملایم. پالت سرمه‌ای #0B2039، آبی #1769D2 و یخی #EAF3FF با یک اکسنت نارنجی خیلی کم. سمت راست کادر کاملاً خلوت برای تیتر فارسی؛ سوژه مبهم خانه فقط در سمت چپ و پس‌زمینه. بدون انسان، بدون متن، بدون لوگو، بدون برند، بدون محصول قابل‌شناسایی، بدون واترمارک. خروجی WebP با کیفیت بالا، لبه‌ها crop-safe.</textarea><button class="button bhc-copy" type="button">کپی پرامپت</button></div>
       <label>عرض کانتینر<input type="number" min="1100" max="1440" name="container_width" value="<?php echo esc_attr($opts['container_width']); ?>"></label>
       <label>خروج تصویر از قاب هیرو<input type="number" min="0" max="120" name="hero_overflow" value="<?php echo esc_attr($opts['hero_overflow']); ?>"></label>
       <label class="bhc-studio-check"><input type="checkbox" name="motion" value="1" <?php checked(!empty($opts['motion'])); ?>> حرکت‌های غیرضروری</label>
      </section>
      <section class="bhc-studio-panel"><header><h2>رنگ‌ها</h2></header>
       <?php foreach(['navy'=>'سرمه‌ای، متن و نشان سربرگ','blue'=>'آبی، دکمه‌ها و لینک‌ها','ice'=>'یخی، سطح کارت‌ها','bg'=>'پس‌زمینه صفحه','accent'=>'نارنجی، تأکید و دکمه دوم'] as $key=>$label): ?><label><?php echo esc_html($label); ?><input type="color" name="colors[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($opts['colors'][$key]); ?>"></label><?php endforeach; ?>
       <p class="description">این پنج رنگ همان متغیرهای زنده سایت هستند: نشان خانه در سربرگ با سرمه‌ای پر می‌شود، دکمه هیرو آبی است، کلمه رنگی عنوان از اکسنت می‌آید و زمینه صفحه از رنگ پس‌زمینه.</p>
       <p class="description">عضویت‌های خبرنامه: <?php echo esc_html(number_format_i18n(is_array($emails)?count($emails):0)); ?></p>
      </section>
      <section class="bhc-studio-panel bhc-live-preview" aria-live="polite">
       <header><h2>پیش‌نمایش زنده سکشن</h2><div class="bhc-preview-switch"><button type="button" class="is-active" data-preview-mode="desktop">دسکتاپ</button><button type="button" data-preview-mode="mobile">موبایل</button></div></header>
       <div class="bhc-preview-stage" data-preview-stage="desktop"><div class="bhc-preview-hero"><span class="bhc-preview-kicker">BAHARAN HOME</span><strong data-preview-title><?php echo esc_html($opts['hero_title']?:'عنوان هیرو'); ?></strong><p data-preview-subtitle><?php echo esc_html($opts['hero_subtitle']?:'زیرعنوان سکشن در اینجا نمایش داده می‌شود.'); ?></p><button type="button" class="bhc-preview-cta" data-preview-cta><?php echo esc_html($opts['copy']['hero_cta']?:'مشاهده محصولات'); ?></button></div></div>
       <div class="bhc-layer-tree"><b>ساختار لایه‌ها</b><span>عنوان هیرو</span><span>زیرعنوان</span><span>تصویر / محصول</span><span>دکمه CTA</span></div>
      </section>
     </div>
     <div id="studio-banners" class="bhc-stack" data-studio-group="banners" hidden>
     <?php foreach($opts['banners'] as $i=>$banner): ?>
     <section class="bhc-studio-panel"><header><h2>بنر <?php echo esc_html($i); ?></h2><p>سه بنر چیدمان ثابت صفحه هستند. متن، محصول و تصویر هر کدام جدا ذخیره می‌شود.</p></header>
      <div class="bhc-fields">
       <label class="bhc-studio-check"><input type="checkbox" name="banner_enabled[<?php echo esc_attr($i); ?>]" value="1" <?php checked(!isset($banner['enabled'])||$banner['enabled'],true); ?>> نمایش این کارت <small class="description">برای حذف کارت از صفحه، این گزینه را بردارید.</small></label>
       <label>عنوان<input name="banner_title[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($banner['title']); ?>"></label>
       <label>متن<input name="banner_text[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($banner['text']); ?>"></label>
       <label>متن دکمه<input name="banner_cta[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($banner['cta']); ?>"></label>
       <label>لینک<input type="url" name="banner_url[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($banner['url']); ?>" placeholder="خالی = لینک محصول انتخاب‌شده"></label>
       <label>رنگ<select name="banner_tone[<?php echo esc_attr($i); ?>]"><?php foreach(['navy'=>'سرمه‌ای','orange'=>'نارنجی','ice'=>'یخی'] as $tone=>$label): ?><option value="<?php echo esc_attr($tone); ?>" <?php selected($banner['tone'],$tone); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
       <label>محصول بیرون‌زده<?php bhc_studio_product_select('banner_product['.$i.']',$banner['product_id']); ?></label>
       <label>محصول دوم<?php bhc_studio_product_select('banner_product_2['.$i.']',$banner['product_id_2']); ?></label>
       <?php $banner_live=[1=>'assets/images/generated/bento-washer.webp',2=>'assets/images/generated/bento-soundbar.webp',3=>'assets/images/generated/bento-airfryer.webp']; $banner_file=$banner_live[$i]??''; ?>
       <label>تصویر دسکتاپ<input id="banner-image-<?php echo esc_attr($i); ?>" type="number" min="0" name="banner_image[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($banner['image_id']); ?>"> <button class="button bhc-media" type="button" data-target="banner-image-<?php echo esc_attr($i); ?>">انتخاب از رسانه</button><span class="bhc-media-preview" data-preview-for="banner-image-<?php echo esc_attr($i); ?>"><?php echo $banner['image_id']?bhc_studio_media_preview($banner['image_id']):($banner_file?bhc_studio_file_preview($banner_file,'همین تصویر الان روی بنر سایت است'):bhc_studio_media_preview(0)); ?></span></label>
       <label>تصویر موبایل<input id="banner-image-mobile-<?php echo esc_attr($i); ?>" type="number" min="0" name="banner_image_mobile[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($banner['image_mobile_id']); ?>"> <button class="button bhc-media" type="button" data-target="banner-image-mobile-<?php echo esc_attr($i); ?>">انتخاب از رسانه</button><span class="bhc-media-preview" data-preview-for="banner-image-mobile-<?php echo esc_attr($i); ?>"><?php echo $banner['image_mobile_id']?bhc_studio_media_preview($banner['image_mobile_id']):($banner_file?bhc_studio_file_preview($banner_file,'موبایل الان همین تصویر دسکتاپ را نشان می‌دهد'):bhc_studio_media_preview(0)); ?></span></label>
       <?php $banner_prompt=['navy'=>'پس‌زمینه افقی ۱۶:۶ برای بنر سرمه‌ای فروشگاه لوازم خانگی، و نسخه جدا ۴:۵ موبایل. آشپزخانه مدرن و مرتب، کابینت سفید و چوب روشن، نور صبح، فضای خالی بزرگ سمت راست برای متن فارسی. پالت سرمه‌ای و آبی. بدون انسان، متن، لوگو و برند. WebP.','orange'=>'تصویر افقی برای کارت نارنجی: فضای گرم خانه با نور نرم، پالت کرم و نارنجی سوخته، فضای منفی برای دکمه. نسخه دسکتاپ ۱۶:۶ و موبایل ۴:۵ جدا. بدون متن، لوگو، دست و محصول قابل‌شناسایی.','ice'=>'فضای روشن و سرد خانه برای بنر یخی، کف روشن، نور آبی-سفید، ترکیب افقی با حاشیه امن برای برش. بدون متن، لوگو و انسان. WebP.']; ?>
       <div class="bhc-prompt"><b>پرامپت تصویر این بنر</b><textarea readonly rows="4"><?php echo esc_textarea($banner_prompt[$banner['tone']]??$banner_prompt['navy']); ?></textarea><button class="button bhc-copy" type="button">کپی پرامپت</button></div>
      </div>
     </section>
     <?php endforeach; ?>
     </div>
     <section class="bhc-studio-panel" id="studio-archive" data-studio-group="shop" hidden><header><h2>صفحه دسته‌بندی محصولات</h2><p>چیدمان آرشیو از داده واقعی ووکامرس استفاده می‌کند؛ این گزینه‌ها نمایش، ترتیب و رفتار فیلترها را کنترل می‌کنند.</p></header><div class="bhc-fields"><label>ستون‌های دسکتاپ<select name="archive[columns]"><option value="2" <?php selected($opts['archive']['columns'],2); ?>>۲ ستون</option><option value="3" <?php selected($opts['archive']['columns'],3); ?>>۳ ستون</option><option value="4" <?php selected($opts['archive']['columns'],4); ?>>۴ ستون</option></select></label><label>تعداد محصول در صفحه<input type="number" min="6" max="48" name="archive[products_per_page]" value="<?php echo esc_attr($opts['archive']['products_per_page']); ?>"></label><label>شعاع کارت<input type="number" min="12" max="40" name="archive[card_radius]" value="<?php echo esc_attr($opts['archive']['card_radius']); ?>"></label><label class="bhc-studio-check"><input type="checkbox" name="archive[show_banner]" value="1" <?php checked($opts['archive']['show_banner'],1); ?>> نمایش بنر دسته</label><label class="bhc-studio-check"><input type="checkbox" name="archive[show_filters]" value="1" <?php checked($opts['archive']['show_filters'],1); ?>> نمایش فیلترها</label><label class="bhc-studio-check"><input type="checkbox" name="archive[show_sale_filter]" value="1" <?php checked($opts['archive']['show_sale_filter'],1); ?>> نمایش فیلتر تخفیف</label><label class="bhc-studio-check"><input type="checkbox" name="archive[show_rating]" value="1" <?php checked($opts['archive']['show_rating'],1); ?>> نمایش امتیاز</label><label class="bhc-studio-check"><input type="checkbox" name="archive[show_stock]" value="1" <?php checked($opts['archive']['show_stock'],1); ?>> نمایش وضعیت موجودی</label><label>تعداد اولیه برند/دسته<input type="number" min="4" max="40" name="archive[filter_initial_limit]" value="<?php echo esc_attr($opts['archive']['filter_initial_limit']); ?>"></label><label class="bhc-studio-check"><input type="checkbox" name="archive[filter_sticky]" value="1" <?php checked($opts['archive']['filter_sticky'],1); ?>> چسبندگی فیلتر دسکتاپ</label><label class="bhc-field-wide">ترتیب فیلترها <?php $filter_names=['price'=>'قیمت','brand'=>'برند','category'=>'دسته‌بندی','availability'=>'موجودی','sale'=>'تخفیف']; foreach($opts['archive']['filter_order'] as $slot=>$key): ?><select name="archive[filter_order][]" aria-label="جایگاه <?php echo esc_attr($slot+1); ?>"><?php foreach($filter_names as $filter_key=>$filter_label): ?><option value="<?php echo esc_attr($filter_key); ?>" <?php selected($key,$filter_key); ?>><?php echo esc_html(($slot+1).'. '.$filter_label); ?></option><?php endforeach; ?></select><?php endforeach; ?><small>هر فیلتر را فقط یک‌بار انتخاب کنید. ترتیب از بالا به پایین همان ترتیب صفحه است.</small></label><label>عنوان قیمت<input name="archive[filter_label_price]" value="<?php echo esc_attr($opts['archive']['filter_labels']['price']); ?>"></label><label>عنوان برند<input name="archive[filter_label_brand]" value="<?php echo esc_attr($opts['archive']['filter_labels']['brand']); ?>"></label><label>عنوان دسته‌بندی<input name="archive[filter_label_category]" value="<?php echo esc_attr($opts['archive']['filter_labels']['category']); ?>"></label><label>عنوان موجودی<input name="archive[filter_label_availability]" value="<?php echo esc_attr($opts['archive']['filter_labels']['availability']); ?>"></label><label>عنوان تخفیف<input name="archive[filter_label_sale]" value="<?php echo esc_attr($opts['archive']['filter_labels']['sale']); ?>"></label></div>     </section>
     <?php $identity_logo=absint(get_theme_mod('custom_logo')); $identity_icon=absint(get_option('site_icon')); $commerce_now=class_exists('Baharaan_Commerce')?Baharaan_Commerce::opts():[]; ?>
     <section class="bhc-studio-panel" id="studio-identity" data-studio-group="shop" hidden><header><h2>نام، لوگو و نشان سایت</h2><p>همین مقدارها در سربرگ، فوتر، عنوان مرورگر و آیکن تب استفاده می‌شوند. نشان خانه، SVG ثابت قالب است و رنگش از سرمه‌ای هیرو می‌آید.</p></header>
      <div class="bhc-fields">
       <label>نام سایت در عنوان مرورگر<input name="identity_blogname" value="<?php echo esc_attr(get_option('blogname')); ?>"></label>
       <label>بخش اول نام در سربرگ<input name="commerce[brand_first]" value="<?php echo esc_attr($opts['commerce']['brand_first']??($commerce_now['brand_first']??'بهاران')); ?>"></label>
       <label>بخش رنگی نام<input name="commerce[brand_second]" value="<?php echo esc_attr($opts['commerce']['brand_second']??($commerce_now['brand_second']??'هوم')); ?>"></label>
       <label class="is-wide">لوگوی تصویری سربرگ <small class="description">اگر انتخاب شود، به‌جای نشان خانه و نام متنی می‌نشیند. SVG نشان همچنان برای حالت بدون لوگو باقی می‌ماند.</small><input id="identity-logo" type="number" min="0" name="identity_logo_id" value="<?php echo esc_attr($identity_logo); ?>"> <button class="button bhc-media" type="button" data-target="identity-logo">انتخاب از رسانه</button><span class="bhc-media-preview" data-preview-for="identity-logo"><?php echo $identity_logo?bhc_studio_media_preview($identity_logo):'<span class="bhc-media-empty">الان نشان خانه SVG نمایش داده می‌شود</span>'; ?></span></label>
       <label class="bhc-studio-check"><input type="checkbox" name="identity_logo_clear" value="1"> حذف لوگوی تصویری و بازگشت به نشان خانه</label>
       <label class="is-wide">آیکن سایت (فاویکون) <small class="description">مربع، ترجیحاً ۵۱۲ پیکسل. اگر خالی بماند، favicon.svg قالب استفاده می‌شود.</small><input id="identity-icon" type="number" min="0" name="identity_icon_id" value="<?php echo esc_attr($identity_icon); ?>"> <button class="button bhc-media" type="button" data-target="identity-icon">انتخاب از رسانه</button><span class="bhc-media-preview" data-preview-for="identity-icon"><?php echo $identity_icon?bhc_studio_media_preview($identity_icon):bhc_studio_file_preview('assets/images/favicon.svg','فاویکون فعلی قالب'); ?></span></label>
       <label class="bhc-studio-check"><input type="checkbox" name="identity_icon_clear" value="1"> حذف آیکن بارگذاری‌شده و بازگشت به SVG قالب</label>
      </div>
     </section>
     <section class="bhc-studio-panel" id="studio-commerce" data-studio-group="shop" hidden><header><h2>تنظیمات یکپارچه فروشگاه</h2><p>هیرو، ارتباط، رفتار فروشگاه، منوی مگا، چت، تصویر و SEO از همین بخش کنترل می‌شوند.</p></header><div class="bhc-fields"><h3>هیرو و اطلاع‌رسانی</h3><label>متن دکمه اصلی هیرو<input name="commerce[hero_cta_label]" value="<?php echo esc_attr($opts['commerce']['hero_cta_label']); ?>"></label><label>لینک دکمه هیرو<input type="url" name="commerce[hero_cta_url]" value="<?php echo esc_attr($opts['commerce']['hero_cta_url']); ?>"></label><label class="bhc-field-wide">اطلاع‌رسانی بالای سایت<input name="commerce[home_notice]" value="<?php echo esc_attr($opts['commerce']['home_notice']); ?>"></label><h3>تماس و پشتیبانی</h3><label>نام فروشگاه<input name="commerce[store_name]" value="<?php echo esc_attr($opts['commerce']['store_name']??''); ?>"></label><label class="bhc-field-wide">آدرس فروشگاه<textarea name="commerce[store_address]" rows="3"><?php echo esc_textarea($opts['commerce']['store_address']??''); ?></textarea></label><label>موبایل اصلی<input name="commerce[phone]" value="<?php echo esc_attr($opts['commerce']['phone']); ?>" placeholder="09111490198"></label><label>موبایل دوم<input name="commerce[phone_2]" value="<?php echo esc_attr($opts['commerce']['phone_2']??''); ?>" placeholder="09377735083"></label><label>ایمیل<input type="email" name="commerce[email]" value="<?php echo esc_attr($opts['commerce']['email']); ?>"></label><label>اینستاگرام<input type="url" name="commerce[instagram]" value="<?php echo esc_attr($opts['commerce']['instagram']); ?>"></label><label>ساعت پشتیبانی<input name="commerce[support_hours]" value="<?php echo esc_attr($opts['commerce']['support_hours']); ?>"></label><label>واتساپ<input name="commerce[whatsapp]" value="<?php echo esc_attr($opts['commerce']['whatsapp']); ?>"></label><label>تلگرام<input type="url" name="commerce[telegram]" value="<?php echo esc_attr($opts['commerce']['telegram']); ?>"></label><label>تلگرام ادمین<input type="url" name="commerce[telegram_admin]" value="<?php echo esc_attr($opts['commerce']['telegram_admin']??''); ?>"></label><label>بله<input name="commerce[bale]" value="<?php echo esc_attr($opts['commerce']['bale']); ?>"></label><label>ایتا<input name="commerce[eitaa]" value="<?php echo esc_attr($opts['commerce']['eitaa']); ?>"></label><h3>رفتار و منوی مگا</h3><label class="bhc-studio-check"><input type="checkbox" name="commerce[show_wishlist]" value="1" <?php checked($opts['commerce']['show_wishlist'],1); ?>> علاقه‌مندی‌ها</label><label class="bhc-studio-check"><input type="checkbox" name="commerce[show_compare]" value="1" <?php checked($opts['commerce']['show_compare'],1); ?>> مقایسه محصولات</label><label class="bhc-studio-check"><input type="checkbox" name="commerce[sticky_buy]" value="1" <?php checked($opts['commerce']['sticky_buy'],1); ?>> خرید چسبان موبایل</label><label>سیاست ناموجود<select name="commerce[stock_display_policy]"><option value="in_stock_first" <?php selected($opts['commerce']['stock_display_policy'],'in_stock_first'); ?>>موجودها اول</option><option value="hide_out_of_stock" <?php selected($opts['commerce']['stock_display_policy'],'hide_out_of_stock'); ?>>عدم نمایش ناموجود</option><option value="show_all" <?php selected($opts['commerce']['stock_display_policy'],'show_all'); ?>>نمایش همه</option></select></label><label class="bhc-studio-check"><input type="checkbox" name="commerce[mega_menu_enabled]" value="1" <?php checked($opts['commerce']['mega_menu_enabled'],1); ?>> منوی مگا فعال</label><label>تعداد آیتم مگا<input type="number" min="4" max="12" name="commerce[mega_menu_limit]" value="<?php echo esc_attr($opts['commerce']['mega_menu_limit']); ?>"></label><label class="bhc-field-wide">محصول ویژه مگا<?php bhc_studio_product_select('commerce[mega_menu_feature_product_id]',$opts['commerce']['mega_menu_feature_product_id']); ?></label><h3>چت، هوش مصنوعی و تصویر</h3><label class="bhc-field-wide">متن چت<textarea name="commerce[chat_greeting]" rows="2"><?php echo esc_textarea($opts['commerce']['chat_greeting']); ?></textarea></label><label class="bhc-field-wide">آدرس آواتار چت<input type="url" name="commerce[chat_avatar]" value="<?php echo esc_attr($opts['commerce']['chat_avatar']); ?>"></label><label class="bhc-studio-check"><input type="checkbox" name="commerce[ai_enabled]" value="1" <?php checked($opts['commerce']['ai_enabled'],1); ?>> هوش مصنوعی فعال</label><label>Endpoint AI<input type="url" name="commerce[ai_endpoint]" value="<?php echo esc_attr($opts['commerce']['ai_endpoint']); ?>"></label><label>مدل AI<input name="commerce[ai_model]" value="<?php echo esc_attr($opts['commerce']['ai_model']); ?>"></label><label>کلید API<input type="password" name="commerce[ai_key]" value="" placeholder="خالی = حفظ کلید فعلی"></label><label class="bhc-studio-check"><input type="checkbox" name="commerce[webp_enabled]" value="1" <?php checked($opts['commerce']['webp_enabled'],1); ?>> WebP</label><label>کیفیت تصویر<input type="number" min="50" max="100" name="commerce[image_quality]" value="<?php echo esc_attr($opts['commerce']['image_quality']); ?>"></label><h3>SEO و داده ساختاریافته</h3><label class="bhc-studio-check"><input type="checkbox" name="seo[product_schema_enabled]" value="1" <?php checked($opts['seo']['product_schema_enabled'],1); ?>> داده ساختاریافته محصول</label><label class="bhc-field-wide">توضیح متای سایت<textarea name="seo[site_meta_description]" rows="2"><?php echo esc_textarea($opts['seo']['site_meta_description']); ?></textarea></label><label>نام سازمان<input name="seo[organization_name]" value="<?php echo esc_attr($opts['seo']['organization_name']); ?>"></label><label>لینک سازمان<input type="url" name="seo[organization_url]" value="<?php echo esc_attr($opts['seo']['organization_url']); ?>"></label><label class="is-wide">لوگوی سازمان برای داده ساختاریافته<input id="seo-org-logo" type="number" min="0" name="seo[organization_logo_id]" value="<?php echo esc_attr($opts['seo']['organization_logo_id']); ?>"> <button class="button bhc-media" type="button" data-target="seo-org-logo">انتخاب از رسانه</button><span class="bhc-media-preview" data-preview-for="seo-org-logo"><?php echo bhc_studio_media_preview($opts['seo']['organization_logo_id']); ?></span></label></div></section>
     <section class="bhc-studio-panel" id="studio-copy" data-studio-group="content" hidden><header><h2>فضاها و متن‌ها</h2><p>فضاها فقط به دسته‌های واقعی وصل می‌شوند. عنوان هر فیلد، همان متنی است که در صفحه دیده می‌شود.</p></header>
      <?php $copy_labels=['utility'=>'نوار بالای سایت','hero_kicker'=>'برچسب هیرو','hero_title'=>'عنوان هیرو','hero_accent'=>'بخش رنگی عنوان','hero_subtitle'=>'زیرعنوان هیرو','hero_cta'=>'دکمه هیرو','categories_title'=>'عنوان دسته‌ها','categories_lead'=>'توضیح دسته‌ها','featured_title'=>'عنوان محصولات ویژه','offers_title'=>'عنوان پیشنهادها','offers_text'=>'متن پیشنهادها','offers_cta'=>'دکمه پیشنهادها','brands_title'=>'عنوان برندها','brands_lead'=>'توضیح برندها','journal_title'=>'عنوان مجله','journal_lead'=>'توضیح مجله','instagram_title'=>'عنوان ریل‌ها','instagram_lead'=>'توضیح ریل‌ها','newsletter_title'=>'عنوان خبرنامه','newsletter_text'=>'متن خبرنامه','trust_1_title'=>'اعتماد ۱','trust_1_text'=>'شرح اعتماد ۱','trust_2_title'=>'اعتماد ۲','trust_2_text'=>'شرح اعتماد ۲','trust_3_title'=>'اعتماد ۳','trust_3_text'=>'شرح اعتماد ۳','trust_4_title'=>'اعتماد ۴','trust_4_text'=>'شرح اعتماد ۴','footer_about'=>'معرفی فوتر','footer_address'=>'آدرس','footer_phone'=>'تلفن','footer_hours'=>'ساعت پاسخ‌گویی']; ?>
      <div class="bhc-fields">
      <?php for($i=0;$i<3;$i++): ?><label>دسته قاب <?php echo esc_html($i+1); ?><select name="rooms[<?php echo esc_attr($i); ?>]"><option value="0">خودکار از دسته‌های فروشگاه</option><?php foreach($terms as $term): ?><option value="<?php echo esc_attr($term->term_id); ?>" <?php selected($opts['rooms'][$i]??0,$term->term_id); ?>><?php echo esc_html($term->name); ?></option><?php endforeach; ?></select></label><?php endfor; ?>
      <?php foreach($opts['copy'] as $key=>$value): ?><label class="<?php echo strlen($value)>80?'is-wide':''; ?>"><?php echo esc_html($copy_labels[$key]??$key); ?><?php if(strlen($value)>80): ?><textarea name="copy[<?php echo esc_attr($key); ?>]" rows="2"><?php echo esc_textarea($value); ?></textarea><?php else: ?><input name="copy[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>"><?php endif; ?></label><?php endforeach; ?>
      </div>
     </section>
     <section class="bhc-studio-panel" id="studio-prompts" data-studio-group="tools" hidden><details><summary>پرامپت تصویر هر بخش</summary><p>پرامپت را کپی کنید، عکس بسازید و در فیلد تصویر همان بخش بارگذاری کنید.</p>
      <?php foreach((function_exists('bhb_image_prompts')?bhb_image_prompts():[]) as $label=>$prompt): ?>
      <div class="bhc-prompt"><b><?php echo esc_html($label); ?></b><textarea readonly rows="4"><?php echo esc_textarea($prompt); ?></textarea><button class="button bhc-copy" type="button">کپی پرامپت</button></div>
      <?php endforeach; ?>
     </details></section>
     <section class="bhc-studio-panel" id="studio-brands" data-studio-group="catalog" hidden><header><h2>برندها</h2><p>هر کارت یک برند است. اگر لوگوی رسانه انتخاب نشود، همان فایل لوگو که الان روی سایت است نشان داده می‌شود. کالای کارت از نام برند در فروشگاه پیدا می‌شود.</p></header>
      <div class="bhc-repeat" data-repeat="brand">
      <?php foreach($opts['brands'] as $i=>$brand): ?>
      <article class="bhc-card" data-logo-ready="<?php $brand_logo_id=absint($brand['image_id']??0); $brand_file=bhc_studio_brand_file($brand); $brand_logo_ok=$brand_logo_id&&wp_attachment_is_image($brand_logo_id); echo ($brand_logo_ok||$brand_file)?'1':'0'; ?>">
       <div class="bhc-card-head"><b><?php echo esc_html($brand['name']?:'برند'); ?></b><span class="bhc-brand-logo-state <?php echo ($brand_logo_ok||$brand_file)?'is-ready':'is-missing'; ?>" aria-live="polite"><?php echo $brand_logo_ok?'لوگوی رسانه':($brand_file?'لوگوی فعلی سایت':'لوگو نیازمند انتخاب'); ?></span><button class="button-link-delete bhc-remove" type="button">حذف</button></div>
       <div class="bhc-fields">
        <input type="hidden" name="brand_alias[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($brand['alias']??''); ?>">
        <input type="hidden" name="brand_logo[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($brand['logo']??$brand_file); ?>">
        <label>نام فارسی<input name="brand_name[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($brand['name']); ?>"></label>
        <label>نام لاتین<input name="brand_latin[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($brand['latin']); ?>"></label>
        <label class="is-wide">توضیح کوتاه<input name="brand_line[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($brand['line']); ?>"></label>
        <label>دسته‌ها، با ویرگول<input name="brand_cats[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($brand['cats']); ?>"></label>
        <label>لینک<input type="url" name="brand_url[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($brand['url']); ?>" placeholder="خالی = جستجوی نام برند"></label>
        <label class="is-wide">لوگوی رسمی برند <small class="description">فقط فایل رسمی یا مجاز را از کتابخانه رسانه انتخاب کنید؛ لوگوی ساختگی قابل‌قبول نیست.</small><input id="brand-image-<?php echo esc_attr($i); ?>" type="number" min="0" name="brand_image[<?php echo esc_attr($i); ?>]" value="<?php echo esc_attr($brand['image_id']); ?>"> <button class="button bhc-media" type="button" data-target="brand-image-<?php echo esc_attr($i); ?>">انتخاب از رسانه</button><span class="bhc-media-preview" data-preview-for="brand-image-<?php echo esc_attr($i); ?>"><?php echo $brand_logo_ok?bhc_studio_media_preview($brand['image_id']):($brand_file?bhc_studio_file_preview('assets/images/brands/'.$brand_file,'همین فایل الان روی سایت است'):bhc_studio_media_preview(0)); ?></span></label>
       </div>
      </article>
      <?php endforeach; ?>
      </div>
      <button class="button bhc-add" type="button" data-template="bhc-brand-template">افزودن برند</button>
      <template id="bhc-brand-template">
       <article class="bhc-card">
        <div class="bhc-card-head"><b>برند تازه</b><button class="button-link-delete bhc-remove" type="button">حذف</button></div>
        <div class="bhc-fields">
         <input type="hidden" name="brand_alias[__i__]" value="">
         <label>نام فارسی<input name="brand_name[__i__]" value=""></label>
         <label>نام لاتین<input name="brand_latin[__i__]" value=""></label>
         <label class="is-wide">توضیح کوتاه<input name="brand_line[__i__]" value=""></label>
         <label>دسته‌ها، با ویرگول<input name="brand_cats[__i__]" value=""></label>
         <label>لینک<input type="url" name="brand_url[__i__]" value="" placeholder="خالی = جستجوی نام برند"></label>
         <label class="is-wide">شناسه تصویر مجاز<input id="brand-image-__i__" type="number" min="0" name="brand_image[__i__]" value="0"> <button class="button bhc-media" type="button" data-target="brand-image-__i__">انتخاب از رسانه</button></label>
        </div>
       </article>
      </template>
     </section>
     <div class="bhc-studio-save"><span class="bhc-save-state" aria-live="polite">تغییرات منتشرنشده را ابتدا به‌صورت پیش‌نویس ذخیره کنید.</span><button type="submit" class="button" name="bhc_save_mode" value="draft">ذخیره پیش‌نویس</button><button type="submit" class="button button-primary" name="bhc_save_mode" value="publish">ذخیره و انتشار</button></div>
    </div>
   </div>
  </form>
 </div>
 <?php
}
