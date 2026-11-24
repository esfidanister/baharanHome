<?php
if(!defined('ABSPATH'))exit;
add_action('admin_menu',function(){add_submenu_page('baharaan-commerce','کیفیت پاسخ چت‌بات','کیفیت پاسخ‌ها','manage_baharan_commerce','bhc-chat-quality','bhc_chat_quality_page',28);});
add_action('admin_enqueue_scripts',function(){if(sanitize_key($_GET['page']??'')!=='bhc-chat-quality')return;wp_enqueue_style('bhc-chat-quality',plugins_url('../assets/chat-quality.css',__FILE__),[],'1.1.0');});
add_action('admin_init','bhc_chat_quality_export');

function bhc_quality_intent_labels(){
 return ['purchase'=>'خرید','budget'=>'بودجه','compare'=>'مقایسه','support'=>'پشتیبانی','order'=>'پیگیری سفارش','complaint'=>'شکایت','payment'=>'پرداخت','shipping'=>'ارسال','return'=>'مرجوعی','warranty'=>'گارانتی','unknown'=>'نامشخص'];
}
function bhc_quality_reason_label($key){
 $map=['no_catalog_match'=>'محصولی در کاتالوگ پیدا نشد','ai_disabled'=>'هوش مصنوعی خاموش است','ai_unavailable'=>'سرویس هوش مصنوعی در دسترس نبود','empty_answer'=>'پاسخ خالی برگشت','manual'=>'ثبت دستی'];
 $key=sanitize_key((string)$key);
 if($key==='')return 'علت ثبت نشده';
 return $map[$key]??('علت ثبت‌نشده: '.$key);
}
function bhc_quality_health($rate,$sample){
 if($sample<=0)return ['is-empty','بدون رأی','هنوز رأیی نیست'];
 if($rate>=80)return ['is-good','سالم','رضایت رأی‌ها بالا است'];
 if($rate>=50)return ['is-warn','هشدار','رضایت متوسط است'];
 return ['is-risk','خطر','رضایت رأی‌ها پایین است'];
}
function bhc_quality_events(){
 static $rows=null;
 if($rows===null){
  $rows=[];
  foreach((array)get_option('bhc_audit_log',[]) as $r){
   if(!is_array($r)||($r['action']??'')!=='chat_event')continue;
   $at=bhc_quality_ts($r['at']??'');
   $rows[]=['event'=>sanitize_key($r['event']??''),'intent'=>sanitize_key($r['intent']??'unknown')?:'unknown','rating'=>sanitize_key($r['rating']??''),'at'=>$at];
  }
 }
 return $rows;
}
function bhc_quality_ts($mysql){
 $mysql=trim((string)$mysql);
 if($mysql==='')return 0;
 $dt=date_create_immutable($mysql,wp_timezone());
 return $dt?$dt->getTimestamp():0;
}
function bhc_quality_slice($events,$from,$to){
 $out=[];
 foreach($events as $e){if($e['at']>=$from&&$e['at']<$to)$out[]=$e;}
 return $out;
}
function bhc_quality_stats($events){
 $s=['question'=>0,'handoff'=>0,'fallback'=>0,'ai_error'=>0,'helpful'=>0,'not_helpful'=>0,'intents'=>[]];
 foreach($events as $e){
  if(isset($s[$e['event']])&&$e['event']!=='intents')$s[$e['event']]++;
  if($e['event']==='feedback'){if($e['rating']==='helpful')$s['helpful']++;elseif($e['rating']==='not_helpful')$s['not_helpful']++;}
  if($e['event']==='question'){$s['intents'][$e['intent']]=($s['intents'][$e['intent']]??0)+1;}
 }
 $votes=$s['helpful']+$s['not_helpful'];
 $s['votes']=$votes;
 $s['rate']=$votes? (int)round($s['helpful']/$votes*100):0;
 $gaps=$s['handoff']+$s['fallback']+$s['ai_error'];
 $s['gaps']=$gaps;
 $s['coverage']=$s['question']? (int)round(max(0,min(100,100*(1-min(1,$gaps/$s['question']))))):0;
 return $s;
}
function bhc_quality_delta($now,$prev,$higher_is_better=true){
 if($prev===null)return '';
 $d=(int)$now-(int)$prev;
 if($d===0)return '<small class="is-flat">بدون تغییر نسبت به دوره قبل</small>';
 $good=$higher_is_better?$d>0:$d<0;
 return '<small class="'.($good?'is-up':'is-down').'">'.($d>0?'+':'').esc_html(number_format_i18n($d)).' نسبت به دوره قبل</small>';
}

