<?php
declare(strict_types=1);
/**
 * afag3d — Rate Limit Helper  (Phase 4 — static interface)
 *
 * Uses the `rate_limits` table.  Table is auto-created on first use.
 *
 * Typical login flow:
 *   $ip = RateLimit::getIp();
 *   if (!RateLimit::check($db, 'login', $ip, 5, 900)) {
 *       Flash::error('تلاش‌های زیاد...');
 *       $this->redirect('/login');
 *   }
 *   // on failure:
 *   RateLimit::hit($db, 'login', $ip);
 *   // on success:
 *   RateLimit::clear($db, 'login', $ip);
 */
class RateLimit
{
    // ─────────────────────────────────────────────────────────────────────────
    // Static public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns TRUE if this IP is allowed to proceed.
     * Returns FALSE if blocked (too many attempts or currently blocked_until in future).
     *
     * @param int $maxAttempts  Max failures before blocking
     * @param int $windowSeconds  Sliding window in seconds (default 900 = 15 min)
     */
    public static function check(
        PDO    $db,
        string $action,
        string $ip,
        int    $maxAttempts    = 5,
        int    $windowSeconds  = 900
    ): bool {
        self::ensureTable($db);
        self::cleanup($db);

        $row = self::getRow($db, $action, $ip);

        if (!$row) {
            return true; // no record yet — allow
        }

        // Still blocked?
        if ($row['blocked_until'] && strtotime($row['blocked_until']) > time()) {
            return false;
        }

        // Count attempts within window
        $windowStart = date('Y-m-d H:i:s', time() - $windowSeconds);
        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM rate_limits
             WHERE action = :action AND ip = :ip AND last_attempt >= :window'
        );
        $stmt->execute([':action' => $action, ':ip' => $ip, ':window' => $windowStart]);
        $count = (int)$stmt->fetchColumn();

        return $count < $maxAttempts;
    }

    /**
     * Record one failed attempt for this action+IP.
     */
    public static function hit(PDO $db, string $action, string $ip): void
    {
        self::ensureTable($db);

        $stmt = $db->prepare(
            'INSERT INTO rate_limits (action, ip, last_attempt)
             VALUES (:action, :ip, NOW())
             ON DUPLICATE KEY UPDATE last_attempt = NOW()'
        );
        // Use separate inserts so multiple rows accumulate (count-based window)
        $ins = $db->prepare(
            'INSERT INTO rate_limits (action, ip, last_attempt)
             VALUES (:action, :ip, NOW())'
        );
        $ins->execute([':action' => $action, ':ip' => $ip]);
    }

    /**
     * Block this IP for the given action for $seconds.
     */
    public static function block(PDO $db, string $action, string $ip, int $seconds = 900): void
    {
        self::ensureTable($db);
        $until = date('Y-m-d H:i:s', time() + $seconds);

        // Upsert a blocking record
        $stmt = $db->prepare(
            'INSERT INTO rate_limits (action, ip, blocked_until, last_attempt)
             VALUES (:action, :ip, :until, NOW())
             ON DUPLICATE KEY UPDATE blocked_until = :until2, last_attempt = NOW()'
        );
        $stmt->execute([
            ':action' => $action,
            ':ip'     => $ip,
            ':until'  => $until,
            ':until2' => $until,
        ]);
    }

    /**
     * Clear all attempts for this action+IP (call after successful login).
     */
    public static function clear(PDO $db, string $action, string $ip): void
    {
        $stmt = $db->prepare(
            'DELETE FROM rate_limits WHERE action = :action AND ip = :ip'
        );
        $stmt->execute([':action' => $action, ':ip' => $ip]);
    }

    /**
     * Remove records older than 24 hours (called automatically by check()).
     */
    public static function cleanup(PDO $db): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        try {
            $db->exec(
                "DELETE FROM rate_limits
                 WHERE last_attempt < DATE_SUB(NOW(), INTERVAL 24 HOUR)
                   AND (blocked_until IS NULL OR blocked_until < NOW())"
            );
        } catch (Throwable) {
            // Table may not exist yet on first run
        }
        $done = true;
    }

    /**
     * Returns the real client IP address.
     * Safely handles X-Forwarded-For (takes first public IP).
     */
    public static function getIp(): string
    {
        $candidates = [
            $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '',   // Cloudflare
            $_SERVER['HTTP_X_FORWARDED_FOR']  ?? '',   // Proxy
            $_SERVER['HTTP_X_REAL_IP']        ?? '',   // Nginx
            $_SERVER['REMOTE_ADDR']           ?? '',
        ];

        foreach ($candidates as $raw) {
            $ip = trim(explode(',', $raw)[0]);
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return '0.0.0.0';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Instance-based legacy API (kept for any code using new RateLimit($db))
    // ─────────────────────────────────────────────────────────────────────────

    private PDO $pdo;

    public function __construct(PDO $db)
    {
        $this->pdo = $db;
    }

    public function isBlocked(string $ip, string $action): bool
    {
        return !self::check($this->pdo, $action, $ip);
    }

    public function attempt(string $ip, string $action, int $maxAttempts = 5, int $decayMinutes = 15): array
    {
        self::hit($this->pdo, $action, $ip);
        $blocked = !self::check($this->pdo, $action, $ip, $maxAttempts, $decayMinutes * 60);
        if ($blocked) {
            self::block($this->pdo, $action, $ip, $decayMinutes * 60);
        }
        return [
            'blocked'     => $blocked,
            'attempts'    => 0,
            'retry_after' => $blocked ? ($decayMinutes * 60) : 0,
        ];
    }

    public static function clientIp(): string
    {
        return self::getIp();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private
    // ─────────────────────────────────────────────────────────────────────────

    private static function getRow(PDO $db, string $action, string $ip): ?array
    {
        $stmt = $db->prepare(
            'SELECT * FROM rate_limits
             WHERE action = :action AND ip = :ip
             ORDER BY last_attempt DESC LIMIT 1'
        );
        $stmt->execute([':action' => $action, ':ip' => $ip]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private static function ensureTable(PDO $db): void
    {
        static $checked = false;
        if ($checked) {
            return;
        }
        $db->exec(
            "CREATE TABLE IF NOT EXISTS rate_limits (
                id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                action        VARCHAR(64)  NOT NULL,
                ip            VARCHAR(45)  NOT NULL,
                blocked_until DATETIME     NULL,
                last_attempt  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_action_ip (action, ip),
                INDEX idx_last_attempt (last_attempt)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $checked = true;
    }
}
