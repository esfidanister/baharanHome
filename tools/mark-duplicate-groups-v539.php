<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$groups=[];
foreach($ids as $id){$p=wc_get_product($id);if(!$p)continue;$k=strtoupper(preg_replace('/[^A-Z0-9]+/','',str_replace('BHC-','',$p->get_sku())));if($k)$groups[$k][]=$id;}
$marked=0;$groupCount=0;
foreach($groups as $key=>$list){if(count($list)<2)continue;$groupCount++;usort($list,function($a,$b){$ar=get_post_meta($a,'_bhc_catalog_quality_state',true)==='reference_verified';$br=get_post_meta($b,'_bhc_catalog_quality_state',true)==='reference_verified';if($ar!==$br)return $ar?-1:1;$ai=(bool)get_post_thumbnail_id($a);$bi=(bool)get_post_thumbnail_id($b);if($ai!==$bi)return $ai?-1:1;return $a<=>$b;});$canonical=$list[0];foreach($list as $id){update_post_meta($id,'_bhc_duplicate_group',$key);update_post_meta($id,'_bhc_duplicate_canonical_id',$canonical);update_post_meta($id,'_bhc_duplicate_role',$id===$canonical?'canonical_candidate':'duplicate_candidate');$marked++;}}
echo 'GROUPS|'.$groupCount."\n";
echo 'MARKED|'.$marked."\n";
