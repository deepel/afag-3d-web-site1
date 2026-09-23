<?php
declare(strict_types=1);
/**
 * afag3d — Shop Model
 */
class Shop
{
    public static function getAll(PDO $db): array
    {
        return $db->query("SELECT * FROM shops WHERE status = 'active' ORDER BY name")->fetchAll();
    }

    public static function getById(PDO $db, int $id): ?array
    {
        $st = $db->prepare("SELECT * FROM shops WHERE id = ? LIMIT 1");
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function getBySlug(PDO $db, string $slug): ?array
    {
        $st = $db->prepare("SELECT * FROM shops WHERE slug = ? LIMIT 1");
        $st->execute([$slug]);
        $row = $st->fetch();
        return $row ?: null;
    }
}
