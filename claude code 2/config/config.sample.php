<?php
/**
 * afag3d — Configuration Sample
 * Copy this file to config.php and fill in your values.
 */

// ─── Environment ───────────────────────────────────────────────────────────────
define('ENV', 'production'); // 'development' | 'production'

// ─── Application ───────────────────────────────────────────────────────────────
define('APP_NAME',    'افگ تری‌دی');
define('APP_NAME_EN', 'afag3d');
define('APP_URL',     'https://yourdomain.com');  // no trailing slash
define('BASE_URL',    '');                         // sub-directory prefix, e.g. '/afag3d' or ''
define('APP_KEY',     'CHANGE_ME_32_CHAR_RANDOM_STRING!!');  // 32+ random chars
define('APP_LOCALE',  'fa');
define('APP_TIMEZONE','Asia/Tehran');

// ─── Database ──────────────────────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_PORT',    '3306');
define('DB_NAME',    'afag3d');
define('DB_USER',    'afag3d_user');
define('DB_PASS',    'CHANGE_ME_DB_PASSWORD');
define('DB_CHARSET', 'utf8mb4');

// ─── Paths ─────────────────────────────────────────────────────────────────────
define('ROOT_PATH',    dirname(__DIR__));
define('PUBLIC_PATH',  ROOT_PATH . '/public');
define('UPLOAD_PATH',  PUBLIC_PATH . '/uploads');
define('UPLOAD_URL',   BASE_URL . '/uploads');

// ─── Upload limits ─────────────────────────────────────────────────────────────
define('MAX_FILE_SIZE',   10 * 1024 * 1024); // 10 MB
define('ALLOWED_IMAGES',  ['jpg','jpeg','png','webp','gif']);
define('ALLOWED_MODELS',  ['stl','obj','3mf','step','stp']);

// ─── Session ───────────────────────────────────────────────────────────────────
define('SESSION_NAME',     'afag3d_sess');
define('SESSION_LIFETIME', 7200); // 2 hours

// ─── Mail (optional) ───────────────────────────────────────────────────────────
define('MAIL_HOST',     'smtp.example.com');
define('MAIL_PORT',     587);
define('MAIL_USER',     'noreply@yourdomain.com');
define('MAIL_PASS',     'CHANGE_ME_MAIL_PASSWORD');
define('MAIL_FROM',     'noreply@yourdomain.com');
define('MAIL_FROM_NAME','afag3d');

// ─── SMS (optional — Kavenegar) ────────────────────────────────────────────────
define('SMS_API_KEY',  '');
define('SMS_SENDER',   '');

// ─── Pagination ────────────────────────────────────────────────────────────────
define('PER_PAGE', 12);

// ─── Currency ──────────────────────────────────────────────────────────────────
define('CURRENCY',        'تومان');
define('CURRENCY_RATE',   1);   // 1 = Toman, 10 = Rial

// ─── Automation API (n8n / Telegram bridge) ─────────────────────────────────────
// Secret token for /api/* endpoints, sent by n8n as the `X-API-Key` header.
// Use a long random value. Kept in config/ (outside public/) so it is private.
define('API_KEY', 'CHANGE_ME_to_a_long_random_value');

// ─── Rate limiting ─────────────────────────────────────────────────────────────
define('RATE_LIMIT_LOGIN',    5);   // max attempts per window
define('RATE_LIMIT_WINDOW',  900);  // 15 minutes in seconds

// ─── Debug ─────────────────────────────────────────────────────────────────────
define('DISPLAY_ERRORS', false);

if (DISPLAY_ERRORS) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

date_default_timezone_set(APP_TIMEZONE);
