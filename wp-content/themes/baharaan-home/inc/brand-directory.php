<?php
if (!defined('ABSPATH')) exit;

function bhb_brand_seed(){
 $row=function($name,$latin,$line,$cats,$alias=''){
  return ['name'=>$name,'latin'=>$latin,'line'=>$line,'cats'=>$cats,'alias'=>$alias,'url'=>'','image_id'=>0];
 };
 return [
  $row('ال‌جی','LG','برند لوازم شست‌وشو، تصویر و آشپزخانه. اگر کالایی با این نام منتشر شده باشد، همین‌جا دیده می‌شود.','شست‌وشو، صوتی و تصویری، لوازم آشپزخانه'),
  $row('سامسونگ','Samsung','برند لوازم تصویر، نظافت و شست‌وشو. ویترین فقط کالاهای واقعی کاتالوگ را نشان می‌دهد.','نظافت، صوتی و تصویری، شست‌وشو'),
  $row('بوش','Bosch','برند لوازم نظافت، آشپزخانه و شست‌وشو.','نظافت، لوازم آشپزخانه، شست‌وشو'),
  $row('دوو','Daewoo','برند ماشین‌های شست‌وشو و لوازم آشپزخانه.','شست‌وشو، لوازم آشپزخانه'),
  $row('تفال','Tefal','برند اتو، بخار و لوازم کوچک آشپزخانه.','اتو و بخار، لوازم آشپزخانه'),
  $row('فیلیپس','Philips','برند لوازم آشپزخانه و اتو.','لوازم آشپزخانه، اتو و بخار'),
  $row('نینجا','Ninja','برند لوازم پخت و سرخ‌کن.','لوازم آشپزخانه'),
  $row('بلک‌انددکر','Black+Decker','برند ابزار و لوازم کوچک آشپزخانه.','لوازم آشپزخانه','بلک اند دکر|Black'),
  $row('پارس‌خزر','Pars Khazar','برند ایرانی لوازم آشپزخانه و نظافت.','لوازم آشپزخانه، نظافت'),
  $row('اسنوا','Snowa','برند ایرانی لوازم شست‌وشو و آشپزخانه.','شست‌وشو، لوازم آشپزخانه'),
  $row('پاکشوما','Pakshoma','برند ایرانی ماشین‌های شست‌وشو.','شست‌وشو'),
  $row('کنوود','Kenwood','برند لوازم برقی آشپزخانه.','لوازم آشپزخانه'),
  $row('مولینکس','Moulinex','برند لوازم کوچک آشپزخانه.','لوازم آشپزخانه'),
  $row('دلونگی','DeLonghi','برند قهوه و لوازم آشپزخانه.','لوازم آشپزخانه'),
  $row('میله','Miele','برند لوازم شست‌وشو، نظافت و آشپزخانه.','شست‌وشو، نظافت، لوازم آشپزخانه'),
  $row('بکو','Beko','برند لوازم شست‌وشو و آشپزخانه.','شست‌وشو، لوازم آشپزخانه'),
  $row('پاناسونیک','Panasonic','برند لوازم آشپزخانه و صوتی‌تصویری.','لوازم آشپزخانه، صوتی و تصویری'),
  $row('سونی','Sony','برند لوازم صوتی و تصویری.','صوتی و تصویری'),
  $row('ایندزیت','Indesit','برند ماشین‌های شست‌وشو.','شست‌وشو'),
  $row('زانوسی','Zanussi','برند لوازم شست‌وشو و آشپزخانه.','شست‌وشو، لوازم آشپزخانه'),
  $row('آاگ','AEG','برند لوازم شست‌وشو و آشپزخانه.','شست‌وشو، لوازم آشپزخانه'),
  $row('الکترولوکس','Electrolux','برند لوازم شست‌وشو و نظافت.','شست‌وشو، نظافت'),
 ];
}

/* One canonical identity per brand. Aliases are only for matching catalog
 * terms; the public label and Latin wordmark stay stable everywhere. */
