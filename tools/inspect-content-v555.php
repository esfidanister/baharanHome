<?php
require_once '/var/www/html/wp-load.php';
foreach([1007,28,1317,534,531] as $id){echo "\nID $id\n";echo wp_strip_all_tags((string)get_post_field('post_content',$id))."\n";}
