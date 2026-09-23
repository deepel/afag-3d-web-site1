<div class="admin-page-header">
  <h1 class="admin-page-title"><?= $isEdit ? 'ویرایش مقاله' : 'مقاله جدید' ?></h1>
  <a href="<?= BASE_URL ?>/admin/blog" class="btn btn-ghost">بازگشت</a>
</div>

<form method="post"
      action="<?= $isEdit ? BASE_URL . '/admin/blog/' . $post['id'] : BASE_URL . '/admin/blog' ?>"
      enctype="multipart/form-data" class="admin-form">
  <?= $csrf ?>

  <div class="admin-form-grid">
    <div class="admin-form-main">

      <div class="admin-card mb-24">
        <div class="admin-card-header"><h2 class="admin-card-title">محتوا</h2></div>
        <div class="p-20">
          <div class="form-group">
            <label class="form-label" for="bf_title">عنوان مقاله <span class="required-star">*</span></label>
            <input type="text" id="bf_title" name="title" class="form-input"
                   value="<?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES) ?>"
                   required maxlength="255" oninput="autoSlug(this.value)">
          </div>
          <div class="form-group">
            <label class="form-label" for="bf_slug">اسلاگ</label>
            <input type="text" id="bf_slug" name="slug" class="form-input" dir="ltr"
                   value="<?= htmlspecialchars($post['slug'] ?? '', ENT_QUOTES) ?>" maxlength="280">
          </div>
          <div class="form-group">
            <label class="form-label" for="bf_excerpt">خلاصه مقاله</label>
            <textarea id="bf_excerpt" name="excerpt" class="form-input form-textarea" rows="3"
                      maxlength="500" placeholder="یک پاراگراف خلاصه برای نمایش در لیست..."><?= htmlspecialchars($post['excerpt'] ?? '', ENT_QUOTES) ?></textarea>
          </div>
          <div class="form-group">
            <label class="form-label" for="bf_body">متن مقاله</label>
            <textarea id="bf_body" name="body" class="form-input form-textarea blog-body-editor" rows="20"><?= htmlspecialchars($post['body'] ?? '', ENT_QUOTES) ?></textarea>
            <span class="form-hint">HTML ساده قابل استفاده است (h2، p، ul، blockquote، ...)</span>
          </div>
        </div>
      </div>

      <!-- SEO Panel -->
      <div class="admin-card mb-24">
        <div class="admin-card-header">
          <h2 class="admin-card-title">سئو</h2>
          <button type="button" class="btn btn-ghost btn-sm" onclick="this.closest('.admin-card').querySelector('.seo-panel').hidden = !this.closest('.admin-card').querySelector('.seo-panel').hidden">
            نمایش / پنهان
          </button>
        </div>
        <div class="seo-panel p-20" hidden>
          <div class="form-group">
            <label class="form-label" for="bf_meta_title">Meta Title</label>
            <input type="text" id="bf_meta_title" name="meta_title" class="form-input" dir="ltr"
                   value="<?= htmlspecialchars($post['meta_title'] ?? '', ENT_QUOTES) ?>" maxlength="255">
          </div>
          <div class="form-group">
            <label class="form-label" for="bf_meta_desc">Meta Description</label>
            <textarea id="bf_meta_desc" name="meta_desc" class="form-input form-textarea" rows="3"
                      maxlength="400"><?= htmlspecialchars($post['meta_desc'] ?? '', ENT_QUOTES) ?></textarea>
          </div>
        </div>
      </div>

    </div>

    <!-- Side -->
    <div class="admin-form-side">

      <div class="admin-card mb-24">
        <div class="admin-card-header"><h2 class="admin-card-title">انتشار</h2></div>
        <div class="p-20">
          <div class="form-group">
            <label class="form-label" for="bf_status">وضعیت</label>
            <select name="status" id="bf_status" class="form-input">
              <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>پیش‌نویس</option>
              <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>منتشرشده</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="bf_cat">دسته‌بندی</label>
            <select name="category_id" id="bf_cat" class="form-input">
              <option value="">انتخاب کنید</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="bf_tags">برچسب‌ها (با ویرگول)</label>
            <input type="text" id="bf_tags" name="tags" class="form-input"
                   value="<?= htmlspecialchars($post['tags'] ?? '', ENT_QUOTES) ?>"
                   placeholder="PLA, چاپ سه‌بعدی, آموزش">
          </div>
        </div>
      </div>

      <div class="admin-card mb-24">
        <div class="admin-card-header"><h2 class="admin-card-title">تصویر بندانگشتی</h2></div>
        <div class="p-20">
          <?php if ($isEdit && !empty($post['cover'])): ?>
          <div class="current-cover mb-16">
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($post['cover'], ENT_QUOTES) ?>"
                 alt="" style="max-width:100%; border-radius:8px;">
            <p class="form-hint mt-4">برای تغییر، تصویر جدیدی انتخاب کنید</p>
          </div>
          <?php endif; ?>
          <input type="file" name="cover" class="form-input" accept="image/jpeg,image/png,image/webp,image/gif">
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-full">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <?= $isEdit ? 'ذخیره تغییرات' : 'ایجاد مقاله' ?>
      </button>

      <?php if ($isEdit): ?>
      <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>"
         target="_blank" class="btn btn-ghost w-full mt-8">مشاهده مقاله</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<script>
function autoSlug(title) {
  const slugField = document.getElementById('bf_slug');
  if (!slugField || slugField.dataset.manual === '1') return;
  // Basic Persian-to-latin transliteration
  const map = {'آ':'a','ا':'a','ب':'b','پ':'p','ت':'t','ث':'s','ج':'j','چ':'ch','ح':'h','خ':'kh','د':'d','ذ':'z','ر':'r','ز':'z','ژ':'zh','س':'s','ش':'sh','ص':'s','ض':'z','ط':'t','ظ':'z','ع':'a','غ':'gh','ف':'f','ق':'gh','ک':'k','گ':'g','ل':'l','م':'m','ن':'n','و':'v','ه':'h','ی':'y','ئ':'y',' ':'-','‌':'-'};
  let slug = title;
  for (const [k, v] of Object.entries(map)) slug = slug.split(k).join(v);
  slug = slug.toLowerCase().replace(/[^a-z0-9\-]/g, '').replace(/-+/g, '-').replace(/^-|-$/g, '');
  slugField.value = slug;
}
document.getElementById('bf_slug')?.addEventListener('input', function() {
  this.dataset.manual = '1';
});
</script>