function bhb_brand_identity(){
 return [
  'اسنوا'=>['name'=>'اسنوا','latin'=>'Snowa','aliases'=>'Snowa','line'=>'یخچال، شست‌وشو، تلویزیون و لوازم آشپزخانه','cats'=>'شست‌وشو، لوازم آشپزخانه، صوتی و تصویری','logo'=>'snowa.webp'],
  'بوش'=>['name'=>'بوش','latin'=>'Bosch','aliases'=>'Bosch','line'=>'ظرفشویی، جاروبرقی، شست‌وشو و قهوه','cats'=>'نظافت، لوازم آشپزخانه، شست‌وشو','logo'=>'bosch-v2.webp'],
  'پاکشوما'=>['name'=>'پاکشوما','latin'=>'Pakshoma','aliases'=>'Pakshoma','line'=>'لباسشویی، ظرفشویی، یخچال و لوازم خانه','cats'=>'شست‌وشو، لوازم آشپزخانه','logo'=>'pakshoma.webp'],
  'سامسونگ'=>['name'=>'سامسونگ','latin'=>'Samsung','aliases'=>'Samsung','line'=>'تلویزیون، شست‌وشو، یخچال و نظافت','cats'=>'صوتی و تصویری، شست‌وشو، نظافت','logo'=>'samsung.svg'],
  'الجی'=>['name'=>'ال‌جی','latin'=>'LG','aliases'=>'LG|ال جی','line'=>'تلویزیون، شست‌وشو و لوازم خانگی هوشمند','cats'=>'صوتی و تصویری، شست‌وشو','logo'=>'lg.svg'],
  'سونی'=>['name'=>'سونی','latin'=>'Sony','aliases'=>'Sony','line'=>'تلویزیون و صوتی‌تصویری','cats'=>'صوتی و تصویری','logo'=>'sony.svg'],
  'امرسان'=>['name'=>'امرسان','latin'=>'Emersun','aliases'=>'Emersun','line'=>'یخچال و فریزر، تلویزیون و اجاق','cats'=>'یخچال و فریزر، لوازم آشپزخانه','logo'=>'emersun.webp'],
  'دوو'=>['name'=>'دوو','latin'=>'Daewoo','aliases'=>'Daewoo','line'=>'یخچال، تلویزیون، شست‌وشو و پخت','cats'=>'یخچال و فریزر، شست‌وشو، صوتی و تصویری','logo'=>'daewoo.webp'],
  'جیپلاس'=>['name'=>'جی‌پلاس','latin'=>'GPlus','aliases'=>'GPlus|جی پلاس','line'=>'تلویزیون، یخچال، شست‌وشو و کولر گازی','cats'=>'صوتی و تصویری، شست‌وشو، سرمایش','logo'=>'gplus.webp'],
  'ایکسویژن'=>['name'=>'ایکس‌ویژن','latin'=>'X.Vision','aliases'=>'X.Vision|X Vision','line'=>'تلویزیون، شست‌وشو و لوازم خانه','cats'=>'صوتی و تصویری، شست‌وشو','logo'=>'xvision.webp'],
  'سام'=>['name'=>'سام','latin'=>'SAM','aliases'=>'SAM','line'=>'تلویزیون و لوازم خانگی منتخب','cats'=>'صوتی و تصویری','logo'=>'sam.webp'],
  'بلکانددکر'=>['name'=>'بلک‌انددکر','latin'=>'Black+Decker','aliases'=>'Black+Decker|بلک اند دکر|Black','line'=>'لوازم کوچک آشپزخانه و نظافت','cats'=>'لوازم آشپزخانه، نظافت','logo'=>'blackanddecker.webp'],
  'تفال'=>['name'=>'تفال','latin'=>'Tefal','aliases'=>'Tefal','line'=>'اتو، بخار و لوازم کوچک آشپزخانه','cats'=>'اتو و بخار، لوازم آشپزخانه','logo'=>'tefal.webp'],
  'فیلیپس'=>['name'=>'فیلیپس','latin'=>'Philips','aliases'=>'Philips','line'=>'لوازم آشپزخانه، نظافت و اتو','cats'=>'لوازم آشپزخانه، نظافت، اتو و بخار','logo'=>'philips.webp'],
  'نینجا'=>['name'=>'نینجا','latin'=>'Ninja','aliases'=>'Ninja','line'=>'لوازم پخت و سرخ‌کن','cats'=>'لوازم آشپزخانه','logo'=>'ninja.webp'],
 ];
}

