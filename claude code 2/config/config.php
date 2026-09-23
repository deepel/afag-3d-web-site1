<?php
/**
 * afag3d — Active Configuration
 * Generated from config.sample.php — edit values for your environment.
 */

// ─── Environment ───────────────────────────────────────────────────────────────
define('ENV', 'development'); // change to 'production' on live server

// ─── Application ───────────────────────────────────────────────────────────────
define('APP_NAME',    'افگ تری‌دی');
define('APP_NAME_EN', 'afag3d');
define('APP_URL',     'http://localhost');
define('BASE_URL',    '');
define('APP_KEY',     'mK9pL2xQwR7vN4tY8hJ3cF6uB1eD5sA0'); // 32-char random key
define('APP_LOCALE',  'fa');
define('APP_TIMEZONE','Asia/Tehran');

// ─── Database ──────────────────────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_PORT',    '3306');
define('DB_NAME',    'afag3d');
define('DB_USER',    'root');
define('DB_PASS',    '');  // set your DB password here
define('DB_CHARSET', 'utf8mb4');

// ─── Paths ─────────────────────────────────────────────────────────────────────
define('ROOT_PATH',    dirname(__DIR__));
define('PUBLIC_PATH',  ROOT_PATH . '/public');
define('UPLOAD_PATH',  PUBLIC_PATH . '/uploads');
define('UPLOAD_URL',   BASE_URL . '/uploads');

// ─── Upload limits ─────────────────────────────────────────────────────────────
define('MAX_FILE_SIZE',   10 * 1024 * 1024);
define('ALLOWED_IMAGES',  ['jpg','jpeg','png','webp','gif']);
define('ALLOWED_MODELS',  ['stl','obj','3mf','step','stp']);

// ─── Session ───────────────────────────────────────────────────────────────────
define('SESSION_NAME',     'afag3d_sess');
define('SESSION_LIFETIME', 7200);

// ─── Mail ──────────────────────────────────────────────────────────────────────
define('MAIL_HOST',     'smtp.example.com');
define('MAIL_PORT',     587);
define('MAIL_USER',     'noreply@example.com');
define('MAIL_PASS',     '');
define('MAIL_FROM',     'noreply@example.com');
define('MAIL_FROM_NAME','afag3d');

// ─── SMS ───────────────────────────────────────────────────────────────────────
define('SMS_API_KEY',  '');
define('SMS_SENDER',   '');

// ─── Pagination ────────────────────────────────────────────────────────────────
define('PER_PAGE', 12);

// ─── Currency ──────────────────────────────────────────────────────────────────
define('CURRENCY',        'تومان');
define('CURRENCY_RATE',   1);

// ─── Rate limiting ─────────────────────────────────────────────────────────────
define('RATE_LIMIT_LOGIN',    5);
define('RATE_LIMIT_WINDOW',  900);

// ─── ZarinPal ──────────────────────────────────────────────────────────────────
define('ZARINPAL_MERCHANT', 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX');
define('ZARINPAL_SANDBOX',  true); // set false on production

// ─── Automation API (n8n / Telegram bridge) ─────────────────────────────────────
// Secret token for the /api/* endpoints. Sent by n8n in the `X-API-Key` header.
// CHANGE THIS to a long random value on the live server. Lives here in config/
// (outside public/) so it is never web-readable.
define('API_KEY', 'CHANGE_ME_afag3d_api_8f3c1d6b9a2e4705c8d1');

// ─── Debug ─────────────────────────────────────────────────────────────────────
define('DISPLAY_ERRORS', ENV === 'development');

if (DISPLAY_ERRORS) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

date_default_timezone_set(APP_TIMEZONE);
