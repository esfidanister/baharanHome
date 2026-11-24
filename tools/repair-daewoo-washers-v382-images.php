<?php
require '/var/www/html/wp-load.php';
ini_set('memory_limit', '2G');
$items = [220 => 'daewoo-dwk-8140-v382.png', 222 => 'daewoo-dwk-9000-v382.png'];
$src = '/var/www/html/wp-content/plugins/baharaan-commerce/includes/bhc-image-references/';
foreach ($items as $pid => $name) {
    $aid = get_post_thumbnail_id($pid);
    $im = @imagecreatefrompng($src . $name);
    if (!$aid || !$im) continue;
    $w = imagesx($im); $h = imagesy($im); $scale = 4096 / max($w, $h);
    $nw = (int)round($w * $scale); $nh = (int)round($h * $scale);
    $out = imagecreatetruecolor($nw, $nh);
    imagealphablending($out, false); imagesavealpha($out, true);
    $transparent = imagecolorallocatealpha($out, 255, 255, 255, 127);
    imagefilledrectangle($out, 0, 0, $nw, $nh, $transparent);
    imagecopyresampled($out, $im, 0, 0, 0, 0, $nw, $nh, $w, $h);
    $up = wp_upload_dir(); $rel = substr($up['subdir'], 1); $file = $up['path'] . '/' . $name;
    imagepng($out, $file, 6); imagedestroy($im); imagedestroy($out);
    update_post_meta($aid, '_wp_attached_file', $rel . '/' . $name);
    wp_update_attachment_metadata($aid, ['width' => $nw, 'height' => $nh, 'file' => $rel . '/' . $name, 'sizes' => []]);
    wp_update_post(['ID' => $aid, 'post_mime_type' => 'image/png']);
    echo $pid . '|' . $nw . 'x' . $nh . PHP_EOL;
}