function bhb_brand_rows(){
 $known=[
  'اسنوا'=>['latin'=>'Snowa','line'=>'لوازم شست‌وشو، تلویزیون و لوازم آشپزخانه'],
  'ال‌جی'=>['latin'=>'LG','line'=>'تلویزیون، شست‌وشو و لوازم خانگی هوشمند'],
  'امرسان'=>['latin'=>'Emersun','line'=>'یخچال و فریزر، تلویزیون و اجاق'],
  'ایکس‌ویژن'=>['latin'=>'X.Vision','line'=>'تلویزیون، شست‌وشو و لوازم خانه'],
  'بوش'=>['latin'=>'Bosch','line'=>'ظرفشویی، جاروبرقی، شست‌وشو و قهوه'],
  'پاکشوما'=>['latin'=>'Pakshoma','line'=>'لباسشویی، ظرفشویی، یخچال و لوازم خانه'],
  'جی‌پلاس'=>['latin'=>'GPlus','line'=>'تلویزیون، یخچال، شست‌وشو و کولر گازی'],
  'دوو'=>['latin'=>'Daewoo','line'=>'یخچال، تلویزیون، شست‌وشو و پخت'],
  'سامسونگ'=>['latin'=>'Samsung','line'=>'تلویزیون، شست‌وشو، یخچال و نظافت'],
  'سونی'=>['latin'=>'Sony','line'=>'تلویزیون و صوتی‌تصویری'],
 ];
 $identity=bhb_brand_identity();
 $terms=[];
 foreach(['product_brand','pa_brand','pwb-brand'] as $taxonomy){
  if(!taxonomy_exists($taxonomy)) continue;
  $found=get_terms(['taxonomy'=>$taxonomy,'hide_empty'=>false,'orderby'=>'name','order'=>'ASC']);
  if(!is_wp_error($found)) $terms=array_merge($terms,$found);
 }
 if(!$terms) return bhb_brand_seed();
 $managed=[];
 if(function_exists('bhc_studio_opts')){
  $studio=bhc_studio_opts();
  foreach((array)($studio['brands']??[]) as $item){
   $needles=array_merge([$item['name']??'',$item['latin']??''],preg_split('/\|/u',(string)($item['alias']??'')));
   foreach($needles as $needle){$key=bhb_brand_key($needle);if($key!=='')$managed[$key]=$item;}
  }
 }
 $rows=[];$seen=[];
 foreach($terms as $term){
  $raw_key=bhb_brand_key($term->name);if($raw_key==='')continue;
  $key=$raw_key;$meta=null;
  foreach($identity as $identity_key=>$identity_meta){
   $needles=array_merge([$identity_key,$identity_meta['name'],$identity_meta['latin']],preg_split('/\|/u',(string)$identity_meta['aliases']));
   foreach($needles as $needle) if($raw_key===bhb_brand_key($needle)){$key=$identity_key;$meta=$identity_meta;break 2;}
  }
  if(isset($seen[$key]))continue;$seen[$key]=true;
  $meta=$meta?:($known[$term->name]??['latin'=>$term->name,'line'=>'محصولات این برند در کاتالوگ بهاران هوم','cats'=>'']);
  $custom=$managed[$raw_key]??$managed[$key]??[];
  $logo=sanitize_file_name($custom['logo']??'');
  if($logo===''||!is_file(get_theme_file_path('assets/images/brands/'.$logo))) $logo=sanitize_file_name($meta['logo']??'');
  $rows[]=['name'=>$custom['name']??$meta['name']??$term->name,'latin'=>$custom['latin']??$meta['latin'],'line'=>$custom['line']??$meta['line'],'cats'=>$custom['cats']??$meta['cats']??'','alias'=>$custom['alias']??($meta['aliases']??''),'url'=>$custom['url']??'','image_id'=>absint($custom['image_id']??0),'logo'=>$logo];
 }
 return $rows;
}

