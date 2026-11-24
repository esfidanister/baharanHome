<?php
require_once '/var/www/html/wp-load.php';
$rows = [
  243 => [
    'model' => 'DLE-43M6000',
    'urls' => [
      'https://arkarno.com/brand/%D8%AF%D9%88%D9%88/',
      'https://topcopon.ir/blog/the-cheapest-smart-tv/'
    ],
    'note' => 'منابع بازار نسخه دارای پسوند DLE-43M6000EM را نشان می‌دهند؛ رکورد فعلی پسوند کامل ندارد و تا تطبیق پلاک/دفترچه آزاد نمی‌شود.'
  ],
  244 => [
    'model' => 'DLE-55M6000',
    'urls' => [
      'https://radek.shop/product/daewoo-dle-55m6000eu-uhd-led-tv-55-inch/',
      'https://bvf.ir/DAEWOO-55M6000EU',
      'https://moqayese.com/home-kitchen/daewoo-dle-55m6000eu'
    ],
    'note' => 'منابع بازار نسخه دارای پسوند DLE-55M6000EU را نشان می‌دهند؛ رکورد فعلی پسوند کامل ندارد و تا تطبیق پلاک/دفترچه آزاد نمی‌شود.'
  ],
];
$count=0;
foreach($rows as $id=>$r){
  $p=wc_get_product($id); if(!$p){echo "MISSING|{$id}\n";continue;}
  update_post_meta($id,'_bhc_source_candidates',$r['urls']);
  update_post_meta($id,'_bhc_source_type','family_suffix_needs_exact');
  update_post_meta($id,'_bhc_source_checked_at',current_time('mysql'));
  update_post_meta($id,'_bhc_source_gap_reason',$r['note']);
  update_post_meta($id,'_bhc_source_review_notes','فاز ۶۱۸: خانواده مدل پیدا شد، اما suffix دقیق رکورد فعلی تأیید نشده است.');
  update_post_meta($id,'_bhc_verification_status','pending_source');
  update_post_meta($id,'_bhc_catalog_quality_state','reference_needs_verification');
  update_post_meta($id,'_bhc_release_blocked','yes');
  echo "RECORDED|{$id}|{$r['model']}|CANDIDATES|".count($r['urls'])."\n";
  $count++;
}
echo "UPDATED|{$count}\n";
