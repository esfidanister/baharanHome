<?php
require_once '/var/www/html/wp-load.php';
$term=get_term_by('slug','%d8%aa%d9%84%d9%88%db%8c%d8%b2%db%8c%d9%88%d9%86','product_cat');
if(!$term) $term=get_term_by('name','تلویزیون','product_cat');
echo 'CATEGORY|'.($term?$term->term_id.'|'.$term->slug:'NOT_FOUND')."\n";
foreach(['product_brand','pa_brand'] as $tax){
 $terms=taxonomy_exists($tax)?get_terms(['taxonomy'=>$tax,'hide_empty'=>false]):[];
 if(is_wp_error($terms))continue;
 foreach($terms as $t) if(mb_stripos($t->name,'اسنوا')!==false||mb_stripos($t->slug,'snowa')!==false) echo 'BRAND|'.$tax.'|'.$t->term_id.'|'.$t->name.'|'.$t->slug."\n";
}
