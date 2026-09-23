<?php
declare(strict_types=1);
/**
 * afag3d — URL Helper
 */
class Url
{
    /** Build a URL with the configured BASE_URL prefix. */
    public static function to(string $path = '/'): string
    {
        return BASE_URL . '/' . ltrim($path, '/');
    }

    /** Build a full absolute URL including APP_URL. */
    public static function abs(string $path = '/'): string
    {
        return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
    }

    /** URL to a public asset (CSS, JS, image). */
    public static function asset(string $path): string
    {
        return BASE_URL . '/assets/' . ltrim($path, '/');
    }

    /** URL to an uploaded file. */
    public static function upload(string $path): string
    {
        return BASE_URL . '/uploads/' . ltrim($path, '/');
    }

    /** Return current request URI. */
    public static function current(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    /** Check if current URI starts with the given path (for nav active state). */
    public static function isActive(string $path): bool
    {
        $current = self::current();
        $full    = BASE_URL . '/' . ltrim($path, '/');
        return str_starts_with($current, rtrim($full, '/'));
    }

    /** Redirect via header and exit. */
    public static function redirect(string $path): never
    {
        header('Location: ' . self::to($path));
        exit;
    }

    /** Generate a slug from a Persian/Arabic/Latin string. */
    public static function slug(string $text): string
    {
        // Transliteration map for common Persian characters
        $map = [
            'آ'=>'a','ا'=>'a','ب'=>'b','پ'=>'p','ت'=>'t','ث'=>'s','ج'=>'j',
            'چ'=>'ch','ح'=>'h','خ'=>'kh','د'=>'d','ذ'=>'z','ر'=>'r','ز'=>'z',
            'ژ'=>'zh','س'=>'s','ش'=>'sh','ص'=>'s','ض'=>'z','ط'=>'t','ظ'=>'z',
            'ع'=>'a','غ'=>'gh','ف'=>'f','ق'=>'gh','ک'=>'k','گ'=>'g','ل'=>'l',
            'م'=>'m','ن'=>'n','و'=>'v','ه'=>'h','ی'=>'y','ئ'=>'y',
            ' '=>'-','‌'=>'-',
        ];
        $text = strtr($text, $map);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\-]/', '', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }

    /** Format an integer price as Persian currency string. */
    public static function price(int|float $amount, bool $withUnit = true): string
    {
        $formatted = number_format((int) $amount);
        // Convert to Persian digits
        $formatted = self::toPersianDigits($formatted);
        return $withUnit ? $formatted . ' ' . CURRENCY : $formatted;
    }

    /**
     * Digit formatter. Per owner preference the site uses Latin (English)
     * numerals throughout, so this now returns the text unchanged. Kept as a
     * single choke-point so the whole site can be switched back by editing here.
     */
    public static function toPersianDigits(string $text): string
    {
        return $text;
    }
}
