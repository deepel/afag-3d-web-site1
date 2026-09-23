-- ============================================================
-- afag3d — Automation Layer Migration
-- Incremental, non-destructive. Safe to run once on an existing DB.
-- See ASSUMPTIONS.md for the design decisions behind this migration.
-- ============================================================

-- ── products: new automation columns ───────────────────────
-- NOTE: the existing `status` enum ('active','draft','out_of_stock') is the
-- visibility flag and is left untouched. A SEPARATE `lifecycle_status`
-- column drives the automation pipeline (pending → coming_soon → available).
ALTER TABLE `products`
  ADD COLUMN `product_code`     VARCHAR(32)  NULL AFTER `sku`,
  ADD COLUMN `weight_grams`     INT          NULL AFTER `weight`,
  ADD COLUMN `print_hours`      DECIMAL(5,2) NULL AFTER `weight_grams`,
  ADD COLUMN `lifecycle_status` ENUM('pending','coming_soon','available') NOT NULL DEFAULT 'available' AFTER `status`,
  ADD COLUMN `archive_folder`   VARCHAR(255) NULL AFTER `lifecycle_status`,
  ADD COLUMN `auto_created`     TINYINT(1)   NOT NULL DEFAULT 0 AFTER `archive_folder`,
  ADD COLUMN `price_is_manual`  TINYINT(1)   NOT NULL DEFAULT 0 AFTER `auto_created`,
  MODIFY COLUMN `price` DECIMAL(12,0) NULL;

-- Unique product code (added separately so the ALTER above stays portable)
ALTER TABLE `products`
  ADD UNIQUE KEY `uq_product_code` (`product_code`);

-- Backfill codes for existing products so orders/admin always show one.
UPDATE `products`
   SET `product_code` = CONCAT('AFAG-', LPAD(`id`, 4, '0'))
 WHERE `product_code` IS NULL;

-- ── pricing_settings: single-row coefficient table ─────────
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

-- ── Schema/code alignment (pre-existing gap) ────────────────
-- The Product model + admin controller already write these columns, but the
-- base schema never created them, so admin add/edit product was broken.
-- Added here (nullable, additive) to make the existing CRUD work. See ASSUMPTIONS.md.
ALTER TABLE `products`
  ADD COLUMN `short_desc`    VARCHAR(400)  NULL AFTER `description`,
  ADD COLUMN `compare_price` DECIMAL(12,0) NULL AFTER `price`,
  ADD COLUMN `image`         VARCHAR(255)  NULL AFTER `sale_price`;
