<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('bhc_chat_contains_secret')) :
function bhc_chat_contains_secret($text){
 $text=(string)$text;
 if(preg_match('/(?:رمز|password|پسورد|cvv|کد\s*ملی|شماره\s*کارت|otp|رمز\s*یک\s*بارمصرف|رمز\s*پویا)/iu',$text))return true;
 return (bool)preg_match('/(?:\d[ \-]*){13,19}/',$text);
}
function bhc_unanswered_with_lock($callback){
 $key='bhc_unanswered_lock';
 $got=false;
 for($i=0;$i<8&&!$got;$i++){
  if(add_option($key,(string)(time()+8),'',false)){$got=true;break;}
  $until=(int)get_option($key,0);
  if($until>0&&$until<time()){$got=(bool)update_option($key,(string)(time()+8),false);}
  if(!$got)usleep(80000);
 }
 if(!$got)return new WP_Error('bhc_unanswered_lock','صف هم‌زمان در حال به‌روزرسانی است. چند ثانیه بعد دوباره تلاش کنید.');
 try{return $callback();}
 finally{delete_option($key);}
}
function bhc_unanswered_cap(array $rows){
 $rows=array_values($rows);
 if(count($rows)<=200)return $rows;
 usort($rows,function($a,$b){
  $rank=function($row){
   $status=$row['status']??'open';
   $urgent=in_array($row['intent']??'',['complaint','order'],true);
   if($status==='open'&&($urgent||(int)($row['count']??1)>=2))return 0;
   if($status==='open')return 1;
   if($status==='approved')return 2;
   return 3;
  };
  $diff=$rank($a)<=>$rank($b);
  if($diff!==0)return $diff;
  return strcmp((string)($b['last_seen']??''),(string)($a['last_seen']??''));
 });
 return array_slice($rows,0,200);
}
/** Store only anonymous, deduplicated unanswered questions for human review. */
function bhc_chat_unanswered_add($question,$intent='unknown',$reason=''){
 $question=sanitize_textarea_field($question);
 $question=preg_replace('/\s+/u',' ',trim((string)$question));
 if($question===''||mb_strlen($question)>700||bhc_chat_contains_secret($question))return false;
 $intent=in_array($intent,['purchase','budget','compare','support','order','complaint','unknown'],true)?$intent:'unknown';
 $result=bhc_unanswered_with_lock(function()use($question,$intent,$reason){
  $rows=array_values((array)get_option('bhc_chat_unanswered',[]));
  $normalized=mb_strtolower($question,'UTF-8');
  foreach($rows as &$row){
   if(($row['normalized']??'')===$normalized&&($row['status']??'open')==='open'){
    $row['count']=max(1,(int)($row['count']??1))+1;
    $row['last_seen']=current_time('mysql');
    if(empty($row['id']))$row['id']=wp_generate_password(16,false,false);
    update_option('bhc_chat_unanswered',bhc_unanswered_cap($rows),false);
    return true;
   }
  }
  unset($row);
  $rows[]=['id'=>wp_generate_password(16,false,false),'question'=>$question,'normalized'=>$normalized,'intent'=>$intent,'reason'=>sanitize_key($reason),'count'=>1,'first_seen'=>current_time('mysql'),'last_seen'=>current_time('mysql'),'status'=>'open','suggested_answer'=>'','history'=>[]];
  update_option('bhc_chat_unanswered',bhc_unanswered_cap($rows),false);
  return true;
 });
 return !is_wp_error($result)&&$result;
}
endif;
function bhc_chat_detect_language($text){
 $text=(string)$text;$fa=(int)preg_match_all('/[آ-ی]/u',$text);$ar=(int)preg_match_all('/[\u0621-\u064A]/u',$text)-$fa;$en=(int)preg_match_all('/[A-Za-z]/',$text);
 if($fa>0&&$en>0)return 'mixed';if($fa>0)return 'fa';if($ar>0)return 'ar';if($en>0)return 'en';return 'unknown';
}
function bhc_chat_normalize($text){
 $text=mb_strtolower((string)$text,'UTF-8');$text=strtr($text,['ي'=>'ی','ى'=>'ی','ك'=>'ک','ۀ'=>'ه','ة'=>'ه','ؤ'=>'و','إ'=>'ا','أ'=>'ا','ـ'=>'','۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9']);
 $text=preg_replace('/[!?؟،؛,:]+/u',' ',$text);$text=preg_replace('/\s+/u',' ',trim((string)$text));
 return str_ireplace(['plz','pls','thx','u','ur'],['please','please','thanks','you','your'],$text);
}

