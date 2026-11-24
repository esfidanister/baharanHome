<?php
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
ini_set('memory_limit', '2G');
set_time_limit(0);

$q = new WP_Query(['post_type'=>'product','post_status'=>['publish','draft','pending','private'],'posts_per_page'=>-1,'fields'=>'ids']);
$resized = 0; $prompts = 0; $checked = 0;
foreach ($q->posts as $pid) {
    $aid = get_post_thumbnail_id($pid);
    if (!$aid) continue;
    $file = get_attached_file($aid);
    $meta = wp_get_attachment_metadata($aid);
    $w = (int)($meta['width'] ?? 0); $h = (int)($meta['height'] ?? 0);
    if ($w && $h && max($w, $h) > 4096 && is_file($file)) {
        $mime = function_exists('mime_content_type') ? @mime_content_type($file) : '';
        $im = ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) ? @imagecreatefromwebp($file) : @imagecreatefrompng($file);
        if ($im) {
            $scale = 4096 / max(imagesx($im), imagesy($im));
            $nw = max(1, (int)round(imagesx($im) * $scale));
            $nh = max(1, (int)round(imagesy($im) * $scale));
            $out = imagecreatetruecolor($nw, $nh);
            imagealphablending($out, false); imagesavealpha($out, true);
            $clear = imagecolorallocatealpha($out, 255, 255, 255, 127);
            imagefilledrectangle($out, 0, 0, $nw, $nh, $clear);
            imagecopyresampled($out, $im, 0, 0, 0, 0, $nw, $nh, imagesx($im), imagesy($im));
            $target = ($mime === 'image/webp') ? preg_replace('/\.[^.]+$/', '.png', $file) : $file;
            imagepng($out, $target, 6);
            if ($target !== $file && is_file($target)) { @unlink($file); update_attached_file($aid, $target); wp_update_post(['ID'=>$aid, 'post_mime_type'=>'image/png']); $file = $target; }
            imagedestroy($im); imagedestroy($out);
            $newMeta = wp_generate_attachment_metadata($aid, $file);
            if (is_array($newMeta)) wp_update_attachment_metadata($aid, $newMeta);
            update_post_meta($pid, '_bhc_image_long_edge', 4096);
            $resized++;
        }
    } elseif ($w && $h) {
        update_post_meta($pid, '_bhc_image_long_edge', max($w, $h));
    }
    if (!get_post_meta($pid, '_bhc_image_prompt', true)) {
        $title = wp_strip_all_tags(get_the_title($pid));
        $model = get_post_meta($pid, '_bhc_model', true);
        update_post_meta($pid, '_bhc_image_prompt', $title . ' ' . ($model ?: '') . ' product-specific 4K transparent PNG, isolated product cutout, no room, no text, no logo overlay, no watermark, accurate shape and finish.');
        $prompts++;
    }
    $checked++;
}
echo "IMAGE_V526|checked=$checked|resized=$resized|prompts_added=$prompts\n";
