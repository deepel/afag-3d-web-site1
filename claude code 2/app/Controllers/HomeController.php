<?php
declare(strict_types=1);
/**
 * afag3d — Home Controller
 */
class HomeController extends BaseController
{
    /** GET / */
    public function index(): void
    {
        $settings = new Setting($this->db);
        $siteData = $settings->all();

        // ── SEO ───────────────────────────────────────────────────────────────
        SEO::set([
            'title'       => ($siteData['site_name'] ?? 'afag3d') . ' — استودیوی چاپ سه‌بعدی',
            'description' => $siteData['seo_meta_desc'] ?? 'استودیوی تخصصی چاپ سه‌بعدی، معماری و ساخت ماکت',
            'canonical'   => BASE_URL . '/',
            'og_type'     => 'website',
            'og_image'    => BASE_URL . '/assets/img/og-default.svg',
        ]);

        // ── Featured products (cached 30 min) ─────────────────────────────────
        $db = $this->db;
        $featuredProducts = Cache::remember('home_featured_products', 1800, function () use ($db) {
            return $db->query(
                'SELECT p.*, pi.path AS cover_image
                 FROM products p
                 LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_cover = 1
                 WHERE p.status = "active" AND p.is_featured = 1
                 ORDER BY p.id DESC LIMIT 8'
            )->fetchAll();
        });

        // ── Portfolio items (cached 30 min) ───────────────────────────────────
        $portfolio = Cache::remember('home_portfolio', 1800, function () use ($db) {
            return $db->query(
                'SELECT * FROM portfolio WHERE status = 1 AND is_featured = 1 ORDER BY sort_order ASC, id DESC LIMIT 6'
            )->fetchAll();
        });

        // ── Latest blog posts ─────────────────────────────────────────────────
        $blogPosts = $db->query(
            'SELECT bp.*, u.name AS author_name, bc.name AS category_name
             FROM blog_posts bp
             LEFT JOIN users u ON u.id = bp.author_id
             LEFT JOIN blog_categories bc ON bc.id = bp.category_id
             WHERE bp.status = "published"
             ORDER BY bp.published_at DESC LIMIT 3'
        )->fetchAll();

        // ── Stats for counter section (cached 1 hr) ────────────────────────
        $stats = Cache::remember('home_stats', 3600, function () use ($db) {
            return [
                'products' => (int) $db->query('SELECT COUNT(*) FROM products WHERE status="active"')->fetchColumn(),
                'orders'   => (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
                'clients'  => (int) $db->query('SELECT COUNT(*) FROM users WHERE role="customer"')->fetchColumn(),
                'portfolio'=> (int) $db->query('SELECT COUNT(*) FROM portfolio WHERE status=1')->fetchColumn(),
            ];
        });

        $this->render('home.index', [
            'title'           => $siteData['site_name'] ?? APP_NAME,
            'siteData'        => $siteData,
            'featuredProducts'=> $featuredProducts,
            'portfolio'       => $portfolio,
            'blogPosts'       => $blogPosts,
            'stats'           => $stats,
            'loadHeroParallax'=> true,
        ]);
    }

    /** 404 handler */
    public function error404(): void
    {
        http_response_code(404);
        $this->render('errors.404', ['title' => 'صفحه یافت نشد']);
    }
}
