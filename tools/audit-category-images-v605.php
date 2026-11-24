<?php
require_once '/var/www/html/wp-load.php';
$terms=get_terms(['taxonomy'=>'product_cat','hide_empty'=>false]);echo 'TERMS|'.count($terms).'\n';foreach($terms as $t){$thumb=(int)get_term_meta($t->term_id,'thumbnail_id',true);$status=$thumb?(string)get_post_meta($thumb,'_bhc_image_generation_status',true):'';$prompt=$thumb?(string)get_post_meta($thumb,'_bhc_image_prompt',true):'';echo $t->term_id.'|'.$t->slug.'|'.$t->name.'|count='.$t->count.'|thumb='.$thumb.'|status='.$status.'|prompt='.(bool)$prompt.'\n';}
