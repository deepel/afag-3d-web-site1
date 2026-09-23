<?php
declare(strict_types=1);
/**
 * afag3d — Flash Message Helper
 * One-time session messages consumed on the next request.
 */
class Flash
{
    private const SESSION_KEY = '_flash';

    /** Store a flash message. Type: 'success' | 'error' | 'warning' | 'info' */
    public static function set(string $type, string $message): void
    {
        $_SESSION[self::SESSION_KEY][] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    /** Alias for convenience. */
    public static function success(string $message): void
    {
        self::set('success', $message);
    }

    public static function error(string $message): void
    {
        self::set('error', $message);
    }

    public static function warning(string $message): void
    {
        self::set('warning', $message);
    }

    public static function info(string $message): void
    {
        self::set('info', $message);
    }

    /** Check if there are any flash messages. */
    public static function has(): bool
    {
        return !empty($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Get and clear all flash messages.
     * @return array<array{type:string,message:string}>
     */
    public static function all(): array
    {
        $messages = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);
        return $messages;
    }

    /**
     * Render all flash messages as HTML and clear them.
     * Safe to call even if there are no messages.
     */
    public static function render(): string
    {
        if (!self::has()) {
            return '';
        }

        $html = '';
        foreach (self::all() as $flash) {
            $type    = htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8');
            $message = htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8');
            $html   .= "<div class=\"alert alert-{$type}\" role=\"alert\">{$message}</div>\n";
        }
        return $html;
    }
}
