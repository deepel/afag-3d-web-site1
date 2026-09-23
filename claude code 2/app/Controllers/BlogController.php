<?php
declare(strict_types=1);
/**
 * afag3d — Blog Controller (public-facing)
 */
class BlogController extends BaseController
{
    private function getSiteData(): array
    {
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    public function index(): void
    {
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $catId    = (int) ($_GET['cat'] ?? 0);
        $search   = trim($_GET['q'] ?? '');
        $perPage  = 9;

        $result     = Blog::getPublished($this->db, $page, $perPage, $catId ?: null, $search);
        $categories = Blog::getCategories($this->db);
        $siteData   = $this->getSiteData();

        $this->render('blog.index', [
            'title'      => 'بلاگ',
            'posts'      => $result['items'],
            'total'      => $result['total'],
            'pages'      => $result['pages'],
            'page'       => $page,
            'categories' => $categories,
            'activeCat'  => $catId,
            'search'     => $search,
            'siteData'   => $siteData,
        ]);
    }

    public function category(string $slug): void
    {
        $category = Blog::getCategoryBySlug($this->db, $slug);
        if (!$category) {
            $this->error404();
            return;
        }

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 9;

        $result     = Blog::getPublished($this->db, $page, $perPage, (int) $category['id']);
        $categories = Blog::getCategories($this->db);
        $siteData   = $this->getSiteData();

        $this->render('blog.index', [
            'title'      => $category['name'] . ' — بلاگ',
            'posts'      => $result['items'],
            'total'      => $result['total'],
            'pages'      => $result['pages'],
            'page'       => $page,
            'categories' => $categories,
            'activeCat'  => (int) $category['id'],
            'search'     => '',
            'siteData'   => $siteData,
            'category'   => $category,
        ]);
    }

    public function show(string $slug): void
    {
        $post = Blog::getBySlug($this->db, $slug);
        if (!$post) {
            $this->error404();
            return;
        }

        Blog::incrementViews($this->db, (int) $post['id']);

        $related    = Blog::getRelated($this->db, (int) $post['id'], (int) ($post['category_id'] ?? 0));
        $recent     = Blog::getRecent($this->db, 4);
        $categories = Blog::getCategories($this->db);
        $readTime   = Blog::readTime($post['body'] ?? '');
        $siteData   = $this->getSiteData();

        $this->render('blog.post', [
            'title'      => $post['title'],
            'post'       => $post,
            'related'    => $related,
            'recent'     => $recent,
            'categories' => $categories,
            'readTime'   => $readTime,
            'siteData'   => $siteData,
        ]);
    }

    public function search(): void
    {
        $q        = trim($_GET['q'] ?? '');
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $perPage  = 9;

        $result     = Blog::getPublished($this->db, $page, $perPage, null, $q);
        $categories = Blog::getCategories($this->db);
        $siteData   = $this->getSiteData();

        $this->render('blog.index', [
            'title'      => 'جستجو: ' . htmlspecialchars($q, ENT_QUOTES),
            'posts'      => $result['items'],
            'total'      => $result['total'],
            'pages'      => $result['pages'],
            'page'       => $page,
            'categories' => $categories,
            'activeCat'  => 0,
            'search'     => $q,
            'siteData'   => $siteData,
        ]);
    }
}
