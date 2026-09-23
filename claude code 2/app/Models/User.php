<?php
declare(strict_types=1);
/**
 * afag3d — User Model
 */
class User
{
    public function __construct(private PDO $db) {}

    /** Find a user by ID. */
    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Find a user by mobile number. */
    public function findByMobile(string $mobile): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE mobile = ? LIMIT 1');
        $stmt->execute([$mobile]);
        return $stmt->fetch();
    }

    /** Find a user by email. */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /** Create a new user. Returns new user ID. */
    public function create(string $name, string $mobile, string $password, string $role = 'customer'): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, mobile, password, role, status) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, $mobile, $hash, $role, 'active']);
        return (int) $this->db->lastInsertId();
    }

    /** Verify a plain password against the stored hash. */
    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    /** Update the last_login_at timestamp. */
    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Update user profile fields. */
    public function update(int $id, array $fields): bool
    {
        $allowed = ['name', 'email', 'avatar', 'status'];
        $set     = [];
        $values  = [];
        foreach ($fields as $k => $v) {
            if (in_array($k, $allowed, true)) {
                $set[]    = "`{$k}` = ?";
                $values[] = $v;
            }
        }
        if (empty($set)) return false;
        $values[] = $id;
        $stmt = $this->db->prepare('UPDATE users SET ' . implode(', ', $set) . ' WHERE id = ?');
        $stmt->execute($values);
        return $stmt->rowCount() > 0;
    }

    /** Change user password. */
    public function changePassword(int $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->execute([$hash, $id]);
        return $stmt->rowCount() > 0;
    }

    /** Check if a mobile number is already taken. */
    public function mobileExists(string $mobile): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE mobile = ? LIMIT 1');
        $stmt->execute([$mobile]);
        return $stmt->fetchColumn() !== false;
    }

    /** List all users with pagination. */
    public function all(int $page = 1, int $perPage = 20, string $role = ''): array
    {
        $offset = ($page - 1) * $perPage;
        if ($role !== '') {
            $stmt = $this->db->prepare(
                'SELECT id, name, mobile, email, role, status, created_at FROM users
                 WHERE role = ? ORDER BY id DESC LIMIT ? OFFSET ?'
            );
            $stmt->execute([$role, $perPage, $offset]);
        } else {
            $stmt = $this->db->prepare(
                'SELECT id, name, mobile, email, role, status, created_at FROM users
                 ORDER BY id DESC LIMIT ? OFFSET ?'
            );
            $stmt->execute([$perPage, $offset]);
        }
        return $stmt->fetchAll();
    }

    /** Count all users (optionally filtered by role). */
    public function count(string $role = ''): int
    {
        if ($role !== '') {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
            $stmt->execute([$role]);
        } else {
            $stmt = $this->db->query('SELECT COUNT(*) FROM users');
        }
        return (int) $stmt->fetchColumn();
    }

    /**
     * Check rate limit for login attempts.
     * Returns true if the IP is blocked.
     */
    public function isRateLimited(string $ip, string $action = 'login'): bool
    {
        $stmt = $this->db->prepare(
            'SELECT attempts, window_start FROM rate_limits WHERE ip = ? AND action = ?'
        );
        $stmt->execute([$ip, $action]);
        $row = $stmt->fetch();

        if (!$row) return false;

        $windowEnd = strtotime($row['window_start']) + RATE_LIMIT_WINDOW;
        if (time() > $windowEnd) {
            // Window expired — reset
            $this->db->prepare('DELETE FROM rate_limits WHERE ip = ? AND action = ?')
                      ->execute([$ip, $action]);
            return false;
        }

        return (int) $row['attempts'] >= RATE_LIMIT_LOGIN;
    }

    /** Increment the login attempt counter for an IP. */
    public function recordAttempt(string $ip, string $action = 'login'): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO rate_limits (ip, action, attempts, window_start)
             VALUES (?, ?, 1, NOW())
             ON DUPLICATE KEY UPDATE attempts = attempts + 1'
        );
        $stmt->execute([$ip, $action]);
    }

    /** Clear rate limit record for IP (after successful login). */
    public function clearAttempts(string $ip, string $action = 'login'): void
    {
        $stmt = $this->db->prepare('DELETE FROM rate_limits WHERE ip = ? AND action = ?');
        $stmt->execute([$ip, $action]);
    }
}
