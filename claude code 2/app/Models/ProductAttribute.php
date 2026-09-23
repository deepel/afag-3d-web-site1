<?php
declare(strict_types=1);

/**
 * Product attribute model.
 * NOTE: class is named ProductAttribute (not "Attribute") because PHP 8
 * reserves the global `Attribute` class for the #[Attribute] annotation,
 * which would shadow an autoloaded class of the same name.
 */
class ProductAttribute
{
    static function getAll(PDO $db): array
    {
        $attrs = $db->query("SELECT * FROM attributes ORDER BY name")->fetchAll();
        $vals  = $db->query("SELECT * FROM attribute_values ORDER BY attribute_id, value")->fetchAll();
        $grouped = [];
        foreach ($attrs as $a) {
            $a['values'] = [];
            $grouped[$a['id']] = $a;
        }
        foreach ($vals as $v) {
            if (isset($grouped[$v['attribute_id']])) {
                $grouped[$v['attribute_id']]['values'][] = $v;
            }
        }
        return array_values($grouped);
    }

    static function getFiltersForShop(PDO $db, int $shopId): array
    {
        // Only return attributes whose values are actually assigned to products in this shop
        $stmt = $db->prepare(
            "SELECT DISTINCT a.id, a.name, a.type, av.id AS value_id, av.value, av.hex AS color_hex,
                    COUNT(DISTINCT pa.product_id) AS product_count
             FROM attributes a
             JOIN attribute_values av ON av.attribute_id = a.id
             JOIN product_attributes pa ON pa.attribute_value_id = av.id
             JOIN products p ON p.id = pa.product_id AND p.shop_id = :shop_id AND p.status = 'active'
             GROUP BY a.id, av.id
             ORDER BY a.name, av.value"
        );
        $stmt->execute(['shop_id' => $shopId]);
        $rows = $stmt->fetchAll();

        $grouped = [];
        foreach ($rows as $row) {
            $aid = $row['id'];
            if (!isset($grouped[$aid])) {
                $grouped[$aid] = [
                    'id'   => $row['id'],
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'values' => [],
                ];
            }
            $grouped[$aid]['values'][] = [
                'id'            => $row['value_id'],
                'value'         => $row['value'],
                'color_hex'     => $row['color_hex'],
                'product_count' => $row['product_count'],
            ];
        }
        return array_values($grouped);
    }
}
