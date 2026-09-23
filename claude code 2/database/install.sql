-- ============================================================
-- afag3d — Database Installation Script
-- MySQL 5.7+ / MariaDB 10.3+  |  Charset: utf8mb4
-- Run: mysql -u root -p afag3d < install.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ─── Settings ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `settings` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key`        VARCHAR(120) NOT NULL UNIQUE,
  `value`      TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Users ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`           VARCHAR(120) NOT NULL,
  `mobile`         VARCHAR(15)  NOT NULL UNIQUE,
  `email`          VARCHAR(180) UNIQUE,
  `password`       VARCHAR(255) NOT NULL,
  `role`           ENUM('admin','shop_owner','customer') NOT NULL DEFAULT 'customer',
  `status`         ENUM('active','inactive','banned') NOT NULL DEFAULT 'active',
  `avatar`         VARCHAR(255),
  `otp`            VARCHAR(10),
  `otp_expires_at` DATETIME,
  `last_login_at`  DATETIME,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Addresses ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `addresses` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT UNSIGNED NOT NULL,
  `title`       VARCHAR(80)  NOT NULL,
  `recipient`   VARCHAR(120) NOT NULL,
  `mobile`      VARCHAR(15)  NOT NULL,
  `province`    VARCHAR(60)  NOT NULL,
  `city`        VARCHAR(60)  NOT NULL,
  `address`     TEXT         NOT NULL,
  `postal_code` VARCHAR(15),
  `is_default`  TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Shops ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `shops` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `owner_id`    INT UNSIGNED NOT NULL,
  `name`        VARCHAR(160) NOT NULL,
  `slug`        VARCHAR(180) NOT NULL UNIQUE,
  `description` TEXT,
  `logo`        VARCHAR(255),
  `banner`      VARCHAR(255),
  `status`      ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending',
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Categories ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `categories` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `parent_id`   INT UNSIGNED,
  `name`        VARCHAR(120) NOT NULL,
  `slug`        VARCHAR(140) NOT NULL UNIQUE,
  `description` TEXT,
  `image`       VARCHAR(255),
  `sort_order`  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `status`      TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Products ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `products` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `shop_id`      INT UNSIGNED NOT NULL,
  `name`         VARCHAR(220) NOT NULL,
  `slug`         VARCHAR(240) NOT NULL UNIQUE,
  `sku`          VARCHAR(60)  UNIQUE,
  `product_code` VARCHAR(32)  UNIQUE COMMENT 'AFAG-XXXX automation code',
  `description`  TEXT,
  `short_desc`   VARCHAR(400),
  `price`        DECIMAL(12,0) NULL COMMENT 'NULL = not priced yet',
  `compare_price` DECIMAL(12,0),
  `sale_price`   DECIMAL(12,0),
  `image`        VARCHAR(255),
  `stock`        INT NOT NULL DEFAULT 0,
  `weight`       DECIMAL(8,2) COMMENT 'grams (legacy display field)',
  `weight_grams` INT          COMMENT 'pricing input — weight in grams',
  `print_hours`  DECIMAL(5,2) COMMENT 'pricing input — print time in hours',
  `status`       ENUM('active','draft','out_of_stock') NOT NULL DEFAULT 'draft',
  `lifecycle_status` ENUM('pending','coming_soon','available') NOT NULL DEFAULT 'available' COMMENT 'automation pipeline',
  `archive_folder` VARCHAR(255) COMMENT 'STL archive folder on owner PC (reference only)',
  `auto_created` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'created via automation API',
  `price_is_manual` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = manual price, skip recalculation',
  `is_featured`  TINYINT(1) NOT NULL DEFAULT 0,
  `views`        INT UNSIGNED NOT NULL DEFAULT 0,
  `meta_title`   VARCHAR(255),
  `meta_desc`    VARCHAR(400),
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`shop_id`) REFERENCES `shops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Pricing settings (single-row coefficient table) ─────────
CREATE TABLE IF NOT EXISTS `pricing_settings` (
  `id`                     TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `filament_rate_per_gram` INT          NOT NULL DEFAULT 500,
  `machine_rate_per_hour`  INT          NOT NULL DEFAULT 25000,
  `fixed_overhead`         INT          NOT NULL DEFAULT 40000,
  `profit_multiplier`      DECIMAL(3,1) NOT NULL DEFAULT 2.5,
  `updated_at`             TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO `pricing_settings` (`id`) VALUES (1);

-- ─── Product Images ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_images` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `path`       VARCHAR(255) NOT NULL,
  `alt`        VARCHAR(200),
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `is_cover`   TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Product Categories ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_categories` (
  `product_id`  INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`),
  FOREIGN KEY (`product_id`)  REFERENCES `products`(`id`)    ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Attributes ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `attributes` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL,
  `type`       ENUM('select','color','text') NOT NULL DEFAULT 'select',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attribute_values` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `attribute_id` INT UNSIGNED NOT NULL,
  `value`        VARCHAR(120) NOT NULL,
  `hex`          VARCHAR(10) COMMENT 'for color type',
  FOREIGN KEY (`attribute_id`) REFERENCES `attributes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_attributes` (
  `id`                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`         INT UNSIGNED NOT NULL,
  `attribute_value_id` INT UNSIGNED NOT NULL,
  `price_modifier`     DECIMAL(12,0) NOT NULL DEFAULT 0,
  `stock_modifier`     INT NOT NULL DEFAULT 0,
  FOREIGN KEY (`product_id`)         REFERENCES `products`(`id`)          ON DELETE CASCADE,
  FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Coupons ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `coupons` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code`          VARCHAR(40) NOT NULL UNIQUE,
  `type`          ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  `value`         DECIMAL(10,2) NOT NULL,
  `min_order`     DECIMAL(12,0) NOT NULL DEFAULT 0,
  `max_discount`  DECIMAL(12,0),
  `uses_total`    INT NOT NULL DEFAULT 0,
  `uses_per_user` TINYINT NOT NULL DEFAULT 1,
  `uses_count`    INT NOT NULL DEFAULT 0,
  `starts_at`     DATETIME,
  `expires_at`    DATETIME,
  `status`        TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Orders ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `orders` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`         INT UNSIGNED NOT NULL,
  `address_id`      INT UNSIGNED,
  `coupon_id`       INT UNSIGNED,
  `status`          ENUM('pending','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_status`  ENUM('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `subtotal`        DECIMAL(12,0) NOT NULL DEFAULT 0,
  `discount`        DECIMAL(12,0) NOT NULL DEFAULT 0,
  `shipping`        DECIMAL(12,0) NOT NULL DEFAULT 0,
  `tax`             DECIMAL(12,0) NOT NULL DEFAULT 0,
  `total`           DECIMAL(12,0) NOT NULL DEFAULT 0,
  `notes`           TEXT,
  `tracking_code`   VARCHAR(60),
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)    ON DELETE RESTRICT,
  FOREIGN KEY (`address_id`) REFERENCES `addresses`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`coupon_id`)  REFERENCES `coupons`(`id`)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Order Items ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`   INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED,
  `name`       VARCHAR(220) NOT NULL,
  `sku`        VARCHAR(60),
  `price`      DECIMAL(12,0) NOT NULL,
  `qty`        SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `options`    JSON,
  FOREIGN KEY (`order_id`)   REFERENCES `orders`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Payments ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `payments` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`       INT UNSIGNED NOT NULL,
  `user_id`        INT UNSIGNED NOT NULL,
  `amount`         DECIMAL(12,0) NOT NULL,
  `gateway`        VARCHAR(40) NOT NULL DEFAULT 'zarinpal',
  `authority`      VARCHAR(80),
  `ref_id`         VARCHAR(80),
  `status`         ENUM('pending','success','failed','refunded') NOT NULL DEFAULT 'pending',
  `raw_response`   JSON,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Reviews ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `reviews` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id`    INT UNSIGNED NOT NULL,
  `order_id`   INT UNSIGNED,
  `rating`     TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `title`      VARCHAR(200),
  `body`       TEXT,
  `status`     ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)  ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)     ON DELETE CASCADE,
  FOREIGN KEY (`order_id`)   REFERENCES `orders`(`id`)    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Print Options ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `print_options` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type`       ENUM('color','material','quality','print_type') NOT NULL,
  `name`       VARCHAR(100) NOT NULL,
  `name_fa`    VARCHAR(100) NOT NULL,
  `price_add`  DECIMAL(12,0) NOT NULL DEFAULT 0,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `status`     TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Print Orders ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `print_orders` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`       INT UNSIGNED NOT NULL,
  `status`        ENUM('pending','quoting','confirmed','printing','done','cancelled') NOT NULL DEFAULT 'pending',
  `color_id`      INT UNSIGNED,
  `material_id`   INT UNSIGNED,
  `quality_id`    INT UNSIGNED,
  `print_type_id` INT UNSIGNED,
  `quantity`      SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `notes`         TEXT,
  `quoted_price`  DECIMAL(12,0),
  `final_price`   DECIMAL(12,0),
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Print Files ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `print_files` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `print_order_id` INT UNSIGNED NOT NULL,
  `original_name`  VARCHAR(255) NOT NULL,
  `stored_name`    VARCHAR(255) NOT NULL,
  `mime`           VARCHAR(100),
  `size`           INT UNSIGNED,
  `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`print_order_id`) REFERENCES `print_orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Portfolio ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `portfolio` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(220) NOT NULL,
  `slug`        VARCHAR(240) NOT NULL UNIQUE,
  `description` TEXT,
  `cover`       VARCHAR(255),
  `client`      VARCHAR(120),
  `tags`        VARCHAR(255),
  `sort_order`  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `status`      TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `portfolio_images` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `portfolio_id` INT UNSIGNED NOT NULL,
  `path`         VARCHAR(255) NOT NULL,
  `caption`      VARCHAR(255),
  `sort_order`   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  FOREIGN KEY (`portfolio_id`) REFERENCES `portfolio`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Blog Categories ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(120) NOT NULL,
  `slug`       VARCHAR(140) NOT NULL UNIQUE,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `status`     TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Blog Posts ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `author_id`   INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED,
  `title`       VARCHAR(255) NOT NULL,
  `slug`        VARCHAR(280) NOT NULL UNIQUE,
  `excerpt`     TEXT,
  `body`        LONGTEXT,
  `cover`       VARCHAR(255),
  `tags`        VARCHAR(500),
  `status`      ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `views`       INT UNSIGNED NOT NULL DEFAULT 0,
  `meta_title`  VARCHAR(255),
  `meta_desc`   VARCHAR(400),
  `published_at` DATETIME,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`author_id`)   REFERENCES `users`(`id`)           ON DELETE RESTRICT,
  FOREIGN KEY (`category_id`) REFERENCES `blog_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Pages ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pages` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`      VARCHAR(220) NOT NULL,
  `slug`       VARCHAR(240) NOT NULL UNIQUE,
  `body`       LONGTEXT,
  `status`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Media ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `media` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT UNSIGNED NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `stored_name`  VARCHAR(255) NOT NULL,
  `path`         VARCHAR(500) NOT NULL,
  `mime`         VARCHAR(100),
  `size`         INT UNSIGNED,
  `alt`          VARCHAR(255),
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Rate Limits ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `action`        VARCHAR(64) NOT NULL,
  `ip`            VARCHAR(45) NOT NULL,
  `blocked_until` DATETIME NULL,
  `last_attempt`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_action_ip` (`action`,`ip`),
  INDEX `idx_last_attempt` (`last_attempt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Settings
