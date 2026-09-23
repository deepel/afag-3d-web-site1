<?php
declare(strict_types=1);
/**
 * afag3d — Admin Product Controller
 */
class AdminProductController extends BaseController
{
    public function index(): void
    {
        $this->requireAdmin();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'search'  => $_GET['search']  ?? '',
            'shop_id' => (int)($_GET['shop_id'] ?? 0) ?: null,
            'status'  => $_GET['status']  ?? '',
        ];
        $result  = Product::adminGetAll($this->db, array_filter($filters), $page, 20);
        $shops   = Shop::getAll($this->db);

        $this->render('admin.products.index', [
            'title'    => 'مدیریت محصولات',
            'products' => $result['items'],
            'total'    => $result['total'],
            'pages'    => $result['pages'],
            'page'     => $page,
            'filters'  => $filters,
            'shops'    => $shops,
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireAdmin();
        $shops      = Shop::getAll($this->db);
        $categories = Category::getAll($this->db);
        $attributes = ProductAttribute::getAll($this->db);

        $this->render('admin.products.form', [
            'title'      => 'افزودن محصول',
            'product'    => null,
            'shops'      => $shops,
            'categories' => $categories,
            'attributes' => $attributes,
            'prodCats'   => [],
            'prodAttrs'  => [],
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/products/create');
        }

        $data = $this->buildProductData();
        $data['image'] = $this->handleMainImage(0);

        $id = Product::create($this->db, $data);

        // Categories
        $this->syncCategories($id, (array)($_POST['categories'] ?? []));
        // Attributes
        $this->syncAttributes($id, (array)($_POST['attributes'] ?? []));

        Flash::set('success', 'محصول با موفقیت افزوده شد.');
        $this->redirect('/admin/products/' . $id . '/edit');
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();
        $product = Product::getById($this->db, $id);
        if (!$product) { $this->error404(); return; }

        $shops      = Shop::getAll($this->db);
        $categories = Category::getAll($this->db);
        $attributes = ProductAttribute::getAll($this->db);
        $prodCats   = array_column(Product::getCategories($this->db, $id), 'id');
        $images     = Product::getImages($this->db, $id);
        // Get assigned attribute values
        $prodAttrs  = [];
        $attrRows   = Product::getAttributes($this->db, $id);
        foreach ($attrRows as $ar) {
            $prodAttrs[] = $ar['value'];
        }

        $this->render('admin.products.form', [
            'title'      => 'ویرایش محصول',
            'product'    => $product,
            'shops'      => $shops,
            'categories' => $categories,
            'attributes' => $attributes,
            'prodCats'   => $prodCats,
            'prodAttrs'  => $prodAttrs,
            'images'     => $images,
        ], 'admin');
    }

    public function update(int $id): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/products/' . $id . '/edit');
        }

        $product = Product::getById($this->db, $id);
        if (!$product) { $this->error404(); return; }

        $data = $this->buildProductData();

        // Only replace image if new one uploaded
        $newImage = $this->handleMainImage($id);
        $data['image'] = $newImage ?: ($product['image'] ?? '');

        Product::update($this->db, $id, $data);
        $this->syncCategories($id, (array)($_POST['categories'] ?? []));
        $this->syncAttributes($id, (array)($_POST['attributes'] ?? []));

        Flash::set('success', 'محصول به‌روز شد.');
        $this->redirect('/admin/products/' . $id . '/edit');
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/products');
        }
        Product::delete($this->db, $id);
        Flash::set('success', 'محصول حذف شد.');
        $this->redirect('/admin/products');
    }

    public function uploadImage(int $id): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            $this->json(['ok' => false, 'msg' => 'خطای امنیتی.']);
        }

        $file = $_FILES['image'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->json(['ok' => false, 'msg' => 'فایلی آپلود نشد.']);
        }

        $path = $this->saveProductImage($id, $file);
        if (!$path) {
            $this->json(['ok' => false, 'msg' => 'فرمت فایل مجاز نیست.']);
        }

        // Insert into product_images
        $isMain = (int)($_POST['is_main'] ?? 0);
        $this->db->prepare(
            "INSERT INTO product_images (product_id, path, is_cover, sort_order) VALUES (?,?,?,?)"
        )->execute([$id, $path, $isMain, 0]);

        $this->json(['ok' => true, 'path' => $path, 'url' => BASE_URL . '/uploads/products/' . $id . '/' . basename($path)]);
    }

    // ── Private helpers ─────────────────────────────────────────

    private function buildProductData(): array
    {
        // Pricing inputs (nullable)
        $grams = ($_POST['weight_grams'] ?? '') !== '' ? (int)$_POST['weight_grams'] : null;
        $hours = ($_POST['print_hours']  ?? '') !== '' ? (float)$_POST['print_hours'] : null;

        // Price resolution: a typed price is manual and wins; otherwise compute
        // from weight+hours; otherwise leave NULL ("not priced yet").
        $typed = (int)preg_replace('/\D/', '', $_POST['price'] ?? '');
        if ($typed > 0) {
            $price = $typed; $manual = 1;
        } elseif ($grams !== null && $hours !== null) {
            $price = PricingEngine::compute($grams, $hours, PricingEngine::settings($this->db)); $manual = 0;
        } else {
            $price = null; $manual = 0;
        }

        $lifecycle = in_array($_POST['lifecycle_status'] ?? '', ['pending','coming_soon','available'], true)
            ? $_POST['lifecycle_status'] : 'available';

        return [
            'shop_id'         => (int)($_POST['shop_id'] ?? 0),
            'name'            => trim($_POST['name'] ?? ''),
            'slug'            => trim($_POST['slug'] ?? '') ?: Url::slug(trim($_POST['name'] ?? '')),
            'short_desc'      => trim($_POST['short_desc'] ?? ''),
            'description'     => $_POST['description'] ?? '',
            'price'           => $price,
            'price_is_manual' => $manual,
            'compare_price'   => (int)preg_replace('/\D/', '', $_POST['compare_price'] ?? '0') ?: null,
            'sku'             => trim($_POST['sku'] ?? ''),
            'stock'           => (int)($_POST['stock'] ?? 0),
            'weight'          => (float)($_POST['weight'] ?? 0),
            'weight_grams'    => $grams,
            'print_hours'     => $hours,
            'archive_folder'  => trim($_POST['archive_folder'] ?? '') ?: null,
            'lifecycle_status'=> $lifecycle,
            'status'          => in_array($_POST['status'] ?? '', ['active', 'out_of_stock', 'draft'], true) ? $_POST['status'] : 'active',
            'is_featured'     => isset($_POST['is_featured']) ? 1 : 0,
            'meta_title'      => trim($_POST['meta_title'] ?? ''),
            'meta_desc'       => trim($_POST['meta_desc'] ?? ''),
        ];
    }

    private function handleMainImage(int $productId): string
    {
        $file = $_FILES['main_image'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return '';
        return $this->saveProductImage($productId ?: 0, $file) ?? '';
    }

    private function saveProductImage(int $productId, array $file): ?string
    {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($file['tmp_name']);

        if (!in_array($ext, $allowed) || !in_array($mime, $allowedMime)) return null;

        $dir = UPLOAD_PATH . '/products/' . max(1, $productId);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

        return 'products/' . max(1, $productId) . '/' . $filename;
    }

    private function syncCategories(int $productId, array $catIds): void
    {
        $this->db->prepare("DELETE FROM product_categories WHERE product_id = ?")->execute([$productId]);
        $st = $this->db->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?,?)");
        foreach ($catIds as $cid) {
            $cid = (int)$cid;
            if ($cid > 0) $st->execute([$productId, $cid]);
        }
    }

    private function syncAttributes(int $productId, array $attrValues): void
    {
        // attrValues is flat array of attribute_value IDs
        $this->db->prepare("DELETE FROM product_attribute_values WHERE product_id = ?")->execute([$productId]);
        // Get attribute for each value
        $st = $this->db->prepare(
            "SELECT av.id, av.attribute_id, av.value, av.label FROM attribute_values av WHERE av.id = ?"
        );
        $ins = $this->db->prepare(
            "INSERT IGNORE INTO product_attribute_values (product_id, attribute_id, value, value_label) VALUES (?,?,?,?)"
        );
        foreach ($attrValues as $valId) {
            $valId = (int)$valId;
            if (!$valId) continue;
            $st->execute([$valId]);
            $av = $st->fetch();
            if ($av) {
                $ins->execute([$productId, $av['attribute_id'], $av['value'], $av['label'] ?? $av['value']]);
            }
        }
    }
}
