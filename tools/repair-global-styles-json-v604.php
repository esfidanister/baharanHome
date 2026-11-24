<?php
require_once '/var/www/html/wp-load.php';
$p=get_post(20);
if(!$p || $p->post_type!=='wp_global_styles'){echo "STOP_NOT_GLOBAL_STYLES\n";exit;}
$decoded=json_decode((string)$p->post_content,true);
if(json_last_error()===JSON_ERROR_NONE){echo "ALREADY_VALID|20\n";exit;}
$r=wp_update_post(['ID'=>20,'post_content'=>'{}'],true);
if(is_wp_error($r)){echo 'ERROR|'.$r->get_error_message().'\n';exit;}
$after=get_post(20);json_decode((string)$after->post_content,true);echo 'REPAIRED|20|JSON_ERROR|'.json_last_error_msg().'|CONTENT|'.$after->post_content."\n";