function bhc_chatbot_v3(){
 check_ajax_referer('bhc_nonce','nonce');
 $q=sanitize_textarea_field(wp_unslash($_POST['message']??''));
 if(!$q||mb_strlen($q)>1200) wp_send_json_error(['message'=>'لطفاً سؤال را کوتاه‌تر و واضح‌تر بنویسید.'],400);
 $o=Baharaan_Commerce::opts();
 if(empty($_POST['ai_consent'])) wp_send_json_error(['message'=>'برای پاسخ‌گویی هوشمند، رضایت ارسال متن سؤال را فعال کنید.'],400);
 $ip=sanitize_text_field($_SERVER['REMOTE_ADDR']??'unknown');$key='bhc_chat_v3_'.hash('sha256',$ip);$used=(int)get_transient($key);
 if($used>=20) wp_send_json_error(['message'=>'تعداد پرسش‌ها زیاد است؛ لطفاً یک دقیقه بعد دوباره تلاش کنید.'],429);set_transient($key,$used+1,MINUTE_IN_SECONDS);
 $normal=bhc_chat_normalize($q);$language=bhc_chat_detect_language($normal);
 $terms=array_values(array_unique(array_filter([$q,$normal])));$ids=[];
 $digits=strtr($normal,['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9','٬'=>'',','=>'']);
 $budget=0;if(preg_match('/(?:زیر|کمتر از|تا|under|below|less than)\s*([0-9]{3,})/iu',$digits,$m))$budget=(float)$m[1];
 $want_stock=(bool)preg_match('/موجود|در انبار|available|in stock/iu',$normal);$want_out=(bool)preg_match('/ناموجود|تمام شده|out of stock/iu',$normal);
 $intent='purchase';
 if(preg_match('/مقایسه|فرق|تفاوت|بهتره|compare|difference|versus|vs/iu',$normal))$intent='compare';
 elseif(preg_match('/پیگیری|کد رهگیری|سفارش من|order|tracking/iu',$normal))$intent='order';
 elseif(preg_match('/شکایت|ناراضی|خراب|بد بود|complaint|angry/iu',$normal))$intent='complaint';
 elseif(preg_match('/پشتیبانی|گارانتی|مرجوع|تعویض|support|warranty|return/iu',$normal))$intent='support';
 elseif($budget||preg_match('/بودجه|قیمت مناسب|ارزان|اقتصادی|budget|cheap|affordable/iu',$normal))$intent='budget';
 $faq='';
 if(preg_match('/چطور خرید|روش خرید|how to buy|purchase process/iu',$normal))$faq='برای خرید، محصول را انتخاب کنید، مشخصات و موجودی را بررسی کنید، روی «افزودن به سبد خرید» بزنید و سپس مراحل سبد و پرداخت را کامل کنید.';
 elseif(preg_match('/تماس|شماره|ایمیل|contact|phone|email/iu',$normal)){$contact=[];if(!empty($o['phone']))$contact[]='تلفن: '.sanitize_text_field($o['phone']);if(!empty($o['email']))$contact[]='ایمیل: '.sanitize_email($o['email']);$faq=$contact?'راه‌های ارتباطی فروشگاه: '.implode(' · ',$contact):'اطلاعات تماس در تنظیمات فروشگاه ثبت نشده است؛ لطفاً از فرم پشتیبانی استفاده کنید.';}
 elseif(preg_match('/ساعت|چه زمانی|opening hours|working hours/iu',$normal))$faq=!empty($o['support_hours'])?'ساعات پاسخ‌گویی: '.sanitize_text_field($o['support_hours']):'ساعات پاسخ‌گویی در تنظیمات فروشگاه ثبت نشده است؛ لطفاً درخواست خود را ثبت کنید تا پشتیبانی پیگیری کند.';
 elseif(preg_match('/پرداخت|درگاه|payment|gateway/iu',$normal))$faq='روش‌های پرداخت باید در مرحله پرداخت و بر اساس تنظیمات فعال فروشگاه نمایش داده شوند. اگر درگاه را نمی‌بینید، لطفاً با پشتیبانی تماس بگیرید.';
 if($faq)wp_send_json_success(['message'=>$faq,'ai'=>false,'intent'=>$intent,'source'=>'faq','products'=>[]]);
 if ( class_exists( 'BHC_Chat_Knowledge_Store' ) ) { foreach ( BHC_Chat_Knowledge_Store::search( $normal, 3 ) as $entry ) { if ( ! empty( $entry['answer'] ) ) { wp_send_json_success( array( 'message' => sanitize_textarea_field( $entry['answer'] ), 'ai' => false, 'intent' => $entry['intent'] ?: $intent, 'source' => 'knowledge_base', 'products' => array() ) ); } } }
 if(function_exists('wc_get_products')) foreach($terms as $term){$ids=array_merge($ids,(array)wc_get_products(['status'=>'publish','limit'=>12,'s'=>$term,'return'=>'ids']));$ids=array_merge($ids,(array)wc_get_products(['status'=>'publish','limit'=>8,'sku'=>$term,'return'=>'ids']));}
 $ids=array_values(array_unique(array_filter(array_map('absint',$ids))));
 if(($budget||$want_stock||$want_out)&&function_exists('wc_get_products')){$pool=wc_get_products(['status'=>'publish','limit'=>200,'orderby'=>'popularity','return'=>'objects']);$filtered=[];foreach($pool as $candidate){$price=(float)$candidate->get_price();$ok=$budget?($price>0&&$price<=$budget):true;if($want_stock)$ok=$ok&&$candidate->is_in_stock();if($want_out)$ok=$ok&&!$candidate->is_in_stock();if($ok)$filtered[]=$candidate->get_id();}$ids=$filtered?:$ids;}
 if(!$ids&&function_exists('wc_get_products')) $ids=(array)wc_get_products(['status'=>'publish','limit'=>8,'orderby'=>'popularity','return'=>'ids']);
 $items=[];foreach(array_slice($ids,0,10) as $id){$p=wc_get_product($id);if(!$p)continue;$attrs=[];foreach($p->get_attributes() as $a){$v=$a->is_taxonomy()?wc_get_product_terms($id,$a->get_name(),['fields'=>'names']):$a->get_options();if($v)$attrs[wc_attribute_label($a->get_name())]=array_values(array_map('sanitize_text_field',(array)$v));}$items[]=['id'=>$id,'name'=>$p->get_name(),'sku'=>$p->get_sku(),'price'=>wp_strip_all_tags($p->get_price_html()),'stock'=>$p->is_in_stock()?'موجود':'ناموجود','attributes'=>$attrs,'summary'=>mb_substr(wp_strip_all_tags($p->get_short_description()),0,500),'url'=>get_permalink($id)];}
 $knowledge='بانک دانش فروشگاه: قیمت و موجودی فقط از کاتالوگ زنده معتبر است. درباره ضمانت، ارسال، مرجوعی، پیگیری سفارش یا پرداخت اگر داده واقعی در تنظیمات یا کاتالوگ نیست، ادعا نکن و کاربر را به پشتیبانی ارجاع بده. اطلاعات کارت بانکی، رمز، کد یک‌بارمصرف و گذرواژه هرگز درخواست نشود.';
 $system='You are Baharaan Home multilingual shopping assistant. Detect Persian, English, Arabic, or Finglish and answer in the same language. The detected user intent is '.$intent.'. For compare, compare only supplied catalog attributes and state missing fields. For budget, respect the applied price filter. For order, complaint, support or warranty questions, do not invent order data or policy; request the minimum safe identifier and route to human support. Understand misspellings, incomplete questions, follow-ups, category, SKU and stock questions. Use only the catalog JSON and knowledge policy below. Never invent facts, prices, specifications, warranty, delivery, compatibility or availability. If data is missing, say so and ask one concise clarifying question. Keep answers useful but concise; when products match, include product name, live price, stock and reason. Catalog JSON: '.wp_json_encode($items,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).' Policy: '.$knowledge;
 $messages=[['role'=>'system','content'=>$system]];$ctx=sanitize_textarea_field(wp_unslash($_POST['conversation_context']??''));$decoded=json_decode($ctx,true);if(is_array($decoded))foreach(array_slice($decoded,-10) as $row){if(!is_array($row)||empty($row['content']))continue;$role=($row['role']??'')==='user'?'user':'assistant';$messages[]=['role'=>$role,'content'=>mb_substr(sanitize_textarea_field($row['content']),0,700)];}$messages[]=['role'=>'user','content'=>$normal];
 if(empty($o['ai_enabled'])||empty($o['ai_key'])){if(!$items)bhc_chat_unanswered_add($normal,$intent,'ai_disabled');wp_send_json_success(['message'=>'دستیار هوشمند هنوز فعال نشده است؛ لطفاً از راه‌های تماس پشتیبانی استفاده کنید.','ai'=>false,'intent'=>$intent,'source'=>'handoff','products'=>$items]);}
 $resp=wp_safe_remote_post(Baharaan_Commerce::api_endpoint($o['ai_endpoint']??''),['timeout'=>25,'headers'=>['Authorization'=>'Bearer '.$o['ai_key'],'Content-Type'=>'application/json','HTTP-Referer'=>home_url('/'),'X-Title'=>get_bloginfo('name')],'body'=>wp_json_encode(['model'=>$o['ai_model'],'messages'=>$messages,'temperature'=>0.2,'max_tokens'=>650])]);
 if(is_wp_error($resp)||wp_remote_retrieve_response_code($resp)<200||wp_remote_retrieve_response_code($resp)>=300){bhc_chat_unanswered_add($normal,$intent,'ai_unavailable');wp_send_json_success(['message'=>'پاسخ‌گویی هوشمند موقتاً در دسترس نیست؛ لطفاً سؤال را دوباره ارسال کنید یا با پشتیبانی تماس بگیرید.','ai'=>false,'intent'=>$intent,'source'=>'handoff','products'=>$items]);}
 $json=json_decode(wp_remote_retrieve_body($resp),true);$answer=sanitize_textarea_field($json['choices'][0]['message']['content']??'');if($answer===''){bhc_chat_unanswered_add($normal,$intent,'empty_answer');$answer='برای پاسخ دقیق‌تر، مدل یا دسته محصول را مشخص کنید.';}
 wp_send_json_success(['message'=>$answer,'ai'=>true,'intent'=>$intent,'language'=>$language,'source'=>'ai','products'=>$items]);
}
