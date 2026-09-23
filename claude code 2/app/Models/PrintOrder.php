<?php
declare(strict_types=1);
/**
 * afag3d — PrintOrder Model
 */
class PrintOrder
{
    // Status constants
    const STATUS_PENDING    = 'pending';
    const STATUS_QUOTING    = 'quoting';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_PRINTING   = 'printing';
    const STATUS_DONE       = 'done';
    const STATUS_CANCELLED  = 'cancelled';

    static function create(PDO $db, array $data): int
    {
        $stmt = $db->prepare("
            INSERT INTO print_orders
              (user_id, status, color_id, material_id, quality_id, print_type_id,
               quantity, notes, created_at, updated_at)
            VALUES
              (:user_id, :status, :color_id, :material_id, :quality_id, :print_type_id,
               :quantity, :notes, NOW(), NOW())
        ");
        $stmt->execute([
            ':user_id'       => $data['user_id'],
            ':status'        => $data['status'] ?? self::STATUS_PENDING,
            ':color_id'      => $data['color_id'] ?? null,
            ':material_id'   => $data['material_id'] ?? null,
            ':quality_id'    => $data['quality_id'] ?? null,
            ':print_type_id' => $data['print_type_id'] ?? null,
            ':quantity'      => $data['quantity'] ?? 1,
            ':notes'         => $data['notes'] ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    static function addFile(PDO $db, int $printOrderId, array $fileData): int
    {
        $stmt = $db->prepare("
            INSERT INTO print_files
              (print_order_id, original_name, stored_name, mime, size, created_at)
            VALUES
              (:print_order_id, :original_name, :stored_name, :mime, :size, NOW())
        ");
        $stmt->execute([
            ':print_order_id' => $printOrderId,
            ':original_name'  => $fileData['original_name'],
            ':stored_name'    => $fileData['stored_name'],
            ':mime'           => $fileData['mime'] ?? null,
            ':size'           => $fileData['size'] ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    static function getById(PDO $db, int $id): ?array
    {
        $stmt = $db->prepare("
            SELECT po.*,
                   u.name  AS customer_name,
                   u.mobile AS customer_mobile,
                   u.email  AS customer_email,
                   c.name_fa  AS color_name,
                   m.name_fa  AS material_name,
                   q.name_fa  AS quality_name,
                   pt.name_fa AS print_type_name
            FROM print_orders po
            LEFT JOIN users        u  ON u.id  = po.user_id
            LEFT JOIN print_options c  ON c.id  = po.color_id
            LEFT JOIN print_options m  ON m.id  = po.material_id
            LEFT JOIN print_options q  ON q.id  = po.quality_id
            LEFT JOIN print_options pt ON pt.id = po.print_type_id
            WHERE po.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function getFiles(PDO $db, int $printOrderId): array
    {
        $stmt = $db->prepare("
            SELECT * FROM print_files
            WHERE print_order_id = :id
            ORDER BY created_at ASC
        ");
        $stmt->execute([':id' => $printOrderId]);
        return $stmt->fetchAll();
    }

    static function getAll(PDO $db, int $page = 1, int $perPage = 20, string $status = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where  = $status ? 'WHERE po.status = :status' : '';
        $params = $status ? [':status' => $status] : [];
        $params[':limit']  = $perPage;
        $params[':offset'] = $offset;

        $stmt = $db->prepare("
            SELECT po.*,
                   u.name   AS customer_name,
                   u.mobile AS customer_mobile,
                   pt.name_fa AS print_type_name,
                   m.name_fa  AS material_name,
                   (SELECT COUNT(*) FROM print_files pf WHERE pf.print_order_id = po.id) AS file_count
            FROM print_orders po
            LEFT JOIN users        u  ON u.id  = po.user_id
            LEFT JOIN print_options pt ON pt.id = po.print_type_id
            LEFT JOIN print_options m  ON m.id  = po.material_id
            $where
            ORDER BY po.created_at DESC
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
        return $stmt->fetchAll();
    }

    static function count(PDO $db, string $status = ''): int
    {
        if ($status) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM print_orders WHERE status = :status");
            $stmt->execute([':status' => $status]);
        } else {
            $stmt = $db->query("SELECT COUNT(*) FROM print_orders");
        }
        return (int) $stmt->fetchColumn();
    }

    static function pendingCount(PDO $db): int
    {
        $stmt = $db->query("SELECT COUNT(*) FROM print_orders WHERE status = 'pending'");
        return (int) $stmt->fetchColumn();
    }

    static function updateStatus(PDO $db, int $id, string $status, string $adminNotes = '', ?int $quotedPrice = null): void
    {
        $stmt = $db->prepare("
            UPDATE print_orders
            SET status = :status,
                notes  = CASE WHEN :notes != '' THEN :notes ELSE notes END,
                quoted_price = CASE WHEN :qp IS NOT NULL THEN :qp ELSE quoted_price END,
                updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            ':status' => $status,
            ':notes'  => $adminNotes,
            ':qp'     => $quotedPrice,
            ':id'     => $id,
        ]);
    }

    static function getOptions(PDO $db, string $type): array
    {
        $stmt = $db->prepare("
            SELECT * FROM print_options
            WHERE type = :type
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute([':type' => $type]);
        return $stmt->fetchAll();
    }

    static function getAllOptions(PDO $db): array
    {
        $stmt = $db->query("SELECT * FROM print_options ORDER BY type ASC, sort_order ASC, id ASC");
        $rows = $stmt->fetchAll();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['type']][] = $row;
        }
        return $grouped;
    }

    static function updateOption(PDO $db, int $id, string $status): void
    {
        $stmt = $db->prepare("UPDATE print_options SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);
    }

    static function createOption(PDO $db, array $data): int
    {
        $stmt = $db->prepare("
            INSERT INTO print_options (type, name, name_fa, price_add, sort_order, status)
            VALUES (:type, :name, :name_fa, :price_add, :sort_order, :status)
        ");
        $stmt->execute([
            ':type'       => $data['type'],
            ':name'       => $data['name'] ?? $data['name_fa'],
            ':name_fa'    => $data['name_fa'],
            ':price_add'  => $data['price_add'] ?? 0,
            ':sort_order' => $data['sort_order'] ?? 0,
            ':status'     => $data['status'] ?? 1,
        ]);
        return (int) $db->lastInsertId();
    }

    static function deleteOption(PDO $db, int $id): void
    {
        $stmt = $db->prepare("DELETE FROM print_options WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    static function getFileById(PDO $db, int $fileId): ?array
    {
        $stmt = $db->prepare("SELECT * FROM print_files WHERE id = :id");
        $stmt->execute([':id' => $fileId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
