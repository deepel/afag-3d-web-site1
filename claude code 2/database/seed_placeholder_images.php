<?php
declare(strict_types=1);
/**
 * afag3d — Branded placeholder image seeder
 *
 * Gives every product a tasteful, on-brand cover so the shop never shows an
 * empty box during testing. Each cover is a LOCAL SVG (offline-resilient, no
 * external refs): warm gradient + the stepped-pyramid logo + the afag3d
 * wordmark. The owner replaces these with real photos from the admin panel.
 *
 * Run once:  php database/seed_placeholder_images.php
 * Safe to re-run: only fills products that have no cover image yet.
 */
require_once __DIR__ . '/../config/config.php';

$db = new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET),
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

// Warm tones for the models shop, cooler tones for parts/filament.
$warm = [
    ['#EA580C', '#7C2D12'], // molten orange
    ['#C2410C', '#431407'], // terracotta
    ['#D97706', '#78350F'], // amber
    ['#B45309', '#3F2511'], // clay
    ['#9A3412', '#2A1208'], // burnt
];
$cool = [
    ['#2DD4BF', '#0F3D38'], // teal
    ['#0D9488', '#0A2E2A'], // deep teal
    ['#64748B', '#1E293B'], // steel
];

/** Build a self-contained branded SVG cover for one product. */
function coverSvg(int $seed, array $g): string
{
    [$c1, $c2] = $g;
    $rot = ($seed * 37) % 40 - 20; // gentle gradient angle variation
    // stepped-pyramid logo (same geometry as the brand mark)
    $mark = <<<MARK
<g transform="translate(400,360) scale(2.4)" stroke="rgba(0,0,0,.18)" stroke-width=".75" stroke-linejoin="round">
  <polygon points="20,86 60,106 60,118 20,98" fill="#EA580C"/>
  <polygon points="100,86 60,106 60,118 100,98" fill="#C2410C"/>
  <polygon points="60,66 100,86 60,106 20,86" fill="#FDBA74"/>
  <polygon points="32,74 60,88 60,100 32,86" fill="#EA580C"/>
  <polygon points="88,74 60,88 60,100 88,86" fill="#C2410C"/>
  <polygon points="60,54 88,74 60,88 32,74" fill="#FDBA74"/>
  <polygon points="44,62 60,70 60,82 44,74" fill="#EA580C"/>
  <polygon points="76,62 60,70 60,82 76,74" fill="#C2410C"/>
  <polygon points="60,46 76,62 60,70 44,62" fill="#FFEDD5"/>
</g>
MARK;

    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
  <defs>
    <linearGradient id="bg" gradientTransform="rotate($rot 0.5 0.5)">
      <stop offset="0" stop-color="$c1"/>
      <stop offset="1" stop-color="$c2"/>
    </linearGradient>
    <pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse">
      <path d="M48 0H0V48" fill="none" stroke="rgba(255,255,255,.06)" stroke-width="1"/>
    </pattern>
    <radialGradient id="vig" cx="50%" cy="42%" r="65%">
      <stop offset="0" stop-color="rgba(0,0,0,0)"/>
      <stop offset="1" stop-color="rgba(0,0,0,.32)"/>
    </radialGradient>
  </defs>
  <rect width="800" height="800" fill="url(#bg)"/>
  <rect width="800" height="800" fill="url(#grid)"/>
  <rect width="800" height="800" fill="url(#vig)"/>
  $mark
  <text x="400" y="690" text-anchor="middle" font-family="Space Grotesk, Segoe UI, Arial, sans-serif"
        font-size="44" font-weight="700" letter-spacing="2" fill="#F5F2EC">afag<tspan fill="#FDBA74">3d</tspan></text>
  <text x="400" y="726" text-anchor="middle" font-family="Space Mono, monospace"
        font-size="16" letter-spacing="5" fill="rgba(245,242,236,.55)">3D PRINT STUDIO</text>
</svg>
SVG;
}

// Products with no cover image yet.
$rows = $db->query(
    "SELECT p.id, p.shop_id
       FROM products p
      WHERE NOT EXISTS (
          SELECT 1 FROM product_images pi WHERE pi.product_id = p.id AND pi.is_cover = 1
      )
      ORDER BY p.id"
)->fetchAll();

$ins = $db->prepare(
    "INSERT INTO product_images (product_id, path, is_cover, sort_order)
     VALUES (:pid, :path, 1, 0)"
);

$n = 0;
foreach ($rows as $r) {
    $id   = (int) $r['id'];
    $pool = ((int) $r['shop_id'] === 2) ? $cool : $warm;
    $g    = $pool[$id % count($pool)];

    $dir = PUBLIC_PATH . '/uploads/products/' . $id;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $rel  = 'products/' . $id . '/cover.svg';
    file_put_contents(PUBLIC_PATH . '/uploads/' . $rel, coverSvg($id, $g));
    $ins->execute(['pid' => $id, 'path' => $rel]);
    $n++;
}

echo "Seeded {$n} placeholder cover image(s).\n";
