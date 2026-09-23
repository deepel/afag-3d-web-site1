<?php
declare(strict_types=1);

class CartController extends BaseController
{
    public function index(): void
    {
        $items    = Cart::items($this->db);
        $subtotal = (int)($_SESSION['_cart_subtotal'] ?? 0);
        $coupon   = Cart::getCoupon();
        $discount = $coupon ? Coupon::calculate($coupon, $subtotal) : 0;

        // Shipping
        $shippingCost   = $this->getShippingCost($subtotal);
        $total          = $subtotal - $discount + $shippingCost;

        $this->render('cart.index', [
            'title'        => 'سبد خرید',
            'items'        => $items,
            'subtotal'     => $subtotal,
            'discount'     => $discount,
            'coupon'       => $coupon,
            'shippingCost' => $shippingCost,
            'total'        => $total,
        ]);
    }

    public function add(): void
    {
        if (!CSRF::validateReusable($_POST['csrf_token'] ?? '')) {
            $this->json(['ok' => false, 'msg' => 'خطای امنیتی.']);
        }
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty       = max(1, (int)($_POST['qty'] ?? 1));
        $result    = Cart::add($this->db, $productId, $qty);
        $this->json($result);
    }

    public function update(): void
    {
        if (!CSRF::validateReusable($_POST['csrf_token'] ?? '')) {
            $this->json(['ok' => false, 'msg' => 'خطای امنیتی.']);
        }
        $productId = (int)($_POST['product_id'] ?? 0);
        $qty       = (int)($_POST['qty'] ?? 0);
        Cart::update($productId, $qty);
        $this->json(['ok' => true, 'count' => Cart::count()]);
    }

    public function remove(): void
    {
        if (!CSRF::validateReusable($_POST['csrf_token'] ?? '')) {
            $this->json(['ok' => false, 'msg' => 'خطای امنیتی.']);
        }
        $productId = (int)($_POST['product_id'] ?? 0);
        Cart::remove($productId);
        $this->json(['ok' => true, 'count' => Cart::count()]);
    }

    public function applyCoupon(): void
    {
        if (!CSRF::validateReusable($_POST['csrf_token'] ?? '')) {
            $this->json(['ok' => false, 'msg' => 'خطای امنیتی.']);
        }
        $code   = trim($_POST['code'] ?? '');
        $result = Cart::applyCoupon($this->db, $code);
        $this->json($result);
    }

    public function removeCoupon(): void
    {
        if (!CSRF::validateReusable($_POST['csrf_token'] ?? '')) {
            $this->json(['ok' => false, 'msg' => 'خطای امنیتی.']);
        }
        Cart::removeCoupon();
        $this->json(['ok' => true]);
    }

    private function getShippingCost(int $subtotal): int
    {
        $stmt = $this->db->query(
            "SELECT `key`, `value` FROM settings WHERE `key` IN ('shipping_cost','shipping_free_above','shipping_method')"
        );
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) $settings[$r['key']] = $r['value'];

        $method    = $settings['shipping_method']    ?? 'fixed';
        $cost      = (int)($settings['shipping_cost']       ?? 0);
        $freeAbove = (int)($settings['shipping_free_above'] ?? 0);

        if ($freeAbove > 0 && $subtotal >= $freeAbove) return 0;
        if ($method === 'fixed') return $cost;
        return $cost;
    }
}
