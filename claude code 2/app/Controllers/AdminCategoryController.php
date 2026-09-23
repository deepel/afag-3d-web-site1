<?php
declare(strict_types=1);
/**
 * afag3d — Admin Category Controller
 */
class AdminCategoryController extends BaseController
{
    public function index(): void
    {
        $this->requireAdmin();
        $cats = Category::getAll($this->db);
        $shops = Shop::getAll($this->db);

        $this->render('admin.categories.index', [
            'title'      => 'دسته‌بندی‌ها',
            'categories' => $cats,
            'shops'      => $shops,
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireAdmin();
        $shops = Shop::getAll($this->db);
        $cats  = Category::getAll($this->db);

        $this->render('admin.categories.form', [
            'title'    => 'افزودن دسته‌بندی',
            'category' => null,
            'shops'    => $shops,
            'allCats'  => $cats,
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/categories/create');
        }

        $data = $this->buildData();
        $this->db->prepare(
            "INSERT INTO categories (shop_id, parent_id, name, slug, description, image, sort_order, created_at)
             VALUES (?,?,?,?,?,?,?,NOW())"
        )->execute([
            $data['shop_id'], $data['parent_id'], $data['name'],
            $data['slug'], $data['description'], $data['image'], $data['sort_order'],
        ]);

        Flash::set('success', 'دسته‌بندی افزوده شد.');
        $this->redirect('/admin/categories');
    }

    public function edit(int $id): void
    {
        $this->requireAdmin();
        $category = $this->db->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
        $category->execute([$id]);
        $category = $category->fetch();
        if (!$category) { $this->error404(); return; }

        $shops   = Shop::getAll($this->db);
        $allCats = Category::getAll($this->db);

        $this->render('admin.categories.form', [
            'title'    => 'ویرایش دسته‌بندی',
            'category' => $category,
            'shops'    => $shops,
            'allCats'  => $allCats,
        ], 'admin');
    }

    public function update(int $id): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/categories/' . $id . '/edit');
        }

        $data = $this->buildData();
        $this->db->prepare(
            "UPDATE categories SET shop_id=?,parent_id=?,name=?,slug=?,description=?,image=?,sort_order=?
             WHERE id=?"
        )->execute([
            $data['shop_id'], $data['parent_id'], $data['name'],
            $data['slug'], $data['description'], $data['image'], $data['sort_order'], $id,
        ]);

        Flash::set('success', 'دسته‌بندی به‌روز شد.');
        $this->redirect('/admin/categories');
    }

    public function delete(int $id): void
    {
        $this->requireAdmin();
        if (!CSRF::verify($_POST['csrf_token'] ?? '')) {
            Flash::set('error', 'خطای امنیتی.');
            $this->redirect('/admin/categories');
        }
        $this->db->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        Flash::set('success', 'دسته‌بندی حذف شد.');
        $this->redirect('/admin/categories');
    }

    private function buildData(): array
    {
        $name = trim($_POST['name'] ?? '');
        return [
            'shop_id'     => (int)($_POST['shop_id'] ?? 0) ?: null,
            'parent_id'   => (int)($_POST['parent_id'] ?? 0) ?: null,
            'name'        => $name,
            'slug'        => trim($_POST['slug'] ?? '') ?: Url::slug($name),
            'description' => trim($_POST['description'] ?? ''),
            'image'       => trim($_POST['image'] ?? ''),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
        ];
    }
}
