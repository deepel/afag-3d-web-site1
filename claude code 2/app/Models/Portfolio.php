<?php
declare(strict_types=1);
/**
 * afag3d — Portfolio Model
 */
class Portfolio
{
    static function getAll(PDO $db, int $limit = 0, int $page = 1): array
    {
        $perPage = $limit ?: 20;
        $offset  = ($page - 1) * $perPage;
        $limitSql = $limit > 0 ? "LIMIT :limit" : "LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare("
            SELECT p.*,
                   (SELECT pi2.path FROM portfolio_images pi2
                    WHERE pi2.portfolio_id = p.id ORDER BY pi2.sort_order ASC LIMIT 1) AS first_image_path
            FROM portfolio p
            WHERE p.status = 1
            ORDER BY p.sort_order ASC, p.id DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static function getById(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("SELECT * FROM portfolio WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function getImages(PDO $db, int $portfolioId): array
    {
        $stmt = $db->prepare("
            SELECT * FROM portfolio_images
            WHERE portfolio_id = :id
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute([':id' => $portfolioId]);
        return $stmt->fetchAll();
    }

    static function getFeatured(PDO $db, int $limit = 6): array
    {
        $stmt = $db->prepare("
            SELECT p.*,
                   (SELECT pi2.path FROM portfolio_images pi2
                    WHERE pi2.portfolio_id = p.id ORDER BY pi2.sort_order ASC LIMIT 1) AS first_image_path
            FROM portfolio p
            WHERE p.status = 1 AND p.is_featured = 1
            ORDER BY p.sort_order ASC, p.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare("
            INSERT INTO portfolio
              (title, slug, description, cover, client, tags, sort_order, is_featured, status, created_at)
            VALUES
              (:title, :slug, :description, :cover, :client, :tags, :sort_order, :is_featured, :status, NOW())
        ");
        $stmt->execute([
            ':title'       => $data['title'],
            ':slug'        => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':cover'       => $data['cover'] ?? null,
            ':client'      => $data['client'] ?? null,
            ':tags'        => $data['tags'] ?? null,
            ':sort_order'  => $data['sort_order'] ?? 0,
            ':is_featured' => $data['is_featured'] ?? 0,
            ':status'      => $data['status'] ?? 1,
        ]);
        return (int) $db->lastInsertId();
    }

    static function update(PDO $db, int $id, array $data): void
    {
        $fields = [];
        $params = [':id' => $id];
        $allowed = ['title','slug','description','cover','client','tags','sort_order','is_featured','status'];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "`$col` = :$col";
                $params[":$col"] = $data[$col];
            }
        }
        if (!$fields) return;
        $sql = "UPDATE portfolio SET " . implode(', ', $fields) . " WHERE id = :id";
        $db->prepare($sql)->execute($params);
    }

    static function delete(PDO $db, int $id): void
    {
        $db->prepare("DELETE FROM portfolio WHERE id = :id")->execute([':id' => $id]);
    }

    static function addImage(PDO $db, int $portfolioId, array $imageData): int
    {
        $stmt = $db->prepare("
            INSERT INTO portfolio_images (portfolio_id, path, caption, sort_order)
            VALUES (:portfolio_id, :path, :caption, :sort_order)
        ");
        $stmt->execute([
            ':portfolio_id' => $portfolioId,
            ':path'         => $imageData['path'],
            ':caption'      => $imageData['caption'] ?? null,
            ':sort_order'   => $imageData['sort_order'] ?? 0,
        ]);
        return (int) $db->lastInsertId();
    }

    static function deleteImage(PDO $db, int $imageId): void
    {
        $db->prepare("DELETE FROM portfolio_images WHERE id = :id")->execute([':id' => $imageId]);
    }

    static function getImageById(PDO $db, int $imageId): ?array
    {
        $stmt = $db->prepare("SELECT * FROM portfolio_images WHERE id = :id");
        $stmt->execute([':id' => $imageId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function count(PDO $db): int
    {
        return (int) $db->query("SELECT COUNT(*) FROM portfolio")->fetchColumn();
    }

    static function getAllAdmin(PDO $db, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare("
            SELECT p.*,
                   (SELECT COUNT(*) FROM portfolio_images pi2 WHERE pi2.portfolio_id = p.id) AS image_count
            FROM portfolio p
            ORDER BY p.sort_order ASC, p.id DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
