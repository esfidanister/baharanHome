<?php
require_once '/var/www/html/wp-load.php';
if(!function_exists('bhb_products')||!function_exists('wc_get_products')){echo "MISSING_HELPERS\n";exit(1);}
$old=(array)get_option('bhc_options',[]);$results=[];
try{
 foreach(['in_stock_first','in_stock_only','all'] as $policy){
  $next=$old;$next['stock_display_policy']=$policy;update_option('bhc_options',$next,false);
  $items=bhb_products(['limit'=>20,'orderby'=>'date','order'=>'DESC']);
  $in=0;$out=0;$seenOut=false;$violations=0;
  foreach($items as $product){if($product->is_in_stock()){$in++;if($seenOut)$violations++;}else{$out++;$seenOut=true;}}
  $results[$policy]=['items'=>count($items),'in_stock'=>$in,'out_of_stock'=>$out,'order_violations'=>$violations];
 }
}finally{update_option('bhc_options',$old,false);}
foreach($results as $policy=>$row)echo 'POLICY|'.$policy.'|ITEMS='.$row['items'].'|IN='.$row['in_stock'].'|OUT='.$row['out_of_stock'].'|ORDER_VIOLATIONS='.$row['order_violations'].PHP_EOL;