function bhc_chat_quality_export(){
 if(!is_admin()||!isset($_GET['bhc_quality_export']))return;
 if(!current_user_can('manage_baharan_commerce'))wp_die('مجوز کافی نیست.');
 check_admin_referer('bhc_quality_export');
 $rows=bhc_quality_filtered_queue();
 nocache_headers();
 header('Content-Type: text/csv; charset=utf-8');
 header('Content-Disposition: attachment; filename=chat-quality-queue.csv');
 echo "\xEF\xBB\xBF";
 $out=fopen('php://output','w');
 fputcsv($out,['question','intent','status','repeats','reason','last_seen']);
 foreach($rows as $r){
  fputcsv($out,[
   $r['question']??'',
   $r['intent']??'',
   $r['status']??'open',
   (int)($r['count']??1),
   $r['reason']??'',
   $r['last_seen']??'',
  ]);
 }
 fclose($out);
 exit;
}

function bhc_quality_request(){
 $range=isset($_GET['range'])?(string)wp_unslash($_GET['range']):'7';
 if(!in_array($range,['7','30','0'],true))$range='7';
 $intent=sanitize_key($_GET['intent']??'');
 $labels=bhc_quality_intent_labels();
 if($intent!==''&&!isset($labels[$intent]))$intent='';
 $status=sanitize_key($_GET['status']??'open');
 if(!in_array($status,['open','approved','ignored','all'],true))$status='open';
 $sort=sanitize_key($_GET['sort']??'count');
 if(!in_array($sort,['count','recent'],true))$sort='count';
 $q=sanitize_text_field(wp_unslash($_GET['q']??''));
 return compact('range','intent','status','sort','q');
}
function bhc_quality_filtered_queue(){
 $req=bhc_quality_request();
 $all=array_values((array)get_option('bhc_chat_unanswered',[]));
 $rows=[];
 foreach($all as $index=>$row){
  if(!is_array($row))continue;
  $st=$row['status']??'open';
  if($req['status']!=='all'&&$st!==$req['status'])continue;
  if($req['intent']!==''&&sanitize_key($row['intent']??'')!==$req['intent'])continue;
  if($req['q']!==''&&mb_stripos((string)($row['question']??''),$req['q'])===false)continue;
  $row['_index']=$index;
  $rows[]=$row;
 }
 usort($rows,function($a,$b) use($req){
  if($req['sort']==='recent')return strcmp((string)($b['last_seen']??''),(string)($a['last_seen']??''));
  return ((int)($b['count']??1))<=>((int)($a['count']??1));
 });
 return $rows;
}

