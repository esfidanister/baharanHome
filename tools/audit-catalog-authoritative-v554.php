<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
$c=['total'=>count($ids),'verified'=>0,'needs_verification'=>0,'invalid'=>0,'ready_for_release'=>0,'blocked'=>0,'exact_source'=>0,'specific_image'=>0,'image_blocked'=>0,'complete_content'=>0,'structured_specs'=>0,'duplicates'=>0];
$skuMap=[];$gaps=[];
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;
 $state=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);$blocked=get_post_meta($id,'_bhc_release_blocked',true)==='yes';$source=(string)get_post_meta($id,'_bhc_source_url',true);$spec=json_decode((string)get_post_meta($id,'_bhc_structured_specs',true),true);$content=(string)get_post_field('post_content',$id);$imgStatus=(string)get_post_meta($id,'_bhc_image_generation_status',true);$imageBlocked=in_array($imgStatus,['blocked_until_exact_model','blocked_until_valid_model'],true);
 if($state==='reference_verified'){$c['verified']++;if($source)$c['exact_source']++;}elseif($state==='reference_invalid')$c['invalid']++;else $c['needs_verification']++;
 if($blocked)$c['blocked']++;if($imageBlocked)$c['image_blocked']++;if($imgStatus==='generated_4k_transparent_verified'&&!$imageBlocked&&$state!=='reference_invalid')$c['specific_image']++;
 if(is_array($spec)&&count($spec)>=3)$c['structured_specs']++;
 if(mb_stripos($content,'درباره محصول')!==false&&mb_stripos($content,'اطلاعات فنی')!==false&&mb_stripos($content,'وضعیت کاتالوگ')!==false)$c['complete_content']++;
 $ready=(string)get_post_meta($id,'_bhc_release_state',true)==='ready_for_release';if($ready)$c['ready_for_release']++;
 $sku=strtoupper(preg_replace('/[^A-Z0-9]+/','',str_replace('BHC-','',(string)$p->get_sku())));if($sku)$skuMap[$sku][]=$id;
 if($state!=='reference_verified')$gaps[]=$id.'|'.$p->get_sku().'|'.($state?:'EMPTY').'|'.($blocked?'blocked':'unblocked');
}
foreach($skuMap as $v)if(count($v)>1)$c['duplicates']++;
foreach($c as $k=>$v)echo strtoupper($k).'|'.$v."\n";
echo "GAP_SAMPLE|".count($gaps)."\n";foreach(array_slice($gaps,0,30) as $g)echo $g."\n";
