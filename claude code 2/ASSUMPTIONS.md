# ASSUMPTIONS — afag3d Phase 1

## Architecture decisions

1. **No Composer** — all code is vanilla PHP 8.1+. Autoloading is handled by a single `spl_autoload_register` in `public/index.php` that scans `app/Helpers/`, `app/Models/`, and `app/Controllers/`.

2. **Router** — flat array-based router in `public/index.php`. Dynamic segments (e.g. `/product/{slug}`) will be added in Phase 2 with a simple regex scanner.

3. **BaseController** — `app/Controllers/BaseController.php` must be manually `require_once`-d in `index.php` before instantiation because it is not in an autoload-friendly location relative to the class name. All other controllers extend it.

4. **Layout system** — `render(string $view, array $data, string $layout)` uses output buffering. The view file is captured into `$content`, then the layout file is included which echoes `$content`. No template engine required.

5. **Sessions** — session is started in `index.php` before any output. ID is regenerated every 30 minutes (not on every request) to balance security and performance.

6. **CSRF** — one-time tokens stored in `$_SESSION['_csrf_tokens']` as `token => expiry` map. Tokens expire in 1 hour. Maximum 20 tokens are kept to prevent session bloat.

7. **Password hashing** — bcrypt cost 12 (`PASSWORD_BCRYPT`). The install SQL ships a placeholder; run `php database/seed_admin.php` to set the real hash.

8. **Rate limiting** — IP-based, stored in `rate_limits` DB table. Default: 5 attempts per 15 minutes. On success, record is cleared.

## Database

9. **MySQL/MariaDB only** — PDO with `charset=utf8mb4` and `ATTR_EMULATE_PREPARES=false`. No SQLite support in Phase 1.

10. **JSON columns** — `order_items.options` and `payments.raw_response` use MySQL JSON type (MySQL 5.7+ / MariaDB 10.2+). If your host is older, change to `TEXT` and manually `json_encode/decode`.

11. **Sample data passwords** — only the admin user is inserted. The plaintext password is `Admin@1234`. Run `seed_admin.php` to generate the proper bcrypt hash.

## Frontend

12. **RTL-first** — `<html dir="rtl" lang="fa">`. LTR elements (phone numbers, URLs, passwords, SKUs) use `dir="ltr"` inline.

13. **Fonts** — `@font-face` declarations reference `/assets/fonts/*.woff2`. Until font files are installed, the browser falls back to `Tahoma` (for Vazirmatn) and `Segoe UI` / `Courier New`. The CSS is ready; just drop the woff2 files in place.

14. **No CDN** — zero external network requests in production. All assets are local.

15. **Dark mode default** — `data-theme="dark"` is set on `<html>` by the PHP layout before the page renders. The JS theme init reads `localStorage` and sets it immediately to prevent flash-of-wrong-theme.

16. **No JavaScript frameworks** — vanilla ES2020. No jQuery, no Alpine, no React.

## Security

17. **CSP** — `Content-Security-Policy` header is set in PHP (not Apache) so it applies uniformly. `unsafe-inline` is allowed for styles and scripts because there is no build step to generate nonces. Phase 2 should introduce nonces or move all inline styles to classes.

18. **Upload directory** — `public/uploads/.htaccess` blocks PHP execution. Uploaded filenames are never used as-is; they are stored as `md5(uniqid()) . '.' . $ext`.

