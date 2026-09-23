<div class="admin-page-header">
  <h1 class="admin-page-title">دسته‌بندی‌های بلاگ</h1>
  <a href="<?= BASE_URL ?>/admin/blog" class="btn btn-ghost">بازگشت به مقالات</a>
</div>

<div class="admin-grid-2">

  <!-- Category List -->
  <div class="admin-card">
    <div class="admin-card-header"><h2 class="admin-card-title">دسته‌بندی‌ها</h2></div>
    <?php if (empty($categories)): ?>
    <p class="text-muted px-20 py-16">هیچ دسته‌ای وجود ندارد.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr><th>#</th><th>نام</th><th>اسلاگ</th><th>مقالات</th><th>وضعیت</th><th>عملیات</th></tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $cat): ?>
          <tr>
            <td><?= $cat['id'] ?></td>
            <td><?= htmlspecialchars($cat['name'], ENT_QUOTES) ?></td>
            <td dir="ltr"><?= htmlspecialchars($cat['slug'], ENT_QUOTES) ?></td>
            <td><?= $cat['post_count'] ?></td>
            <td><span class="badge <?= $cat['status'] ? 'badge-success' : 'badge-outline' ?>"><?= $cat['status'] ? 'فعال' : 'غیرفعال' ?></span></td>
            <td>
              <div class="table-actions">
                <a href="<?= BASE_URL ?>/admin/blog/categories/<?= $cat['id'] ?>/edit" class="btn btn-ghost btn-sm">ویرایش</a>
                <form method="post" action="<?= BASE_URL ?>/admin/blog/categories/<?= $cat['id'] ?>/delete"
                      onsubmit="return confirm('آیا مطمئن هستید؟')">
                  <?= $csrf ?>
                  <button type="submit" class="btn btn-ghost btn-sm btn-danger">حذف</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Add / Edit Form -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title"><?= $editCat ? 'ویرایش دسته‌بندی' : 'افزودن دسته‌بندی' ?></h2>
    </div>
    <div class="p-20">
      <form method="post"
            action="<?= $editCat ? BASE_URL . '/admin/blog/categories/' . $editCat['id'] : BASE_URL . '/admin/blog/categories' ?>">
        <?= $csrf ?>
        <div class="form-group">
          <label class="form-label" for="cat_name">نام دسته <span class="required-star">*</span></label>
          <input type="text" id="cat_name" name="name" class="form-input"
                 value="<?= htmlspecialchars($editCat['name'] ?? '', ENT_QUOTES) ?>"
                 required maxlength="120"
                 oninput="autoCatSlug(this.value)">
        </div>
        <div class="form-group">
          <label class="form-label" for="cat_slug">اسلاگ</label>
          <input type="text" id="cat_slug" name="slug" class="form-input" dir="ltr"
                 value="<?= htmlspecialchars($editCat['slug'] ?? '', ENT_QUOTES) ?>" maxlength="140">
        </div>
        <div class="form-group">
          <label class="form-label" for="cat_sort">ترتیب</label>
          <input type="number" id="cat_sort" name="sort_order" class="form-input" dir="ltr"
                 value="<?= $editCat['sort_order'] ?? 0 ?>">
        </div>
        <div class="form-group">
          <label class="check-label">
            <input type="checkbox" name="status" class="check-input"
                   <?= !$editCat || $editCat['status'] ? 'checked' : '' ?>>
            <span class="checkmark"></span>
            فعال
          </label>
        </div>
        <div class="form-row mt-16">
          <button type="submit" class="btn btn-primary">
            <?= $editCat ? 'ذخیره تغییرات' : 'افزودن دسته' ?>
          </button>
          <?php if ($editCat): ?>
          <a href="<?= BASE_URL ?>/admin/blog/categories" class="btn btn-ghost">انصراف</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function autoCatSlug(name) {
  const f = document.getElementById('cat_slug');
  if (!f || f.dataset.manual === '1') return;
  const map = {'آ':'a','ا':'a','ب':'b','پ':'p','ت':'t','ث':'s','ج':'j','چ':'ch','ح':'h','خ':'kh','د':'d','ذ':'z','ر':'r','ز':'z','ژ':'zh','س':'s','ش':'sh','ص':'s','ض':'z','ط':'t','ظ':'z','ع':'a','غ':'gh','ف':'f','ق':'gh','ک':'k','گ':'g','ل':'l','م':'m','ن':'n','و':'v','ه':'h','ی':'y','ئ':'y',' ':'-','‌':'-'};
  let s = name;
  for (const [k,v] of Object.entries(map)) s = s.split(k).join(v);
  s = s.toLowerCase().replace(/[^a-z0-9\-]/g,'').replace(/-+/g,'-').replace(/^-|-$/g,'');
  f.value = s;
}
document.getElementById('cat_slug')?.addEventListener('input', function() { this.dataset.manual='1'; });
</script>
