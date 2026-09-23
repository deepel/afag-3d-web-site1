<?php
declare(strict_types=1);
/**
 * afag3d — Sitemap & Robots Controller  (Phase 4)
 */
class SitemapController extends BaseController
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /sitemap.xml
    // ─────────────────────────────────────────────────────────────────────────

    public function sitemap(): void
    {
        $siteUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : 'https://afag3d.com';
        $today   = date('Y-m-d');

        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex');          // The sitemap itself shouldn't be indexed
        header('Cache-Control: public, max-age=3600');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        echo '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        // ── Static pages ──────────────────────────────────────────────
        $staticPages = [
            ['loc' => '/',            'priority' => '1.0', 'changefreq' => 'daily',  'lastmod' => $today],
            ['loc' => '/print-order', 'priority' => '0.8', 'changefreq' => 'weekly', 'lastmod' => $today],
            ['loc' => '/portfolio',   'priority' => '0.7', 'changefreq' => 'weekly', 'lastmod' => $today],
            ['loc' => '/blog',        'priority' => '0.7', 'changefreq' => 'daily',  'lastmod' => $today],
        ];

        // ── Shops (dynamic) ───────────────────────────────────────────
        try {
            $shops = $this->db->query('SELECT slug, updated_at FROM shops WHERE status = 1 ORDER BY id ASC')
                              ->fetchAll();
            foreach ($shops as $shop) {
                $staticPages[] = [
                    'loc'        => '/shop/' . $shop['slug'],
                    'priority'   => '0.8',
                    'changefreq' => 'daily',
                    'lastmod'    => $this->formatDate($shop['updated_at']),
                ];
            }
        } catch (Throwable) { /* table may not exist in dev */ }

        // Output static pages
        foreach ($staticPages as $page) {
            $this->urlEntry($siteUrl, $page['loc'], $page['priority'], $page['changefreq'], $page['lastmod']);
        }

        // ── Products ──────────────────────────────────────────────────
        try {
            $products = $this->db->query(
                "SELECT slug, updated_at FROM products
                 WHERE status = 'active' AND slug IS NOT NULL AND slug != ''
                 ORDER BY updated_at DESC"
            )->fetchAll();

            foreach ($products as $p) {
                $this->urlEntry($siteUrl, '/product/' . $p['slug'], '0.8', 'weekly', $this->formatDate($p['updated_at']));
            }
        } catch (Throwable) {}

        // ── Blog posts ────────────────────────────────────────────────
        try {
            $posts = $this->db->query(
                "SELECT slug, updated_at, published_at FROM blog_posts
                 WHERE status = 'published' AND slug IS NOT NULL AND slug != ''
                 ORDER BY published_at DESC"
            )->fetchAll();

            foreach ($posts as $post) {
                $lastmod = $this->formatDate($post['updated_at'] ?: $post['published_at']);
                $this->urlEntry($siteUrl, '/blog/' . $post['slug'], '0.7', 'weekly', $lastmod);
            }
        } catch (Throwable) {}

        // ── Portfolio items ───────────────────────────────────────────
        try {
            $items = $this->db->query(
                "SELECT id, updated_at FROM portfolio
                 WHERE status = 1
                 ORDER BY id DESC"
            )->fetchAll();

            foreach ($items as $item) {
                $this->urlEntry($siteUrl, '/portfolio/' . $item['id'], '0.7', 'monthly', $this->formatDate($item['updated_at']));
            }
        } catch (Throwable) {}

        echo '</urlset>' . "\n";
        exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /robots.txt
    // ─────────────────────────────────────────────────────────────────────────

    public function robots(): void
    {
        $siteUrl = defined('APP_URL') ? rtrim(APP_URL, '/') : 'https://afag3d.com';

        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=86400');

        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /uploads/print-orders/\n";
        echo "Disallow: /config/\n";
        echo "Disallow: /cache/\n";
        echo "Disallow: /storage/\n";
        echo "\n";
        echo "# Block common exploit scanners\n";
        echo "Disallow: /wp-admin\n";
        echo "Disallow: /wp-login.php\n";
        echo "\n";
        echo "Sitemap: " . $siteUrl . "/sitemap.xml\n";
        exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function urlEntry(
        string $siteUrl,
        string $path,
        string $priority,
        string $changefreq,
        string $lastmod
    ): void {
        $faUrl = $siteUrl . $path;
        $enUrl = $siteUrl . '/en' . $path;

        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars($faUrl, ENT_XML1, 'UTF-8') . "</loc>\n";
        echo "    <xhtml:link rel=\"alternate\" hreflang=\"fa\" href=\"" . htmlspecialchars($faUrl, ENT_XML1, 'UTF-8') . "\"/>\n";
        echo "    <xhtml:link rel=\"alternate\" hreflang=\"en\" href=\"" . htmlspecialchars($enUrl, ENT_XML1, 'UTF-8') . "\"/>\n";
        echo "    <changefreq>" . htmlspecialchars($changefreq, ENT_XML1) . "</changefreq>\n";
        echo "    <priority>" . htmlspecialchars($priority, ENT_XML1) . "</priority>\n";
        echo "    <lastmod>" . htmlspecialchars($lastmod, ENT_XML1) . "</lastmod>\n";
        echo "  </url>\n";
    }

    private function formatDate(?string $dt): string
    {
        if (!$dt) {
            return date('Y-m-d');
        }
        $ts = strtotime($dt);
        return $ts ? date('Y-m-d', $ts) : date('Y-m-d');
    }
}
