<?php
declare(strict_types=1);

class Coupon
{
    static function findByCode(PDO $db, string $code): ?array
    {
        $stmt = $db->prepare("SELECT * FROM coupons WHERE code = :code LIMIT 1");
        $stmt->execute(['code' => strtoupper(trim($code))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    static function isValid(array $coupon, int $orderTotal): array
    {
        if (!(int)($coupon['status'] ?? 0)) {
            return ['ok' => false, 'msg' => 'این کد تخفیف غیرفعال است.'];
        }
        if (!empty($coupon['expires_at']) && strtotime($coupon['expires_at']) < time()) {
            return ['ok' => false, 'msg' => 'کد تخفیف منقضی شده است.'];
        }
        $usageLimit = (int)($coupon['uses_total'] ?? 0);
        $usedCount  = (int)($coupon['uses_count'] ?? 0);
        if ($usageLimit > 0 && $usedCount >= $usageLimit) {
            return ['ok' => false, 'msg' => 'ظرفیت استفاده از این کد تخفیف تمام شده.'];
        }
        if (!empty($coupon['min_order']) && $orderTotal < (int)$coupon['min_order']) {
            return ['ok' => false, 'msg' => 'حداقل مبلغ سفارش برای استفاده از این کد ' . number_format((int)$coupon['min_order']) . ' تومان است.'];
        }
        return ['ok' => true, 'msg' => ''];
    }

    static function use(PDO $db, int $couponId): void
    {
        $db->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = :id")
           ->execute(['id' => $couponId]);
    }

    static function calculate(array $coupon, int $subtotal): int
    {
        if ($coupon['type'] === 'percent') {
            $discount = (int)($subtotal * $coupon['value'] / 100);
            if (!empty($coupon['max_discount']) && $discount > (int)$coupon['max_discount']) {
                $discount = (int)$coupon['max_discount'];
            }
            return $discount;
        }
        // Fixed amount
        return min($subtotal, (int)$coupon['value']);
    }
}
