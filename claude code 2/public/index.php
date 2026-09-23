<?php
declare(strict_types=1);
/**
 * afag3d — Front Controller
 */

// ─── Config ───────────────────────────────────────────────
require_once dirname(__DIR__) . '/config/config.php';

// ─── Static files (PHP built-in server router) ────────────
// When running under `php -S` with index.php as the router script,
// every request hits this file. Let the built-in server serve real
// files (CSS, JS, images, uploads) directly instead of 404-ing.
if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $file = __DIR__ . '/' . ltrim($requestPath, '/');
    if ($requestPath !== '/' && file_exists($file) && !is_dir($file)) {
        return false;
    }
}

// ─── Autoloader ───────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $dirs = [
        dirname(__DIR__) . '/app/Helpers/',
        dirname(__DIR__) . '/app/Models/',
        dirname(__DIR__) . '/app/Controllers/',
        dirname(__DIR__) . '/app/Services/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ─── Database connection ──────────────────────────────────────
try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );
    $db = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    if (ENV === 'development') {
        die('<pre>DB Error: ' . htmlspecialchars($e->getMessage()) . '</pre>');
    }
    http_response_code(500);
    die('خطا در اتصال به پایگاه داده. لطفاً بعداً تلاش کنید.');
}
// Make $db available via GLOBALS for layouts (e.g. admin sidebar pending badges)
$GLOBALS['db'] = $db;

// ─── Session ──────────────────────────────────────────────────
session_name(SESSION_NAME);
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.gc_maxlifetime',  (string) SESSION_LIFETIME);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', '1');
}
session_start();

// ─── Cache init ───────────────────────────────────────────────
Cache::init();

