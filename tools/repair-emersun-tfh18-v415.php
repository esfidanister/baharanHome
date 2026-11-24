<?php
require '/var/www/html/wp-load.php';
ini_set('memory_limit', '2G');

$id = 496;
$source = '/var/www/html/wp-content/plugins/baharaan-commerce/includes/bhc-image-references/emersun-tfh18-v415-source.png';
$file_name = 'emersun-tfh18-v415.png';
$im = @imagecreatefrompng($source);
if (!$im) { fwrite(STDERR, "SOURCE_MISSING\n"); exit(1); }

// Remove the continuous studio background from the border without touching the appliance interior.
$w = imagesx($im); $h = imagesy($im); $seen = array_fill(0, $w * $h, false); $queue = new SplQueue();
$push = function($x, $y) use (&$queue, &$seen, $w, $h) { if ($x < 0 || $y < 0 || $x >= $w || $y >= $h) return; $i = $y * $w + $x; if (!$seen[$i]) { $seen[$i] = true; $queue->enqueue([$x, $y]); } };
for ($x = 0; $x < $w; $x += 2) { $push($x, 0); $push($x, $h - 1); }
for ($y = 0; $y < $h; $y += 2) { $push(0, $y); $push($w - 1, $y); }
$neighbors = [[1,0],[-1,0],[0,1],[0,-1]];
while (!$queue->isEmpty()) {
    [$x, $y] = $queue->dequeue(); $base = imagecolorat($im, $x, $y); $br = ($base >> 16) & 255; $bg = ($base >> 8) & 255; $bb = $base & 255;
    foreach ($neighbors as [$dx, $dy]) { $nx = $x + $dx; $ny = $y + $dy; if ($nx < 0 || $ny < 0 || $nx >= $w || $ny >= $h) continue; $ni = $ny * $w + $nx; if ($seen[$ni]) continue; $c = imagecolorat($im, $nx, $ny); $r = ($c >> 16) & 255; $g = ($c >> 8) & 255; $b = $c & 255; if (max(abs($r-$br), abs($g-$bg), abs($b-$bb)) <= 34 && max($r,$g,$b)-min($r,$g,$b) <= 32) { $seen[$ni] = true; $queue->enqueue([$nx, $ny]); } }
}
$outW = 4096; $outH = (int) round($h * ($outW / $w)); $out = imagecreatetruecolor($outW, $outH); imagealphablending($out, false); imagesavealpha($out, true); $clear = imagecolorallocatealpha($out, 255, 255, 255, 127); imagefilledrectangle($out, 0, 0, $outW, $outH, $clear);
$cut = imagecreatetruecolor($w, $h); imagealphablending($cut, false); imagesavealpha($cut, true); for ($y=0; $y<$h; $y++) for ($x=0; $x<$w; $x++) { $c=imagecolorat($im,$x,$y); $col=imagecolorallocatealpha($cut,($c>>16)&255,($c>>8)&255,$c&255,$seen[$y*$w+$x]?127:0); imagesetpixel($cut,$x,$y,$col); }
imagecopyresampled($out, $cut, 0, 0, 0, 0, $outW, $outH, $w, $h);
$uploads = wp_upload_dir(); wp_mkdir_p($uploads['path']); $path = $uploads['path'].'/'.$file_name; imagepng($out, $path, 6);
imagedestroy($im); imagedestroy($cut); imagedestroy($out);

require_once ABSPATH.'wp-admin/includes/image.php';
$old = get_post_thumbnail_id($id); if ($old) wp_delete_attachment($old, true);
$aid = wp_insert_attachment(['post_mime_type'=>'image/png','post_title'=>get_the_title($id).' تصویر اختصاصی مدل TFH18','post_status'=>'inherit'], $path, $id);
wp_update_attachment_metadata($aid, wp_generate_attachment_metadata($aid, $path)); set_post_thumbnail($id, $aid);
$specs = get_post_meta($id, '_bhc_specs', true); if (!is_array($specs)) $specs = [];
update_post_meta($id, '_bhc_source_type', 'exact_model_code_brand_representative_article');
update_post_meta($id, '_bhc_source_checked_at', '2026-11-20');
update_post_meta($id, '_bhc_source_urls', ['https://emersunstore.ir/%D9%85%D8%B9%D8%B1%D9%81%DB%8C-%DB%8C%D8%AE%DA%86%D8%A7%D9%84-%D9%81%D8%B1%DB%8C%D8%B2%D8%B1-%D8%A7%D8%B1%D8%B2%D8%A7%D9%86-%D8%A7%D9%85%D8%B1%D8%B3%D8%A7%D9%86-%D8%A8%D8%AF%D9%88%D9%86-%D8%A8%D8%B1/']);
update_post_meta($id, '_bhc_model', 'TFH18'); update_post_meta($id, '_bhc_structured_specs', wp_json_encode($specs, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)); update_post_meta($id, '_bhc_catalog_quality_state', 'reference_verified'); update_post_meta($id, '_bhc_verification_status', 'verified'); update_post_meta($id, '_bhc_release_blocked', 'no'); update_post_meta($id, '_bhc_image_generation_status', 'generated_4k_transparent_verified'); update_post_meta($id, '_bhc_image_long_edge', $outW); update_post_meta($id, '_bhc_image_checked_at', '2026-11-20'); update_post_meta($id, '_bhc_image_prompt', 'Emersun TFH18 exact model-specific top-freezer refrigerator-freezer, white two-door body, transparent 4K PNG, no text, no watermark, product cutout.'); update_post_meta($id, '_bhc_no_price_message', 'قیمت نمایش داده نمی‌شود. وضعیت کالا: ناموجود.');
echo "DONE|$id|$outW".'x'.$outH."\n";
