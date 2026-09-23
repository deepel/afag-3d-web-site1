<?php
declare(strict_types=1);
/**
 * afag3d — CSRF Helper
 * Double-submit pattern using session-stored tokens.
 */
class CSRF
{
    private const SESSION_KEY = '_csrf_tokens';
    private const TOKEN_TTL   = 3600; // 1 hour
    private const MAX_TOKENS  = 20;   // prevent session bloat

    /** Generate a new token and store it in session. Returns the token string. */
    public static function generate(): string
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY][$token] = time() + self::TOKEN_TTL;

        // Prune expired tokens and limit count
        self::cleanup();

        return $token;
    }

    /** Validate a submitted token. Returns true if valid (and consumes the token). */
    public static function validate(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $tokens = $_SESSION[self::SESSION_KEY] ?? [];

        if (!isset($tokens[$token])) {
            return false;
        }

        // Check expiry
        if ($tokens[$token] < time()) {
            unset($_SESSION[self::SESSION_KEY][$token]);
            return false;
        }

        // Consume token (one-time use)
        unset($_SESSION[self::SESSION_KEY][$token]);
        return true;
    }

    /**
     * Validate a submitted token WITHOUT consuming it.
     * Use for AJAX endpoints that may be called multiple times per page load
     * (e.g. cart add/update/remove) using the same page-rendered token.
     */
    public static function validateReusable(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $tokens = $_SESSION[self::SESSION_KEY] ?? [];

        if (!isset($tokens[$token])) {
            return false;
        }

        // Check expiry
        if ($tokens[$token] < time()) {
            unset($_SESSION[self::SESSION_KEY][$token]);
            return false;
        }

        return true;
    }

    /**
     * Validate the CSRF token from POST or die with 403.
     * Call at the top of any POST handler.
     */
    public static function check(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!self::validate($token)) {
            http_response_code(403);
            die('درخواست نامعتبر است. لطفاً صفحه را بازخوانی کنید.');
        }
    }

    /** Return an HTML hidden input with a fresh CSRF token. */
    public static function field(): string
    {
        $token = self::generate();
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /** Return just the token string (for JS / AJAX usage). */
    public static function token(): string
    {
        return self::generate();
    }

    /** Remove expired tokens and cap token list. */
    private static function cleanup(): void
    {
        $tokens = $_SESSION[self::SESSION_KEY] ?? [];
        $now    = time();

        // Remove expired
        foreach ($tokens as $t => $exp) {
            if ($exp < $now) {
                unset($tokens[$t]);
            }
        }

        // Keep only the most recent MAX_TOKENS
        if (count($tokens) > self::MAX_TOKENS) {
            arsort($tokens); // newest first
            $tokens = array_slice($tokens, 0, self::MAX_TOKENS, true);
        }

        $_SESSION[self::SESSION_KEY] = $tokens;
    }
}