function bhb_brand_key($value){
 $value=mb_strtolower(trim(wp_strip_all_tags((string)$value)),'UTF-8');
 return preg_replace('/[\s\-_‌‍]+/u','',str_replace(['ي','ك','ۀ'],['ی','ک','ه'],$value));
}

function bhb_brand_needles($brand){
 $parts=preg_split('/\|/u',(string)($brand['alias']??''));
 return array_values(array_filter(array_map('trim',array_merge([$brand['name']??'',$brand['latin']??''],is_array($parts)?$parts:[]))));
}

function bhb_brand_url($brand){
 foreach(['product_brand','pa_brand','pwb-brand'] as $tax){
  if(!taxonomy_exists($tax)) continue;
  foreach(bhb_brand_needles($brand) as $needle){
   $term=get_term_by('name',$needle,$tax);
   if(!$term&&!empty($brand['latin'])) $term=get_term_by('slug',sanitize_title($brand['latin']),$tax);
   if($term&&!is_wp_error($term)){ $url=get_term_link($term,$tax); if(!is_wp_error($url)) return $url; }
  }
 }
 return !empty($brand['url'])?esc_url($brand['url']):add_query_arg(['s'=>$brand['name'],'post_type'=>'product'],home_url('/'));
}

function bhb_product_matches_brand($product,$brand){
 if(!$product) return false;
 foreach(['product_brand','pa_brand','pwb-brand'] as $tax){
  if(!taxonomy_exists($tax)) continue;
  $terms=wp_get_post_terms($product->get_id(),$tax,['fields'=>'names']);
  if(!is_wp_error($terms)) foreach((array)$terms as $term){
   foreach(bhb_brand_needles($brand) as $needle) if($needle!==''&&(function_exists('mb_stripos')?mb_stripos($term,$needle)!==false:stripos($term,$needle)!==false)) return true;
  }
 }
 $title=$product->get_name();
 foreach(bhb_brand_needles($brand) as $needle) if($needle!==''&&(function_exists('mb_stripos')?mb_stripos($title,$needle)!==false:stripos($title,$needle)!==false)) return true;
 return false;
}

function bhb_brand_index(){
 static $index=null;
 if($index!==null) return $index;
 $index=[];
 $brands=bhb_brand_rows();
 foreach($brands as $i=>$brand) $index[$i]=['count'=>0,'product'=>null];
 if(!function_exists('wc_get_products')) return $index;
 $cache_key='bhb_brand_index_v3';
 $cached=get_transient($cache_key);
 if(is_array($cached)&&isset($cached['rows'])&&is_array($cached['rows'])){
  foreach($brands as $i=>$brand){
   $row=$cached['rows'][bhb_brand_key($brand['name']??'')]??[];
   $index[$i]=['count'=>absint($row['count']??0),'product'=>!empty($row['product_id'])?wc_get_product(absint($row['product_id'])):null];
  }
  return $index;
 }
 foreach(wc_get_products(['status'=>'publish','limit'=>-1,'orderby'=>'date','order'=>'DESC','return'=>'objects']) as $product){
  foreach($brands as $i=>$brand){
   if(!bhb_product_matches_brand($product,$brand)) continue;
   $index[$i]['count']++;
   if(!$index[$i]['product']&&$product->get_image_id()) $index[$i]['product']=$product;
  }
 }
 $rows=[];
 foreach($brands as $i=>$brand)$rows[bhb_brand_key($brand['name']??'')]=['count'=>(int)$index[$i]['count'],'product_id'=>$index[$i]['product']?(int)$index[$i]['product']->get_id():0];
 set_transient($cache_key,['rows'=>$rows],10*MINUTE_IN_SECONDS);
 return $index;
}

