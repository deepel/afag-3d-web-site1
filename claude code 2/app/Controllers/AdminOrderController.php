<?php
declare(strict_types=1);
/**
 * afag3d — Admin Order Controller
 */
class AdminOrderController extends BaseController
{
    public function index(): void
    {
        $this->requireAdmin();
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $status = $_GET['status'] ?? '';

        $where  = $status ? "WHERE status = ?" : '';
        $params = $status ? [$status] : [];

        $total  = (int)$this->db->prepare("SELECT COUNT(*) FROM orders $where")->execute($params) ? 0 : 0;
        $cntSt  = $this->db->prepare("SELECT COUNT(*) FROM orders $where");
        $cntSt->execute($params);
        $total  = (int)$cntSt->fetchColumn();
        $pages  = max(1, (int)ceil($total / $perPage));

        $st     = $this->db->prepare("SELECT o.*, u.email AS user_email
            FROM orders o LEFT JOIN users u ON u.id = o.user_id
            $where ORDER BY o.created_at DESC LIMIT ? OFFSET ?");
        $params[] = $perPage;
        $params[] = $offset;
        $st->execute($params);
        $orders = $st->fetchAll();

        $this->render('admin.orders.index', [
            'title'   => 'سفارشات',
            'orders'  => $orders,
            'total'   => $total,
            'pages'   => $pages,
            'page'    => $page,
            'status'  => $status,
        ], 'admin');
    }

    public function show(int $id): void
    {
        $this->requireAdmin();
        $order = Order::getById($this->db, $id);
        if (!$order) { $this->error404(); return; }

        $items = Order::getItems($this->db, $id);

        $this->render('admin.orders.show', [
            'title' => 'سفارش #' . $id,
            'order' => $order,
            'items' => $items,
        ], 'admin');
    }

    public function updateStatus(int $id): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/orders/' . $id);
        }

        $allowed = ['pending', 'processing', 'shipped', 'completed', 'cancelled', 'paid', 'refunded'];
        $status  = $_POST['status'] ?? '';
        if (!in_array($status, $allowed)) {
            Flash::set('error', 'وضعیت نامعتبر.');
            $this->redirect('/admin/orders/' . $id);
        }

        Order::updateStatus($this->db, $id, $status);
        Flash::set('success', 'وضعیت سفارش به‌روز شد.');
        $this->redirect('/admin/orders/' . $id);
    }
}