INSERT INTO `settings` (`key`, `value`) VALUES
('site_name',          'افگ تری‌دی'),
('site_tagline',       'چاپ سه‌بعدی حرفه‌ای'),
('site_email',         'info@afag3d.ir'),
('site_phone',         '021-12345678'),
('site_address',       'تهران، خیابان ولیعصر'),
('site_logo',          ''),
('site_favicon',       ''),
('maintenance_mode',   '0'),
('order_prefix',       'AFG'),
('shipping_fee',       '35000'),
('free_shipping_from', '500000'),
('tax_rate',           '9'),
('instagram',          'https://instagram.com/afag3d'),
('telegram',           'https://t.me/afag3d'),
('whatsapp',           '09000000000'),
('seo_meta_desc',      'استودیوی چاپ سه‌بعدی افگ — طراحی، چاپ و فروش محصولات سه‌بعدی'),
('google_analytics',   ''),
('recaptcha_site_key', ''),
('recaptcha_secret',   '');

-- Admin user
-- Password for 'Admin@1234' — run database/seed_admin.php after install
INSERT INTO `users` (`name`, `mobile`, `email`, `password`, `role`, `status`) VALUES
('مدیر سایت', '09000000000', 'admin@afag3d.ir',
 '$2y$12$PLACEHOLDER_RUN_SEED_ADMIN_PHP_TO_SET_REAL_HASH_HERE000', 'admin', 'active');

