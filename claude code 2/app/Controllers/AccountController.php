<?php
declare(strict_types=1);
/**
 * afag3d — Account Controller
 * All methods require an authenticated user.
 */
class AccountController extends BaseController
{
    private function siteData(): array
    {
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    // ── GET /account ────────────────────────────────────────────────────────
    public function dashboard(): void
    {
        $this->requireAuth();
        $userId   = Auth::id();
        $siteData = $this->siteData();

        // Recent orders (last 5)
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5"
        );
        $stmt->execute(['uid' => $userId]);
        $recentOrders = $stmt->fetchAll();

        // Recent print orders (last 3)
        $stmt = $this->db->prepare(
            "SELECT po.*, pt.label AS print_type_label, m.label AS material_label
             FROM print_orders po
             LEFT JOIN print_options pt ON pt.id = po.print_type_id AND pt.category = 'type'
             LEFT JOIN print_options m  ON m.id  = po.material_id  AND m.category  = 'material'
             WHERE po.user_id = :uid ORDER BY po.created_at DESC LIMIT 3"
        );
        $stmt->execute(['uid' => $userId]);
        $recentPrintOrders = $stmt->fetchAll();

        // Summary stats
        $totalOrders = (int) $this->db->prepare(
            "SELECT COUNT(*) FROM orders WHERE user_id = ?"
        )->execute([$userId]) ? $this->db->prepare(
            "SELECT COUNT(*) FROM orders WHERE user_id = ?"
        )->execute([$userId]) : 0;

        $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = :uid");
        $stmtCount->execute(['uid' => $userId]);
        $totalOrders = (int) $stmtCount->fetchColumn();

        $stmtPending = $this->db->prepare(
            "SELECT COUNT(*) FROM orders WHERE user_id = :uid AND status IN ('pending','processing')"
        );
        $stmtPending->execute(['uid' => $userId]);
        $pendingOrders = (int) $stmtPending->fetchColumn();

        $stmtSpent = $this->db->prepare(
            "SELECT COALESCE(SUM(total),0) FROM orders WHERE user_id = :uid AND status IN ('paid','processing','shipped','completed')"
        );
        $stmtSpent->execute(['uid' => $userId]);
        $totalSpent = (int) $stmtSpent->fetchColumn();

        SEO::set(['title' => 'داشبورد — حساب کاربری']);

        $this->render('account.dashboard', [
            'title'             => 'داشبورد',
            'siteData'          => $siteData,
            'recentOrders'      => $recentOrders,
            'recentPrintOrders' => $recentPrintOrders,
            'totalOrders'       => $totalOrders,
            'pendingOrders'     => $pendingOrders,
            'totalSpent'        => $totalSpent,
        ]);
    }

    // ── GET /account/orders ─────────────────────────────────────────────────
    public function orders(): void
    {
        $this->requireAuth();
        $userId   = Auth::id();
        $siteData = $this->siteData();
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = 15;
        $offset   = ($page - 1) * $perPage;

        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim OFFSET :off"
        );
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll();

        $stmtTotal = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = :uid");
        $stmtTotal->execute(['uid' => $userId]);
        $total = (int) $stmtTotal->fetchColumn();
        $pages = (int) ceil($total / $perPage);

        SEO::set(['title' => 'سفارش‌های من']);