add_action('save_post_product',function($post_id){
 if(wp_is_post_revision($post_id)||wp_is_post_autosave($post_id))return;
 delete_transient('bhb_brand_index_v2'); delete_transient('bhb_brand_index_v3');
},30);

function bhb_brand_products($name,$latin,$limit=2,$alias=''){
 $hits=[];
 foreach(bhb_brand_rows() as $i=>$brand){
  if($brand['name']!==$name||$brand['latin']!==$latin) continue;
  $row=bhb_brand_index()[$i]??['count'=>0,'product'=>null];
  if(!empty($row['product'])) $hits[]=$row['product'];
  break;
 }
 return array_slice($hits,0,$limit);
}

function bhb_category_url($name){
 $name=trim($name);
 if($name!==''&&taxonomy_exists('product_cat')){
  $term=get_term_by('name',$name,'product_cat');
  if($term&&!is_wp_error($term)){
   $link=get_term_link($term);
   if(!is_wp_error($link)) return $link;
  }
 }
 return function_exists('bhb_shop_url')?bhb_shop_url():home_url('/');
}

function bhb_product_prompt($name){
 $name=trim(wp_strip_all_tags($name))?:'لوازم خانگی';
 return "پرامپت تولید تصویر محصول برای بهاران هوم — فقط وقتی مرجع واقعی محصول، برند و مدل در اختیار است استفاده شود.\n\nموضوع دقیق: {$name}\nهدف: تصویر کاتالوگی حرفه‌ای برای کارت محصول، بدون تغییر در هویت محصول. اگر تصویر مرجع وجود دارد، همان مرجع منبع حقیقت باشد و شکل بدنه، رنگ، پنل، دکمه‌ها، اتصالات و تناسبات دقیقاً حفظ شود. اگر مرجع یا مدل دقیق نداریم، تولید را متوقف کن و تصویر ساختگی برای محصول واقعی نساز.\n\nخروجی: عکس تجاری فوق‌واقعی 4K، کادر عمودی یا مربعی متناسب با محصول، محصول تنها و کامل، زاویه سه‌ربع کنترل‌شده، لنز 70 تا 85 میلی‌متر، فوکوس کامل روی محصول، نورپردازی استودیویی نرم با rim light ظریف، سایه تماسی بسیار ملایم، برش تمیز، پس‌زمینه کاملاً شفاف، مناسب استفاده روی پس‌زمینه روشن و آبی بسیار روشن.\nممنوع: متن، قیمت، برچسب تبلیغاتی، واترمارک، جعبه، دست انسان، پس‌زمینه شلوغ، انعکاس اغراق‌شده، قطعه اضافه، تغییر مدل، قابلیت یا مشخصات، و هر لوگوی ساختگی.\n\nEnglish production prompt: Use only with an exact reference image and verified brand/model. The reference is the single source of truth; preserve the exact silhouette, materials, colors, controls, ports, proportions and identity. If no exact reference exists, stop instead of inventing a real product. Ultra-realistic 4K commercial catalog cutout of \"{$name}\", complete product, controlled three-quarter view, 70–85mm lens, sharp full-product focus, soft studio key light with subtle rim light, very soft contact shadow, clean edges, fully transparent background, no packaging, no hands, no text, no pricing, no watermark, no invented logo, no extra parts, no changed features, no fabricated specifications.";
}

