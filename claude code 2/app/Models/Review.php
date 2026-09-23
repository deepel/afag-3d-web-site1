<?php
declare(strict_types=1);

class Review
{
    static function getByProduct(PDO $db, int $productId): array
    {
        $stmt = $db->prepare(
            "SELECT * FROM reviews WHERE product_id = :id AND status = 'approved' ORDER BY created_at DESC"
        );
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll();
    }

    static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare(
            "INSERT INTO reviews (product_id, user_id, name, rating, body, status, created_at)
             VALUES (:product_id,:user_id,:name,:rating,:body,'pending',NOW())"
        );
        $stmt->execute([
            'product_id' => $data['product_id'],
            'user_id'    => $data['user_id']  ?? null,
            'name'       => $data['name'],
            'rating'     => $data['rating'],
            'body'       => $data['body'],
        ]);
        return (int)$db->lastInsertId();
    }

    static function getAvg(PDO $db, int $productId): float
    {
        $stmt = $db->prepare(
            "SELECT AVG(rating) FROM reviews WHERE product_id = :id AND status = 'approved'"
        );
        $stmt->execute(['id' => $productId]);
        return round((float)$stmt->fetchColumn(), 1);
    }

    static function countApproved(PDO $db, int $productId): int
    {
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM reviews WHERE product_id = :id AND status = 'approved'"
        );
        $stmt->execute(['id' => $productId]);
        return (int)$stmt->fetchColumn();
    }
}
