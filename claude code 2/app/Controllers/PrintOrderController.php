<?php
declare(strict_types=1);
/**
 * afag3d — PrintOrder Controller (public-facing)
 */
class PrintOrderController extends BaseController
{
    private function getSiteData(): array
    {
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $rows ?: [];
    }

    public function index(): void
    {
        $options = PrintOrder::getAllOptions($this->db);
        $siteData = $this->getSiteData();
        $csrf = CSRF::field();

        $this->render('print-order.index', [
            'title'    => 'سفارش چاپ سفارشی',
            'options'  => $options,
            'siteData' => $siteData,
            'csrf'     => $csrf,
        ]);
    }

    public function store(): void
    {
        CSRF::check();

        $errors = [];

        // Sanitised fields
        $name      = trim($_POST['name'] ?? '');
        $mobile    = trim($_POST['mobile'] ?? '');
        $notes     = trim($_POST['notes'] ?? '');
        $printType = (int) ($_POST['print_type_id'] ?? 0);
        $material  = (int) ($_POST['material_id'] ?? 0);
        $quality   = (int) ($_POST['quality_id'] ?? 0);
        $color     = (int) ($_POST['color_id'] ?? 0);
        $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));

        // Validation
        if ($name === '') {
            $errors[] = 'نام و نام خانوادگی الزامی است.';
        }
        if (!preg_match('/^09\d{9}$/', $mobile)) {
            $errors[] = 'شماره موبایل باید ۱۱ رقم و با ۰۹ شروع شود.';
        }
        if ($printType <= 0) {
            $errors[] = 'نوع چاپ را انتخاب کنید.';
        }
        if ($material <= 0) {
            $errors[] = 'جنس مواد را انتخاب کنید.';
        }
        if ($quality <= 0) {
            $errors[] = 'کیفیت چاپ را انتخاب کنید.';
        }
        if ($quantity < 1 || $quantity > 99) {
            $errors[] = 'تعداد باید بین ۱ تا ۹۹ باشد.';
        }

        // File upload
        $uploadedFile = null;
        if (!empty($_FILES['model_file']['name'])) {
            $file      = $_FILES['model_file'];
            $origName  = basename($file['name']);
            $ext       = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $maxSize   = defined('MAX_FILE_SIZE') ? MAX_FILE_SIZE : (50 * 1024 * 1024);
            $allowedExt  = ['stl','obj','3mf','step','stp','gcode','zip','rar'];
            $allowedMime = [
                'application/octet-stream',
                'model/stl',
                'application/zip',
                'application/x-zip',
                'application/x-zip-compressed',
                'application/x-rar-compressed',
                'application/x-rar',
                'multipart/x-zip',
                'application/vnd.rar',
            ];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'خطا در آپلود فایل. لطفاً دوباره تلاش کنید.';
            } elseif (!in_array($ext, $allowedExt, true)) {
                $errors[] = 'فرمت فایل قابل قبول نیست. فرمت‌های مجاز: STL, OBJ, 3MF, STEP, STP, GCODE, ZIP, RAR';
            } elseif ($file['size'] > $maxSize) {
                $errors[] = 'حجم فایل از حد مجاز (' . round($maxSize / 1048576) . ' مگابایت) بیشتر است.';
            } else {
                $mime = mime_content_type($file['tmp_name']) ?: 'application/octet-stream';
                // Accept if either mime matches or it's octet-stream (common for 3D files)
                $mimeOk = in_array($mime, $allowedMime, true) || $mime === 'application/octet-stream';
                if (!$mimeOk) {
                    // Still allow by extension only for 3D model files since MIME is often unreliable
                    if (!in_array($ext, ['stl','obj','3mf','step','stp','gcode'], true)) {
                        $errors[] = 'نوع فایل معتبر نیست.';
                    }
                }
                if (empty($errors)) {
                    $uploadedFile = [
                        'tmp'          => $file['tmp_name'],
                        'original_name'=> $origName,
                        'ext'          => $ext,
                        'mime'         => $mime,
                        'size'         => $file['size'],
                    ];
                }
            }
        }

        if ($errors) {
            foreach ($errors as $e) {
                Flash::error($e);
            }
            $this->redirect('/print-order');
        }

        // Ensure a guest user record exists — use mobile as identifier
        // For Phase 3 we create a guest user row if not logged in
        $userId = Auth::id();
        if (!$userId) {
            // Find or create a lightweight user record for the order
            $stmt = $this->db->prepare("SELECT id FROM users WHERE mobile = :mobile LIMIT 1");
            $stmt->execute([':mobile' => $mobile]);
            $existingUser = $stmt->fetchColumn();
            if ($existingUser) {
                $userId = (int) $existingUser;
            } else {
                $ins = $this->db->prepare("
                    INSERT INTO users (name, mobile, password, role, status, created_at, updated_at)
                    VALUES (:name, :mobile, :password, 'customer', 'active', NOW(), NOW())
                ");
                $ins->execute([
                    ':name'     => $name,
                    ':mobile'   => $mobile,
                    ':password' => password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT),
                ]);
                $userId = (int) $this->db->lastInsertId();
            }
        }

        // Insert print order
        $orderId = PrintOrder::create($this->db, [
            'user_id'       => $userId,
            'status'        => 'pending',
            'color_id'      => $color ?: null,
            'material_id'   => $material ?: null,
            'quality_id'    => $quality ?: null,
            'print_type_id' => $printType ?: null,
            'quantity'      => $quantity,
            'notes'         => $notes,
        ]);

        // Store uploaded file
        if ($uploadedFile) {
            $dir = UPLOAD_PATH . '/print-orders/' . $orderId . '/';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $storedName = bin2hex(random_bytes(16)) . '.' . $uploadedFile['ext'];
            $destPath   = $dir . $storedName;
            if (move_uploaded_file($uploadedFile['tmp'], $destPath)) {
                PrintOrder::addFile($this->db, $orderId, [
                    'original_name' => $uploadedFile['original_name'],
                    'stored_name'   => $orderId . '/' . $storedName,
                    'mime'          => $uploadedFile['mime'],
                    'size'          => $uploadedFile['size'],
                ]);
            }
        }

        Flash::success('سفارش شما با موفقیت ثبت شد. تیم ما در اسرع وقت با شما تماس می‌گیرد.');
        $this->redirect('/print-order/success');
    }

    public function success(): void
    {
        $siteData = $this->getSiteData();
        $this->render('print-order.success', [
            'title'    => 'سفارش ثبت شد',
            'siteData' => $siteData,
        ]);
    }
}
