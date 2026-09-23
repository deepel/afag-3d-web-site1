<?php
declare(strict_types=1);
/**
 * afag3d — Setting Model
 * Key/value settings with in-request caching.
 */
class Setting
{
    private array $cache = [];

    public function __construct(private PDO $db) {}

    /** Load all settings into cache and return them as associative array. */
    public function all(): array
    {
        if (!empty($this->cache)) {
            return $this->cache;
        }
        $stmt = $this->db->query('SELECT `key`, `value` FROM settings');
        foreach ($stmt->fetchAll() as $row) {
            $this->cache[$row['key']] = $row['value'];
        }
        return $this->cache;
    }

    /** Get a single setting value. Returns $default if not found. */
    public function get(string $key, string $default = ''): string
    {
        if (empty($this->cache)) {
            $this->all();
        }
        return $this->cache[$key] ?? $default;
    }

    /** Set (upsert) a single setting. */
    public function set(string $key, string $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
        );
        $stmt->execute([$key, $value]);
        $this->cache[$key] = $value;
    }

    /** Bulk-save an associative array of key => value. */
    public function saveMany(array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
        );
        $this->db->beginTransaction();
        try {
            foreach ($data as $key => $value) {
                $stmt->execute([(string) $key, (string) $value]);
                $this->cache[$key] = (string) $value;
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Delete a setting by key. */
    public function delete(string $key): void
    {
        $stmt = $this->db->prepare('DELETE FROM settings WHERE `key` = ?');
        $stmt->execute([$key]);
        unset($this->cache[$key]);
    }

    /** Flush the in-request cache. */
    public function flush(): void
    {
        $this->cache = [];
    }
}