function bhc_chat_quality_page(){
 if(!current_user_can('manage_baharan_commerce'))return;
 $req=bhc_quality_request();
 $days=(int)$req['range'];
 $now=(new DateTimeImmutable('now',wp_timezone()))->getTimestamp();
 $events=bhc_quality_events();
 if($days===0){$current=$events;$previous=[];$has_prev=false;$from=0;}
 else{$from=$now-($days*DAY_IN_SECONDS);$current=bhc_quality_slice($events,$from,$now);$previous=bhc_quality_slice($events,$from-($days*DAY_IN_SECONDS),$from);$has_prev=true;}
 $s=bhc_quality_stats($current);
 $p=$has_prev?bhc_quality_stats($previous):null;
 $health=bhc_quality_health($s['rate'],$s['votes']);
 $labels=bhc_quality_intent_labels();
 $queue_all=array_values((array)get_option('bhc_chat_unanswered',[]));
 $counts=['open'=>0,'approved'=>0,'ignored'=>0];
 $intent_unique=['queue'=>[],'repeats'=>[]];
 foreach($queue_all as $row){
  $st=$row['status']??'open';
  if(isset($counts[$st]))$counts[$st]++;
  if($st==='open'){
   $k=sanitize_key($row['intent']??'unknown')?:'unknown';
   $intent_unique['queue'][$k]=($intent_unique['queue'][$k]??0)+1;
   $intent_unique['repeats'][$k]=($intent_unique['repeats'][$k]??0)+(int)($row['count']??1);
  }
 }
 $filtered=bhc_quality_filtered_queue();
 $shown=array_slice($filtered,0,50);
 $base=admin_url('admin.php');
 $self=function($over=[]) use($base,$req){
  return esc_url(add_query_arg(array_merge(['page'=>'bhc-chat-quality'],$req,$over),$base));
 };
 $export=wp_nonce_url(add_query_arg(array_merge(['page'=>'bhc-chat-quality','bhc_quality_export'=>1],$req),$base),'bhc_quality_export');
 $kb=admin_url('admin.php?page=bhc-chat-kb');
 $range_label=$days===0?'کل لاگ موجود (حداکثر ۴۰۰ رویداد اخیر)':($days.' روز اخیر');
 $log_empty=count($events)===0;

 echo '<div class="wrap bhc-chat-quality bhc-quality" dir="rtl">';
 echo '<div class="bhc-quality-hero"><div><span class="bhc-eyebrow">کیفیت گفتگو</span><h1>مرکز کیفیت پاسخ‌گویی چت‌بات</h1><p>دو شاخص جدا است: رضایت فقط از رأی مفید و غیرمفید، پوشش پاسخ از نسبت سؤال به ارجاع، پاسخ جایگزین و خطای هوش مصنوعی. صف، وضعیت فعلی است و با بازهٔ زمانی فیلتر نمی‌شود.</p></div>';
 echo '<div class="bhc-quality-actions"><a class="button button-primary" href="'.esc_url(admin_url('admin.php?page=bhc-chat-unanswered')).'">صف پرسش‌ها</a><a class="button" href="'.esc_url($kb).'">بانک دانش</a><a class="button" href="'.esc_url($export).'">خروجی CSV صف</a></div></div>';

 if($log_empty)echo '<div class="bhc-quality-callout is-empty"><strong>رویداد چت ثبت نشده است.</strong><span>این صفر به معنی کیفیت عالی نیست. یا هنوز کسی با چت حرف نزده، یا لاگ خالی است.</span></div>';

 echo '<form class="bhc-quality-filters" method="get"><input type="hidden" name="page" value="bhc-chat-quality">';
 echo '<label>بازه<select name="range"><option value="7" '.selected($req['range'],'7',false).'>۷ روز</option><option value="30" '.selected($req['range'],'30',false).'>۳۰ روز</option><option value="0" '.selected($req['range'],'0',false).'>همهٔ لاگ</option></select></label>';
 echo '<label>نیت<select name="intent"><option value="">همه نیت‌ها</option>';
 foreach($labels as $k=>$lab)echo '<option value="'.esc_attr($k).'" '.selected($req['intent'],$k,false).'>'.esc_html($lab).'</option>';
 echo '</select></label>';
 echo '<label>وضعیت صف<select name="status"><option value="open" '.selected($req['status'],'open',false).'>باز</option><option value="approved" '.selected($req['status'],'approved',false).'>تأییدشده</option><option value="ignored" '.selected($req['status'],'ignored',false).'>نادیده گرفته</option><option value="all" '.selected($req['status'],'all',false).'>همه</option></select></label>';
 echo '<label>چینش<select name="sort"><option value="count" '.selected($req['sort'],'count',false).'>بیشترین تکرار</option><option value="recent" '.selected($req['sort'],'recent',false).'>تازه‌ترین</option></select></label>';
 echo '<label class="is-grow">جستجو<input type="search" name="q" value="'.esc_attr($req['q']).'" placeholder="متن پرسش"></label>';
 echo '<button class="button button-primary bhc-quality-apply">اعمال فیلتر</button></form>';
 echo '<p class="bhc-quality-range">شاخص‌های زیر برای '.esc_html($range_label).' هستند.'.($has_prev?' مقایسه با '.esc_html((string)$days).' روز قبل از آن است.':'').($current||$log_empty?'':' در این بازه رویدادی نیست؛ صفر اینجا به معنی کیفیت عالی نیست.').'</p>';

 $cards=[
  ['سؤال', $s['question'], $p?$p['question']:null, true, 'هر بار که کاربر سؤال فرستاده', ''],
  ['رضایت رأی‌ها', $s['votes']?$s['rate'].'%':'—', ($p&&$p['votes'])?$p['rate']:null, true, number_format_i18n($s['votes']).' رأی · ارجاع و بی‌پاسخ داخل این درصد نیست', $health[0]],
  ['پوشش پاسخ', $s['question']?$s['coverage'].'%':'—', ($p&&$p['question'])?$p['coverage']:null, true, '۱۰۰ منهای سهم ارجاع، پاسخ جایگزین و خطای هوش مصنوعی نسبت به سؤال', $s['question']?($s['coverage']>=80?'is-good':($s['coverage']>=50?'is-warn':'is-risk')):'is-empty'],
  ['ارجاع پشتیبانی', $s['handoff'], $p?$p['handoff']:null, false, 'کاربر به انسان سپرده شده', 'is-risk'],
  ['پاسخ جایگزین', $s['fallback'], $p?$p['fallback']:null, false, 'کاتالوگ جواب قطعی نداده', 'is-warn'],
  ['خطای هوش مصنوعی', $s['ai_error'], $p?$p['ai_error']:null, false, 'سرویس پاسخ نداده', 'is-risk'],
 ];
 echo '<div class="bhc-quality-metrics">';
 foreach($cards as $c){
  $numeric=is_int($c[1])||is_float($c[1]);
  echo '<article class="'.esc_attr($c[5]).'"><span>'.esc_html($c[0]).'</span><b>'.esc_html($numeric?number_format_i18n($c[1]):$c[1]).'</b>';
  if($c[2]!==null){
   $now_n=$numeric?(int)$c[1]:(int)$c[1];
   echo bhc_quality_delta($now_n,(int)$c[2],$c[3]);
  }
  echo '<small>'.esc_html($c[4]).'</small></article>';
 }
 echo '</div>';

 echo '<div class="bhc-quality-summary"><section class="bhc-quality-panel"><span class="bhc-section-kicker">وضعیت سلامت</span><h2>رضایت رأی‌داده‌شده</h2>';
 echo '<div class="bhc-quality-health '.esc_attr($health[0]).'"><strong>'.($s['votes']?esc_html($s['rate']).'%':'—').'</strong><div>';
 echo '<span class="bhc-quality-pill">'.esc_html($health[1]).'</span>';
 echo '<div class="bhc-quality-track" role="meter" aria-valuemin="0" aria-valuemax="100" aria-valuenow="'.esc_attr($s['votes']?$s['rate']:0).'" aria-label="نرخ رضایت"><i style="width:'.esc_attr($s['votes']?$s['rate']:0).'%"></i></div>';
 echo '<p>'.esc_html($health[2]).'. مفید: '.esc_html(number_format_i18n($s['helpful'])).' · نیازمند بهبود: '.esc_html(number_format_i18n($s['not_helpful'])).'.</p></div></div>';
 echo '<div class="bhc-quality-mini-stats">';
 foreach(['open'=>'باز در صف','approved'=>'تأیید و رفته به دانش','ignored'=>'نادیده گرفته'] as $st=>$lab){
  echo '<a href="'.$self(['status'=>$st]).'">'.$lab.' <b>'.esc_html(number_format_i18n($counts[$st])).'</b></a>';
 }
 echo '</div></section>';

 echo '<section class="bhc-quality-panel"><span class="bhc-section-kicker">تقاضای واقعی</span><h2>نیت سؤال‌ها در این بازه</h2>';
 if(!$s['intents'])echo '<div class="bhc-quality-empty">در این بازه سؤالی ثبت نشده<small>بازه را عوض کنید یا منتظر گفتگوی جدید بمانید.</small></div>';
 else{
  arsort($s['intents']);$max=max($s['intents']);
  echo '<ul class="bhc-intent-list">';
  foreach($s['intents'] as $key=>$count){
   $w=$max?round($count/$max*100):0;
   $uq=$intent_unique['queue'][$key]??0;
   $rep=$intent_unique['repeats'][$key]??0;
   echo '<li><span>'.esc_html($labels[$key]??$key).'</span><b>'.esc_html(number_format_i18n($count)).' سؤال</b><small>صف باز: '.esc_html(number_format_i18n($uq)).' پرسش یکتا، '.esc_html(number_format_i18n($rep)).' تکرار</small><i style="width:'.esc_attr($w).'%"></i></li>';
  }
  echo '</ul>';
 }
 echo '</section></div>';

 $status_name=['open'=>'باز','approved'=>'تأییدشده','ignored'=>'نادیده گرفته'];
 echo '<section class="bhc-quality-panel bhc-quality-questions"><header><div><span class="bhc-section-kicker">اقدام انسانی</span><h2>پرسش‌های صف</h2><p>'.esc_html(number_format_i18n(count($shown))).' از '.esc_html(number_format_i18n(count($filtered))).' مورد فیلترشده. عدد تکرار، دفعات دیده‌شدن همان پرسش است و با تعداد پرسش یکتا یکی نیست.</p></div></header>';
 if(!$shown)echo '<div class="bhc-quality-empty"><strong>موردی با این فیلتر نیست</strong><span>وضعیت، نیت یا عبارت جستجو را عوض کنید.</span></div>';
 else{
  echo '<div class="bhc-quality-table-wrap"><table class="widefat striped"><colgroup><col class="c-q"><col class="c-i"><col class="c-s"><col class="c-n"><col class="c-d"><col class="c-a"></colgroup><thead><tr><th>پرسش</th><th>نیت</th><th>وضعیت</th><th>تکرار</th><th>آخرین ثبت</th><th>اقدام</th></tr></thead><tbody>';
  foreach($shown as $r){
   $intent=sanitize_key($r['intent']??'unknown')?:'unknown';
   $st=$r['status']??'open';
   $seen=$r['last_seen']??'';
   $seen_label=$seen?mysql2date('Y/m/d H:i',$seen):'—';
   $href=esc_url(admin_url('admin.php?page=bhc-chat-unanswered&status='.rawurlencode($st).'#bhc-unanswered-'.(int)$r['_index']));
   $reason=bhc_quality_reason_label($r['reason']??'');
   echo '<tr><td><strong>'.esc_html($r['question']??'—').'</strong><small>'.esc_html($reason).'</small></td>';
   echo '<td><span class="bhc-intent-badge">'.esc_html($labels[$intent]??$intent).'</span></td>';
   echo '<td><span class="bhc-status-badge is-'.esc_attr($st).'">'.esc_html($status_name[$st]??$st).'</span></td>';
   echo '<td>'.esc_html(number_format_i18n((int)($r['count']??1))).'</td>';
   echo '<td><time datetime="'.esc_attr($seen).'">'.esc_html($seen_label).'</time></td>';
   echo '<td class="bhc-row-actions"><a class="button button-small" href="'.$href.'">همین پرسش</a>';
   if($st==='approved')echo '<a class="button button-small" href="'.esc_url($kb).'">بانک دانش</a>';
   echo '</td></tr>';
  }
  echo '</tbody></table></div>';
  echo '<div class="bhc-quality-cards">';
  foreach($shown as $r){
   $intent=sanitize_key($r['intent']??'unknown')?:'unknown';
   $st=$r['status']??'open';
   $seen=$r['last_seen']??'';
   $href=esc_url(admin_url('admin.php?page=bhc-chat-unanswered&status='.rawurlencode($st).'#bhc-unanswered-'.(int)$r['_index']));
   echo '<article><strong>'.esc_html($r['question']??'—').'</strong><small>'.esc_html(bhc_quality_reason_label($r['reason']??'')).'</small><p><span class="bhc-intent-badge">'.esc_html($labels[$intent]??$intent).'</span> <span class="bhc-status-badge is-'.esc_attr($st).'">'.esc_html($status_name[$st]??$st).'</span></p><p>تکرار '.esc_html(number_format_i18n((int)($r['count']??1))).' · '.esc_html($seen?mysql2date('Y/m/d H:i',$seen):'—').'</p><a class="button button-small" href="'.$href.'">همین پرسش</a></article>';
  }
  echo '</div>';
  if(count($filtered)>50)echo '<p class="bhc-quality-more">۵۰ مورد اول این فهرست است. بقیه را با جستجو محدود کنید یا از صف کامل باز کنید.</p>';
 }
 echo '</section>';
 echo '<p class="bhc-quality-privacy">این صفحه متن پرسش بی‌پاسخ را نشان می‌دهد چون برای بررسی انسانی لازم است. نام، ایمیل، IP و متن کامل مکالمه در این گزارش نیست. لاگ رویداد حداکثر ۴۰۰ رکورد آخر را نگه می‌دارد؛ بازهٔ «همه» همین سقف است، نه تمام تاریخ فروشگاه.</p></div>';
}
