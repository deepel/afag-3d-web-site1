<?php
declare(strict_types=1);

class Category
{
    static function getByShop(PDO $db, int $shopId): array
    {
        // Categories are global; a category "belongs" to a shop when it has
        // at least one active product in that shop (via product_categories).
        $stmt = $db->prepare(
            "SELECT c.*, COUNT(DISTINCT pc.product_id) AS product_count
             FROM categories c
             INNER JOIN product_categories pc ON pc.category_id = c.id
             INNER JOIN products p ON p.id = pc.product_id
                   AND p.status = 'active' AND p.shop_id = :shop_id
             WHERE c.status = 1
             GROUP BY c.id
             ORDER BY c.sort_order, c.name"
        );
        $stmt->execute(['shop_id' => $shopId]);
        return $stmt->fetchAll();
    }

    static function getBySlug(PDO $db, string $slug): ?array
    {
        $stmt = $db->prepare("SELECT * FROM categories WHERE slug = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function getAll(PDO $db): array
    {
        $stmt = $db->query("SELECT * FROM categories ORDER BY sort_order, name");
        return $stmt->fetchAll();
    }

    static function getTree(PDO $db): array
    {
        $all  = self::getAll($db);
        $tree = [];
        $map  = [];
        foreach ($all as $cat) {
            $map[$cat['id']] = $cat;
            $map[$cat['id']]['children'] = [];
        }
        foreach ($map as $id => &$cat) {
            $pid = $cat['parent_id'] ?? null;
            if ($pid && isset($map[$pid])) {
                $map[$pid]['children'][] = &$cat;
            } else {
                $tree[] = &$cat;
            }
        }
        return $tree;
    }
}
