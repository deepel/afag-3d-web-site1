<?php
declare(strict_types=1);
/**
 * afag3d — File-based Cache  (Phase 4)
 *
 * Supports two usage patterns:
 *   A) Key/Value data cache  — Cache::get(key), Cache::set(key, value, ttl), Cache::remember(...)
 *   B) Full-page HTML cache  — Cache::has(uri, ttl), Cache::get(uri), Cache::put(uri, html)
 *
 * Cache files live in  public/cache/  (protected by its own .htaccess).
 * Each file stores: [expiry_timestamp]\n[serialized_value]
 */
class Cache
{
    private static string $dir = '';

    // ─────────────────────────────────────────────────────────────────────────
    // Bootstrap
    // ─────────────────────────────────────────────────────────────────────────

    public static function init(): void
    {
        if (self::$dir !== '') {
            return;
        }

        // Prefer public/cache/ (protected by .htaccess), fall back to storage/cache/
        if (defined('PUBLIC_PATH')) {
            self::$dir = PUBLIC_PATH . '/cache/';
        } elseif (defined('UPLOAD_PATH')) {
            self::$dir = dirname(UPLOAD_PATH) . '/cache/';
        } else {
            self::$dir = dirname(__DIR__, 2) . '/public/cache/';
        }

        if (!is_dir(self::$dir)) {
            mkdir(self::$dir, 0755, true);
        }

        // Protect cache directory from direct web access
        $htaccess = self::$dir . '.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Order deny,allow\nDeny from all\n");
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Key/Value cache  (Phase 4 primary interface)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Retrieve a cached value by key.
     * Returns null if the entry is missing or expired.
     */
    public static function get(string $key): mixed
    {
        self::ensureInit();
        $file = self::dataPath($key);

        if (!file_exists($file)) {
            return null;
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            return null;
        }

        $nl     = strpos($raw, "\n");
        if ($nl === false) {
            return null;
        }

        $expiry  = (int)substr($raw, 0, $nl);
        $payload = substr($raw, $nl + 1);

        if ($expiry !== 0 && time() > $expiry) {
            @unlink($file);
            return null;
        }

        return unserialize($payload);
    }

    /**
     * Store a value under $key for $ttl seconds (0 = forever).
     */
    public static function set(string $key, mixed $value, int $ttl = 3600): void
    {
        self::ensureInit();
        $file   = self::dataPath($key);
        $expiry = ($ttl > 0) ? time() + $ttl : 0;
        $raw    = $expiry . "\n" . serialize($value);

        self::atomicWrite($file, $raw);
    }

    /**
     * Delete a single cached entry.
     */
    public static function forget(string $key): void
    {
        self::ensureInit();
        $file = self::dataPath($key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Delete ALL cache files (both data and page-cache).
     */
    public static function flush(): void
    {
        self::ensureInit();
        $files = glob(self::$dir . '*.cache');
        if ($files) {
            foreach ($files as $f) {
                @unlink($f);
            }
        }
        // Also clear legacy .html files
        $html = glob(self::$dir . '*.html');
        if ($html) {
            foreach ($html as $f) {
                @unlink($f);
            }
        }
    }

    /**
     * Get-or-compute: returns the cached value if fresh,
     * otherwise runs $callback, stores the result, and returns it.
     *
     * Usage:
     *   $products = Cache::remember('home_featured', 1800, fn() => Product::getFeatured($db, 6));
     */
    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $cached = self::get($key);
        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        self::set($key, $value, $ttl);
        return $value;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Full-page HTML cache  (legacy / optional — kept for compatibility)
    // ─────────────────────────────────────────────────────────────────────────

    /** Return true if a page-cache file exists and is fresher than $ttl seconds. */
    public static function has(string $uri, int $ttl = 3600): bool
    {
        self::ensureInit();
        $file = self::pagePath($uri);
        if (!file_exists($file)) {
            return false;
        }
        return (time() - filemtime($file)) < $ttl;
    }

    /**
     * Return raw cached HTML for a URI, or null if missing.
     * (For data-cache keys use Cache::get() instead.)
     */
    public static function getPage(string $uri): ?string
    {
        self::ensureInit();
        $file    = self::pagePath($uri);
        $content = file_exists($file) ? file_get_contents($file) : false;
        return $content !== false ? $content : null;
    }

    /** Write raw HTML to the page cache for $uri. */
    public static function put(string $uri, string $html): void
    {
        self::ensureInit();
        self::atomicWrite(self::pagePath($uri), $html);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Pattern-based invalidation
    // ─────────────────────────────────────────────────────────────────────────

    /** Clear cached files whose key string contains $pattern. */
    public static function flushPattern(string $pattern): void
    {
        self::ensureInit();
        $files = array_merge(
            glob(self::$dir . '*.cache') ?: [],
            glob(self::$dir . '*.html')  ?: []
        );
        foreach ($files as $f) {
            $base = basename($f);
            // We cannot reverse md5, so flush all on page cache
            // For data-cache we store key in filename comment — use flush() as safe fallback
            @unlink($f);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Garbage collection
    // ─────────────────────────────────────────────────────────────────────────

    /** Remove data-cache files that have passed their embedded expiry. */
    public static function gc(): void
    {
        self::ensureInit();
        $now = time();
        foreach (glob(self::$dir . '*.cache') ?: [] as $file) {
            $raw = @file_get_contents($file);
            if ($raw === false) {
                continue;
            }
            $nl     = strpos($raw, "\n");
            $expiry = $nl !== false ? (int)substr($raw, 0, $nl) : 0;
            if ($expiry !== 0 && $now > $expiry) {
                @unlink($file);
            }
        }
        // Old HTML page-cache: remove files older than 1 day
        foreach (glob(self::$dir . '*.html') ?: [] as $file) {
            if (($now - filemtime($file)) > 86400) {
                @unlink($file);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private static function ensureInit(): void
    {
        if (self::$dir === '') {
            self::init();
        }
    }

    /** File path for key/value data cache entries. */
    private static function dataPath(string $key): string
    {
        return self::$dir . md5($key) . '.cache';
    }

    /** File path for full-page HTML cache entries. */
    private static function pagePath(string $uri): string
    {
        return self::$dir . md5($uri) . '.html';
    }

    /** Atomic write via temp file + rename to avoid partial reads. */
    private static function atomicWrite(string $dest, string $content): void
    {
        $tmp = $dest . '.tmp.' . getmypid();
        file_put_contents($tmp, $content, LOCK_EX);
        rename($tmp, $dest);
    }
}
