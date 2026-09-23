<?php
declare(strict_types=1);
/**
 * afag3d — Dynamic XML Sitemap
 * Served directly as /sitemap.xml via the rewrite rule in .htaccess.
 */

// Load config (gives us DB constants + BASE_URL)
require_once dirname(__DIR__) . '/config/config.php';

// Connect to DB
try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );
    $db = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(503);
    die('<!-- DB error -->');
}

// Base URL for sitemap (use APP_URL which is the full domain)
$base = rtrim(defined('APP_URL') ? APP_URL : 'https://afag3d.com', '/');

$today = date('Y-m-d');

// ── Static pages ─────────────────────────────────────────────────────────────
$staticPages = [
    ['loc' => '/',                  'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => '/blog',              'priority' => '0.7', 'changefreq' => 'daily'],
    ['loc' => '/portfolio',         'priority' => '0.7', 'changefreq' => 'weekly'],
    ['loc' => '/print-order',       'priority' => '0.8', 'changefreq' => 'monthly'],
    ['loc' => '/shop/models',       'priority' => '0.9', 'changefreq' => 'daily'],
    ['loc' => '/shop/supplies',     'priority' => '0.9', 'changefreq' => 'daily'],
    ['loc' => '/about',             'priority' => '0.5', 'changefreq' => 'monthly'],
    ['loc' => '/contact',           'priority' => '0.5', 'changefreq' => 'monthly'],
    ['loc' => '/privacy',           'priority' => '0.3', 'changefreq' => 'yearly'],
    ['loc' => '/terms',             'priority' => '0.3', 'changefreq' => 'yearly'],
];

// ── Dynamic: published products ──────────────────────────────────────────────
$products = [];
try {
    $stmt = $db->query(
        "SELECT p.slug, p.updated_at, s.slug AS shop_slug
         FROM products p
         LEFT JOIN shops s ON s.id = p.shop_id
         WHERE p.status = 'active'
         ORDER BY p.id DESC"
    );
    $products = $stmt->fetchAll();
} catch (PDOException) {}

// ── Dynamic: published blog posts ────────────────────────────────────────────
$blogPosts = [];
try {
    $stmt = $db->query(
        "SELECT slug, updated_at
         FROM blog_posts
         WHERE status = 'published'
         ORDER BY published_at DESC"
    );
    $blogPosts = $stmt->fetchAll();
} catch (PDOException) {}

// ── Dynamic: portfolio items ─────────────────────────────────────────────────
$portfolioItems = [];
try {
    $stmt = $db->query(
        "SELECT id, updated_at
         FROM portfolio
         WHERE status = 1
         ORDER BY id DESC"
    );
    $portfolioItems = $stmt->fetchAll();
} catch (PDOException) {}

// ── Dynamic: blog categories ─────────────────────────────────────────────────
$blogCategories = [];
try {
    $stmt = $db->query("SELECT slug FROM blog_categories ORDER BY id");
    $blogCategories = $stmt->fetchAll();
} catch (PDOException) {}

// ── Output XML ───────────────────────────────────────────────────────────────
header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');   // Sitemap itself should not be indexed as a page

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Helper
function sitemapUrl(string $base, string $loc, string $lastmod, string $changefreq, string $priority): string
{
    $url  = htmlspecialchars($base . $loc, ENT_XML1);
    $out  = "  <url>\n";
    $out .= "    <loc>{$url}</loc>\n";
    $out .= "    <lastmod>{$lastmod}</lastmod>\n";
    $out .= "    <changefreq>{$changefreq}</changefreq>\n";
    $out .= "    <priority>{$priority}</priority>\n";
    $out .= "  </url>\n";
    return $out;
}

// Static
foreach ($staticPages as $p) {
    echo sitemapUrl($base, $p['loc'], $today, $p['changefreq'], $p['priority']);
}

// Products
foreach ($products as $p) {
    $lastmod = $p['updated_at'] ? date('Y-m-d', strtotime($p['updated_at'])) : $today;
    // Direct product URL
    echo sitemapUrl($base, '/product/' . $p['slug'], $lastmod, 'weekly', '0.8');
    // Also shop-prefixed URL
    if (!empty($p['shop_slug'])) {
        echo sitemapUrl($base, '/shop/' . $p['shop_slug'] . '/' . $p['slug'], $lastmod, 'weekly', '0.8');
    }
}

// Blog posts
foreach ($blogPosts as $p) {
    $lastmod = $p['updated_at'] ? date('Y-m-d', strtotime($p['updated_at'])) : $today;
    echo sitemapUrl($base, '/blog/' . $p['slug'], $lastmod, 'monthly', '0.7');
}

// Portfolio
foreach ($portfolioItems as $p) {
    $lastmod = $p['updated_at'] ? date('Y-m-d', strtotime($p['updated_at'])) : $today;
    echo sitemapUrl($base, '/portfolio/' . $p['id'], $lastmod, 'monthly', '0.7');
}

// Blog categories
foreach ($blogCategories as $c) {
    echo sitemapUrl($base, '/blog/category/' . $c['slug'], $today, 'weekly', '0.6');
}

echo '</urlset>' . "\n";
