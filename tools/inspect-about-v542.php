<?php require_once '/var/www/html/wp-load.php'; foreach([370,376,27] as $id){echo "ID|$id\n";echo mb_substr((string)get_post_field('post_content',$id),0,500)."\n---\n";}