-- Sample shops
INSERT INTO `shops` (`owner_id`, `name`, `slug`, `description`, `status`) VALUES
(1, 'فروشگاه مدل‌ها و اکسسوری', 'models', 'مدل‌های آماده چاپ، فیگورین، دکوری و لوازم جانبی', 'active'),
(1, 'لوازم و قطعات پرینتر', 'supplies', 'فیلامنت، رزین، قطعات و لوازم جانبی پرینترهای سه‌بعدی', 'active');

-- Categories
INSERT INTO `categories` (`parent_id`, `name`, `slug`, `sort_order`, `status`) VALUES
(NULL, 'دکوراسیون منزل',    'home-decor',   1, 1),
(NULL, 'اسباب‌بازی و سرگرمی','toys',         2, 1),
(NULL, 'ابزار و قطعات',     'tools-parts',   3, 1),
(NULL, 'هنر و مجسمه',       'art-sculpture', 4, 1),
(NULL, 'جواهرات',           'jewelry',       5, 1),
(NULL, 'آموزشی',            'educational',   6, 1);

-- Products
INSERT INTO `products` (`shop_id`,`name`,`slug`,`sku`,`description`,`price`,`sale_price`,`stock`,`weight`,`status`,`is_featured`) VALUES
(1, 'گلدان مدرن هندسی', 'modern-geometric-vase', 'AFG-001',
 'گلدان با طراحی هندسی مدرن، مناسب برای دکوراسیون داخلی. چاپ شده با پلاستیک PLA درجه یک.',
 250000, 199000, 15, 120, 'active', 1),

