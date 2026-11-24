<?php get_header(); ?>
<div class="bhb-home">
<?php
$studio=bhb_studio();
foreach((array)($studio['order']??[]) as $key){
 if(in_array($key,(array)($studio['enabled']??[]),true)) bhb_render_section($key);
}
?>
</div>
<?php get_footer(); ?>
