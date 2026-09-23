<?php
declare(strict_types=1);
/**
 * afag3d — Admin User Controller
 */
class AdminUserController extends BaseController
{
    private User $userModel;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->userModel = new User($db);
    }

    private function siteData(): array
    {
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    // ── GET /admin/users ─────────────────────────────────────────────────────
    public function index(): void
    {
        $this->requireAdmin();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;
        $search  = trim($_GET['search'] ?? '');
        $role    = trim($_GET['role']   ?? '');

        // Build query
        $where  = [];
        $params = [];

        if ($search !== '') {
            $where[]         = "(name LIKE :q OR mobile LIKE :q2 OR email LIKE :q3)";
            $params[':q']    = "%{$search}%";
            $params[':q2']   = "%{$search}%";
            $params[':q3']   = "%{$search}%";
        }
        if ($role !== '') {
            $where[]        = "role = :role";
            $params[':role'] = $role;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM users {$whereClause}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();
        $pages = (int) ceil($total / $perPage);

        $params[':lim'] = $perPage;
        $params[':off'] = $offset;

        $stmt = $this->db->prepare(
            "SELECT id, name, mobile, email, role, status, created_at
             FROM users {$whereClause}
             ORDER BY id DESC LIMIT :lim OFFSET :off"
        );
        // Bind integers separately for PDO
        foreach ($params as $k => $v) {
            if ($k === ':lim' || $k === ':off') {
                $stmt->bindValue($k, $v, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($k, $v);
            }
        }
        $stmt->execute();
        $users = $stmt->fetchAll();

        $this->render('admin.users.index', [
            'title'   => 'مدیریت کاربران',
            'users'   => $users,
            'total'   => $total,
            'page'    => $page,
            'pages'   => $pages,
            'search'  => $search,
            'role'    => $role,
            'csrf'    => CSRF::field(),
        ], 'admin');
    }

    // ── GET /admin/users/{id} ────────────────────────────────────────────────
    public function show(int $id): void
    {
        $this->requireAdmin();

        $user = $this->userModel->find($id);
        if (!$user) {
            $this->error404();
            return;
        }

        // Orders
        $stmt = $this->db->prepare(
            "SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC LIMIT 20"
        );
        $stmt->execute(['uid' => $id]);
        $orders = $stmt->fetchAll();

        // Print orders
        $stmt = $this->db->prepare(
            "SELECT po.*, pt.label AS print_type_label, m.label AS material_label
             FROM print_orders po
             LEFT JOIN print_options pt ON pt.id = po.print_type_id AND pt.category='type'
             LEFT JOIN print_options m  ON m.id  = po.material_id   AND m.category='material'
             WHERE po.user_id = :uid ORDER BY po.created_at DESC LIMIT 20"
        );
        $stmt->execute(['uid' => $id]);
        $printOrders = $stmt->fetchAll();

        $this->render('admin.users.show', [
            'title'       => 'جزئیات کاربر',
            'user'        => $user,
            'orders'      => $orders,
            'printOrders' => $printOrders,
            'csrf'        => CSRF::field(),
        ], 'admin');
    }

    // ── POST /admin/users/{id}/toggle ───────────────────────────────────────
    public function toggleStatus(int $id): void
    {
        $this->ban($id);
    }

    // ── POST /admin/users/{id}/ban ───────────────────────────────────────────
    public function ban(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $user = $this->userModel->find($id);
        if (!$user) {
            Flash::error('کاربر یافت نشد.');
            $this->redirect('/admin/users');
            return;
        }

        // Don't ban yourself
        if ($id === Auth::id()) {
            Flash::error('نمی‌توانید حساب خودتان را مسدود کنید.');
            $this->redirect('/admin/users');
            return;
        }

        $newStatus = ($user['status'] === 'banned') ? 'active' : 'banned';
        $this->userModel->update($id, ['status' => $newStatus]);

        $msg = $newStatus === 'banned' ? 'کاربر مسدود شد.' : 'کاربر فعال شد.';
        Flash::success($msg);
        $this->redirect('/admin/users');
    }

    // ── POST /admin/users/{id}/admin ──────────────────────────────────────────
    public function makeAdmin(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $user = $this->userModel->find($id);
        if (!$user) {
            Flash::error('کاربر یافت نشد.');
            $this->redirect('/admin/users');
            return;
        }

        $this->db->prepare("UPDATE users SET role='admin' WHERE id=:id")
                 ->execute(['id' => $id]);

        Flash::success('کاربر به مدیر ارتقا یافت.');
        $this->redirect('/admin/users');
    }
}
