<?php
$isEdit = $category !== null;
$action = $isEdit
    ? BASE_URL . '/admin/categories/' . $category['id']
    : BASE_URL . '/admin/categories';
?>
<div class="admin-page-header">
  <h1 class="admin-page-title"><?= $isEdit ? 'ویرایش دسته‌بندی' : 'افزودن دسته‌بندی' ?></h1>
  <a href="<?= BASE_URL ?>/admin/categories" class="btn btn-ghost">← برگشت</a>
</div>

<div class="admin-card" style="max-width:640px">
  <form method="POST" action="<?= $action ?>">
    <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">

    <div class="form-group">
      <label class="form-label" for="name">نام دسته‌بندی *</label>
      <input type="text" id="catName" name="name" class="form-control" required
             value="<?= htmlspecialchars($category['name'] ?? '', ENT_QUOTES) ?>">
    </div>
    <div class="form-group">
      <label class="form-label" for="slug">اسلاگ</label>
      <input type="text" id="catSlug" name="slug" class="form-control" dir="ltr"
             value="<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES) ?>">
    </div>
    <div class="form-group">
      <label class="form-label" for="shop_id">فروشگاه</label>
      <select name="shop_id" class="form-control">
        <option value="">انتخاب کنید</option>
        <?php foreach ($shops as $sh): ?>
        <option value="<?= $sh['id'] ?>" <?= ($category['shop_id'] ?? '') == $sh['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($sh['name'], ENT_QUOTES) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label" for="parent_id">دسته والد</label>
      <select name="parent_id" class="form-control">
        <option value="">— بدون والد —</option>
        <?php foreach ($allCats as $ac): ?>
          <?php if ($isEdit && $ac['id'] === $category['id']) continue; ?>
          <option value="<?= $ac['id'] ?>" <?= ($category['parent_id'] ?? '') == $ac['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($ac['name'], ENT_QUOTES) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label" for="description">توضیحات</label>
      <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($category['description'] ?? '', ENT_QUOTES) ?></textarea>
    </div>
    <div class="form-group">
      <label class="form-label" for="sort_order">ترتیب نمایش</label>
      <input type="number" name="sort_order" class="form-control" value="<?= $category['sort_order'] ?? 0 ?>">
    </div>

    <button type="submit" class="btn btn-primary">ذخیره</button>
  </form>
</div>

<script>
(function(){
  const n = document.getElementById('catName');
  const s = document.getElementById('catSlug');
  if (n && s) {
    n.addEventListener('input', function(){
      if (!s.dataset.manual) {
        s.value = n.value.toLowerCase().replace(/\s+/g,'-').replace(/[^a-z0-9\-]/g,'').replace(/-+/g,'-').replace(/^-|-$/g,'');
      }
    });
    s.addEventListener('input', function(){ s.dataset.manual='1'; });
  }
})();
</script>
