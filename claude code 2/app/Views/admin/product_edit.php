<div class="admin-content">
  <div class="admin-header">
    <h1 class="admin-title"><?= $product ? 'ویرایش محصول' : 'محصول جدید' ?></h1>
    <a href="<?= BASE_URL ?>/admin/products" class="btn btn-ghost">← بازگشت به لیست</a>
  </div>

  <form method="POST" action="<?= BASE_URL ?>/admin/products/<?= $product ? $product['id'] : '0' ?>/edit" enctype="multipart/form-data">
    <?= $csrf ?>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">

      <!-- ── Main fields ── -->
      <div>

        <!-- Basic Info -->
        <div class="admin-card" style="margin-bottom:20px;">
          <h3 class="admin-card-title">اطلاعات اصلی</h3>
          <div class="form-group">
            <label class="form-label" for="name">نام محصول *</label>
            <input type="text" id="name" name="name" class="form-input" required
                   value="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES) ?>">
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label" for="sku">کد محصول (SKU)</label>
              <input type="text" id="sku" name="sku" class="form-input" dir="ltr"
                     value="<?= htmlspecialchars($product['sku'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="form-group">
              <label class="form-label" for="weight">وزن (گرم)</label>
              <input type="number" id="weight" name="weight" class="form-input" min="0"
                     value="<?= (int)($product['weight'] ?? 0) ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="description">توضیحات</label>
            <textarea id="description" name="description" class="form-input" rows="6" style="resize:vertical;"><?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES) ?></textarea>
          </div>
        </div>

        <!-- Pricing & Stock -->
        <div class="admin-card" style="margin-bottom:20px;">
          <h3 class="admin-card-title">قیمت و موجودی</h3>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
            <div class="form-group">
              <label class="form-label" for="price">قیمت (تومان) *</label>
              <input type="number" id="price" name="price" class="form-input" min="0" required
                     value="<?= (int)($product['price'] ?? 0) ?>">
            </div>
            <div class="form-group">
              <label class="form-label" for="compare_price">قیمت قبل از تخفیف</label>
              <input type="number" id="compare_price" name="compare_price" class="form-input" min="0"
                     value="<?= (int)($product['compare_price'] ?? 0) ?: '' ?>">
            </div>
            <div class="form-group">
              <label class="form-label" for="stock">موجودی</label>
              <input type="number" id="stock" name="stock" class="form-input" min="0"
                     value="<?= (int)($product['stock'] ?? 0) ?>">
            </div>
          </div>
        </div>

        <!-- Images -->
        <div class="admin-card" style="margin-bottom:20px;">
          <h3 class="admin-card-title">تصاویر</h3>
          <?php if ($images): ?>
          <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
            <?php foreach ($images as $img): ?>
            <div style="position:relative;">
              <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($img['path'], ENT_QUOTES) ?>"
                   style="width:80px;height:80px;object-fit:cover;border-radius:4px;border:1px solid var(--line);">
              <?php if ($img['is_cover']): ?>
              <span style="position:absolute;bottom:2px;right:2px;background:var(--orange);color:#fff;font-size:9px;padding:1px 4px;border-radius:2px;">اصلی</span>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <div class="form-group">
            <label class="form-label">آپلود تصویر جدید (چند فایل انتخاب کنید)</label>
            <input type="file" name="images[]" multiple accept="image/*" class="form-input"
                   style="padding:10px;cursor:pointer;">
            <p style="font-size:12px;color:var(--concrete);margin-top:6px;">فرمت‌های مجاز: JPG, PNG, WebP — حداکثر 10MB</p>
          </div>
        </div>

        <!-- Categories -->
        <div class="admin-card">
          <h3 class="admin-card-title">دسته‌بندی‌ها</h3>
          <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <?php foreach ($allCats as $cat): ?>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
              <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>"
                     <?= in_array($cat['id'], $productCats) ? 'checked' : '' ?>>
              <span><?= htmlspecialchars($cat['name'], ENT_QUOTES) ?></span>
            </label>
            <?php endforeach; ?>
            <?php if (!$allCats): ?>
            <p style="color:var(--concrete);font-size:13px;">هنوز دسته‌بندی ایجاد نشده است.</p>
            <?php endif; ?>
          </div>
        </div>

      </div><!-- /.main fields -->

      <!-- ── Sidebar ── -->
      <div>

        <!-- Status -->
        <div class="admin-card" style="margin-bottom:16px;">
          <h3 class="admin-card-title">وضعیت</h3>
          <select name="status" class="form-input" style="margin-bottom:14px;">
            <option value="active"  <?= ($product['status'] ?? '') === 'active'  ? 'selected' : '' ?>>فعال</option>
            <option value="draft"   <?= ($product['status'] ?? '') === 'draft'   ? 'selected' : '' ?>>پیش‌نویس</option>
            <option value="archive" <?= ($product['status'] ?? '') === 'archive' ? 'selected' : '' ?>>آرشیو</option>
          </select>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
            <input type="checkbox" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
            <span>محصول ویژه</span>
          </label>
        </div>

        <!-- Shop -->
        <div class="admin-card" style="margin-bottom:16px;">
          <h3 class="admin-card-title">فروشگاه</h3>
          <select name="shop_id" class="form-input">
            <?php foreach ($shops as $shop): ?>
            <option value="<?= $shop['id'] ?>" <?= ($product['shop_id'] ?? '') == $shop['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($shop['name'], ENT_QUOTES) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Submit -->
        <div class="admin-card">
          <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;">
            <?= $product ? 'ذخیره تغییرات' : 'ایجاد محصول' ?>
          </button>
          <?php if ($product): ?>
          <a href="<?= BASE_URL ?>/admin/products/<?= $product['id'] ?>/delete"
             onclick="return confirm('آیا از حذف این محصول مطمئن هستید؟')"
             class="btn btn-ghost" style="width:100%;margin-top:8px;text-align:center;color:#ef4444;">
            حذف محصول
          </a>
          <?php endif; ?>
        </div>

      </div><!-- /.sidebar -->
    </div>
  </form>
</div>
