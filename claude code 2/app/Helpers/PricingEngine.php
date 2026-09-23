<?php
declare(strict_types=1);
/**
 * afag3d — Parametric Pricing Engine
 *
 * Cost  = (weight_grams × filament_rate_per_gram)
 *       + (print_hours  × machine_rate_per_hour)
 *       + fixed_overhead
 * Sell  = round(Cost × profit_multiplier)
 *
 * Coefficients live in the `pricing_settings` table (single row, id=1),
 * never hard-coded. This logic is part of the CRITICAL PHP layer — it must
 * work with no external services.
 */
class PricingEngine
{
    /** Read the single coefficients row (with safe fallbacks). */
    public static function settings(PDO $db): array
    {
        $row = $db->query('SELECT * FROM pricing_settings WHERE id = 1 LIMIT 1')->fetch();
        return [
            'filament_rate_per_gram' => (int)   ($row['filament_rate_per_gram'] ?? 500),
            'machine_rate_per_hour'  => (int)   ($row['machine_rate_per_hour']  ?? 25000),
            'fixed_overhead'         => (int)   ($row['fixed_overhead']         ?? 40000),
            'profit_multiplier'      => (float) ($row['profit_multiplier']      ?? 2.5),
        ];
    }

    /** Compute the sell price (Toman) from weight + hours using given coefficients. */
    public static function compute(int $grams, float $hours, array $s): int
    {
        $cost = ($grams * $s['filament_rate_per_gram'])
              + ($hours * $s['machine_rate_per_hour'])
              + $s['fixed_overhead'];
        return (int) round($cost * $s['profit_multiplier']);
    }

    /**
     * Compute price for a product row. Returns null if it lacks weight/hours.
     * Reads coefficients itself unless $s is supplied (for batch loops).
     */
    public static function computeForProduct(PDO $db, array $product, ?array $s = null): ?int
    {
        $grams = $product['weight_grams'] ?? null;
        $hours = $product['print_hours']  ?? null;
        if ($grams === null || $hours === null || $grams === '' || $hours === '') {
            return null;
        }
        $s ??= self::settings($db);
        return self::compute((int) $grams, (float) $hours, $s);
    }

    /** Persist the four coefficients (validated, clamped to sane minimums). */
    public static function saveSettings(PDO $db, array $data): void
    {
        $stmt = $db->prepare(
            'UPDATE pricing_settings SET
                filament_rate_per_gram = :f,
                machine_rate_per_hour  = :m,
                fixed_overhead         = :o,
                profit_multiplier      = :p
             WHERE id = 1'
        );
        $stmt->execute([
            'f' => max(0, (int) ($data['filament_rate_per_gram'] ?? 0)),
            'm' => max(0, (int) ($data['machine_rate_per_hour']  ?? 0)),
            'o' => max(0, (int) ($data['fixed_overhead']         ?? 0)),
            'p' => max(0.1, round((float) ($data['profit_multiplier'] ?? 1), 1)),
        ]);
    }

    /**
     * Recompute price for every product that has weight + hours and is NOT
     * manually priced. Returns the number of products updated.
     */
    public static function recalcCatalog(PDO $db): int
    {
        $s    = self::settings($db);
        $rows = $db->query(
            'SELECT id, weight_grams, print_hours
               FROM products
              WHERE weight_grams IS NOT NULL
                AND print_hours  IS NOT NULL
                AND price_is_manual = 0'
        )->fetchAll();

        $upd = $db->prepare('UPDATE products SET price = :price WHERE id = :id');
        $n   = 0;
        foreach ($rows as $r) {
            $price = self::compute((int) $r['weight_grams'], (float) $r['print_hours'], $s);
            $upd->execute(['price' => $price, 'id' => (int) $r['id']]);
            $n++;
        }
        return $n;
    }
}
