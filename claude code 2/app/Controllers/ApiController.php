<?php
declare(strict_types=1);
/**
 * afag3d — Automation API Controller
 *
 * JSON endpoints for the n8n / Telegram bridge. Protected by the X-API-Key
 * header (compared to API_KEY in config/). These endpoints DO NOT use the
 * session or CSRF — the API key is the auth. All business logic (pricing,
 * code generation, status) stays here in PHP; n8n is just a messenger.
 *
 * Routes (registered in public/index.php):
 *   POST /api/product/create
 *   POST /api/product/update-price
 *   GET  /api/product/pending
 *   POST /api/product/publish
 */
class ApiController extends BaseController
{
    // ── Auth + JSON helpers ────────────────────────────────────────────────

    /** Verify X-API-Key; emit 401 JSON and stop if missing/wrong. */
    private function guard(): void
    {
        $sent = $_SERVER['HTTP_X_API_KEY'] ?? '';
        if (!defined('API_KEY') || API_KEY === '' || !hash_equals(API_KEY, (string) $sent)) {
            $this->apiError('کلید API نامعتبر است.', 401);
        }
    }

    /** Read JSON body (falls back to form POST). */
    private function input(): array
    {
        $raw = file_get_contents('php://input') ?: '';
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $json;
        }
        return $_POST;
    }

    private function apiOk(array $data = [], int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true] + $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function apiError(string $message, int $status = 400, array $extra = []): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => $message] + $extra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /** Generate the next unique AFAG-XXXX code (4-digit, zero-padded). */
    private function nextProductCode(): string
    {
        // Highest numeric suffix among existing AFAG-#### codes.
        $row = $this->db->query(
            "SELECT MAX(CAST(SUBSTRING(product_code, 6) AS UNSIGNED)) AS mx
               FROM products
              WHERE product_code REGEXP '^AFAG-[0-9]+$'"
        )->fetch();
        $next = ((int) ($row['mx'] ?? 0)) + 1;
        return 'AFAG-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /** Find a product by its code. */
    private function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE product_code = :c LIMIT 1');
        $stmt->execute(['c' => $code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Save base64 images sent by n8n (from Telegram) into a per-product folder.
     * Each item: { "filename": "x.jpg", "data": "<base64>" } OR a plain base64 string.
     * Returns array of stored relative paths. Mirrors the site's upload sandbox.
     */
    private function saveImages(array $images, int $productId): array
    {
        $saved = [];
        if (!$images) {
            return $saved;
        }
        $dir = PUBLIC_PATH . '/uploads/products/' . $productId . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        foreach ($images as $img) {
            $b64 = is_array($img) ? ($img['data'] ?? '') : (string) $img;
            $name = is_array($img) ? ($img['filename'] ?? '') : '';
            if ($b64 === '') {
                continue;
            }
            // Strip data-URI prefix if present
            if (preg_match('#^data:image/[\w.+-]+;base64,#i', $b64)) {
                $b64 = preg_replace('#^data:image/[\w.+-]+;base64,#i', '', $b64);
            }
            $bin = base64_decode($b64, true);
            if ($bin === false || strlen($bin) === 0 || strlen($bin) > MAX_FILE_SIZE) {
                continue;
            }
            // Validate it is a real image and pick extension from actual content
            $info = @getimagesizefromstring($bin);
            if ($info === false) {
                continue;
            }
            $extMap = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
            $ext = $extMap[$info[2]] ?? null;
            if ($ext === null) {
                continue;
            }
            $fname = bin2hex(random_bytes(8)) . '.' . $ext;
            if (file_put_contents($dir . $fname, $bin) !== false) {
                $saved[] = 'products/' . $productId . '/' . $fname;
            }
        }
        return $saved;
    }

    // ── Endpoints ───────────────────────────────────────────────────────────

    /** POST /api/product/create */
    public function createProduct(): void
    {
        $this->guard();
        $in = $this->input();

        $name = trim((string) ($in['name'] ?? ''));
        if ($name === '') {
            $this->apiError('نام محصول الزامی است.', 422);
        }

        $shopId = (int) ($in['shop_id'] ?? 1);
        $shopOk = $this->db->prepare('SELECT 1 FROM shops WHERE id = :s');
        $shopOk->execute(['s' => $shopId]);
        if (!$shopOk->fetchColumn()) {
            $this->apiError('shop_id نامعتبر است.', 422);
        }

        $weight = isset($in['weight_grams']) && $in['weight_grams'] !== '' ? (int) $in['weight_grams'] : null;
        $hours  = isset($in['print_hours'])  && $in['print_hours']  !== '' ? (float) $in['print_hours'] : null;

        // Price: if weight+hours given, auto-compute; otherwise stays NULL (coming soon).
        $price = null;
        if ($weight !== null && $hours !== null) {
            $price = PricingEngine::compute($weight, $hours, PricingEngine::settings($this->db));
        }

        $code = $this->nextProductCode();
        $slug = Url::slug($name);
        if ($slug === '') {
            $slug = 'product-' . time();
        }
        // Guarantee slug uniqueness
        $check = $this->db->prepare('SELECT 1 FROM products WHERE slug = :s');
        $base  = $slug; $i = 1;
        while (true) {
            $check->execute(['s' => $slug]);
            if (!$check->fetchColumn()) break;
            $slug = $base . '-' . (++$i);
        }

        // lifecycle: coming_soon (no price yet) keeps it visible with a badge but unbuyable.
        $lifecycle = $price === null ? 'coming_soon' : 'coming_soon';

        $stmt = $this->db->prepare(
            "INSERT INTO products
                (shop_id, name, slug, sku, product_code, description, price,
                 weight_grams, print_hours, stock, status, lifecycle_status,
                 auto_created, price_is_manual, created_at)
             VALUES
                (:shop_id, :name, :slug, :sku, :code, :desc, :price,
                 :weight, :hours, 0, 'active', :lifecycle,
                 1, 0, NOW())"
        );
        $stmt->execute([
            'shop_id'   => $shopId,
            'name'      => $name,
            'slug'      => $slug,
            'sku'       => $code, // reuse code as SKU for convenience
            'code'      => $code,
            'desc'      => trim((string) ($in['description'] ?? $in['caption'] ?? '')),
            'price'     => $price,
            'weight'    => $weight,
            'hours'     => $hours,
            'lifecycle' => $lifecycle,
        ]);
        $id = (int) $this->db->lastInsertId();

        // Images (base64 array under "images")
        $images = $in['images'] ?? [];
        if (is_array($images) && $images) {
            $paths = $this->saveImages($images, $id);
            $ins = $this->db->prepare(
                "INSERT INTO product_images (product_id, path, is_cover, sort_order)
                 VALUES (:pid, :path, :cover, :sort)"
            );
            foreach ($paths as $idx => $path) {
                $ins->execute(['pid' => $id, 'path' => $path, 'cover' => $idx === 0 ? 1 : 0, 'sort' => $idx]);
            }
        }

        $this->apiOk([
            'id'           => $id,
            'product_code' => $code,
            'price'        => $price,
            'status'       => $lifecycle,
        ], 201);
    }

    /** POST /api/product/update-price */
    public function updatePrice(): void
    {
        $this->guard();
        $in   = $this->input();
        $code = trim((string) ($in['product_code'] ?? ''));
        if ($code === '') {
            $this->apiError('product_code الزامی است.', 422);
        }
        $product = $this->findByCode($code);
        if (!$product) {
            $this->apiError('محصولی با این کد یافت نشد.', 404);
        }

        // Explicit price wins; else compute from weight/hours.
        if (isset($in['price']) && $in['price'] !== '') {
            $price  = max(0, (int) $in['price']);
            $manual = 1;
        } else {
            $price = PricingEngine::computeForProduct($this->db, $product);
            if ($price === null) {
                $this->apiError('قیمت ارسال نشده و وزن/ساعت برای محاسبه موجود نیست.', 422);
            }
            $manual = 0;
        }

        $this->db->prepare(
            'UPDATE products SET price = :p, price_is_manual = :m WHERE id = :id'
        )->execute(['p' => $price, 'm' => $manual, 'id' => (int) $product['id']]);

        $this->apiOk(['product_code' => $code, 'price' => $price, 'price_is_manual' => (bool) $manual]);
    }

    /** GET /api/product/pending */
    public function pending(): void
    {
        $this->guard();
        $rows = $this->db->query(
            "SELECT id, product_code, name, price, weight_grams, print_hours,
                    lifecycle_status, shop_id, created_at
               FROM products
              WHERE lifecycle_status IN ('pending','coming_soon')
              ORDER BY created_at DESC"
        )->fetchAll();

        $this->apiOk(['count' => count($rows), 'products' => $rows]);
    }

    /** POST /api/product/publish */
    public function publish(): void
    {
        $this->guard();
        $in   = $this->input();
        $code = trim((string) ($in['product_code'] ?? ''));
        if ($code === '') {
            $this->apiError('product_code الزامی است.', 422);
        }
        $product = $this->findByCode($code);
        if (!$product) {
            $this->apiError('محصولی با این کد یافت نشد.', 404);
        }
        if ($product['price'] === null) {
            $this->apiError('محصول قبل از انتشار باید قیمت داشته باشد.', 409);
        }

        $this->db->prepare(
            "UPDATE products SET lifecycle_status = 'available', status = 'active' WHERE id = :id"
        )->execute(['id' => (int) $product['id']]);

        $this->apiOk(['product_code' => $code, 'status' => 'available']);
    }
}