19. **XSS** — all output uses `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. The `Url::price()` and other helpers also escape output.

## Automation layer (n8n / Telegram bridge)

20. **Separate `lifecycle_status` column** — the prompt asked for a product `status`
    enum `('pending','coming_soon','available')`, but the existing `status` column is
    already `('active','draft','out_of_stock')` and is used across many shop/admin
    queries. Repurposing it would break the site. **Decision:** added a separate
    `lifecycle_status` column for the automation pipeline; the original `status`
    stays the visibility flag. A product shows in the shop when `status='active'`,
    and the "coming soon" badge/disabled-buy is driven by `lifecycle_status='coming_soon'`
    OR `price IS NULL`.

21. **`lifecycle_status` default = `available`, not `pending`** — the spec table said
    DEFAULT `pending`, but defaulting existing live products to `pending` was risky.
    Existing rows were backfilled to `available`. The API `create` endpoint explicitly
    sets `coming_soon` for new automated products. Net effect matches the spec intent.

22. **`price` made NULLable** — `NULL` means "not priced yet" (coming soon). Existing
    rows keep their values. Card/detail/listing views all guard against NULL price.

23. **Pre-existing schema/code gap fixed** — `Product::create()`/`update()` and the admin
    product form already referenced `short_desc`, `compare_price`, and `image` columns
    that the base `install.sql` never created, so admin add/edit product was effectively
    broken. Added those three columns (nullable, additive) to align schema with the
    authored code. This was necessary so `archive_folder` (and the other new fields)
    are editable from the admin product form, per the prompt.

24. **Product code generation** — `AFAG-XXXX`, 4-digit zero-padded, max-suffix + 1.
    Implemented once in `Product::nextCode()` and reused by both the API and admin.
    Existing products were backfilled with codes based on their id.

25. **API auth & transport** — `/api/*` routes go through the existing front controller
    but skip session/CSRF; auth is the `X-API-Key` header (constant `API_KEY` in
    `config/`, outside `public/`), compared with `hash_equals`. CSP/security headers
    still apply; JSON output is unaffected. No CSP exception was needed.

26. **STL files are never uploaded** — only a reference is stored: `product_code` +
    `archive_folder` (a folder name on the owner's PC). Both are shown next to each
    line item on the admin order-detail page for fast fulfillment.

27. **Image intake from n8n** — base64 in the `images` array (Telegram-sourced). Saved
    with the same sandbox as the rest of the site: real-image validation via
    `getimagesizefromstring`, size ≤ `MAX_FILE_SIZE`, random filename, stored under
    `public/uploads/products/{id}/`, path recorded in `product_images`.

28. **Manual price priority** — a `price_is_manual` flag. Typing a price in the admin
    form or sending `price` to `update-price` sets it to 1; the "recalculate catalog"
    action skips manual-priced products.

29. **Migration file** — `database/migration_automation.sql` applies all of the above to
    an existing DB (run once). `database/install.sql` was also updated so fresh cPanel
    installs get every column + the `pricing_settings` table from the start.

## Redesign pass (visual brief)

30. **Did not rebuild working features** — the redesign brief lists cart, admin panel,
    ZarinPal checkout, routing, and the two shops as "missing/broken." They already
    exist and were verified working in earlier phases, so they were left intact rather
    than rebuilt. Only the genuinely-new visual asks were implemented.

31. **Softer dark palette** — per the brief, the dark theme moved off near-black
    `#141210` to warm charcoal: `--bg #1C1B1A`, `--bg2 #232220`, `--bg3 #2A2826` (card
    surface). Bone text and orange/terracotta accents unchanged. The earlier teal
    (`--cyan`) data-accent is kept as the cool counterpoint to molten orange.

32. **Unified product-card tokens** — the shop product card referenced undefined
    `--surface-1/--surface-2/--border/--shadow` variables (rendered transparent). It
    now uses the canonical `--bg3/--line` tokens, matching the home card. Both cards
    share the same hover: `-6px` lift, warm orange glow shadow, `1.06` image zoom.

33. **`prefers-reduced-motion` guard** added globally (brief requirement).

34. **Placeholder images are generated, not seeded from disk** — the owner wanted
    "test images so no product is empty, don't be picky." The pasted product photos
    can't be written to disk from chat, and the Downloads folder is a mix of unrelated
    personal files (verified: one candidate was a scanned exam paper). So
    `database/seed_placeholder_images.php` generates a LOCAL branded SVG cover per
    product (warm gradient + stepped-pyramid logo + afag3d wordmark; cooler tones for
    the parts/filament shop). Offline-resilient, no external refs, no risk of assigning
    a wrong personal image. The owner replaces these with real photos via the admin
    panel's multi-image upload. Re-running the seeder only fills products lacking a cover.

## What is NOT in Phase 1

- Payment gateway integration (Zarinpal stub ready in DB)
- SMS OTP login
- File upload endpoints
- Product/category CRUD forms
- Shopping cart
- Order creation flow
- Email notifications
- Blog/Portfolio CRUD
- Search
- Pagination component (DB queries include LIMIT/OFFSET; UI pagination links are Phase 2)