(1, 'قاب عکس دیواری مینیمال', 'minimal-wall-frame', 'AFG-002',
 'قاب عکس با طراحی مینیمال و مدرن، قابل نصب روی دیوار.',
 180000, NULL, 30, 85, 'active', 1),

(1, 'آرم‌متر رومیزی اسم', 'name-desk-nameplate', 'AFG-003',
 'آرم‌متر شخصی‌سازی شده با نام دلخواه، مناسب برای روی میز کار.',
 120000, NULL, 50, 60, 'active', 0),

(1, 'ماکت برج ایفل', 'eiffel-tower-model', 'AFG-004',
 'ماکت دقیق برج ایفل با جزئیات کامل، مناسب برای کلکسیون.',
 350000, 290000, 8, 200, 'active', 1),

(1, 'نگهدارنده گوشی رومیزی', 'phone-stand-desk', 'AFG-005',
 'پایه نگهدارنده گوشی با زاویه قابل تنظیم، مناسب برای تماشای فیلم.',
 95000, NULL, 40, 70, 'active', 0),

(2, 'فایل STL سر اژدها', 'dragon-head-stl', 'DIG-001',
 'فایل آماده چاپ سر اژدها با جزئیات بسیار دقیق. فرمت STL+OBJ.',
 45000, NULL, 999, NULL, 'active', 1),

(2, 'فایل STL ربات فضایی', 'space-robot-stl', 'DIG-002',
 'ربات فضایی مفصل‌دار برای چاپ تک‌تکه. مناسب برای چاپگرهای FDM.',
 65000, 50000, 999, NULL, 'active', 1),

(1, 'دارنده کابل‌های رومیزی', 'cable-management-desk', 'AFG-006',
 'سیستم مدیریت کابل برای میز کار. شامل ۵ عدد نگهدارنده.',
 75000, NULL, 25, 45, 'active', 0),

