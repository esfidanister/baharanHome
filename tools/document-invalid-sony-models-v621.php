<?php
require_once '/var/www/html/wp-load.php';
$rows=[
 482=>[
  'urls'=>['https://www.sony-asia.com/electronics/televisions/x75k-series/specifications','https://www.sony.co.id/id/electronics/televisi/x75k-series/specifications'],
  'reason'=>'صفحه رسمی سری X75K مدل‌های 43، 50، 55 و 65 اینچ را فهرست می‌کند و KD-75X75K در فهرست رسمی این سری وجود ندارد؛ بنابراین رکورد فعلی تا ارائه پلاک/منبع مستقل دقیق، نامعتبر و مسدود باقی می‌ماند.'
 ],
 478=>[
  'urls'=>['https://www.sony.com/','https://www.sony.com/electronics/televisions'],
  'reason'=>'برای کد X-98S30 تطبیق معتبر با مدل تلویزیون سونی پیدا نشد؛ چون کد قابل اثبات نیست، مشخصات یا تصویر مدل مشابه جایگزین نمی‌شود و رکورد مسدود باقی می‌ماند.'
 ],
];
foreach($rows as $id=>$row){
 if(!wc_get_product($id))continue;
 update_post_meta($id,'_bhc_source_candidates',$row['urls']);
 update_post_meta($id,'_bhc_source_candidate_reason',$row['reason']);
 update_post_meta($id,'_bhc_invalid_evidence',$row['reason']);
 update_post_meta($id,'_bhc_source_candidate_checked_at',current_time('mysql'));
 echo 'DOCUMENTED_INVALID|'.$id.PHP_EOL;
}
