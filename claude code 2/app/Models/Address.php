<?php
declare(strict_types=1);
/**
 * afag3d — Address Model
 */
class Address
{
    /** Get all addresses for a user. */
    static function getByUser(PDO $db, int $userId): array
    {
        $stmt = $db->prepare(
            "SELECT * FROM addresses WHERE user_id = :uid ORDER BY is_default DESC, id DESC"
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    /** Get a single address by ID. */
    static function getById(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("SELECT * FROM addresses WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Create a new address. Returns new address ID. */
    static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare(
            "INSERT INTO addresses
             (user_id, title, name, mobile, province, city, address, postal_code, is_default, created_at)
             VALUES
             (:user_id, :title, :name, :mobile, :province, :city, :address, :postal_code, :is_default, NOW())"
        );
        $stmt->execute([
            'user_id'     => $data['user_id'],
            'title'       => $data['title']       ?? '',
            'name'        => $data['name']         ?? '',
            'mobile'      => $data['mobile']       ?? '',
            'province'    => $data['province']     ?? '',
            'city'        => $data['city']         ?? '',
            'address'     => $data['address']      ?? '',
            'postal_code' => $data['postal_code']  ?? '',
            'is_default'  => (int)($data['is_default'] ?? 0),
        ]);
        return (int) $db->lastInsertId();
    }

    /** Update an existing address. */
    static function update(PDO $db, int $id, array $data): void
    {
        $db->prepare(
            "UPDATE addresses
             SET title=:title, name=:name, mobile=:mobile, province=:province,
                 city=:city, address=:address, postal_code=:postal_code
             WHERE id=:id"
        )->execute([
            'title'       => $data['title']      ?? '',
            'name'        => $data['name']        ?? '',
            'mobile'      => $data['mobile']      ?? '',
            'province'    => $data['province']    ?? '',
            'city'        => $data['city']        ?? '',
            'address'     => $data['address']     ?? '',
            'postal_code' => $data['postal_code'] ?? '',
            'id'          => $id,
        ]);
    }

    /** Delete an address by ID. */
    static function delete(PDO $db, int $id): void
    {
        $db->prepare("DELETE FROM addresses WHERE id = :id")->execute(['id' => $id]);
    }

    /** Set one address as default, clearing others for the same user. */
    static function setDefault(PDO $db, int $id, int $userId): void
    {
        $db->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = :uid")
           ->execute(['uid' => $userId]);
        $db->prepare("UPDATE addresses SET is_default = 1 WHERE id = :id AND user_id = :uid")
           ->execute(['id' => $id, 'uid' => $userId]);
    }

    /** Get the default address for a user. */
    static function getDefault(PDO $db, int $userId): ?array
    {
        $stmt = $db->prepare(
            "SELECT * FROM addresses WHERE user_id = :uid AND is_default = 1 LIMIT 1"
        );
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
