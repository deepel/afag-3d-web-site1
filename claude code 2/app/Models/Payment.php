<?php
declare(strict_types=1);

class Payment
{
    static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare(
            "INSERT INTO payments (order_id, amount, authority, status, gateway, created_at)
             VALUES (:order_id,:amount,:authority,:status,'zarinpal',NOW())"
        );
        $stmt->execute([
            'order_id'  => $data['order_id'],
            'amount'    => $data['amount'],
            'authority' => $data['authority'] ?? '',
            'status'    => $data['status']    ?? 'pending',
        ]);
        return (int)$db->lastInsertId();
    }

    static function updateByAuthority(PDO $db, string $authority, string $status, string $refId = ''): void
    {
        $db->prepare(
            "UPDATE payments SET status = :status, ref_id = :ref_id, verified_at = NOW() WHERE authority = :auth"
        )->execute(['status' => $status, 'ref_id' => $refId, 'auth' => $authority]);
    }

    static function getByAuthority(PDO $db, string $authority): ?array
    {
        $stmt = $db->prepare("SELECT * FROM payments WHERE authority = :auth LIMIT 1");
        $stmt->execute(['auth' => $authority]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