function bhb_image_prompts(){
 return [
  'هیرو، کالای بیرون‌زده'=>'تصویر تزئینی premium برای هیرو فروشگاه لوازم خانگی: یک وسیله خانگی عمومی و بدون برند، با طراحی مینیمال و قابل‌اعتماد، نمای سه‌ربع، بدنه سفید مات و جزئیات آبی بسیار ظریف، نور استودیویی نرم، rim light کنترل‌شده، سایه تماس بسیار لطیف، محصول کامل و جدا از پس‌زمینه، خروجی WEBP یا PNG با پس‌زمینه شفاف، فضای خالی کافی برای تیتر فارسی در سمت راست، بدون متن، بدون لوگو، بدون شماره مدل، بدون ادعای محصول واقعی. این asset فقط تزئینی است و جایگزین کالای واقعی ووکامرس نیست.',
  'دسته‌بندی‌ها'=>'چهار asset جداگانه برای دسته‌بندی‌های فروشگاه، نه یک تصویر ترکیبی: تلویزیون مینیمال، جاروبرقی ایستاده، ماشین لباسشویی مدرن و قهوه‌ساز رومیزی؛ هرکدام در فایل جدا، با silhouette واضح، متریال واقعی، نور یکدست، پس‌زمینه شفاف، کادر عمودی متعادل، سایه بسیار کم، بدون برند، بدون متن، بدون لوگو و بدون طراحی شبیه محصول خاص. برای مصرف روی کارت‌های دسته‌بندی و breakpointهای موبایل/دسکتاپ.',
  'بنر سرمه‌ای آشپزخانه'=>'پس‌زمینه افقی premium برای بنر فروشگاه با نسبت 16:6 و نسخه mobile جداگانه 4:5؛ آشپزخانه مدرن و مرتب، کابینت‌های سفید و چوب روشن، accents سرمه‌ای و آبی، نور طبیعی صبح، depth of field کنترل‌شده، فضای منفی بزرگ برای متن فارسی، ترکیب‌بندی مناسب برش responsive، بدون انسان، بدون متن، بدون لوگو، بدون برند و بدون ادعای محصول. خروجی WEBP با کیفیت بالا.',
  'بنر نارنجی'=>'تصویر تزئینی افقی برای کارت نارنجی: فضای آشپزخانه گرم و روشن با یک وسیله کوچک عمومی و بدون برند، پالت کرم، سفید و نارنجی سوخته، نور نرم، کنتراست کافی با متن، فضای منفی برای CTA، بدون نوشته، واترمارک، لوگو، دست یا محصول قابل‌شناسایی به‌عنوان مدل واقعی. نسخه دسکتاپ و موبایل جدا.',
  'بنر یخی نظافت'=>'تصویر lifestyle تمیز و سرد برای بخش نظافت: فضای خانه روشن با کف روشن، نور آبی-سفید، یک ابزار نظافت عمومی بدون برند در پس‌زمینه، حس بهداشت و دقت، composition افقی responsive، بدون متن، بدون لوگو، بدون انسان و بدون ادعای نتیجه یا عملکرد واقعی. خروجی WEBP و crop-safe.',
  'کارت محصول صفحه اصلی'=>'این مورد فقط با تصویر مرجع دقیق همان محصول ووکامرس تولید شود. هویت، مدل، رنگ و جزئیات مرجع بدون تغییر حفظ شود؛ برش کاتالوگی شفاف، نور نرم، سایه تماس بسیار کم، بدون متن و لوگوی ساختگی. اگر مرجع دقیق نیست، از تولید خودداری کن و fallback تزئینی را نمایش بده.',
  'کاور مجله'=>'کاور تصویری مجله خانه با نسبت 16:9، فضای داخلی واقعی و آرام، نور طبیعی، ترکیب‌بندی editorial، جزئیات متریال چوب و پارچه با کیفیت بالا، فضای منفی برای عنوانی که قالب جداگانه نمایش می‌دهد، بدون متن چاپ‌شده داخل تصویر، بدون لوگو، بدون انسان قابل‌شناسایی، مناسب WEBP و lazy loading.',
  'کاور ریل اینستاگرام'=>'کاور عمودی 9:16 برای ریل لوازم خانگی، فضای داخلی مدرن با یک وسیله عمومی و بدون برند، نورپردازی جذاب و premium، سوژه در safe area مرکزی برای cropهای مختلف، کنتراست مناسب thumbnail، بدون متن، بدون لوگوی اینستاگرام، بدون واترمارک و بدون ادعای محصول واقعی. خروجی WEBP با کیفیت بالا.',
  'نشان برند'=>'لوگوی رسمی را فقط اگر فایل مجاز برند را دارید بارگذاری کنید. اگر لوگو ندارید، از مونوگرام متنی قالب استفاده شود. پرامپت جایگزین لوگو نسازید.',
 ];
}
