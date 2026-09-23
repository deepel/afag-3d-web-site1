<?php
declare(strict_types=1);
/**
 * afag3d — Blog Model
 */
class Blog
{
    static function getPublished(PDO $db, int $page = 1, int $perPage = 9, ?int $categoryId = null, string $search = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where  = ["bp.status = 'published'"];
        $params = [];

        if ($categoryId) {
            $where[] = "bp.category_id = :cat_id";
            $params[':cat_id'] = $categoryId;
        }
        if ($search !== '') {
            $where[] = "(bp.title LIKE :search OR bp.excerpt LIKE :search2)";
            $params[':search']  = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        $whereStr = 'WHERE ' . implode(' AND ', $where);

        $countStmt = $db->prepare("SELECT COUNT(*) FROM blog_posts bp $whereStr");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();
        $pages = (int) ceil($total / $perPage);

        $params[':limit']  = $perPage;
        $params[':offset'] = $offset;

        $stmt = $db->prepare("
            SELECT bp.*,
                   u.name  AS author_name,
                   bc.name AS category_name,
                   bc.slug AS category_slug
            FROM blog_posts bp
            LEFT JOIN users          u  ON u.id  = bp.author_id
            LEFT JOIN blog_categories bc ON bc.id = bp.category_id
            $whereStr
            ORDER BY bp.published_at DESC
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $k => &$v) {
            if ($k === ':limit' || $k === ':offset') {
                $stmt->bindValue($k, $v, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        $stmt->execute();
        $items = $stmt->fetchAll();

        return ['items' => $items, 'total' => $total, 'pages' => $pages];
    }

    static function getById(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("
            SELECT bp.*,
                   u.name  AS author_name,
                   bc.name AS category_name,
                   bc.slug AS category_slug
            FROM blog_posts bp
            LEFT JOIN users          u  ON u.id  = bp.author_id
            LEFT JOIN blog_categories bc ON bc.id = bp.category_id
            WHERE bp.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function getBySlug(PDO $db, string $slug): ?array
    {
        $stmt = $db->prepare("
            SELECT bp.*,
                   u.name  AS author_name,
                   bc.name AS category_name,
                   bc.slug AS category_slug
            FROM blog_posts bp
            LEFT JOIN users          u  ON u.id  = bp.author_id
            LEFT JOIN blog_categories bc ON bc.id = bp.category_id
            WHERE bp.slug = :slug AND bp.status = 'published'
        ");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function getRelated(PDO $db, int $postId, int $categoryId, int $limit = 3): array
    {
        $stmt = $db->prepare("
            SELECT bp.*,
                   u.name  AS author_name,
                   bc.name AS category_name,
                   bc.slug AS category_slug
            FROM blog_posts bp
            LEFT JOIN users          u  ON u.id  = bp.author_id
            LEFT JOIN blog_categories bc ON bc.id = bp.category_id
            WHERE bp.id != :post_id
              AND bp.category_id = :cat_id
              AND bp.status = 'published'
            ORDER BY bp.published_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':post_id', $postId,     PDO::PARAM_INT);
        $stmt->bindValue(':cat_id',  $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',   $limit,       PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static function getRecent(PDO $db, int $limit = 3): array
    {
        $stmt = $db->prepare("
            SELECT bp.id, bp.title, bp.slug, bp.cover, bp.published_at
            FROM blog_posts bp
            WHERE bp.status = 'published'
            ORDER BY bp.published_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static function incrementViews(PDO $db, int $id): void
    {
        $db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = :id")
           ->execute([':id' => $id]);
    }

    static function getCategories(PDO $db): array
    {
        $stmt = $db->query("
            SELECT bc.*,
                   COUNT(bp.id) AS post_count
            FROM blog_categories bc
            LEFT JOIN blog_posts bp ON bp.category_id = bc.id AND bp.status = 'published'
            WHERE bc.status = 1
            GROUP BY bc.id
            ORDER BY bc.sort_order ASC, bc.id ASC
        ");
        return $stmt->fetchAll();
    }

    static function getCategoryBySlug(PDO $db, string $slug): ?array
    {
        $stmt = $db->prepare("SELECT * FROM blog_categories WHERE slug = :slug AND status = 1");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function getAll(PDO $db, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare("
            SELECT bp.*,
                   u.name  AS author_name,
                   bc.name AS category_name
            FROM blog_posts bp
            LEFT JOIN users          u  ON u.id  = bp.author_id
            LEFT JOIN blog_categories bc ON bc.id = bp.category_id
            ORDER BY bp.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare("
            INSERT INTO blog_posts
              (author_id, category_id, title, slug, excerpt, body, cover, tags,
               status, meta_title, meta_desc, published_at, created_at, updated_at)
            VALUES
              (:author_id, :category_id, :title, :slug, :excerpt, :body, :cover, :tags,
               :status, :meta_title, :meta_desc, :published_at, NOW(), NOW())
        ");
        $publishedAt = ($data['status'] === 'published') ? (date('Y-m-d H:i:s')) : null;
        if (!empty($data['published_at'])) $publishedAt = $data['published_at'];

        $stmt->execute([
            ':author_id'    => $data['author_id'],
            ':category_id'  => $data['category_id'] ?: null,
            ':title'        => $data['title'],
            ':slug'         => $data['slug'],
            ':excerpt'      => $data['excerpt'] ?? null,
            ':body'         => $data['body'] ?? null,
            ':cover'        => $data['cover'] ?? null,
            ':tags'         => $data['tags'] ?? null,
            ':status'       => $data['status'] ?? 'draft',
            ':meta_title'   => $data['meta_title'] ?? null,
            ':meta_desc'    => $data['meta_desc'] ?? null,
            ':published_at' => $publishedAt,
        ]);
        return (int) $db->lastInsertId();
    }

    static function update(PDO $db, int $id, array $data): void
    {
        // If status changed to published and no published_at, set it now
        if (($data['status'] ?? '') === 'published' && empty($data['published_at'])) {
            $curr = $db->prepare("SELECT published_at FROM blog_posts WHERE id = :id");
            $curr->execute([':id' => $id]);
            $existing = $curr->fetchColumn();
            if (!$existing) {
                $data['published_at'] = date('Y-m-d H:i:s');
            }
        }

        $fields = [];
        $params = [':id' => $id];
        $allowed = ['category_id','title','slug','excerpt','body','cover','tags',
                    'status','meta_title','meta_desc','published_at'];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $fields[] = "`$col` = :$col";
                $params[":$col"] = $data[$col] ?: null;
            }
        }
        $fields[] = "updated_at = NOW()";
        if (!$fields) return;
        $sql = "UPDATE blog_posts SET " . implode(', ', $fields) . " WHERE id = :id";
        $db->prepare($sql)->execute($params);
    }

    static function delete(PDO $db, int $id): void
    {
        $db->prepare("DELETE FROM blog_posts WHERE id = :id")->execute([':id' => $id]);
    }

    static function count(PDO $db): int
    {
        return (int) $db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
    }

    static function readTime(string $content): int
    {
        $words = str_word_count(strip_tags($content));
        return max(1, (int) ceil($words / 200));
    }

    // ─── Blog Categories ──────────────────────────────────

    static function createCategory(PDO $db, array $data): int
    {
        $stmt = $db->prepare("
            INSERT INTO blog_categories (name, slug, sort_order, status)
            VALUES (:name, :slug, :sort_order, :status)
        ");
        $stmt->execute([
            ':name'       => $data['name'],
            ':slug'       => $data['slug'],
            ':sort_order' => $data['sort_order'] ?? 0,
            ':status'     => $data['status'] ?? 1,
        ]);
        return (int) $db->lastInsertId();
    }

    static function updateCategory(PDO $db, int $id, array $data): void
    {
        $stmt = $db->prepare("
            UPDATE blog_categories
            SET name = :name, slug = :slug, sort_order = :sort_order, status = :status
            WHERE id = :id
        ");
        $stmt->execute([
            ':name'       => $data['name'],
            ':slug'       => $data['slug'],
            ':sort_order' => $data['sort_order'] ?? 0,
            ':status'     => $data['status'] ?? 1,
            ':id'         => $id,
        ]);
    }

    static function deleteCategory(PDO $db, int $id): void
    {
        $db->prepare("DELETE FROM blog_categories WHERE id = :id")->execute([':id' => $id]);
    }

    static function getAllCategories(PDO $db): array
    {
        $stmt = $db->query("
            SELECT bc.*,
                   COUNT(bp.id) AS post_count
            FROM blog_categories bc
            LEFT JOIN blog_posts bp ON bp.category_id = bc.id
            GROUP BY bc.id
            ORDER BY bc.sort_order ASC, bc.id ASC
        ");
        return $stmt->fetchAll();
    }

    static function getCategoryById(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("SELECT * FROM blog_categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function slugExists(PDO $db, string $slug, int $excludeId = 0): bool
    {
        $stmt = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE slug = :slug AND id != :id");
        $stmt->execute([':slug' => $slug, ':id' => $excludeId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
