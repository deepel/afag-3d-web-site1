<?php
declare(strict_types=1);

class Product
{
    static function getAll(PDO $db, array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where  = ['p.status = "active"'];
        $params = [];

        if (!empty($filters['shop_id'])) {
            $where[]         = 'p.shop_id = :shop_id';
            $params['shop_id'] = (int)$filters['shop_id'];
        }
        if (!empty($filters['category_id'])) {
            $where[]           = 'EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = :cat_id)';
            $params['cat_id']  = (int)$filters['category_id'];
        }
        if (!empty($filters['min_price'])) {
            $where[]             = 'p.price >= :min_price';
            $params['min_price'] = (int)$filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[]             = 'p.price <= :max_price';
            $params['max_price'] = (int)$filters['max_price'];
        }
        if (!empty($filters['search'])) {
            $where[]           = '(p.name LIKE :search OR p.description LIKE :search2)';
            $params['search']  = '%' . $filters['search'] . '%';
            $params['search2'] = '%' . $filters['search'] . '%';
        }

        // Attribute values filter (faceted)
        $avJoin = '';
        if (!empty($filters['attribute_values']) && is_array($filters['attribute_values'])) {
            $avIds = array_map('intval', $filters['attribute_values']);
            $avIn  = implode(',', $avIds);
            // Each selected attribute value must match
            $avJoin = "INNER JOIN product_attributes pa_filter ON pa_filter.product_id = p.id AND pa_filter.attribute_value_id IN ($avIn)";
        }

        $sort = match ($filters['sort'] ?? 'newest') {
            'cheapest'  => 'p.price ASC',
            'expensive' => 'p.price DESC',
            'popular'   => 'p.views DESC',
            default     => 'p.created_at DESC',
        };

        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset   = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(DISTINCT p.id) FROM products p $avJoin $whereStr";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT DISTINCT p.*, s.name AS shop_name, s.slug AS shop_slug,
                    (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_cover = 1 LIMIT 1) AS main_image
                FROM products p
                LEFT JOIN shops s ON s.id = p.shop_id
                $avJoin
                $whereStr
                ORDER BY $sort
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(":$k", $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return [
            'items' => $items,
            'total' => $total,
            'pages' => (int)ceil($total / $perPage),
        ];
    }

    static function getBySlug(PDO $db, string $slug): ?array
    {
        $stmt = $db->prepare(
            "SELECT p.*, s.name AS shop_name, s.slug AS shop_slug
             FROM products p
             LEFT JOIN shops s ON s.id = p.shop_id
             WHERE p.slug = :slug LIMIT 1"
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function getById(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare(
            "SELECT p.*, s.name AS shop_name, s.slug AS shop_slug
             FROM products p
             LEFT JOIN shops s ON s.id = p.shop_id
             WHERE p.id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function getImages(PDO $db, int $productId): array
    {
        $stmt = $db->prepare(
            "SELECT * FROM product_images WHERE product_id = :id ORDER BY is_cover DESC, sort_order ASC"
        );
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll();
    }

    static function getAttributes(PDO $db, int $productId): array
    {
        $stmt = $db->prepare(
            "SELECT a.name AS attr_name, a.type AS attr_type, av.value, av.hex AS color_hex, av.id AS value_id
             FROM product_attributes pa
             JOIN attribute_values av ON av.id = pa.attribute_value_id
             JOIN attributes a ON a.id = av.attribute_id
             WHERE pa.product_id = :id
             ORDER BY a.name, av.value"
        );
        $stmt->execute(['id' => $productId]);
        $rows  = $stmt->fetchAll();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['attr_name']][] = $row;
        }
        return $grouped;
    }

    static function getRelated(PDO $db, int $productId, int $shopId, int $limit = 4): array
    {
        $stmt = $db->prepare(
            "SELECT p.*,
                (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_cover = 1 LIMIT 1) AS main_image
             FROM products p
             WHERE p.shop_id = :shop_id AND p.id != :id AND p.status = 'active'
             ORDER BY RAND()
             LIMIT :lim"
        );
        $stmt->bindValue(':shop_id', $shopId, PDO::PARAM_INT);
        $stmt->bindValue(':id',      $productId, PDO::PARAM_INT);
        $stmt->bindValue(':lim',     $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static function getCategories(PDO $db, int $productId): array
    {
        $stmt = $db->prepare(
            "SELECT c.* FROM categories c
             JOIN product_categories pc ON pc.category_id = c.id
             WHERE pc.product_id = :id"
        );
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll();
    }

    static function incrementViews(PDO $db, int $productId): void
    {
        $db->prepare("UPDATE products SET views = views + 1 WHERE id = :id")
           ->execute(['id' => $productId]);
    }

    static function getFeatured(PDO $db, int $shopId = 0, int $limit = 8): array
    {
        $where  = "p.status = 'active' AND p.is_featured = 1";
        $params = [];
        if ($shopId) {
            $where           .= ' AND p.shop_id = :shop_id';
            $params['shop_id'] = $shopId;
        }
        $stmt = $db->prepare(
            "SELECT p.*,
                (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_cover = 1 LIMIT 1) AS main_image
             FROM products p
             WHERE $where
             ORDER BY p.created_at DESC
             LIMIT :lim"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue(":$k", $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /* ── Admin methods ── */

    /**
     * Admin listing — no status filter restriction, supports search/shop/status filters.
     */
    static function adminGetAll(PDO $db, array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where  = ['p.status != "deleted"'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[]          = '(p.name LIKE :search OR p.sku LIKE :search2)';
            $params['search']  = '%' . $filters['search'] . '%';
            $params['search2'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['shop_id'])) {
            $where[]          = 'p.shop_id = :shop_id';
            $params['shop_id'] = (int)$filters['shop_id'];
        }
        if (!empty($filters['status'])) {
            $where[]          = 'p.status = :status';
            $params['status'] = $filters['status'];
        }

        $whereStr  = 'WHERE ' . implode(' AND ', $where);
        $offset    = ($page - 1) * $perPage;

        $cntSt = $db->prepare("SELECT COUNT(*) FROM products p $whereStr");
        $cntSt->execute($params);
        $total = (int)$cntSt->fetchColumn();

        $st = $db->prepare("SELECT p.*, s.name AS shop_name,
            (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_cover = 1 LIMIT 1) AS main_image
            FROM products p LEFT JOIN shops s ON s.id = p.shop_id
            $whereStr ORDER BY p.created_at DESC LIMIT :lim OFFSET :off");
        foreach ($params as $k => $v) {
            $st->bindValue(":$k", $v, is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $st->bindValue(':off', $offset,  PDO::PARAM_INT);
        $st->execute();

        return [
            'items' => $st->fetchAll(),
            'total' => $total,
            'pages' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    /** Next unique AFAG-XXXX product code (4-digit, zero-padded). */
    static function nextCode(PDO $db): string
    {
        $row = $db->query(
            "SELECT MAX(CAST(SUBSTRING(product_code, 6) AS UNSIGNED)) AS mx
               FROM products WHERE product_code REGEXP '^AFAG-[0-9]+$'"
        )->fetch();
        return 'AFAG-' . str_pad((string)(((int)($row['mx'] ?? 0)) + 1), 4, '0', STR_PAD_LEFT);
    }

    static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare(
            "INSERT INTO products
             (shop_id, name, slug, sku, product_code, short_desc, description, price, compare_price,
              stock, status, lifecycle_status, archive_folder, weight, weight_grams, print_hours,
              price_is_manual, is_featured, image, meta_title, meta_desc, created_at)
             VALUES
             (:shop_id,:name,:slug,:sku,:product_code,:short_desc,:description,:price,:compare_price,
              :stock,:status,:lifecycle_status,:archive_folder,:weight,:weight_grams,:print_hours,
              :price_is_manual,:is_featured,:image,:meta_title,:meta_desc,NOW())"
        );
        $stmt->execute([
            'shop_id'         => $data['shop_id']         ?? null,
            'name'            => $data['name'],
            'slug'            => $data['slug'],
            'sku'             => $data['sku']             ?? '',
            'product_code'    => $data['product_code']    ?? self::nextCode($db),
            'short_desc'      => $data['short_desc']      ?? '',
            'description'     => $data['description']     ?? '',
            'price'           => $data['price'],
            'compare_price'   => $data['compare_price']   ?? null,
            'stock'           => $data['stock']           ?? 0,
            'status'          => $data['status']          ?? 'active',
            'lifecycle_status'=> $data['lifecycle_status']?? 'available',
            'archive_folder'  => $data['archive_folder']  ?? null,
            'weight'          => $data['weight']          ?? 0,
            'weight_grams'    => $data['weight_grams']    ?? null,
            'print_hours'     => $data['print_hours']     ?? null,
            'price_is_manual' => $data['price_is_manual'] ?? 0,
            'is_featured'     => $data['is_featured']     ?? 0,
            'image'           => $data['image']           ?? '',
            'meta_title'      => $data['meta_title']      ?? '',
            'meta_desc'       => $data['meta_desc']       ?? '',
        ]);
        return (int)$db->lastInsertId();
    }

    static function update(PDO $db, int $id, array $data): void
    {
        $set  = [];
        $vals = ['id' => $id];
        $allowed = ['shop_id','name','slug','sku','short_desc','description','price',
                    'compare_price','stock','status','is_featured','weight','image','meta_title','meta_desc',
                    'weight_grams','print_hours','lifecycle_status','archive_folder','price_is_manual'];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $set[]      = "$col = :$col";
                $vals[$col] = $data[$col];
            }
        }
        if (!$set) return;
        $db->prepare("UPDATE products SET " . implode(',', $set) . " WHERE id = :id")->execute($vals);
    }

    static function delete(PDO $db, int $id): void
    {
        $db->prepare("UPDATE products SET status = 'deleted' WHERE id = :id")->execute(['id' => $id]);
    }

    static function count(PDO $db, int $shopId = 0): int
    {
        if ($shopId) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE shop_id = :s AND status != 'deleted'");
            $stmt->execute(['s' => $shopId]);
        } else {
            $stmt = $db->query("SELECT COUNT(*) FROM products WHERE status != 'deleted'");
        }
        return (int)$stmt->fetchColumn();
    }

    static function updateStock(PDO $db, int $id, int $delta): void
    {
        $db->prepare("UPDATE products SET stock = GREATEST(0, stock + :delta) WHERE id = :id")
           ->execute(['delta' => $delta, 'id' => $id]);
    }
}