// Regenerate session ID periodically
if (!isset($_SESSION['_created'])) {
    $_SESSION['_created'] = time();
} elseif (time() - $_SESSION['_created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['_created'] = time();
}

// ─── Security headers ─────────────────────────────────────────
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; font-src 'self'; connect-src 'self'");

// ─── Route parsing ────────────────────────────────────────────
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri    = '/' . trim($uri, '/');
$uri    = ($uri === '/') ? '/' : rtrim($uri, '/');
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Strip BASE_URL prefix if set
if (BASE_URL !== '' && str_starts_with($uri, BASE_URL)) {
    $uri = substr($uri, strlen(BASE_URL)) ?: '/';
}

// ─── Route table ──────────────────────────────────────────────
$routes = [
    'GET' => [
        '/'                    => ['HomeController',            'index'],
        '/login'               => ['AuthController',            'loginForm'],
        '/register'            => ['AuthController',            'registerForm'],
        '/logout'              => ['AuthController',            'logout'],
        '/admin'               => ['AdminController',           'dashboard'],
        '/admin/dashboard'     => ['AdminController',           'dashboard'],
        '/admin/settings'      => ['AdminController',           'settingsForm'],
        '/admin/pricing'       => ['AdminController',           'pricingForm'],
        '/admin/products'      => ['AdminProductController',    'index'],
        '/admin/products/create' => ['AdminProductController',  'create'],
        '/admin/categories'    => ['AdminCategoryController',   'index'],
        '/admin/categories/create' => ['AdminCategoryController', 'create'],
        '/admin/orders'        => ['AdminOrderController',      'index'],
        '/admin/coupons'       => ['AdminCouponController',     'index'],
        '/admin/coupons/create' => ['AdminCouponController',   'create'],
        '/search'              => ['ShopController',            'search'],
        '/print-order'         => ['PrintOrderController',      'index'],
        '/print-order/success' => ['PrintOrderController',      'success'],
        '/portfolio'           => ['PortfolioController',        'index'],
        '/blog'                => ['BlogController',             'index'],
        '/blog/search'         => ['BlogController',             'search'],
        '/admin/print-orders'  => ['AdminPrintOrderController',  'index'],
        '/admin/print-options' => ['AdminPrintOrderController',  'options'],
        '/admin/portfolio'     => ['AdminPortfolioController',   'index'],
        '/admin/portfolio/create' => ['AdminPortfolioController','create'],
        '/admin/blog'          => ['AdminBlogController',        'index'],
        '/admin/blog/create'   => ['AdminBlogController',        'create'],
        '/admin/blog/categories' => ['AdminBlogController',      'categories'],
        '/cart'                => ['CartController',             'index'],
        '/checkout'            => ['CheckoutController',        'index'],
        '/checkout/callback'   => ['CheckoutController',        'callback'],
        '/checkout/success'    => ['CheckoutController',        'success'],
        '/checkout/failed'     => ['CheckoutController',        'failed'],
        '/payment/callback'    => ['CheckoutController',        'callback'],
        '/sitemap.xml'         => ['SitemapController',         'sitemap'],
        '/robots.txt'          => ['SitemapController',         'robots'],
        // Automation API (n8n) — auth via X-API-Key header
        '/api/product/pending' => ['ApiController',             'pending'],
        // Account
        '/account'             => ['AccountController',         'dashboard'],
        '/account/orders'      => ['AccountController',         'orders'],
        '/account/print-orders'=> ['AccountController',         'printOrders'],
        '/account/profile'     => ['AccountController',         'profile'],
        '/account/addresses'   => ['AccountController',         'addresses'],
        // Pages
        '/about'               => ['PageController',            'about'],
        '/contact'             => ['PageController',            'contact'],
        '/privacy'             => ['PageController',            'privacy'],
        '/terms'               => ['PageController',            'terms'],
        '/services'            => ['PageController',            'services'],
        // Admin users
        '/admin/users'         => ['AdminUserController',       'index'],
    ],
    'POST' => [
        '/login'               => ['AuthController',            'login'],
        '/register'            => ['AuthController',            'register'],
        '/logout'              => ['AuthController',            'logout'],
        '/admin/settings'      => ['AdminController',           'saveSettings'],
        '/admin/pricing'       => ['AdminController',           'savePricing'],
        '/admin/pricing/recalc'=> ['AdminController',           'recalcCatalog'],
        '/admin/products'      => ['AdminProductController',    'store'],
        '/admin/categories'    => ['AdminCategoryController',   'store'],
        '/admin/coupons'       => ['AdminCouponController',     'store'],
        '/cart/add'            => ['CartController',            'add'],
        '/cart/update'         => ['CartController',            'update'],
        '/cart/remove'         => ['CartController',            'remove'],
        '/cart/coupon'         => ['CartController',            'applyCoupon'],
        '/cart/coupon/remove'  => ['CartController',            'removeCoupon'],
        '/checkout'            => ['CheckoutController',        'process'],
        '/checkout/process'    => ['CheckoutController',        'process'],
        '/product/review'      => ['ShopController',            'submitReview'],
        // Automation API (n8n) — auth via X-API-Key header
        '/api/product/create'       => ['ApiController',             'createProduct'],
        '/api/product/update-price' => ['ApiController',             'updatePrice'],
        '/api/product/publish'      => ['ApiController',             'publish'],
        '/print-order'              => ['PrintOrderController',       'store'],
        '/admin/print-options'      => ['AdminPrintOrderController',  'saveOptions'],
        '/admin/print-options/new'  => ['AdminPrintOrderController',  'createOption'],
        '/admin/portfolio'          => ['AdminPortfolioController',   'store'],
        '/admin/blog'               => ['AdminBlogController',        'store'],
        '/admin/blog/categories'    => ['AdminBlogController',        'storeCategory'],
        // Account POST
        '/contact'                  => ['PageController',             'submitContact'],
        '/account/profile'          => ['AccountController',          'updateProfile'],
        '/account/addresses'        => ['AccountController',          'addAddress'],
    ],
];

// ─── Pattern routes (dynamic segments) ───────────────────────
$patternRoutes = [
    'GET' => [
        '#^/shop/([a-z0-9-]+)$#'                            => ['ShopController',          'index',        ['shopSlug']],
        '#^/shop/([a-z0-9-]+)/([a-z0-9-]+)$#'               => ['ShopController',          'product',      ['shopSlug','productSlug']],
        '#^/product/([a-z0-9-]+)$#'                          => ['ShopController',          'productBySlug',['slug']],
        '#^/admin/products/(\d+)/edit$#'                     => ['AdminProductController',  'edit',         ['id']],
        '#^/admin/products/(\d+)/images$#'                   => ['AdminProductController',  'edit',         ['id']],
        '#^/admin/categories/(\d+)/edit$#'                   => ['AdminCategoryController', 'edit',         ['id']],
        '#^/admin/orders/(\d+)$#'                            => ['AdminOrderController',    'show',         ['id']],
        '#^/admin/coupons/(\d+)/edit$#'                      => ['AdminCouponController',   'edit',         ['id']],
        '#^/portfolio/(\d+)$#'                               => ['PortfolioController',       'show',         ['id']],
        '#^/blog/category/([a-z0-9\-]+)$#'                  => ['BlogController',            'category',     ['slug']],
        '#^/blog/([a-z0-9\-]+)$#'                           => ['BlogController',            'show',         ['slug']],
        '#^/admin/print-orders/(\d+)$#'                     => ['AdminPrintOrderController', 'show',         ['id']],
        '#^/admin/print-orders/file/(\d+)$#'                => ['AdminPrintOrderController', 'downloadFile', ['fileId']],
        '#^/admin/portfolio/(\d+)/edit$#'                   => ['AdminPortfolioController',  'edit',         ['id']],
        '#^/admin/blog/(\d+)/edit$#'                        => ['AdminBlogController',       'edit',         ['id']],
        '#^/admin/blog/categories/(\d+)/edit$#'             => ['AdminBlogController',       'editCategory', ['id']],
        '#^/account/orders/(\d+)$#'                         => ['AccountController',          'orderDetail',  ['id']],
        '#^/account/addresses/(\d+)/edit$#'                 => ['AccountController',          'editAddress',  ['id']],
        '#^/admin/users/(\d+)$#'                            => ['AdminUserController',         'show',         ['id']],
    ],
    'POST' => [
        '#^/admin/products/(\d+)$#'                          => ['AdminProductController',  'update',       ['id']],
        '#^/admin/products/(\d+)/delete$#'                   => ['AdminProductController',  'delete',       ['id']],
        '#^/admin/products/(\d+)/images$#'                   => ['AdminProductController',  'uploadImage',  ['id']],
        '#^/admin/categories/(\d+)$#'                        => ['AdminCategoryController', 'update',       ['id']],
        '#^/admin/categories/(\d+)/delete$#'                 => ['AdminCategoryController', 'delete',       ['id']],
        '#^/admin/orders/(\d+)/status$#'                     => ['AdminOrderController',    'updateStatus', ['id']],
        '#^/admin/coupons/(\d+)$#'                           => ['AdminCouponController',   'update',       ['id']],
        '#^/admin/coupons/(\d+)/delete$#'                    => ['AdminCouponController',   'delete',       ['id']],
        '#^/admin/print-orders/(\d+)/status$#'              => ['AdminPrintOrderController', 'updateStatus', ['id']],
        '#^/admin/print-options/(\d+)/delete$#'             => ['AdminPrintOrderController', 'deleteOption', ['id']],
        '#^/admin/portfolio/(\d+)$#'                        => ['AdminPortfolioController',  'update',       ['id']],
        '#^/admin/portfolio/(\d+)/delete$#'                 => ['AdminPortfolioController',  'delete',       ['id']],
        '#^/admin/portfolio/(\d+)/images$#'                 => ['AdminPortfolioController',  'uploadImage',  ['id']],
        '#^/admin/portfolio/image/(\d+)/delete$#'           => ['AdminPortfolioController',  'deleteImage',  ['imageId']],
        '#^/admin/blog/(\d+)$#'                             => ['AdminBlogController',       'update',       ['id']],
        '#^/admin/blog/(\d+)/delete$#'                      => ['AdminBlogController',       'delete',       ['id']],
        '#^/admin/blog/categories/(\d+)$#'                  => ['AdminBlogController',       'updateCategory',['id']],
        '#^/admin/blog/categories/(\d+)/delete$#'           => ['AdminBlogController',       'deleteCategory',['id']],
        '#^/account/addresses/(\d+)/delete$#'               => ['AccountController',          'deleteAddress', ['id']],
        '#^/account/addresses/(\d+)/default$#'              => ['AccountController',          'setDefaultAddress', ['id']],
        '#^/account/addresses/(\d+)$#'                      => ['AccountController',          'updateAddress', ['id']],
        '#^/admin/users/(\d+)/toggle$#'                     => ['AdminUserController',         'toggleStatus',  ['id']],
        '#^/admin/users/(\d+)/ban$#'                        => ['AdminUserController',         'ban',           ['id']],
        '#^/admin/users/(\d+)/admin$#'                      => ['AdminUserController',         'makeAdmin',     ['id']],
    ],
];

// ─── Routing ─────────────────────────────────────────────────
$GLOBALS['routeParams'] = [];

require_once dirname(__DIR__) . '/app/Controllers/BaseController.php';

// 1. Flat route lookup
$action = $routes[$method][$uri] ?? null;

if (!$action) {
    // 2. Pattern route lookup
    foreach ($patternRoutes[$method] ?? [] as $pattern => $def) {
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // remove full match
            [$controllerClass, $actionMethod, $paramNames] = $def;
            foreach ($paramNames as $i => $name) {
                $GLOBALS['routeParams'][$name] = $matches[$i] ?? '';
            }
            $action = [$controllerClass, $actionMethod];
            break;
        }
    }
}

if ($action) {
    [$controllerClass, $actionMethod] = $action;
    $controller = new $controllerClass($db);
    // Pass route params as arguments if method accepts them
    $params = array_values($GLOBALS['routeParams']);
    if ($params) {
        $controller->$actionMethod(...array_map(fn($p) => is_numeric($p) ? (int)$p : $p, $params));
    } else {
        $controller->$actionMethod();
    }
} else {
    http_response_code(404);
    $controller = new HomeController($db);
    $controller->error404();
}
