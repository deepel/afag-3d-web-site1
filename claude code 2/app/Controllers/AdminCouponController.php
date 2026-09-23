<?php
declare(strict_types=1);
/**
 * afag3d — Admin Coupon Controller
 */
class AdminCouponController extends BaseController
{
    public function index(): void
    {
        $this->requireAdmin();
        $coupons = $this->db->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();

        $this->render('admin.coupons.index', [
            'title'   => 'کوپن‌های تخفیف',
            'coupons' => $coupons,
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireAdmin();
        $this->render('admin.coupons.form', [
            'title'  => 'افزودن کوپن',
            'coupon' => null,
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/coupons/create');
        }

        $data = $this->buildData();
        $this->db->prepare(
            "INSERT INTO coupons (code,type,value,min_order,max_discount,usage_limit,expires_at,is_active,created_at)
             VALUES (?,?,?,?,?,?,?,?,NOW())"
        )->execute([
            $data['code'], $data['type'], $data['value'],
            $data['min_order'], $data['max_discount'],
            $data['usage_limit'], $data['expires_at'], $data['is_active'],
        ]);

        Flash::set('success', 'کوپن افزوده شد.');
        $this->redirect('/admin/coupons');
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();
        $st = $this->db->prepare("SELECT * FROM coupons WHERE id = ? LIMIT 1");
        $st->execute([$id]);
        $coupon = $st->fetch();
        if (!$coupon) { $this->error404(); return; }

        $this->render('admin.coupons.form', [
            'title'  => 'ویرایش کوپن',
            'coupon' => $coupon,
        ], 'admin');
    }

    public function update(int $id): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/coupons/' . $id . '/edit');
        }

        $data = $this->buildData();
        $this->db->prepare(
            "UPDATE coupons SET code=?,type=?,value=?,min_order=?,max_discount=?,
             usage_limit=?,expires_at=?,is_active=? WHERE id=?"
        )->execute([
            $data['code'], $data['type'], $data['value'],
            $data['min_order'], $data['max_discount'],
            $data['usage_limit'], $data['expires_at'], $data['is_active'], $id,
        ]);

        Flash::set('success', 'کوپن به‌روز شد.');
        $this->redirect('/admin/coupons');
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/coupons');
        }
        $this->db->prepare("DELETE FROM coupons WHERE id = ?")->execute([$id]);
        Flash::set('success', 'کوپن حذف شد.');
        $this->redirect('/admin/coupons');
    }

    private function buildData(): array
    {
        $type = in_array($_POST['type'] ?? '', ['percent', 'fixed']) ? $_POST['type'] : 'percent';
        return [
            'code'          => strtoupper(trim($_POST['code'] ?? '')),
            'type'          => $type,
            'value'         => (float)($_POST['value'] ?? 0),
            'min_order'     => (int)($_POST['min_order'] ?? 0) ?: null,
            'max_discount'  => (int)($_POST['max_discount'] ?? 0) ?: null,
            'usage_limit'   => (int)($_POST['usage_limit'] ?? 0) ?: null,
            'expires_at'    => trim($_POST['expires_at'] ?? '') ?: null,
            'is_active'     => isset($_POST['is_active']) ? 1 : 0,
        ];
    }
}