(1, 'ماسک تزئینی دیواری', 'decorative-wall-mask', 'AFG-007',
 'ماسک تزئینی با طراحی اصیل ایرانی برای نصب روی دیوار.',
 420000, 380000, 12, 180, 'active', 1),

(1, 'مدل علمی مولکول DNA', 'dna-molecule-model', 'AFG-008',
 'مدل آموزشی مولکول DNA با کدرنگی استاندارد. مناسب برای مدارس و دانشگاه‌ها.',
 280000, NULL, 20, 150, 'active', 0);

-- Product categories
INSERT INTO `product_categories` (`product_id`, `category_id`) VALUES
(1,1),(2,1),(3,1),(4,4),(5,3),(6,2),(7,2),(8,3),(9,4),(10,6);

-- Print options
INSERT INTO `print_options` (`type`,`name`,`name_fa`,`price_add`,`sort_order`) VALUES
-- colors
('color','White',     'سفید',    0,      1),
('color','Black',     'مشکی',    0,      2),
('color','Gray',      'خاکستری', 0,      3),
('color','Orange',    'نارنجی',  5000,   4),
-- materials
('material','PLA',    'پی‌ال‌ای',    0,       1),
('material','PETG',   'پت‌جی',       20000,   2),
('material','ABS',    'ای‌بی‌اس',    15000,   3),
('material','Resin',  'رزین',        50000,   4),
-- quality
('quality','Draft',   'پیش‌نویس',   -10000,  1),
('quality','Standard','استاندارد',   0,       2),
('quality','Fine',    'دقیق',        20000,   3),
('quality','Ultra',   'فوق‌دقیق',    40000,   4),
-- print type (only FDM and Resin offered)
('print_type','FDM',   'FDM',    0,       1),
('print_type','Resin', 'Resin',  60000,   2);

-- Portfolio
INSERT INTO `portfolio` (`title`,`slug`,`description`,`client`,`tags`,`is_featured`,`status`) VALUES
('ماکت معماری مجتمع مسکونی',   'architecture-residential',  'مدل سه‌بعدی کامل مجتمع مسکونی با جزئیات داخلی و خارجی برای ارائه به کارفرما.', 'شرکت ساختمانی آبادان',  'معماری,ماکت,ساختمان',  1, 1),
('فیگرین شخصیت‌های انیمیشن',   'animation-figurines',       'چاپ و رنگ‌آمیزی فیگرین‌های اختصاصی برای استودیو انیمیشن.',                     'استودیو آنیماکس',        'فیگرین,انیمیشن,رنگ',   1, 1),
('قطعات یدکی صنعتی',           'industrial-spare-parts',    'تولید سریع قطعات یدکی کمیاب برای خط تولید کارخانه.',                           'کارخانه صنعتی پارس',    'صنعتی,قطعات,تولید',    1, 1),
('جواهرات مکعبی هندسی',        'geometric-jewelry',         'طراحی و چاپ جواهرات رزینی با اشکال هندسی پیچیده.',                              'مستقل',                  'جواهر,رزین,هندسی',      0, 1),
('پروتز دست آموزشی',           'prosthetic-hand-educational','مدل آموزشی پروتز دست برای رشته پزشکی دانشگاه.',                                'دانشگاه علوم پزشکی',     'پزشکی,آموزشی,پروتز',   1, 1);

-- Blog categories
INSERT INTO `blog_categories` (`name`,`slug`,`sort_order`) VALUES
('راهنمای چاپ سه‌بعدی', 'printing-guide',  1),
('معرفی مواد',           'materials',       2),
('پروژه‌های نمونه',       'case-studies',    3);

