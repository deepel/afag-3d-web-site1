<?php
declare(strict_types=1);

class Cart
{
    private const KEY = 'cart';

    static function get(): array
    {
        return $_SESSION[self::KEY] ?? [];
    }

    static function add(PDO $db, int $productId, int $qty = 1): array
    {
        $product = Product::getById($db, $productId);
        if (!$product) {
            return ['ok' => false, 'msg' => 'محصول یافت نشد.'];
        }
        if ($product['status'] !== 'active') {
            return ['ok' => false, 'msg' => 'این محصول در دسترس نیست.'];
        }
        if ($product['stock'] < 1) {
            return ['ok' => false, 'msg' => 'موجودی کافی نیست.'];
        }

        $cart = self::get();
        $current = $cart[$productId] ?? 0;
        $newQty  = $current + $qty;
        if ($newQty > $product['stock']) {
            $newQty = $product['stock'];
        }
        $cart[$productId] = $newQty;
        $_SESSION[self::KEY] = $cart;

        return ['ok' => true, 'msg' => 'به سبد اضافه شد.', 'count' => self::count()];
    }

    static function update(int $productId, int $qty): void
    {
        $cart = self::get();
        if ($qty <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $qty;
        }
        $_SESSION[self::KEY] = $cart;
    }

    static function remove(int $productId): void
    {
        $cart = self::get();
        unset($cart[$productId]);
        $_SESSION[self::KEY] = $cart;
    }

    static function clear(): void
    {
        $_SESSION[self::KEY] = [];
        unset($_SESSION['coupon']);
    }

    static function count(): int
    {
        return array_sum(self::get());
    }

    static function subtotal(): int
    {
        // Can't calculate without DB; use items() for that
        return (int)($_SESSION['_cart_subtotal'] ?? 0);
    }

    static function total(): int
    {
        return (int)($_SESSION['_cart_total'] ?? 0);
    }

    static function items(PDO $db): array
    {
        $cart  = self::get();
        if (!$cart) return [];
        $ids   = array_keys($cart);
        $in    = implode(',', array_fill(0, count($ids), '?'));
        $stmt  = $db->prepare(
            "SELECT p.*, s.slug AS shop_slug,
                (SELECT pi.path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_cover = 1 LIMIT 1) AS main_image
             FROM products p
             LEFT JOIN shops s ON s.id = p.shop_id
             WHERE p.id IN ($in)"
        );
        $stmt->execute($ids);
        $products = [];
        foreach ($stmt->fetchAll() as $p) {
            $products[$p['id']] = $p;
        }

        $items = [];
        $subtotal = 0;
        foreach ($cart as $productId => $qty) {
            if (!isset($products[$productId])) continue;
            $p   = $products[$productId];
            $line = (int)$p['price'] * $qty;
            $subtotal += $line;
            $items[] = [
                'product'  => $p,
                'qty'      => $qty,
                'subtotal' => $line,
            ];
        }

        // Store for quick access
        $_SESSION['_cart_subtotal'] = $subtotal;
        $coupon   = self::getCoupon();
        $discount = 0;
        if ($coupon) {
            $discount = Coupon::calculate($coupon, $subtotal);
        }
        $_SESSION['_cart_total'] = $subtotal - $discount;

        return $items;
    }

    static function applyCoupon(PDO $db, string $code): array
    {
        $coupon = Coupon::findByCode($db, $code);
        if (!$coupon) {
            return ['ok' => false, 'msg' => 'کد تخفیف معتبر نیست.'];
        }
        // Need subtotal; build items first
        self::items($db);
        $subtotal = (int)($_SESSION['_cart_subtotal'] ?? 0);
        $check    = Coupon::isValid($coupon, $subtotal);
        if (!$check['ok']) {
            return $check;
        }
        $_SESSION['coupon'] = $coupon;
        $discount = Coupon::calculate($coupon, $subtotal);
        return ['ok' => true, 'discount' => $discount, 'msg' => 'کد تخفیف اعمال شد.'];
    }

    static function getCoupon(): ?array
    {
        return $_SESSION['coupon'] ?? null;
    }

    static function removeCoupon(): void
    {
        unset($_SESSION['coupon']);
    }
}
