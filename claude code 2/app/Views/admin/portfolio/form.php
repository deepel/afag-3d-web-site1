<div class="admin-page-header">
  <h1 class="admin-page-title"><?= $isEdit ? 'ویرایش نمونه کار' : 'افزودن نمونه کار جدید' ?></h1>
  <a href="<?= BASE_URL ?>/admin/portfolio" class="btn btn-ghost">بازگشت</a>
</div>

<form method="post"
      action="<?= $isEdit ? BASE_URL . '/admin/portfolio/' . $item['id'] : BASE_URL . '/admin/portfolio' ?>"
      enctype="multipart/form-data" class="admin-form">
  <?= $csrf ?>

  <div class="admin-form-grid">
    <div class="admin-form-main">

      <div class="admin-card mb-24">
        <div class="admin-card-header"><h2 class="admin-card-title">اطلاعات اصلی</h2></div>
        <div class="p-20">
          <div class="form-group">
            <label class="form-label" for="pf_title">عنوان پروژه <span class="required-star">*</span></label>
            <input type="text" id="pf_title" name="title" class="form-input"
                   value="<?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES) ?>" required maxlength="220">
          </div>
          <div class="form-group">
            <label class="form-label" for="pf_slug">اسلاگ URL</label>
            <input type="text" id="pf_slug" name="slug" class="form-input" dir="ltr"
                   value="<?= htmlspecialchars($item['slug'] ?? '', ENT_QUOTES) ?>" maxlength="240">
            <span class="form-hint">خودکار از عنوان ساخته می‌شود</span>
          </div>
          <div class="form-group">
            <label class="form-label" for="pf_client">کارفرما / مشتری</label>
            <input type="text" id="pf_client" name="client" class="form-input"
                   value="<?= htmlspecialchars($item['client'] ?? '', ENT_QUOTES) ?>" maxlength="120">
          </div>
          <div class="form-group">
            <label class="form-label" for="pf_tags">برچسب‌ها (با ویرگول جدا کنید)</label>
            <input type="text" id="pf_tags" name="tags" class="form-input"
                   value="<?= htmlspecialchars($item['tags'] ?? '', ENT_QUOTES) ?>"
                   placeholder="معماری, ماکت, رزین">
          </div>
          <div class="form-group">
            <label class="form-label" for="pf_description">توضیحات</label>
            <textarea id="pf_description" name="description" class="form-input form-textarea" rows="5"><?= htmlspecialchars($item['description'] ?? '', ENT_QUOTES) ?></textarea>
          </div>
        </div>
      </div>

      <!-- Images -->
      <div class="admin-card mb-24">
        <div class="admin-card-header"><h2 class="admin-card-title">تصاویر اضافی</h2></div>
        <div class="p-20">
          <?php if ($isEdit && !empty($images)): ?>
          <div class="existing-images mb-20">
            <?php foreach ($images as $img): ?>
            <div class="existing-image-item">
              <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($img['path'], ENT_QUOTES) ?>"
                   alt="<?= htmlspecialchars($img['caption'] ?? '', ENT_QUOTES) ?>">
              <form method="post" action="<?= BASE_URL ?>/admin/portfolio/image/<?= $img['id'] ?>/delete"
                    class="existing-image-delete"
                    onsubmit="return confirm('حذف شود؟')">
                <?= $csrf ?>
                <button type="submit" class="existing-image-remove" title="حذف">✕</button>
              </form>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <div class="form-group">
            <label class="form-label">آپلود تصاویر جدید (چندگانه)</label>
            <input type="file" name="extra_images[]" multiple class="form-input"
                   accept="image/jpeg,image/png,image/webp,image/gif">
            <span class="form-hint">فرمت‌های مجاز: JPG، PNG، WebP — حداکثر 5 مگابایت هر تصویر</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Side -->
    <div class="admin-form-side">

      <div class="admin-card mb-24">
        <div class="admin-card-header"><h2 class="admin-card-title">تصویر اصلی</h2></div>
        <div class="p-20">
          <?php if ($isEdit && $item['cover']): ?>
          <div class="current-cover mb-16">
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($item['cover'], ENT_QUOTES) ?>"
                 alt="تصویر فعلی" style="max-width:100%; border-radius:8px;">
            <p class="form-hint mt-4">برای تغییر، تصویر جدیدی انتخاب کنید</p>
          </div>
          <?php endif; ?>
          <input type="file" name="cover" class="form-input" accept="image/jpeg,image/png,image/webp,image/gif">
        </div>
      </div>

      <div class="admin-card mb-24">
        <div class="admin-card-header"><h2 class="admin-card-title">تنظیمات</h2></div>
        <div class="p-20">
          <div class="form-group">
            <label class="form-label" for="pf_sort">ترتیب نمایش</label>
            <input type="number" id="pf_sort" name="sort_order" class="form-input" dir="ltr"
                   value="<?= $item['sort_order'] ?? 0 ?>">
          </div>
          <div class="form-group">
            <label class="check-label">
              <input type="checkbox" name="is_featured" class="check-input"
                     <?= !empty($item['is_featured']) ? 'checked' : '' ?>>
              <span class="checkmark"></span>
              نمایش در صفحه اصلی (ویژه)
            </label>
          </div>
          <div class="form-group">
            <label class="check-label">
              <input type="checkbox" name="status" class="check-input"
                     <?= !isset($item) || !empty($item['status']) ? 'checked' : '' ?>>
              <span class="checkmark"></span>
              فعال
            </label>
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-full">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <?= $isEdit ? 'ذخیره تغییرات' : 'ایجاد نمونه کار' ?>
      </button>
    </div>
  </div>
</form>
