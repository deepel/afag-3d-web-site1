<?php
declare(strict_types=1);
/**
 * afag3d — Admin Print Orders Controller
 */
class AdminPrintOrderController extends BaseController
{
    private const STATUS_LABELS = [
        'pending'   => 'در انتظار استعلام',
        'quoting'   => 'ارائه قیمت شده',
        'confirmed' => 'تأیید شده',
        'printing'  => 'در حال چاپ',
        'done'      => 'تکمیل‌شده',
        'cancelled' => 'لغو‌شده',
    ];

    public function index(): void
    {
        $this->requireAdmin();

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $status  = $_GET['status'] ?? '';
        $perPage = 20;

        $orders = PrintOrder::getAll($this->db, $page, $perPage, $status);
        $total  = PrintOrder::count($this->db, $status);
        $pages  = (int) ceil($total / $perPage);
        $pendingCount = PrintOrder::pendingCount($this->db);

        // Count per status for tabs
        $statusCounts = [];
        foreach (array_keys(self::STATUS_LABELS) as $s) {
            $statusCounts[$s] = PrintOrder::count($this->db, $s);
        }

        $this->render('admin.print-orders.index', [
            'title'        => 'سفارش‌های چاپ',
            'orders'       => $orders,
            'total'        => $total,
            'page'         => $page,
            'pages'        => $pages,
            'activeStatus' => $status,
            'statusLabels' => self::STATUS_LABELS,
            'statusCounts' => $statusCounts,
            'pendingCount' => $pendingCount,
        ], 'admin');
    }

    public function show(int $id): void
    {
        $this->requireAdmin();

        $order = PrintOrder::getById($this->db, $id);
        if (!$order) {
            $this->error404();
            return;
        }

        $files = PrintOrder::getFiles($this->db, $id);
        $csrf  = CSRF::field();

        $this->render('admin.print-orders.show', [
            'title'        => 'سفارش چاپ #' . $id,
            'order'        => $order,
            'files'        => $files,
            'statusLabels' => self::STATUS_LABELS,
            'csrf'         => $csrf,
        ], 'admin');
    }

    public function updateStatus(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $order = PrintOrder::getById($this->db, $id);
        if (!$order) {
            $this->error404();
            return;
        }

        $status      = $_POST['status'] ?? '';
        $adminNotes  = trim($_POST['admin_notes'] ?? '');
        $quotedPrice = !empty($_POST['quoted_price']) ? (int) $_POST['quoted_price'] : null;

        if (!array_key_exists($status, self::STATUS_LABELS)) {
            Flash::error('وضعیت نامعتبر است.');
            $this->redirect('/admin/print-orders/' . $id);
        }

        PrintOrder::updateStatus($this->db, $id, $status, $adminNotes, $quotedPrice);
        Flash::success('وضعیت سفارش بروزرسانی شد.');
        $this->redirect('/admin/print-orders/' . $id);
    }

    public function downloadFile(int $fileId): void
    {
        $this->requireAdmin();

        $file = PrintOrder::getFileById($this->db, $fileId);
        if (!$file) {
            http_response_code(404);
            die('فایل یافت نشد.');
        }

        $filePath = UPLOAD_PATH . '/print-orders/' . $file['stored_name'];
        if (!file_exists($filePath)) {
            http_response_code(404);
            die('فایل روی سرور یافت نشد.');
        }

        $origName = $file['original_name'];
        // Sanitise filename for Content-Disposition
        $safeName = preg_replace('/[^\w.\-]/', '_', $origName);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . addslashes($safeName) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        readfile($filePath);
        exit;
    }

    public function options(): void
    {
        $this->requireAdmin();

        $options = PrintOrder::getAllOptions($this->db);
        $csrf    = CSRF::field();

        $this->render('admin.print-options.index', [
            'title'   => 'گزینه‌های چاپ',
            'options' => $options,
            'csrf'    => $csrf,
        ], 'admin');
    }

    public function saveOptions(): void
    {
        $this->requireAdmin();
        CSRF::check();

        $statuses = $_POST['status'] ?? [];
        if (is_array($statuses)) {
            foreach ($statuses as $optionId => $status) {
                $statusVal = in_array($status, ['1','0'], true) ? $status : '1';
                PrintOrder::updateOption($this->db, (int) $optionId, $statusVal);
            }
        }

        Flash::success('گزینه‌های چاپ بروزرسانی شدند.');
        $this->redirect('/admin/print-options');
    }

    public function createOption(): void
    {
        $this->requireAdmin();
        CSRF::check();

        $type      = $_POST['type'] ?? '';
        $nameFa    = trim($_POST['name_fa'] ?? '');
        $name      = trim($_POST['name'] ?? $nameFa);
        $priceAdd  = (int) ($_POST['price_add'] ?? 0);
        $sortOrder = (int) ($_POST['sort_order'] ?? 0);

        $allowedTypes = ['color','material','quality','print_type'];
        if (!in_array($type, $allowedTypes, true) || $nameFa === '') {
            Flash::error('اطلاعات نادرست است.');
            $this->redirect('/admin/print-options');
        }

        PrintOrder::createOption($this->db, [
            'type'       => $type,
            'name'       => $name,
            'name_fa'    => $nameFa,
            'price_add'  => $priceAdd,
            'sort_order' => $sortOrder,
            'status'     => 1,
        ]);

        Flash::success('گزینه جدید اضافه شد.');
        $this->redirect('/admin/print-options');
    }

    public function deleteOption(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        PrintOrder::deleteOption($this->db, $id);
        Flash::success('گزینه حذف شد.');
        $this->redirect('/admin/print-options');
    }
}
