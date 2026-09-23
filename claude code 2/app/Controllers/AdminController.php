<?php
declare(strict_types=1);
/**
 * afag3d — Admin Controller
 */
class AdminController extends BaseController
{
    private Setting $settings;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->settings = new Setting($db);
    }

    /** GET /admin  |  GET /admin/dashboard */
    public function dashboard(): void
    {
        $this->requireAdmin();

        $data = [
            'total_users'    => (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'total_products' => (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'total_orders'   => (int) $this->db->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
            'total_revenue'  => (int) $this->db->query('SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status="paid"')->fetchColumn(),
        ];

        // Recent orders
        $stmt = $this->db->query(
            'SELECT o.*, u.name AS user_name FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             ORDER BY o.id DESC LIMIT 10'
        );
        $recentOrders = $stmt->fetchAll();

        // Recent users
        $stmt = $this->db->query(
            'SELECT id, name, mobile, role, status, created_at FROM users ORDER BY id DESC LIMIT 8'
        );
        $recentUsers = $stmt->fetchAll();

        // Order status breakdown
        $stmt = $this->db->query(
            'SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status'
        );
        $orderStats = [];
        foreach ($stmt->fetchAll() as $row) {
            $orderStats[$row['status']] = $row['cnt'];
        }

        $this->render('admin.dashboard', [
            'title'        => 'داشبورد مدیریت',
            'stats'        => $data,
            'recentOrders' => $recentOrders,
            'recentUsers'  => $recentUsers,
            'orderStats'   => $orderStats,
        ], 'admin');
    }

    /** GET /admin/settings */
    public function settingsForm(): void
    {
        $this->requireAdmin();
        $settings = $this->settings->all();
        $this->render('admin.settings', [
            'title'    => 'تنظیمات سایت',
            'settings' => $settings,
            'csrf'     => CSRF::field(),
        ], 'admin');
    }

    /** POST /admin/settings */
    public function saveSettings(): void
    {
        $this->requireAdmin();
        CSRF::check();

        $allowed = [
            'site_name', 'site_tagline', 'site_email', 'site_phone', 'site_address',
            'maintenance_mode', 'shipping_fee', 'free_shipping_from', 'tax_rate',
            'instagram', 'telegram', 'whatsapp', 'seo_meta_desc',
            'google_analytics', 'order_prefix',
            'popup_enabled', 'popup_title', 'popup_content',
            'popup_btn_text', 'popup_btn_url', 'popup_image',
        ];

        $toSave = [];
        foreach ($allowed as $key) {
            $toSave[$key] = trim($_POST[$key] ?? '');
        }

        // Sanitize
        $toSave['maintenance_mode'] = isset($_POST['maintenance_mode']) ? '1' : '0';
        $toSave['popup_enabled']    = isset($_POST['popup_enabled'])    ? '1' : '0';
        $toSave['shipping_fee']     = (string) max(0, (int) $toSave['shipping_fee']);
        $toSave['tax_rate']         = (string) min(100, max(0, (int) $toSave['tax_rate']));

        $this->settings->saveMany($toSave);
        Flash::success('تنظیمات با موفقیت ذخیره شد.');
        $this->redirect('/admin/settings');
    }

    /** GET /admin/pricing */
    public function pricingForm(): void
    {
        $this->requireAdmin();
        $autoCount = (int) $this->db->query(
            'SELECT COUNT(*) FROM products
              WHERE weight_grams IS NOT NULL AND print_hours IS NOT NULL AND price_is_manual = 0'
        )->fetchColumn();

        $this->render('admin.pricing', [
            'title'     => 'تنظیمات قیمت‌گذاری',
            'pricing'   => PricingEngine::settings($this->db),
            'autoCount' => $autoCount,
            'csrf'      => CSRF::field(),
        ], 'admin');
    }

    /** POST /admin/pricing */
    public function savePricing(): void
    {
        $this->requireAdmin();
        CSRF::check();
        PricingEngine::saveSettings($this->db, $_POST);
        Flash::success('ضرایب قیمت‌گذاری ذخیره شد.');
        $this->redirect('/admin/pricing');
    }

    /** POST /admin/pricing/recalc — recompute the whole catalog */
    public function recalcCatalog(): void
    {
        $this->requireAdmin();
        CSRF::check();
        $n = PricingEngine::recalcCatalog($this->db);
        Cache::flush(); // prices changed — drop cached product lists
        Flash::success("قیمت {$n} محصول دوباره محاسبه شد.");
        $this->redirect('/admin/pricing');
    }

    /** GET /admin/users */
    public function users(): void
    {
        $this->requireAdmin();
        $userModel = new User($this->db);
        $page      = max(1, (int) ($_GET['page'] ?? 1));
        $users     = $userModel->all($page, 20);
        $total     = $userModel->count();

        $this->render('admin.users', [
            'title' => 'مدیریت کاربران',
            'users' => $users,
            'total' => $total,
            'page'  => $page,
        ], 'admin');
    }

    /** GET /admin/products */
    public function products(): void
    {
        $this->requireAdmin();
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * PER_PAGE;

        $stmt = $this->db->prepare(
            'SELECT p.*, s.name AS shop_name FROM products p
             LEFT JOIN shops s ON s.id = p.shop_id
             ORDER BY p.id DESC LIMIT ? OFFSET ?'
        );
        $stmt->execute([PER_PAGE, $offset]);
        $products = $stmt->fetchAll();

        $total = (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();

        $this->render('admin.products', [
            'title'    => 'مدیریت محصولات',
            'products' => $products,
            'total'    => $total,
            'page'     => $page,
        ], 'admin');
    }

    /** GET /admin/orders */
    public function orders(): void
    {
        $this->requireAdmin();
        $page   = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * PER_PAGE;

        $stmt = $this->db->prepare(
            'SELECT o.*, u.name AS user_name FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             ORDER BY o.id DESC LIMIT ? OFFSET ?'
        );
        $stmt->execute([PER_PAGE, $offset]);
        $orders = $stmt->fetchAll();

        $total = (int) $this->db->query('SELECT COUNT(*) FROM orders')->fetchColumn();

        $this->render('admin.orders', [
            'title'  => 'مدیریت سفارشات',
            'orders' => $orders,
            'total'  => $total,
            'page'   => $page,
        ], 'admin');
    }

    /** GET /admin/products/{id}/edit  |  POST /admin/products/{id}/edit */
    public function editProduct(): void
    {
        $this->requireAdmin();
        $id      = (int)($GLOBALS['routeParams']['id'] ?? 0);
        $product = $id ? Product::getById($this->db, $id) : null;
        $shops   = $this->db->query('SELECT * FROM shops ORDER BY name')->fetchAll();
        $allCats = Category::getAll($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->saveProduct();
            return;
        }

        $productCats = $id ? array_column(Product::getCategories($this->db, $id), 'id') : [];
        $images      = $id ? Product::getImages($this->db, $id) : [];

        $this->render('admin.product_edit', [
            'title'       => $id ? 'ویرایش محصول' : 'محصول جدید',
            'product'     => $product,
            'shops'       => $shops,
            'allCats'     => $allCats,
            'productCats' => $productCats,
            'images'      => $images,
            'csrf'        => CSRF::field(),
        ], 'admin');
    }

    /** POST /admin/products/{id}/edit */
    public function saveProduct(): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/products');
        }

        $id   = (int)($GLOBALS['routeParams']['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            Flash::set('error', 'نام محصول الزامی است.');
            $this->redirect($id ? "/admin/products/$id/edit" : '/admin/products/0/edit');
        }

        $slug = Url::slug($name);
        if (empty($slug)) $slug = 'product-' . time();

        $data = [
            'shop_id'       => (int)($_POST['shop_id']       ?? 1),
            'name'          => $name,
            'slug'          => $slug,
            'sku'           => trim($_POST['sku']            ?? ''),
            'description'   => trim($_POST['description']   ?? ''),
            'price'         => (int)($_POST['price']         ?? 0),
            'compare_price' => (int)($_POST['compare_price'] ?? 0) ?: null,
            'stock'         => (int)($_POST['stock']         ?? 0),
            'status'        => $_POST['status'] ?? 'active',
            'is_featured'   => isset($_POST['is_featured']) ? 1 : 0,
            'weight'        => (int)($_POST['weight']        ?? 0),
        ];

        if ($id) {
            Product::update($this->db, $id, $data);
            $newId = $id;
        } else {
            $newId = Product::create($this->db, $data);
        }

        // Handle image upload
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = PUBLIC_PATH . '/uploads/products/' . $newId . '/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if (!$tmp) continue;
                $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, ALLOWED_IMAGES, true)) continue;
                $fname = uniqid('img_') . '.' . $ext;
                if (move_uploaded_file($tmp, $uploadDir . $fname)) {
                    $isMain = $i === 0 ? 1 : 0;
                    $this->db->prepare(
                        "INSERT INTO product_images (product_id, path, is_cover, sort_order)
                         VALUES (:pid,:path,:main,:sort)"
                    )->execute(['pid' => $newId, 'path' => 'products/' . $newId . '/' . $fname, 'main' => $isMain, 'sort' => $i]);
                }
            }
        }

        // Categories
        $this->db->prepare("DELETE FROM product_categories WHERE product_id = :id")->execute(['id' => $newId]);
        if (!empty($_POST['categories'])) {
            $catStmt = $this->db->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (:pid,:cid)");
            foreach ((array)$_POST['categories'] as $catId) {
                $catStmt->execute(['pid' => $newId, 'cid' => (int)$catId]);
            }
        }

        Flash::set('success', 'محصول با موفقیت ذخیره شد.');
        $this->redirect('/admin/products');
    }

    /** POST /admin/products/{id}/delete */
    public function deleteProduct(): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            $this->json(['ok' => false, 'msg' => 'خطای امنیتی.']);
        }
        $id = (int)($GLOBALS['routeParams']['id'] ?? 0);
        if ($id) Product::delete($this->db, $id);
        Flash::set('success', 'محصول حذف شد.');
        $this->redirect('/admin/products');
    }

    /** GET /admin/orders/{id} */
    public function orderDetail(): void
    {
        $this->requireAdmin();
        $id    = (int)($GLOBALS['routeParams']['id'] ?? 0);
        $order = Order::getById($this->db, $id);
        if (!$order) { $this->error404(); return; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && CSRF::verify($_POST['csrf_token'] ?? '')) {
            $status = $_POST['status'] ?? '';
            $allowed = ['pending','processing','shipped','delivered','cancelled','paid','failed'];
            if (in_array($status, $allowed, true)) {
                Order::updateStatus($this->db, $id, $status);
                Flash::set('success', 'وضعیت سفارش بروز شد.');
            }
            $this->redirect("/admin/orders/$id");
        }

        $items = Order::getItems($this->db, $id);

        $this->render('admin.order_detail', [
            'title' => 'جزئیات سفارش #' . $id,
            'order' => $order,
            'items' => $items,
            'csrf'  => CSRF::field(),
        ], 'admin');
    }
}