        $this->render('account.orders', [
            'title'    => 'سفارش‌های من',
            'siteData' => $siteData,
            'orders'   => $orders,
            'page'     => $page,
            'pages'    => $pages,
            'total'    => $total,
        ]);
    }

    // ── GET /account/orders/{id} ────────────────────────────────────────────
    public function orderDetail(int $id): void
    {
        $this->order($id);
    }

    public function order(int $id): void
    {
        $this->requireAuth();
        $userId   = Auth::id();
        $siteData = $this->siteData();

        $order = Order::getById($this->db, $id);
        if (!$order || (int)$order['user_id'] !== $userId) {
            $this->error404();
            return;
        }

        $items = Order::getItems($this->db, $id);

        SEO::set(['title' => 'جزئیات سفارش #' . $id]);

        $this->render('account.order-detail', [
            'title'    => 'جزئیات سفارش',
            'siteData' => $siteData,
            'order'    => $order,
            'items'    => $items,
        ]);
    }

    // ── GET /account/print-orders ───────────────────────────────────────────
    public function printOrders(): void
    {
        $this->requireAuth();
        $userId   = Auth::id();
        $siteData = $this->siteData();

        $stmt = $this->db->prepare(
            "SELECT po.*,
                    pt.label AS print_type_label,
                    m.label  AS material_label,
                    q.label  AS quality_label
             FROM print_orders po
             LEFT JOIN print_options pt ON pt.id = po.print_type_id AND pt.category = 'type'
             LEFT JOIN print_options m  ON m.id  = po.material_id   AND m.category  = 'material'
             LEFT JOIN print_options q  ON q.id  = po.quality_id    AND q.category  = 'quality'
             WHERE po.user_id = :uid ORDER BY po.created_at DESC"
        );
        $stmt->execute(['uid' => $userId]);
        $printOrders = $stmt->fetchAll();

        SEO::set(['title' => 'سفارشات چاپ من']);

        $this->render('account.print-orders', [
            'title'       => 'سفارشات چاپ من',
            'siteData'    => $siteData,
            'printOrders' => $printOrders,
        ]);
    }

    // ── GET /account/profile ────────────────────────────────────────────────
    public function profile(): void
    {
        $this->requireAuth();
        $userId   = Auth::id();
        $siteData = $this->siteData();

        $userModel = new User($this->db);
        $user      = $userModel->find($userId);

        SEO::set(['title' => 'ویرایش پروفایل']);

        $this->render('account.profile', [
            'title'    => 'ویرایش پروفایل',
            'siteData' => $siteData,
            'user'     => $user,
            'csrf'     => CSRF::field(),
        ]);
    }

    // ── POST /account/profile ────────────────────────────────────────────────
    public function saveProfile(): void
    {
        $this->requireAuth();
        CSRF::check();

        $userId = Auth::id();
        $name   = trim($this->post('name'));
        $email  = trim($this->post('email'));

        $errors = [];
        if (mb_strlen($name) < 2)  $errors[] = 'نام باید حداقل ۲ حرف باشد.';
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'آدرس ایمیل معتبر نیست.';
        }

        if ($errors) {
            foreach ($errors as $e) Flash::error($e);
            $this->redirect('/account/profile');
            return;
        }

        $userModel = new User($this->db);
        $userModel->update($userId, ['name' => $name, 'email' => $email]);

        // Update session name
        $_SESSION['user_name'] = $name;

        Flash::success('پروفایل با موفقیت بروزرسانی شد.');
        $this->redirect('/account/profile');
    }

    // ── POST /account/profile (alias) ───────────────────────────────────────
    public function updateProfile(): void
    {
        $this->saveProfile();
    }

    // ── GET /account/password ────────────────────────────────────────────────
    public function changePassword(): void
    {
        $this->requireAuth();
        $siteData = $this->siteData();

        SEO::set(['title' => 'تغییر رمز عبور']);

        $this->render('account.password', [
            'title'    => 'تغییر رمز عبور',
            'siteData' => $siteData,
            'csrf'     => CSRF::field(),
        ]);
    }

    // ── POST /account/password ───────────────────────────────────────────────
    public function updatePassword(): void
    {
        $this->requireAuth();
        CSRF::check();

        $userId      = Auth::id();
        $current     = $this->postRaw('current_password');
        $newPass     = $this->postRaw('new_password');
        $confirmPass = $this->postRaw('confirm_password');

        $userModel = new User($this->db);
        $user      = $userModel->find($userId);

        if (!$user || !password_verify($current, $user['password'])) {
            Flash::error('رمز عبور فعلی اشتباه است.');
            $this->redirect('/account/password');
            return;
        }

        if (strlen($newPass) < 8) {
            Flash::error('رمز جدید باید حداقل ۸ کاراکتر باشد.');
            $this->redirect('/account/password');
            return;
        }

        if ($newPass !== $confirmPass) {
            Flash::error('تکرار رمز جدید مطابقت ندارد.');
            $this->redirect('/account/password');
            return;
        }

        $userModel->changePassword($userId, $newPass);

        Flash::success('رمز عبور با موفقیت تغییر یافت.');
        $this->redirect('/account/password');
    }

    // ── GET /account/addresses ───────────────────────────────────────────────
    public function addresses(): void
    {
        $this->requireAuth();
        $userId    = Auth::id();
        $siteData  = $this->siteData();
        $addresses = Address::getByUser($this->db, $userId);

        SEO::set(['title' => 'آدرس‌های من']);

        $this->render('account.addresses', [
            'title'     => 'آدرس‌های من',
            'siteData'  => $siteData,
            'addresses' => $addresses,
            'csrf'      => CSRF::field(),
        ]);
    }

    // ── POST /account/addresses ──────────────────────────────────────────────
    public function addAddress(): void
    {
        $this->requireAuth();
        CSRF::check();

        $userId = Auth::id();

        $data = [
            'user_id'     => $userId,
            'title'       => $this->post('title'),
            'name'        => $this->post('name'),
            'mobile'      => preg_replace('/\D/', '', $this->post('mobile')),
            'province'    => $this->post('province'),
            'city'        => $this->post('city'),
            'address'     => $this->post('address'),
            'postal_code' => $this->post('postal_code'),
            'is_default'  => isset($_POST['is_default']) ? 1 : 0,
        ];

        $errors = [];
        if (mb_strlen($data['name']) < 2)     $errors[] = 'نام گیرنده الزامی است.';
        if (strlen($data['mobile']) !== 11)   $errors[] = 'شماره موبایل ۱۱ رقمی وارد کنید.';
        if (mb_strlen($data['address']) < 5)  $errors[] = 'آدرس کامل را وارد کنید.';

        if ($errors) {
            foreach ($errors as $e) Flash::error($e);
            $this->redirect('/account/addresses');
            return;
        }

        if ($data['is_default']) {
            $this->db->prepare("UPDATE addresses SET is_default=0 WHERE user_id=:uid")
                     ->execute(['uid' => $userId]);
        }

        Address::create($this->db, $data);

        Flash::success('آدرس جدید اضافه شد.');
        $this->redirect('/account/addresses');
    }

    // ── GET /account/addresses/{id}/edit ─────────────────────────────────────
    public function editAddress(int $id): void
    {
        $this->requireAuth();
        $userId   = Auth::id();
        $siteData = $this->siteData();

        $address = Address::getById($this->db, $id);
        if (!$address || (int)$address['user_id'] !== $userId) {
            $this->error404();
            return;
        }

        SEO::set(['title' => 'ویرایش آدرس']);

        $this->render('account.addresses', [
            'title'       => 'ویرایش آدرس',
            'siteData'    => $siteData,
            'addresses'   => Address::getByUser($this->db, $userId),
            'editAddress' => $address,
            'csrf'        => CSRF::field(),
        ]);
    }

    // ── POST /account/addresses/{id} ─────────────────────────────────────────
    public function updateAddress(int $id): void
    {
        $this->requireAuth();
        CSRF::check();

        $userId  = Auth::id();
        $address = Address::getById($this->db, $id);

        if (!$address || (int)$address['user_id'] !== $userId) {
            $this->error404();
            return;
        }

        $data = [
            'title'       => $this->post('title'),
            'name'        => $this->post('name'),
            'mobile'      => preg_replace('/\D/', '', $this->post('mobile')),
            'province'    => $this->post('province'),
            'city'        => $this->post('city'),
            'address'     => $this->post('address'),
            'postal_code' => $this->post('postal_code'),
        ];

        Address::update($this->db, $id, $data);

        Flash::success('آدرس بروزرسانی شد.');
        $this->redirect('/account/addresses');
    }

    // ── POST /account/addresses/{id}/delete ──────────────────────────────────
    public function deleteAddress(int $id): void
    {
        $this->requireAuth();
        CSRF::check();

        $userId  = Auth::id();
        $address = Address::getById($this->db, $id);

        if (!$address || (int)$address['user_id'] !== $userId) {
            $this->error404();
            return;
        }

        Address::delete($this->db, $id);

        Flash::success('آدرس حذف شد.');
        $this->redirect('/account/addresses');
    }

    // ── POST /account/addresses/{id}/default ─────────────────────────────────
    public function setDefaultAddress(int $id): void
    {
        $this->requireAuth();
        CSRF::check();

        $userId  = Auth::id();
        $address = Address::getById($this->db, $id);

        if (!$address || (int)$address['user_id'] !== $userId) {
            $this->error404();
            return;
        }

        Address::setDefault($this->db, $id, $userId);

        Flash::success('آدرس پیش‌فرض تنظیم شد.');
        $this->redirect('/account/addresses');
    }
}
