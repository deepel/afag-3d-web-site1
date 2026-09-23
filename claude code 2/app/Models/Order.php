<?php
declare(strict_types=1);

class Order
{
    static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare(
            "INSERT INTO orders
             (user_id, name, mobile, email, province, city, address, postal_code,
              subtotal, shipping_cost, discount, total, coupon_id, status, notes, created_at)
             VALUES
             (:user_id,:name,:mobile,:email,:province,:city,:address,:postal_code,
              :subtotal,:shipping_cost,:discount,:total,:coupon_id,:status,:notes,NOW())"
        );
        $stmt->execute([
            'user_id'       => $data['user_id']       ?? null,
            'name'          => $data['name'],
            'mobile'        => $data['mobile'],
            'email'         => $data['email']         ?? '',
            'province'      => $data['province']      ?? '',
            'city'          => $data['city']          ?? '',
            'address'       => $data['address']       ?? '',
            'postal_code'   => $data['postal_code']   ?? '',
            'subtotal'      => $data['subtotal'],
            'shipping_cost' => $data['shipping_cost'] ?? 0,
            'discount'      => $data['discount']      ?? 0,
            'total'         => $data['total'],
            'coupon_id'     => $data['coupon_id']     ?? null,
            'status'        => $data['status']        ?? 'pending',
            'notes'         => $data['notes']         ?? '',
        ]);
        $orderId = (int)$db->lastInsertId();

        // Insert order items
        if (!empty($data['items'])) {
            $itemStmt = $db->prepare(
                "INSERT INTO order_items (order_id, product_id, name, price, qty, subtotal)
                 VALUES (:order_id,:product_id,:name,:price,:qty,:subtotal)"
            );
            foreach ($data['items'] as $item) {
                $itemStmt->execute([
                    'order_id'   => $orderId,
                    'product_id' => $item['product']['id'],
                    'name'       => $item['product']['name'],
                    'price'      => $item['product']['price'],
                    'qty'        => $item['qty'],
                    'subtotal'   => $item['subtotal'],
                ]);
            }
        }
        return $orderId;
    }

    static function getById(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare(
            "SELECT o.*, u.email AS user_email FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             WHERE o.id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function getByUserId(PDO $db, int $userId, int $page = 1): array
    {
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;
        $stmt = $db->prepare(
            "SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim OFFSET :off"
        );
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $total = (int)$db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = :uid")->execute(['uid' => $userId]);
        return ['items' => $stmt->fetchAll(), 'total' => $total, 'pages' => (int)ceil($total / $perPage)];
    }

    static function updateStatus(PDO $db, int $id, string $status): void
    {
        $db->prepare("UPDATE orders SET status = :s WHERE id = :id")->execute(['s' => $status, 'id' => $id]);
    }

    static function updatePayment(PDO $db, int $id, string $refId, string $authority): void
    {
        $db->prepare("UPDATE orders SET status = 'paid', ref_id = :ref, authority = :auth WHERE id = :id")
           ->execute(['ref' => $refId, 'auth' => $authority, 'id' => $id]);
    }

    static function count(PDO $db): int
    {
        return (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }

    static function getRecent(PDO $db, int $limit = 10): array
    {
        $stmt = $db->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT :lim");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static function getItems(PDO $db, int $orderId): array
    {
        // Join products so fulfillment can show the product code + STL archive
        // folder next to each line (file lives on the owner's PC, not the server).
        $stmt = $db->prepare(
            "SELECT oi.*, p.product_code, p.archive_folder
               FROM order_items oi
               LEFT JOIN products p ON p.id = oi.product_id
              WHERE oi.order_id = :id"
        );
        $stmt->execute(['id' => $orderId]);
        return $stmt->fetchAll();
    }
}
