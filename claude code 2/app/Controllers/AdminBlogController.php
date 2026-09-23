<?php
declare(strict_types=1);
/**
 * afag3d — Admin Blog Controller
 */
class AdminBlogController extends BaseController
{
    private const ALLOWED_IMAGE_EXT  = ['jpg','jpeg','png','webp','gif'];
    private const ALLOWED_IMAGE_MIME = ['image/jpeg','image/png','image/webp','image/gif'];

    public function index(): void
    {
        $this->requireAdmin();

        $page  = max(1, (int) ($_GET['page'] ?? 1));
        $posts = Blog::getAll($this->db, $page, 20);
        $total = Blog::count($this->db);
        $pages = (int) ceil($total / 20);

        $this->render('admin.blog.index', [
            'title' => 'مقالات بلاگ',
            'posts' => $posts,
            'total' => $total,
            'page'  => $page,
            'pages' => $pages,
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireAdmin();

        $categories = Blog::getAllCategories($this->db);
        $csrf       = CSRF::field();

        $this->render('admin.blog.form', [
            'title'      => 'مقاله جدید',
            'post'       => null,
            'categories' => $categories,
            'csrf'       => $csrf,
            'isEdit'     => false,
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin();
        CSRF::check();

        $data   = $this->buildPostData();
        $errors = $this->validatePost($data);

        if ($errors) {
            foreach ($errors as $e) Flash::error($e);
            $this->redirect('/admin/blog/create');
        }

        // Handle cover image
        $coverPath = $this->handleCoverUpload();
        if ($coverPath) {
            $data['cover'] = $coverPath;
        }

        $data['author_id'] = Auth::id();

        // Ensure unique slug
        $data['slug'] = $this->uniqueSlug($data['slug']);

        Blog::create($this->db, $data);
        Flash::success('مقاله جدید ایجاد شد.');
        $this->redirect('/admin/blog');
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();

        $post = Blog::getById($this->db, $id);
        if (!$post) {
            $this->error404();
            return;
        }

        $categories = Blog::getAllCategories($this->db);
        $csrf       = CSRF::field();

        $this->render('admin.blog.form', [
            'title'      => 'ویرایش مقاله',
            'post'       => $post,
            'categories' => $categories,
            'csrf'       => $csrf,
            'isEdit'     => true,
        ], 'admin');
    }

    public function update(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $post = Blog::getById($this->db, $id);
        if (!$post) {
            $this->error404();
            return;
        }

        $data   = $this->buildPostData();
        $errors = $this->validatePost($data);

        if ($errors) {
            foreach ($errors as $e) Flash::error($e);
            $this->redirect('/admin/blog/' . $id . '/edit');
        }

        // Handle cover image
        $coverPath = $this->handleCoverUpload();
        if ($coverPath) {
            $data['cover'] = $coverPath;
            if ($post['cover'] && file_exists(UPLOAD_PATH . '/' . $post['cover'])) {
                @unlink(UPLOAD_PATH . '/' . $post['cover']);
            }
        }

        // Preserve existing slug unless changed
        if ($data['slug'] !== $post['slug']) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $id);
        }

        Blog::update($this->db, $id, $data);
        Flash::success('مقاله بروزرسانی شد.');
        $this->redirect('/admin/blog/' . $id . '/edit');
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $post = Blog::getById($this->db, $id);
        if ($post && $post['cover'] && file_exists(UPLOAD_PATH . '/' . $post['cover'])) {
            @unlink(UPLOAD_PATH . '/' . $post['cover']);
        }

        Blog::delete($this->db, $id);
        Flash::success('مقاله حذف شد.');
        $this->redirect('/admin/blog');
    }

    // ─── Categories ──────────────────────────────────────────

    public function categories(): void
    {
        $this->requireAdmin();

        $categories = Blog::getAllCategories($this->db);
        $csrf       = CSRF::field();

        $this->render('admin.blog.categories', [
            'title'      => 'دسته‌بندی‌های بلاگ',
            'categories' => $categories,
            'csrf'       => $csrf,
            'editCat'    => null,
        ], 'admin');
    }

    public function storeCategory(): void
    {
        $this->requireAdmin();
        CSRF::check();

        $name = trim($_POST['name'] ?? '');
        $slug = Url::slug(trim($_POST['slug'] ?? '') ?: $name);

        if ($name === '') {
            Flash::error('نام دسته الزامی است.');
            $this->redirect('/admin/blog/categories');
        }

        // Ensure unique slug
        $base = $slug;
        $i    = 1;
        while (true) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM blog_categories WHERE slug = :slug");
            $stmt->execute([':slug' => $slug]);
            if ((int) $stmt->fetchColumn() === 0) break;
            $slug = $base . '-' . $i++;
        }

        Blog::createCategory($this->db, [
            'name'       => $name,
            'slug'       => $slug,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'status'     => isset($_POST['status']) ? 1 : 0,
        ]);

        Flash::success('دسته‌بندی جدید اضافه شد.');
        $this->redirect('/admin/blog/categories');
    }

    public function editCategory(int $id): void
    {
        $this->requireAdmin();

        $cat = Blog::getCategoryById($this->db, $id);
        if (!$cat) {
            $this->error404();
            return;
        }

        $categories = Blog::getAllCategories($this->db);
        $csrf       = CSRF::field();

        $this->render('admin.blog.categories', [
            'title'      => 'ویرایش دسته‌بندی',
            'categories' => $categories,
            'csrf'       => $csrf,
            'editCat'    => $cat,
        ], 'admin');
    }

    public function updateCategory(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        $cat = Blog::getCategoryById($this->db, $id);
        if (!$cat) {
            $this->error404();
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $slug = Url::slug(trim($_POST['slug'] ?? '') ?: $name);

        if ($name === '') {
            Flash::error('نام دسته الزامی است.');
            $this->redirect('/admin/blog/categories/' . $id . '/edit');
        }

        Blog::updateCategory($this->db, $id, [
            'name'       => $name,
            'slug'       => $slug,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'status'     => isset($_POST['status']) ? 1 : 0,
        ]);

        Flash::success('دسته‌بندی بروزرسانی شد.');
        $this->redirect('/admin/blog/categories');
    }

    public function deleteCategory(int $id): void
    {
        $this->requireAdmin();
        CSRF::check();

        Blog::deleteCategory($this->db, $id);
        Flash::success('دسته‌بندی حذف شد.');
        $this->redirect('/admin/blog/categories');
    }

    // ─── Private helpers ─────────────────────────────────────

    private function buildPostData(): array
    {
        $title = trim($_POST['title'] ?? '');
        $slug  = Url::slug(trim($_POST['slug'] ?? '') ?: $title);
        return [
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'title'       => $title,
            'slug'        => $slug,
            'excerpt'     => trim($_POST['excerpt'] ?? ''),
            'body'        => $_POST['body'] ?? '',
            'tags'        => trim($_POST['tags'] ?? ''),
            'status'      => in_array($_POST['status'] ?? '', ['draft','published']) ? $_POST['status'] : 'draft',
            'meta_title'  => trim($_POST['meta_title'] ?? ''),
            'meta_desc'   => trim($_POST['meta_desc'] ?? ''),
        ];
    }

    private function validatePost(array $data): array
    {
        $errors = [];
        if ($data['title'] === '') $errors[] = 'عنوان مقاله الزامی است.';
        return $errors;
    }

    private function uniqueSlug(string $slug, int $excludeId = 0): string
    {
        $base = $slug;
        $i    = 1;
        while (Blog::slugExists($this->db, $slug, $excludeId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function handleCoverUpload(): ?string
    {
        if (empty($_FILES['cover']['name'])) return null;

        $file = $_FILES['cover'];
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($file['tmp_name']) ?: '';

        if (!in_array($ext, self::ALLOWED_IMAGE_EXT, true)) return null;
        if (!in_array($mime, self::ALLOWED_IMAGE_MIME, true)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $dir = UPLOAD_PATH . '/blog/covers/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dest     = $dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;
        return 'blog/covers/' . $filename;
    }
}
