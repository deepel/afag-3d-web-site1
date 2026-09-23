<?php
declare(strict_types=1);

class CheckoutController extends BaseController
{
    public function index(): void
    {
        $items = Cart::items($this->db);
        if (!$items) {
            Flash::set('error', 'سبد خرید شما خالی است.');
            $this->redirect('/cart');
        }

        $subtotal     = (int)($_SESSION['_cart_subtotal'] ?? 0);
        $coupon       = Cart::getCoupon();
        $discount     = $coupon ? Coupon::calculate($coupon, $subtotal) : 0;
        $shippingCost = $this->getShippingCost($subtotal);
        $total        = $subtotal - $discount + $shippingCost;

        $user = Auth::check() ? Auth::user() : null;

        $this->render('checkout.index', [
            'title'        => 'تکمیل خرید',
            'items'        => $items,
            'subtotal'     => $subtotal,
            'discount'     => $discount,
            'coupon'       => $coupon,
            'shippingCost' => $shippingCost,
            'total'        => $total,
            'user'         => $user,
        ]);
    }

    public function process(): void
    {
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/checkout');
        }

        $items = Cart::items($this->db);
        if (!$items) {
            Flash::set('error', 'سبد خرید خالی است.');
            $this->redirect('/cart');
        }

        // Validate
        $name   = trim($_POST['name']   ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        if (!$name || !$mobile) {
            Flash::set('error', 'نام و شماره موبایل الزامی است.');
            $this->redirect('/checkout');
        }
        if (!preg_match('/^09[0-9]{9}$/', $mobile)) {
            Flash::set('error', 'شماره موبایل معتبر نیست.');
            $this->redirect('/checkout');
        }

        $subtotal     = (int)($_SESSION['_cart_subtotal'] ?? 0);
        $coupon       = Cart::getCoupon();
        $couponId     = $coupon ? (int)$coupon['id'] : null;
        $discount     = $coupon ? Coupon::calculate($coupon, $subtotal) : 0;
        $shippingCost = $this->getShippingCost($subtotal);
        $total        = $subtotal - $discount + $shippingCost;

        // Create order
        $orderId = Order::create($this->db, [
            'user_id'       => Auth::check() ? Auth::id() : null,
            'name'          => $name,
            'mobile'        => $mobile,
            'email'         => trim($_POST['email']       ?? ''),
            'province'      => trim($_POST['province']    ?? ''),
            'city'          => trim($_POST['city']        ?? ''),
            'address'       => trim($_POST['address']     ?? ''),
            'postal_code'   => trim($_POST['postal_code'] ?? ''),
            'subtotal'      => $subtotal,
            'shipping_cost' => $shippingCost,
            'discount'      => $discount,
            'total'         => $total,
            'coupon_id'     => $couponId,
            'status'        => 'pending',
            'notes'         => trim($_POST['notes'] ?? ''),
            'items'         => $items,
        ]);

        if ($coupon) {
            Coupon::use($this->db, $coupon['id']);
        }

        // ZarinPal request
        require_once dirname(__DIR__) . '/Services/ZarinPal.php';
        $zp       = new ZarinPal();
        $callback = APP_URL . BASE_URL . '/checkout/callback';
        $result   = $zp->request($total, 'پرداخت سفارش #' . $orderId, $callback, $mobile);

        if (!$result['ok']) {
            Order::updateStatus($this->db, $orderId, 'failed');
            Flash::set('error', 'خطا در اتصال به درگاه: ' . $result['error']);
            $this->redirect('/checkout');
        }

        // Save payment record
        Payment::create($this->db, [
            'order_id'  => $orderId,
            'amount'    => $total,
            'authority' => $result['authority'],
            'status'    => 'pending',
        ]);

        // Store orderId in session for callback
        $_SESSION['pending_order_id'] = $orderId;

        header('Location: ' . $result['url']);
        exit;
    }

    public function callback(): void
    {
        $authority = $_GET['Authority'] ?? '';
        $status    = $_GET['Status']   ?? '';

        if ($status !== 'OK' || !$authority) {
            $this->redirect('/checkout/failed');
        }

        $payment = Payment::getByAuthority($this->db, $authority);
        if (!$payment) {
            $this->redirect('/checkout/failed');
        }

        require_once dirname(__DIR__) . '/Services/ZarinPal.php';
        $zp     = new ZarinPal();
        $result = $zp->verify($authority, (int)$payment['amount']);

        if ($result['ok']) {
            Payment::updateByAuthority($this->db, $authority, 'paid', $result['ref_id']);
            Order::updatePayment($this->db, (int)$payment['order_id'], $result['ref_id'], $authority);

            // Reduce stock
            $items = Order::getItems($this->db, (int)$payment['order_id']);
            foreach ($items as $item) {
                Product::updateStock($this->db, (int)$item['product_id'], -(int)$item['qty']);
            }

            Cart::clear();
            $_SESSION['last_order_id'] = $payment['order_id'];
            $_SESSION['last_ref_id']   = $result['ref_id'];

            $this->redirect('/checkout/success');
        } else {
            Payment::updateByAuthority($this->db, $authority, 'failed');
            Order::updateStatus($this->db, (int)$payment['order_id'], 'failed');
            $this->redirect('/checkout/failed');
        }
    }

    public function success(): void
    {
        $orderId = $_SESSION['last_order_id'] ?? null;
        $refId   = $_SESSION['last_ref_id']   ?? null;
        $order   = $orderId ? Order::getById($this->db, (int)$orderId) : null;

        $this->render('checkout.success', [
            'title' => 'پرداخت موفق',
            'order' => $order,
            'refId' => $refId,
        ]);
    }

    public function failed(): void
    {
        $this->render('checkout.failed', [
            'title' => 'پرداخت ناموفق',
        ]);
    }

    private function getShippingCost(int $subtotal): int
    {
        $stmt = $this->db->query(
            "SELECT `key`, `value` FROM settings WHERE `key` IN ('shipping_cost','shipping_free_above','shipping_method')"
        );
        $rows     = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) $settings[$r['key']] = $r['value'];

        $cost      = (int)($settings['shipping_cost']       ?? 0);
        $freeAbove = (int)($settings['shipping_free_above'] ?? 0);

        if ($freeAbove > 0 && $subtotal >= $freeAbove) return 0;
        return $cost;
    }
}
