<?php
$root = '/var/www/html';
$uploads = $root . '/wp-content/uploads';
$mysqli = mysqli_init();
$mysqli->real_connect('db', 'wordpress', 'wordpress_local_only', 'wordpress', 3306);
$mysqli->set_charset('utf8mb4');

$missing = [];
$raster = 0;
$webp = 0;
$other = 0;
$result = $mysqli->query("SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key='_wp_attached_file'");
while ($row = $result->fetch_assoc()) {
    $rel = (string) $row['meta_value'];
    $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
    if (in_array($ext, ['png', 'jpg', 'jpeg'], true)) {
        $raster++;
    } elseif ($ext === 'webp') {
        $webp++;
    } else {
        $other++;
    }
    if (!is_file($uploads . '/' . $rel)) {
        $missing[] = $row['post_id'] . ' ' . $rel;
    }
}
echo "attached webp={$webp} raster={$raster} other={$other} missing=" . count($missing) . "\n";
echo implode("\n", array_slice($missing, 0, 20)) . "\n";

$metaMissing = 0;
$metaRaster = 0;
$badSerialize = 0;
$meta = $mysqli->query("SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key='_wp_attachment_metadata'");
while ($row = $meta->fetch_assoc()) {
    $data = @unserialize((string) $row['meta_value'], ['allowed_classes' => false]);
    if (!is_array($data)) {
        $badSerialize++;
        continue;
    }
    $blob = (string) $row['meta_value'];
    if (preg_match('/\.(png|jpe?g)/i', $blob)) {
        $metaRaster++;
    }
    $file = (string) ($data['file'] ?? '');
    if ($file !== '' && !is_file($uploads . '/' . $file)) {
        $metaMissing++;
        if ($metaMissing <= 15) {
            echo "meta_file {$row['post_id']} {$file}\n";
        }
    }
    $dir = $file !== '' ? dirname($file) : '';
    if (!empty($data['original_image'])) {
        $original = ($dir === '.' || $dir === '' ? '' : $dir . '/') . $data['original_image'];
        if (!is_file($uploads . '/' . $original)) {
            $metaMissing++;
            if ($metaMissing <= 15) {
                echo "original {$row['post_id']} {$original}\n";
            }
        }
    }
}
$urls = (int) $mysqli->query("SELECT COUNT(*) c FROM wp_postmeta WHERE meta_value LIKE '%wp-content/uploads/%' AND (meta_value LIKE '%.png%' OR meta_value LIKE '%.jpg%' OR meta_value LIKE '%.jpeg%')")->fetch_assoc()['c'];
$thumbs = $mysqli->query("SELECT t.post_id, t.meta_value thumb, a.meta_value file FROM wp_postmeta t LEFT JOIN wp_postmeta a ON a.post_id=t.meta_value AND a.meta_key='_wp_attached_file' WHERE t.meta_key='_thumbnail_id' AND t.meta_value<>'' AND t.meta_value<>'0'");
$thumbMissing = 0;
$thumbTotal = 0;
while ($row = $thumbs->fetch_assoc()) {
    $thumbTotal++;
    $file = (string) ($row['file'] ?? '');
    if ($file === '' || !is_file($uploads . '/' . $file)) {
        $thumbMissing++;
        if ($thumbMissing <= 15) {
            echo "thumb {$row['post_id']} id={$row['thumb']} file={$file}\n";
        }
    }
}
echo "meta_missing={$metaMissing} meta_raster={$metaRaster} bad_serialize={$badSerialize} upload_urls={$urls}\n";
echo "thumbs={$thumbTotal} thumb_missing={$thumbMissing}\n";
$products = $mysqli->query("SELECT COUNT(*) published FROM wp_posts WHERE post_type='product' AND post_status='publish'")->fetch_assoc();
echo "products={$products['published']}\n";
