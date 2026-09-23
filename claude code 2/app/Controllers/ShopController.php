<?php
declare(strict_types=1);

class ShopController extends BaseController
{
    private function getShopBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM shops WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function index(): void
    {
        $shopSlug = $GLOBALS['routeParams']['shopSlug'] ?? '';
        $shop     = $this->getShopBySlug($shopSlug);
        if (!$shop) {
            $this->error404();
            return;
        }

        // Build filters from GET
        $filters = [
            'shop_id' => $shop['id'],
            'status'  => 'active',
        ];
        if (!empty($_GET['cat']))    $filters['category_id']    = (int)$_GET['cat'];
        if (!empty($_GET['min']))    $filters['min_price']       = (int)$_GET['min'];
        if (!empty($_GET['max']))    $filters['max_price']       = (int)$_GET['max'];
        if (!empty($_GET['av']))     $filters['attribute_values'] = (array)$_GET['av'];
        if (!empty($_GET['sort']))   $filters['sort']             = $_GET['sort'];
        if (!empty($_GET['search'])) $filters['search']           = $_GET['search'];

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $result  = Product::getAll($this->db, $filters, $page, PER_PAGE);
        $cats    = Category::getByShop($this->db, $shop['id']);
        $attrFilters = ProductAttribute::getFiltersForShop($this->db, $shop['id']);

        // Active category
        $activeCategory = null;
        if (!empty($filters['category_id'])) {
            foreach ($cats as $c) {
                if ($c['id'] === $filters['category_id']) {
                    $activeCategory = $c;
                    break;
                }
            }
        }

        $this->render('shop.index', [
            'title'          => $shop['name'],
            'shop'           => $shop,
            'products'       => $result['items'],
            'totalProducts'  => $result['total'],
            'pages'          => $result['pages'],
            'page'           => $page,
            'categories'     => $cats,
            'attrFilters'    => $attrFilters,
            'filters'        => $filters,
            'activeCategory' => $activeCategory,
        ]);
    }

    public function product(): void
    {
        $shopSlug    = $GLOBALS['routeParams']['shopSlug']    ?? '';
        $productSlug = $GLOBALS['routeParams']['productSlug'] ?? '';

        $shop = $this->getShopBySlug($shopSlug);
        if (!$shop) { $this->error404(); return; }

        $product = Product::getBySlug($this->db, $productSlug);
        if (!$product || $product['shop_id'] != $shop['id']) {
            $this->error404();
            return;
        }

        Product::incrementViews($this->db, $product['id']);

        $images   = Product::getImages($this->db, $product['id']);
        $attrs    = Product::getAttributes($this->db, $product['id']);
        $related  = Product::getRelated($this->db, $product['id'], $shop['id'], 4);
        $reviews  = Review::getByProduct($this->db, $product['id']);
        $avgRating = Review::getAvg($this->db, $product['id']);
        $reviewCount = Review::countApproved($this->db, $product['id']);

        $this->render('shop.product', [
            'title'       => $product['name'],
            'shop'        => $shop,
            'product'     => $product,
            'images'      => $images,
            'attrs'       => $attrs,
            'related'     => $related,
            'reviews'     => $reviews,
            'avgRating'   => $avgRating,
            'reviewCount' => $reviewCount,
        ]);
    }

    public function search(): void
    {
        $query   = trim($_GET['q'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $result  = Product::getAll($this->db, ['search' => $query, 'status' => 'active'], $page, PER_PAGE);

        $this->render('shop.search', [
            'title'       => 'جستجو: ' . $query,
            'query'       => $query,
            'products'    => $result['items'],
            'total'       => $result['total'],
            'pages'       => $result['pages'],
            'page'        => $page,
        ]);
    }

    /**
     * Direct product page by slug (no shop prefix required)
     */
    public function productBySlug(string $slug = ''): void
    {
        if (!$slug) $slug = $GLOBALS['routeParams']['slug'] ?? '';
        $product = Product::getBySlug($this->db, $slug);
        if (!$product) { $this->error404(); return; }

        $shop = $this->getShopBySlug($product['shop_slug'] ?? '');

        Product::incrementViews($this->db, $product['id']);
        $images      = Product::getImages($this->db, $product['id']);
        $attrs       = Product::getAttributes($this->db, $product['id']);
        $related     = Product::getRelated($this->db, $product['id'], (int)$product['shop_id'], 4);
        $reviews     = Review::getByProduct($this->db, $product['id']);
        $avgRating   = Review::getAvg($this->db, $product['id']);
        $reviewCount = Review::countApproved($this->db, $product['id']);

        $this->render('shop.product', [
            'title'       => $product['name'],
            'shop'        => $shop ?? ['name' => $product['shop_name'] ?? '', 'slug' => $product['shop_slug'] ?? ''],
            'product'     => $product,
            'images'      => $images,
            'attrs'       => $attrs,
            'related'     => $related,
            'reviews'     => $reviews,
            'avgRating'   => $avgRating,
            'reviewCount' => $reviewCount,
        ]);
    }

    public function submitReview(): void
    {
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/');
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $product   = Product::getById($this->db, $productId);
        if (!$product) { $this->error404(); return; }

        $rating = (int)($_POST['rating'] ?? 0);
        if ($rating < 1 || $rating > 5) {
            Flash::set('error', 'امتیاز نامعتبر.');
            $this->redirect('/shop/' . $product['shop_slug'] . '/' . $product['slug']);
        }

        $name = trim($_POST['name'] ?? '');
        $body = trim($_POST['body'] ?? '');
        if (!$name || !$body) {
            Flash::set('error', 'نام و متن نظر الزامی است.');
            $this->redirect('/shop/' . $product['shop_slug'] . '/' . $product['slug']);
        }

        Review::create($this->db, [
            'product_id' => $productId,
            'user_id'    => Auth::check() ? Auth::id() : null,
            'name'       => $name,
            'rating'     => $rating,
            'body'       => $body,
        ]);

        Flash::set('success', 'نظر شما با موفقیت ثبت شد و پس از تأیید نمایش داده می‌شود.');
        $this->redirect('/shop/' . $product['shop_slug'] . '/' . $product['slug']);
    }
}
