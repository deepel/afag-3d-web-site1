<?php
declare(strict_types=1);
/**
 * afag3d — Auth Helper
 * Stateless helper that reads/writes $_SESSION.
 */
class Auth
{
    private const KEY_USER_ID   = '_auth_uid';
    private const KEY_USER_ROLE = '_auth_role';
    private const KEY_USER_NAME = '_auth_name';

    /** Store user data in session after login. */
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION[self::KEY_USER_ID]   = (int) $user['id'];
        $_SESSION[self::KEY_USER_ROLE] = $user['role'];
        $_SESSION[self::KEY_USER_NAME] = $user['name'];
        $_SESSION['_created']          = time();
    }

    /** Clear session and destroy it. */
    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();
    }

    /** Is any user logged in? */
    public static function check(): bool
    {
        return isset($_SESSION[self::KEY_USER_ID]);
    }

    /** Is the logged-in user an admin? */
    public static function isAdmin(): bool
    {
        return ($_SESSION[self::KEY_USER_ROLE] ?? '') === 'admin';
    }

    /** Is the logged-in user a shop owner? */
    public static function isShopOwner(): bool
    {
        return ($_SESSION[self::KEY_USER_ROLE] ?? '') === 'shop_owner';
    }

    /** Get the current user's ID (0 if guest). */
    public static function id(): int
    {
        return (int) ($_SESSION[self::KEY_USER_ID] ?? 0);
    }

    /** Get the current user's role string. */
    public static function role(): string
    {
        return $_SESSION[self::KEY_USER_ROLE] ?? 'guest';
    }

    /** Get the current user's display name. */
    public static function name(): string
    {
        return $_SESSION[self::KEY_USER_NAME] ?? '';
    }

    /** Refresh user info stored in session from DB row. */
    public static function refresh(array $user): void
    {
        $_SESSION[self::KEY_USER_ROLE] = $user['role'];
        $_SESSION[self::KEY_USER_NAME] = $user['name'];
    }
}
