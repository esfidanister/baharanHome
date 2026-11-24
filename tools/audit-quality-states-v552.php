<?php
require_once '/var/www/html/wp-load.php';
$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$counts=[];
foreach($ids as $id){$s=(string)get_post_meta($id,'_bhc_catalog_quality_state',true);$counts[$s!==''?$s:'EMPTY']=($counts[$s!==''?$s:'EMPTY']??0)+1;}
foreach($counts as $k=>$v)echo $k.'|'.$v."\n";
