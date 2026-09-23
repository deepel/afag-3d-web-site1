<?php
/**
 * afag3d — Sample product seeder
 * Adds printer-supply products to shop 2 (supplies) and a few extra models to shop 1.
 * Safe to re-run: skips products whose slug already exists.
 */
declare(strict_types=1);

require __DIR__ . '/../config/config.php';

$db = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET),
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

// Resolve shop ids by slug
$shopId = [];
foreach ($db->query("SELECT id, slug FROM shops") as $s) {
    $shopId[$s['slug']] = (int) $s['id'];
}
$models   = $shopId['models']   ?? 1;
$supplies = $shopId['supplies'] ?? 2;

// Move the misplaced STL files from supplies → models
$db->prepare("UPDATE products SET shop_id = ? WHERE shop_id = ? AND name LIKE '%STL%'")
   ->execute([$models, $supplies]);

/**
 * Each row: [shop_id, name, slug, sku, description, price, sale_price, stock, weight, featured]
 */
$products = [
    // ── Printer supplies (shop 2) ──────────────────────────────────────────
    [$supplies, 'فیلامنت PLA پریمیوم ۱ کیلوگرم', 'pla-filament-1kg', 'SUP-PLA-01',
     'فیلامنت PLA با کیفیت بالا، قطر ۱.۷۵ میلی‌متر، مناسب برای چاپ‌های روزمره با چسبندگی عالی و کمترین تاب‌خوردگی. موجود در رنگ‌های متنوع.',
     520000, 469000, 60, 1050, 1],

    [$supplies, 'فیلامنت PETG شفاف ۱ کیلوگرم', 'petg-filament-clear-1kg', 'SUP-PETG-01',
     'فیلامنت PETG مقاوم و نیمه‌شفاف، ایده‌آل برای قطعات مهندسی و کاربردی با مقاومت حرارتی و ضربه‌ای بالا.',
     640000, null, 35, 1050, 1],

    [$supplies, 'رزین استاندارد ۱ لیتری', 'standard-resin-1l', 'SUP-RES-01',
     'رزین فوتوپلیمر استاندارد برای پرینترهای رزینی LCD/MSLA، با جزئیات بسیار بالا و سختی مناسب برای مینیاتور و ماکت.',
     980000, 890000, 25, 1100, 1],

    [$supplies, 'پک نازل برنجی ۰.۴ میلی‌متر (۵ عددی)', 'brass-nozzle-04-pack', 'SUP-NOZ-04',
     'مجموعه ۵ عددی نازل برنجی ۰.۴ میلی‌متر سازگار با هات‌اند MK8، برای تعویض دوره‌ای و حفظ کیفیت چاپ.',
     150000, null, 80, 40, 0],

    [$supplies, 'صفحه چاپ مغناطیسی PEI فنری', 'magnetic-pei-build-plate', 'SUP-PEI-01',
     'صفحه چاپ فنری دو طرفه با پوشش PEI و پایه مغناطیسی، جدا کردن آسان قطعه و چسبندگی فوق‌العاده. ابعاد ۲۳۵×۲۳۵.',
     680000, 599000, 18, 350, 1],

    [$supplies, 'اسپری چسب چاپ سه‌بعدی', '3d-print-adhesive-spray', 'SUP-ADH-01',
     'اسپری چسب مخصوص بستر چاپ برای جلوگیری از بلند شدن گوشه‌ها (Warping)، مناسب انواع فیلامنت.',
     220000, null, 45, 400, 0],

    [$supplies, 'تسمه تایمینگ GT2 (۲ متر)', 'gt2-timing-belt-2m', 'SUP-BELT-01',
     'تسمه تایمینگ GT2 با عرض ۶ میلی‌متر و الیاف فایبرگلاس، مناسب تعمیر و ارتقای محورهای X و Y پرینتر.',
     130000, null, 70, 60, 0],

    [$supplies, 'هیت‌بلاک و ترمیستور یدکی', 'heatblock-thermistor-kit', 'SUP-HTB-01',
     'کیت یدکی هیت‌بلاک به‌همراه ترمیستور و هیتر کارتریجی، برای سرویس و نگهداری هات‌اند.',
     290000, 259000, 22, 90, 0],

    // ── A couple more ready models (shop 1) ────────────────────────────────
    [$models, 'گلدان مارپیچ واز-مود', 'spiral-vase-mode', 'MDL-VASE-02',
     'گلدان تزئینی با طراحی مارپیچ چاپ‌شده در حالت Vase Mode، سبک، تک‌دیواره و چشم‌نواز برای دکور مدرن.',
     160000, 139000, 28, 180, 1],

    [$models, 'فیگور اژدهای مفصلی', 'articulated-dragon-figure', 'MDL-DRG-01',
     'فیگور اژدهای مفصل‌دار (Articulated) با حرکت روان مفاصل، چاپ‌شده یکپارچه بدون نیاز به مونتاژ.',
     340000, null, 16, 220, 1],
];

$insert = $db->prepare(
    "INSERT INTO products
        (shop_id, name, slug, sku, description, price, sale_price, stock, weight, status, is_featured)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)"
);
$exists = $db->prepare("SELECT COUNT(*) FROM products WHERE slug = ?");

$added = 0; $skipped = 0;
foreach ($products as $p) {
    $exists->execute([$p[2]]);
    if ((int) $exists->fetchColumn() > 0) { $skipped++; continue; }
    $insert->execute($p);
    $added++;
}

echo "Added: $added | Skipped (already exist): $skipped\n";
echo "Totals — models: " .
     $db->query("SELECT COUNT(*) FROM products WHERE shop_id=$models")->fetchColumn() .
     " | supplies: " .
     $db->query("SELECT COUNT(*) FROM products WHERE shop_id=$supplies")->fetchColumn() . "\n";