-- Blog posts
INSERT INTO `blog_posts` (`author_id`,`category_id`,`title`,`slug`,`excerpt`,`body`,`status`,`published_at`) VALUES
(1, 1,
 'راهنمای کامل انتخاب مواد چاپ سه‌بعدی',
 'guide-to-3d-printing-materials',
 'در این مقاله با انواع مواد چاپ سه‌بعدی شامل PLA، PETG، ABS و رزین آشنا می‌شوید.',
 '<p>چاپ سه‌بعدی دنیای متنوعی از مواد را در اختیار شما می‌گذارد. هر ماده ویژگی‌های منحصربه‌فردی دارد که برای کاربردهای خاصی مناسب است.</p><h2>PLA</h2><p>PLA ساده‌ترین ماده برای شروع است. زیست‌تجزیه‌پذیر، بی‌بو و با کمترین تنظیمات قابل چاپ است.</p><h2>PETG</h2><p>PETG ترکیبی از PLA و ABS است. انعطاف‌پذیر، مقاوم در برابر ضربه و مقاوم در برابر حرارت نسبی.</p>',
 'published', NOW()),

(1, 3,
 'پروژه چاپ ماکت معماری برای ارائه سرمایه‌گذار',
 'architecture-model-case-study',
 'چگونه یک ماکت معماری دقیق با چاپگر FDM ساختیم و چه چالش‌هایی داشتیم.',
 '<p>یکی از چالش‌برانگیزترین پروژه‌های ما ساخت ماکت یک مجتمع مسکونی ۱۲ طبقه در مقیاس ۱:۱۰۰ بود.</p><p>مدل‌سازی در Fusion360 انجام شد و چاپ در ۴۸ قطعه مجزا با PLA خاکستری.</p>',
 'published', NOW()),

(1, 2,
 'رزین یا FDM؟ کدام را انتخاب کنیم',
 'resin-vs-fdm-comparison',
 'مقایسه جامع دو روش اصلی چاپ سه‌بعدی برای کمک به انتخاب بهتر شما.',
 '<p>FDM و SLA (رزین) دو روش اصلی چاپ سه‌بعدی هستند که هر کدام مزایا و معایب خاص خود را دارند.</p><h2>FDM</h2><p>ارزان‌تر، سریع‌تر، مناسب برای قطعات بزرگ و کاربردی.</p><h2>SLA/رزین</h2><p>دقت بسیار بالا، سطح صاف، مناسب برای جواهرات و فیگرین.</p>',
 'published', NOW());

-- Pages
INSERT INTO `pages` (`title`,`slug`,`body`,`status`) VALUES
('درباره ما', 'about',
 '<h1>درباره افگ تری‌دی</h1><p>افگ تری‌دی یک استودیوی تخصصی چاپ سه‌بعدی در تهران است که از سال ۱۴۰۰ فعالیت می‌کند.</p>',
 1),
('تماس با ما', 'contact',
 '<h1>تماس با ما</h1><p>برای ارتباط با ما از فرم زیر استفاده کنید یا با شماره ۰۲۱-۱۲۳۴۵۶۷۸ تماس بگیرید.</p>',
 1),
('قوانین و مقررات', 'terms',
 '<h1>قوانین و مقررات</h1><p>استفاده از خدمات افگ تری‌دی به منزله پذیرش قوانین زیر است.</p>',
 1);

-- ─── Contact Messages ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(120) NOT NULL,
  `contact`    VARCHAR(120) NOT NULL,
  `subject`    VARCHAR(200) NOT NULL,
  `message`    TEXT NOT NULL,
  `ip`         VARCHAR(45),
  `read_at`    DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Extra pages seed ─────────────────────────────────────────────────────
INSERT IGNORE INTO `pages` (`slug`, `title`, `body`, `status`) VALUES
('about',   'درباره ما',          '<p>استودیوی تخصصی چاپ سه‌بعدی افگ — ۹ سال تجربه، ۴۸۰+ پروژه، ۱۲+ متریال.</p>', 1),
('contact', 'تماس با ما',         '<p>برای ارتباط با ما از فرم تماس استفاده کنید.</p>',                             1),
('privacy', 'حریم خصوصی',        '<p>سیاست حریم خصوصی استودیوی افگ تری‌دی.</p>',                                  1),
('terms',   'قوانین و مقررات',    '<p>قوانین و شرایط استفاده از خدمات افگ تری‌دی.</p>',                            1);

SET FOREIGN_KEY_CHECKS = 1;
