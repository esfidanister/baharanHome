<?php
require_once '/var/www/html/wp-load.php';$ids=get_posts(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);$c=[];foreach($ids as $id){$s=(string)get_post_meta($id,'_bhc_source_audit_status',true);$c[$s?:'not_reviewed']=($c[$s?:'not_reviewed']??0)+1;}foreach($c as $k=>$v)echo $k.'|'.$v."\n";
