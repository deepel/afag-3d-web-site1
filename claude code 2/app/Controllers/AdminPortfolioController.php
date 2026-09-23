<?php
declare(strict_types=1);
/**
 * afag3d — Admin Portfolio Controller
 */
class AdminPortfolioController extends BaseController
{
    private const ALLOWED_IMAGE_EXT  = ['jpg','jpeg','png','webp','gif'];
    private const ALLOWED_IMAGE_MIME = ['image/jpeg','image/png','image/webp','image/gif'];

    public function index(): void
    {
        $this->requireAdmin();

        $page  = max(1, (int) ($_GET['page'] ?? 1));
        $items = Portfolio::getAllAdmin($this->db, $page, 20);
        $total = Portfolio::count($this->db);
        $pages = (int) ceil($total / 20);

        $this->render('admin.portfolio.index', [
            'title' => 'نمونه کارها',
            'items' => $items,
            'total' => $total,
            'page'  => $page,
            'pages' => $pages,
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireAdmin();
        $csrf = CSRF::field();
        $this->render('admin.portfolio.form', [
            'title'    => 'افزودن نمونه کار',
            'item'     => null,
            'csrf'     => $csrf,
            'isEdit'   => false,
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin();
        CSRF::check();

        $data   = $this->buildFormData();
        $errors = $this->validate($data);

        if ($errors) {
            foreach ($errors as $e) Flash::error($e);
            $this->redirect('/admin/portfolio/create');
        }

        // Handle cover image upload
        $coverPath = $this->handleCoverUpload();
        if ($coverPath) {
            $data['cover'] = $coverPath;
        }

        // Ensure unique slug
        $data['slug'] = $this->uniqueSlug($data['slug']);

        $id = Portfolio::create($this->db, $data);

        // Handle additional images
        $this->handleAdditionalImages($id);

        Flash::success('نمونه کار جدید اضافه شد.');
        $this->redirect('/admin/portfolio');
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();

        $item = Portfolio::getById($this->db, $id);
        if (!$item) {
            $this->error404();
            return;
        }

        $images = Portfolio::getImages($this->db, $id);
        $csrf   = CSRF::field();

        $this->render('admin.portfolio.form', [
            'title'  => 'ویرایش نمونه کار',
            'item'   => $item,
            'images' => $images,
            'csrf'   => $csrf,
            'isEdit' => true,
        ], 'admin');
    }

    public function update(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $item = Portfolio::getById($this->db, $id);
        if (!$item) {
            $this->error404();
            return;
        }

        $data   = $this->buildFormData();
        $errors = $this->validate($data);

        if ($errors) {
            foreach ($errors as $e) Flash::error($e);
            $this->redirect('/admin/portfolio/' . $id . '/edit');
        }

        // Handle cover image upload
        $coverPath = $this->handleCoverUpload();
        if ($coverPath) {
            $data['cover'] = $coverPath;
            // Delete old cover
            if ($item['cover'] && file_exists(UPLOAD_PATH . '/' . $item['cover'])) {
                @unlink(UPLOAD_PATH . '/' . $item['cover']);
            }
        }

        Portfolio::update($this->db, $id, $data);

        // Handle additional images
        $this->handleAdditionalImages($id);

        Flash::success('نمونه کار بروزرسانی شد.');
        $this->redirect('/admin/portfolio/' . $id . '/edit');
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $item = Portfolio::getById($this->db, $id);
        if (!$item) {
            $this->error404();
            return;
        }

        // Delete images from disk
        $images = Portfolio::getImages($this->db, $id);
        foreach ($images as $img) {
            $p = UPLOAD_PATH . '/' . $img['path'];
            if (file_exists($p)) @unlink($p);
        }
        if ($item['cover']) {
            $p = UPLOAD_PATH . '/' . $item['cover'];
            if (file_exists($p)) @unlink($p);
        }

        Portfolio::delete($this->db, $id);
        Flash::success('نمونه کار حذف شد.');
        $this->redirect('/admin/portfolio');
    }

    public function uploadImage(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $item = Portfolio::getById($this->db, $id);
        if (!$item) {
            $this->error404();
            return;
        }

        if (empty($_FILES['image']['name'])) {
            Flash::error('فایلی انتخاب نشده است.');
            $this->redirect('/admin/portfolio/' . $id . '/edit');
        }

        $path = $this->saveImage($_FILES['image'], 'portfolio');
        if (!$path) {
            Flash::error('خطا در آپلود تصویر.');
            $this->redirect('/admin/portfolio/' . $id . '/edit');
        }

        $caption = trim($_POST['caption'] ?? '');
        Portfolio::addImage($this->db, $id, [
            'path'    => $path,
            'caption' => $caption,
        ]);

        Flash::success('تصویر اضافه شد.');
        $this->redirect('/admin/portfolio/' . $id . '/edit');
    }

    public function deleteImage(int $imageId): void
    {
        $this->requireAdmin();
        CSRF::check();

        $image = Portfolio::getImageById($this->db, $imageId);
        if ($image) {
            $p = UPLOAD_PATH . '/' . $image['path'];
            if (file_exists($p)) @unlink($p);
        }

        Portfolio::deleteImage($this->db, $imageId);
        Flash::success('تصویر حذف شد.');

        $referer = $_SERVER['HTTP_REFERER'] ?? '/admin/portfolio';
        header('Location: ' . $referer);
        exit;
    }

    // ─── Private helpers ─────────────────────────────────────

    private function buildFormData(): array
    {
        return [
            'title'       => trim($_POST['title'] ?? ''),
            'slug'        => Url::slug(trim($_POST['slug'] ?? '') ?: trim($_POST['title'] ?? '')),
            'description' => trim($_POST['description'] ?? ''),
            'client'      => trim($_POST['client'] ?? ''),
            'tags'        => trim($_POST['tags'] ?? ''),
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'status'      => isset($_POST['status']) ? 1 : 0,
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if ($data['title'] === '') $errors[] = 'عنوان الزامی است.';
        return $errors;
    }

    private function uniqueSlug(string $slug, int $excludeId = 0): string
    {
        $base  = $slug;
        $i     = 1;
        while (true) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM portfolio WHERE slug = :slug AND id != :id");
            $stmt->execute([':slug' => $slug, ':id' => $excludeId]);
            if ((int) $stmt->fetchColumn() === 0) break;
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function handleCoverUpload(): ?string
    {
        if (empty($_FILES['cover']['name'])) return null;
        return $this->saveImage($_FILES['cover'], 'portfolio/covers');
    }

    private function handleAdditionalImages(int $portfolioId): void
    {
        if (empty($_FILES['extra_images']['name'][0])) return;
        $files = $_FILES['extra_images'];
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];
            if ($file['error'] !== UPLOAD_ERR_OK) continue;
            $path = $this->saveImage($file, 'portfolio');
            if ($path) {
                Portfolio::addImage($this->db, $portfolioId, ['path' => $path]);
            }
        }
    }

    private function saveImage(array $file, string $subDir): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($file['tmp_name']) ?: '';

        if (!in_array($ext, self::ALLOWED_IMAGE_EXT, true)) return null;
        if (!in_array($mime, self::ALLOWED_IMAGE_MIME, true)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $dir = UPLOAD_PATH . '/' . $subDir . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest     = $dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;
        return $subDir . '/' . $filename;
    }
}
