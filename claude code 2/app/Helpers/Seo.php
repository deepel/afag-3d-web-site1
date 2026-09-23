<?php
declare(strict_types=1);
/**
 * afag3d — SEO Helper  (Phase 4)
 * All methods are static; call SEO::set([...]) early in each controller action.
 *
 * Alias: class SEO extends Seo {} is defined at the bottom so both names work.
 */
class Seo
{
    /** @var array<string,mixed> */
    private static array $data = [];

    // ─────────────────────────────────────────────────────────────────────────
    // Core
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Set / merge page SEO data.
     *
     * Accepted keys:
     *   title, description, keywords, canonical, og_image, og_type, og_locale,
     *   twitter_card, schema, breadcrumbs, noindex
     */
    public static function set(array $data): void
    {
        self::$data = array_merge(self::$data, $data);
    }

    public static function reset(): void
    {
        self::$data = [];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // <title>
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns full page title: "{page_title} — {site_name}"
     * (guidance: keep under 60 chars)
     */
    public static function title(): string
    {
        $siteName  = defined('APP_NAME') ? APP_NAME : 'afag3d';
        $pageTitle = self::$data['title'] ?? '';

        if ($pageTitle === '' || $pageTitle === $siteName) {
            return self::e($siteName);
        }

        return self::e($pageTitle . ' — ' . $siteName);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Meta tag block
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns all <meta> / <link> HTML for the <head>.
     * Includes: description, keywords, robots, canonical, hreflang,
     *           Open Graph, Twitter Card, preload hints.
     */
    public static function metaTags(): string
    {
        $d        = self::$data;
        $siteName = defined('APP_NAME') ? APP_NAME : 'afag3d';
        $appUrl   = defined('APP_URL')  ? APP_URL  : 'https://afag3d.com';
        $baseUrl  = defined('BASE_URL') ? BASE_URL : '';

        $pageTitle = $d['title'] ?? $siteName;
        $fullTitle = ($pageTitle !== $siteName)
            ? $pageTitle . ' — ' . $siteName
            : $siteName;

        $desc    = mb_substr(
            $d['description'] ?? 'استودیوی تخصصی چاپ سه‌بعدی — طراحی، چاپ و فروش محصولات سه‌بعدی.',
            0, 160
        );
        $kw      = $d['keywords']  ?? 'چاپ سه‌بعدی, افگ, ماکت, مدل‌سازی, پرینت سه بعدی';
        $ogImage = $d['og_image']  ?? (rtrim($appUrl, '/') . '/assets/img/og-default.svg');
        $ogType  = $d['og_type']   ?? 'website';
        $locale  = $d['og_locale'] ?? 'fa_IR';
        $noindex = !empty($d['noindex']);
        $tCard   = $d['twitter_card'] ?? 'summary_large_image';
        $robots  = $noindex
            ? 'noindex,nofollow'
            : 'index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1';

        // Canonical
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $canonical   = $d['canonical'] ?? (rtrim($appUrl, '/') . $requestPath);

        // hreflang URLs
        $faUrl = $canonical;
        $enUrl = rtrim($appUrl, '/') . '/en' . $requestPath;

        $h  = '';

        // ── Basic ──
        $h .= '<meta name="description" content="' . self::e($desc)    . '">' . "\n";
        $h .= '<meta name="keywords"    content="' . self::e($kw)      . '">' . "\n";
        $h .= '<meta name="robots"      content="' . self::e($robots)  . '">' . "\n";
        $h .= '<meta name="author"      content="' . self::e($siteName). '">' . "\n";
        $h .= '<link rel="canonical"    href="'    . self::e($canonical). '">' . "\n";

        // ── hreflang ──
        $h .= '<link rel="alternate" hreflang="fa"        href="' . self::e($faUrl) . '">' . "\n";
        $h .= '<link rel="alternate" hreflang="en"        href="' . self::e($enUrl) . '">' . "\n";
        $h .= '<link rel="alternate" hreflang="x-default" href="' . self::e($faUrl) . '">' . "\n";

        // ── Open Graph ──
        $h .= '<meta property="og:title"       content="' . self::e($fullTitle). '">' . "\n";
        $h .= '<meta property="og:description" content="' . self::e($desc)    . '">' . "\n";
        $h .= '<meta property="og:image"       content="' . self::e($ogImage) . '">' . "\n";
        $h .= '<meta property="og:url"         content="' . self::e($canonical). '">' . "\n";
        $h .= '<meta property="og:type"        content="' . self::e($ogType)  . '">' . "\n";
        $h .= '<meta property="og:locale"      content="' . self::e($locale)  . '">' . "\n";
        $h .= '<meta property="og:site_name"   content="' . self::e($siteName). '">' . "\n";

        // ── Twitter Card ──
        $h .= '<meta name="twitter:card"        content="' . self::e($tCard)    . '">' . "\n";
        $h .= '<meta name="twitter:title"       content="' . self::e($fullTitle). '">' . "\n";
        $h .= '<meta name="twitter:description" content="' . self::e($desc)     . '">' . "\n";
        $h .= '<meta name="twitter:image"       content="' . self::e($ogImage)  . '">' . "\n";

        // ── Preload critical CSS ──
        $h .= '<link rel="preload" href="' . self::e($baseUrl . '/assets/css/main.css') . '" as="style">' . "\n";

        return $h;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Breadcrumbs
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Renders an HTML <nav> breadcrumb + BreadcrumbList JSON-LD.
     *
     * Set via: SEO::set(['breadcrumbs' => [
     *   ['label' => 'خانه',   'url' => '/'],
     *   ['label' => 'فروشگاه','url' => '/shop'],
     *   ['label' => 'محصول',  'url' => null],   // current page
     * ]])
     */
    public static function breadcrumbs(): string
    {
        $items  = self::$data['breadcrumbs'] ?? [];
        $appUrl = defined('APP_URL') ? APP_URL : 'https://afag3d.com';

        if (empty($items)) {
            return '';
        }

        $count = count($items);

        // ── HTML nav ──
        $html  = '<nav class="breadcrumb" aria-label="مسیر صفحه">' . "\n";
        $html .= '<ol class="breadcrumb-list">' . "\n";

        foreach ($items as $i => $item) {
            $label   = self::e($item['label'] ?? '');
            $url     = $item['url'] ?? null;
            $isLast  = ($i === $count - 1);
            $fullUrl = $url
                ? (str_starts_with($url, 'http') ? $url : rtrim($appUrl, '/') . $url)
                : '';

            $html .= '<li class="breadcrumb-item' . ($isLast ? ' breadcrumb-item--active' : '') . '">';
            if ($fullUrl && !$isLast) {
                $html .= '<a href="' . self::e($fullUrl) . '">' . $label . '</a>';
            } else {
                $html .= '<span>' . $label . '</span>';
            }
            $html .= '</li>' . "\n";

            if (!$isLast) {
                $html .= '<li class="breadcrumb-sep" aria-hidden="true">/</li>' . "\n";
            }
        }

        $html .= '</ol>' . "\n";
        $html .= '</nav>' . "\n";

        // ── JSON-LD BreadcrumbList ──
        $listItems = [];
        foreach ($items as $i => $item) {
            $url     = $item['url'] ?? '/';
            $fullUrl = str_starts_with($url, 'http') ? $url : rtrim($appUrl, '/') . $url;
            $listItems[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $item['label'] ?? '',
                'item'     => $fullUrl,
            ];
        }

        $html .= self::schema([
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ]);

        return $html;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // JSON-LD helpers
    // ─────────────────────────────────────────────────────────────────────────

    /** Wraps arbitrary data in a <script type="application/ld+json"> block. */
    public static function schema(array $schemaData): string
    {
        $json = json_encode(
            $schemaData,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );
        return '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
    }

    /** Product JSON-LD schema. */
    public static function productSchema(array $product, float $avgRating = 0, int $reviewCount = 0): string
    {
        $appUrl   = defined('APP_URL')  ? APP_URL  : 'https://afag3d.com';
        $siteName = defined('APP_NAME') ? APP_NAME : 'افگ تری‌دی';

        $imageUrl = !empty($product['cover_image'])
            ? rtrim($appUrl, '/') . '/uploads/' . $product['cover_image']
            : rtrim($appUrl, '/') . '/assets/img/og-default.svg';

        $inStock = isset($product['stock']) && (int)$product['stock'] > 0;

        $data = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $product['name'] ?? '',
            'description' => strip_tags($product['short_desc'] ?? $product['description'] ?? ''),
            'image'       => $imageUrl,
            'sku'         => $product['sku'] ?? (string)($product['id'] ?? ''),
            'brand'       => ['@type' => 'Brand', 'name' => 'afag3d'],
            'offers'      => [
                '@type'         => 'Offer',
                'priceCurrency' => 'IRR',
                'price'         => (string)(int)($product['price'] ?? 0),
                'availability'  => $inStock
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'url'           => rtrim($appUrl, '/') . '/product/' . ($product['slug'] ?? ''),
                'seller'        => ['@type' => 'Organization', 'name' => $siteName],
            ],
        ];

        if ($avgRating > 0 && $reviewCount > 0) {
            $data['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => number_format($avgRating, 1),
                'reviewCount' => (string)$reviewCount,
                'bestRating'  => '5',
                'worstRating' => '1',
            ];
        }

        return self::schema($data);
    }

    /** Article JSON-LD schema. */
    public static function articleSchema(array $post, string $siteUrl = '', string $siteName = ''): string
    {
        $siteUrl  = $siteUrl  ?: (defined('APP_URL')  ? APP_URL  : 'https://afag3d.com');
        $siteName = $siteName ?: (defined('APP_NAME') ? APP_NAME : 'افگ تری‌دی');

        $imageUrl = !empty($post['cover'])
            ? rtrim($siteUrl, '/') . '/uploads/' . $post['cover']
            : rtrim($siteUrl, '/') . '/assets/img/og-default.svg';

        $toIso = static function (string $dt): string {
            $ts = strtotime($dt);
            return $ts ? date('c', $ts) : $dt;
        };

        $published = $toIso($post['published_at'] ?? $post['created_at'] ?? date('Y-m-d H:i:s'));
        $modified  = $toIso($post['updated_at']   ?? $post['published_at'] ?? date('Y-m-d H:i:s'));

        $data = [
            '@context'         => 'https://schema.org',
            '@type'            => 'Article',
            'headline'         => $post['title'] ?? '',
            'description'      => strip_tags($post['excerpt'] ?? mb_substr(strip_tags($post['body'] ?? ''), 0, 160)),
            'image'            => $imageUrl,
            'datePublished'    => $published,
            'dateModified'     => $modified,
            'url'              => rtrim($siteUrl, '/') . '/blog/' . ($post['slug'] ?? ''),
            'author'           => ['@type' => 'Person', 'name' => $post['author_name'] ?? 'تیم afag3d'],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => $siteName,
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => rtrim($siteUrl, '/') . '/assets/img/og-default.svg',
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => rtrim($siteUrl, '/') . '/blog/' . ($post['slug'] ?? ''),
            ],
            'inLanguage' => 'fa-IR',
        ];

        return self::schema($data);
    }

    /**
     * Organization + WebSite JSON-LD schema.
     * Output on every page — uses a combined @graph block.
     */
    public static function organizationSchema(array $settings, string $siteUrl = ''): string
    {
        $siteUrl  = $siteUrl  ?: (defined('APP_URL')  ? APP_URL  : 'https://afag3d.com');
        $siteName = $settings['site_name'] ?? (defined('APP_NAME') ? APP_NAME : 'افگ تری‌دی');

        $orgSchema = [
            '@type'  => 'Organization',
            '@id'    => rtrim($siteUrl, '/') . '/#organization',
            'name'   => $siteName,
            'url'    => rtrim($siteUrl, '/') . '/',
            'logo'   => [
                '@type' => 'ImageObject',
                'url'   => rtrim($siteUrl, '/') . '/assets/img/og-default.svg',
            ],
        ];

        if (!empty($settings['site_phone'])) {
            $orgSchema['contactPoint'] = [
                '@type'             => 'ContactPoint',
                'telephone'         => $settings['site_phone'],
                'contactType'       => 'customer service',
                'areaServed'        => 'IR',
                'availableLanguage' => 'Persian',
            ];
        }

        $sameAs = [];
        if (!empty($settings['instagram'])) $sameAs[] = $settings['instagram'];
        if (!empty($settings['telegram']))  $sameAs[] = $settings['telegram'];
        if ($sameAs) $orgSchema['sameAs'] = $sameAs;

        $webSiteSchema = [
            '@type'           => 'WebSite',
            '@id'             => rtrim($siteUrl, '/') . '/#website',
            'name'            => $siteName,
            'url'             => rtrim($siteUrl, '/') . '/',
            'inLanguage'      => 'fa',
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => rtrim($siteUrl, '/') . '/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];

        return self::schema([
            '@context' => 'https://schema.org',
            '@graph'   => [$orgSchema, $webSiteSchema],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Admin SEO score
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Computes a 0–100 SEO score for admin product/blog forms.
     *
     * @param array{
     *   title?: string,
     *   meta_title?: string,
     *   meta_desc?: string,
     *   slug?: string,
     *   focus_keyword?: string,
     *   has_image?: bool,
     *   alt_text?: string,
     *   description?: string,
     * } $fields
     *
     * @return array{score: int, checks: list<array{label: string, pass: bool, tip: string}>}
     */
    public static function seoScore(array $fields): array
    {
        $title  = $fields['meta_title']   ?? $fields['title']      ?? '';
        $desc   = $fields['meta_desc']    ?? '';
        $slug   = $fields['slug']         ?? '';
        $kw     = mb_strtolower(trim($fields['focus_keyword'] ?? ''));
        $body   = strip_tags($fields['description'] ?? '');
        $hasImg = !empty($fields['has_image']);
        $alt    = $fields['alt_text']     ?? '';

        $titleLen = mb_strlen($title);
        $descLen  = mb_strlen($desc);
        $tLow     = mb_strtolower($title);
        $dLow     = mb_strtolower($desc);
        $sLow     = mb_strtolower($slug);
        $bLow     = mb_strtolower($body);

        $checks = [];

        $checks[] = [
            'label' => 'طول عنوان متا (۵۰–۶۰ کاراکتر)',
            'pass'  => $titleLen >= 50 && $titleLen <= 60,
            'tip'   => 'الان: ' . $titleLen . ' کاراکتر.',
        ];
        $checks[] = [
            'label' => 'طول توضیح متا (۱۲۰–۱۶۰ کاراکتر)',
            'pass'  => $descLen >= 120 && $descLen <= 160,
            'tip'   => 'الان: ' . $descLen . ' کاراکتر.',
        ];
        $checks[] = [
            'label' => 'کلمه کلیدی در عنوان متا',
            'pass'  => $kw !== '' && str_contains($tLow, $kw),
            'tip'   => 'عنوان باید کلمه کلیدی اصلی را داشته باشد.',
        ];
        $checks[] = [
            'label' => 'کلمه کلیدی در اسلاگ URL',
            'pass'  => $kw !== '' && str_contains($sLow, $kw),
            'tip'   => 'اسلاگ باید کلمه کلیدی اصلی را داشته باشد.',
        ];
        $checks[] = [
            'label' => 'کلمه کلیدی در توضیح متا',
            'pass'  => $kw !== '' && str_contains($dLow, $kw),
            'tip'   => 'توضیح متا باید کلمه کلیدی اصلی را داشته باشد.',
        ];
        $checks[] = [
            'label' => 'کلمه کلیدی در محتوا',
            'pass'  => $kw !== '' && str_contains($bLow, $kw),
            'tip'   => 'محتوا باید کلمه کلیدی اصلی را داشته باشد.',
        ];
        $slugOk = $slug !== '' && (bool)preg_match('/^[a-z0-9][a-z0-9\-]*[a-z0-9]$/', $slug);
        $checks[] = [
            'label' => 'slug فقط حروف کوچک و خط‌تیره',
            'pass'  => $slugOk,
            'tip'   => 'اسلاگ باید فقط a-z، ۰-۹ و خط‌تیره داشته باشد.',
        ];
        $checks[] = [
            'label' => 'تصویر وجود دارد',
            'pass'  => $hasImg,
            'tip'   => 'داشتن تصویر برای سئو ضروری است.',
        ];
        $checks[] = [
            'label' => 'متن جایگزین تصویر (alt)',
            'pass'  => $alt !== '',
            'tip'   => 'متن alt به خوانایی تصویر برای موتورهای جستجو کمک می‌کند.',
        ];
        $words = (int)str_word_count(preg_replace('/\s+/', ' ', $body) ?: '');
        $checks[] = [
            'label' => 'محتوا حداقل ۳۰۰ کلمه',
            'pass'  => $words >= 300,
            'tip'   => 'الان: ' . $words . ' کلمه. هدف: ۳۰۰+',
        ];

        $passed = count(array_filter($checks, fn($c) => $c['pass']));
        $score  = (int)round(($passed / count($checks)) * 100);

        return ['score' => $score, 'checks' => $checks];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Legacy backward-compat aliases (kept from Phase 3 Seo class)
    // ─────────────────────────────────────────────────────────────────────────

    /** @deprecated Use organizationSchema() */
    public static function schemaOrganization(array $siteData): string
    {
        return self::organizationSchema($siteData);
    }

    /** @deprecated Use articleSchema() */
    public static function schemaArticle(array $post, array $siteData): string
    {
        $siteUrl  = defined('APP_URL') ? APP_URL : 'https://afag3d.com';
        $siteName = $siteData['site_name'] ?? (defined('APP_NAME') ? APP_NAME : 'afag3d');
        return self::articleSchema($post, $siteUrl, $siteName);
    }

    /** @deprecated Use productSchema() */
    public static function schemaProduct(array $product, array $reviews = [], float $avgRating = 0, int $reviewCount = 0): string
    {
        return self::productSchema($product, $avgRating, $reviewCount);
    }

    /** @deprecated Use breadcrumbs() */
    public static function schemaBreadcrumb(array $items): string
    {
        // Convert old format {name, url} to new {label, url}
        $converted = array_map(fn($i) => ['label' => $i['name'] ?? '', 'url' => $i['url'] ?? '/'], $items);
        self::$data['breadcrumbs'] = $converted;
        return self::breadcrumbs();
    }

    /** @deprecated Use metaTags() */
    public static function schemaWebSite(array $siteData): string
    {
        return self::organizationSchema($siteData);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private
    // ─────────────────────────────────────────────────────────────────────────

    private static function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

/** Case-insensitive alias so both SEO:: and Seo:: work. */
if (!class_exists('SEO', false)) {
    class_alias('Seo', 'SEO');
}
