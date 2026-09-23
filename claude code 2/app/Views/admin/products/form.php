<?php
$isEdit    = $product !== null;
$actionUrl = $isEdit
    ? BASE_URL . '/admin/products/' . $product['id']
    : BASE_URL . '/admin/products';
$prodCats  = $prodCats  ?? [];
$prodAttrs = $prodAttrs ?? [];
$images    = $images    ?? [];
?>

<div class="admin-page-header">
  <h1 class="admin-page-title"><?= $isEdit ? 'ویرایش محصول' : 'افزودن محصول جدید' ?></h1>
  <a href="<?= BASE_URL ?>/admin/products" class="btn btn-ghost">← برگشت</a>
</div>

<form method="POST" action="<?= $actionUrl ?>" enctype="multipart/form-data" id="productForm">
  <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">

  <div class="admin-product-form">

    <!-- ══ LEFT: Main info ══ -->
    <div class="product-form-main">

      <!-- Basic Info -->
      <div class="admin-card">
        <h2 class="card-title">اطلاعات پایه</h2>
        <div class="form-group">
          <label class="form-label" for="name">نام محصول *</label>
          <input type="text" id="pname" name="name" class="form-control" required
                 value="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES) ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="slug">اسلاگ (URL)</label>
          <input type="text" id="pslug" name="slug" class="form-control" dir="ltr"
                 value="<?= htmlspecialchars($product['slug'] ?? '', ENT_QUOTES) ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="short_desc">توضیح کوتاه</label>
          <textarea id="short_desc" name="short_desc" class="form-control" rows="2"><?= htmlspecialchars($product['short_desc'] ?? '', ENT_QUOTES) ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label" for="description">توضیحات کامل</label>
          <textarea id="description" name="description" class="form-control" rows="8"><?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES) ?></textarea>
        </div>
      </div>

      <!-- Pricing & Stock -->
      <div class="admin-card">
        <h2 class="card-title">قیمت و موجودی</h2>
        <div class="form-row three-col">
          <div class="form-group">
            <label class="form-label" for="price">قیمت (تومان) *</label>
            <input type="number" id="price" name="price" class="form-control" min="0" required
                   value="<?= $product['price'] ?? 0 ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="compare_price">قیمت قبل از تخفیف</label>
            <input type="number" id="compare_price" name="compare_price" class="form-control" min="0"
                   value="<?= $product['compare_price'] ?? 0 ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="stock">موجودی</label>
            <input type="number" id="stock" name="stock" class="form-control" min="0"
                   value="<?= $product['stock'] ?? 0 ?>">
          </div>
        </div>
        <div class="form-row two-col">
          <div class="form-group">
            <label class="form-label" for="sku">کد محصول (SKU)</label>
            <input type="text" id="sku" name="sku" class="form-control" dir="ltr"
                   value="<?= htmlspecialchars($product['sku'] ?? '', ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="weight">وزن (گرم)</label>
            <input type="number" id="weight" name="weight" class="form-control" min="0" step="0.1"
                   value="<?= $product['weight'] ?? 0 ?>">
          </div>
        </div>

        <!-- ── Parametric pricing inputs ── -->
        <div class="form-row two-col" style="margin-top:4px;padding-top:12px;border-top:1px solid var(--line, #2a2522)">
          <div class="form-group">
            <label class="form-label" for="weight_grams">وزن چاپ (گرم) — ورودی قیمت‌گذاری</label>
            <input type="number" id="weight_grams" name="weight_grams" class="form-control" min="0"
                   value="<?= $product['weight_grams'] ?? '' ?>" placeholder="مثلاً 80">
          </div>
          <div class="form-group">
            <label class="form-label" for="print_hours">ساعت چاپ — ورودی قیمت‌گذاری</label>
            <input type="number" id="print_hours" name="print_hours" class="form-control" min="0" step="0.1"
                   value="<?= $product['print_hours'] ?? '' ?>" placeholder="مثلاً 4.5">
          </div>
        </div>
        <p class="form-hint" style="margin-top:8px;font-size:12.5px;color:var(--gray,#9a8)">
          اگر «قیمت» را خالی/صفر بگذارید و وزن و ساعت را پر کنید، قیمت از روی فرمول
          (<a href="<?= BASE_URL ?>/admin/pricing">تنظیمات قیمت‌گذاری</a>) خودکار حساب می‌شود.
          اگر قیمت را دستی وارد کنید، همان دستی می‌ماند و با «محاسبهٔ مجدد» تغییر نمی‌کند.
        </p>
      </div>

      <!-- Images -->
      <div class="admin-card">
        <h2 class="card-title">تصاویر</h2>
        <div class="form-group">
          <label class="form-label">تصویر اصلی</label>
          <?php if (!empty($product['image'])): ?>
          <div style="margin-bottom:12px">
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>"
                 style="width:120px;height:120px;object-fit:cover;border-radius:8px;" alt="">
          </div>
          <?php endif; ?>
          <input type="file" name="main_image" class="form-control" accept="image/jpeg,image/png,image/webp,image/svg+xml">
        </div>

        <?php if ($isEdit): ?>
        <div class="form-group">
          <label class="form-label">تصاویر اضافی</label>
          <div class="extra-images-grid">
            <?php foreach ($images as $img): ?>
            <div class="extra-image-item">
              <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($img['path'], ENT_QUOTES) ?>"
                   style="width:80px;height:80px;object-fit:cover;border-radius:6px;" alt="">
            </div>
            <?php endforeach; ?>
          </div>
          <!-- Upload additional images via AJAX -->
          <div style="margin-top:12px">
            <input type="file" id="extraImageFile" accept="image/*">
            <button type="button" class="btn btn-ghost btn-sm" onclick="uploadExtraImage(<?= $product['id'] ?>)">
              آپلود تصویر
            </button>
          </div>
          <div id="uploadMsg" style="font-size:13px;margin-top:8px"></div>
        </div>
        <?php endif; ?>
      </div>

      <!-- SEO Panel -->
      <div class="admin-card seo-panel">
        <h2 class="card-title">تنظیمات سئو</h2>
        <div class="form-group">
          <label class="form-label" for="meta_title">عنوان متا
            <span class="seo-len" id="metaTitleLen">0</span>/60
          </label>
          <input type="text" id="meta_title" name="meta_title" class="form-control"
                 value="<?= htmlspecialchars($product['meta_title'] ?? '', ENT_QUOTES) ?>">
          <div class="seo-meter"><div class="seo-meter-bar" id="metaTitleBar"></div></div>
        </div>
        <div class="form-group">
          <label class="form-label" for="meta_desc">توضیح متا
            <span class="seo-len" id="metaDescLen">0</span>/160
          </label>
          <textarea id="meta_desc" name="meta_desc" class="form-control" rows="3"><?= htmlspecialchars($product['meta_desc'] ?? '', ENT_QUOTES) ?></textarea>
          <div class="seo-meter"><div class="seo-meter-bar" id="metaDescBar"></div></div>
        </div>
        <div class="seo-checks" id="seoChecks"></div>
      </div>

    </div><!-- /.product-form-main -->

    <!-- ══ RIGHT: Sidebar ══ -->
    <div class="product-form-sidebar">

      <!-- Publish -->
      <div class="admin-card">
        <h2 class="card-title">انتشار</h2>
        <div class="form-group">
          <label class="form-label" for="status">وضعیت</label>
          <select id="status" name="status" class="form-control">
            <option value="active"   <?= ($product['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>فعال</option>
            <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>غیرفعال</option>
            <option value="draft"    <?= ($product['status'] ?? '') === 'draft'    ? 'selected' : '' ?>>پیش‌نویس</option>
          </select>
        </div>
        <div class="form-group">
          <label class="toggle-label">
            <input type="checkbox" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
            <span>محصول ویژه</span>
          </label>
        </div>
        <button type="submit" class="btn btn-primary w-full">ذخیره محصول</button>
      </div>

      <!-- Automation & archive -->
      <div class="admin-card">
        <h2 class="card-title">اتوماسیون و آرشیو</h2>
        <?php if ($isEdit && !empty($product['product_code'])): ?>
        <div class="form-group">
          <label class="form-label">کد محصول</label>
          <input type="text" class="form-control" dir="ltr" readonly
                 value="<?= htmlspecialchars($product['product_code'], ENT_QUOTES) ?>"
                 style="opacity:.8;cursor:default">
        </div>
        <?php endif; ?>
        <div class="form-group">
          <label class="form-label" for="lifecycle_status">وضعیت چرخهٔ محصول</label>
          <select id="lifecycle_status" name="lifecycle_status" class="form-control">
            <?php $lc = $product['lifecycle_status'] ?? 'available'; ?>
            <option value="pending"     <?= $lc === 'pending'     ? 'selected' : '' ?>>در انتظار</option>
            <option value="coming_soon" <?= $lc === 'coming_soon' ? 'selected' : '' ?>>به‌زودی</option>
            <option value="available"   <?= $lc === 'available'   ? 'selected' : '' ?>>منتشر شده</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="archive_folder">پوشهٔ آرشیو فایل STL (روی کامپیوتر شما)</label>
          <input type="text" id="archive_folder" name="archive_folder" class="form-control" dir="ltr"
                 value="<?= htmlspecialchars($product['archive_folder'] ?? '', ENT_QUOTES) ?>"
                 placeholder="مثلاً Dragons/AFAG-0042">
        </div>
      </div>

      <!-- Shop -->
      <div class="admin-card">
        <h2 class="card-title">فروشگاه</h2>
        <select name="shop_id" class="form-control">
          <option value="">انتخاب فروشگاه</option>
          <?php foreach ($shops as $sh): ?>
          <option value="<?= $sh['id'] ?>" <?= ($product['shop_id'] ?? '') == $sh['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($sh['name'], ENT_QUOTES) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Categories -->
      <div class="admin-card">
        <h2 class="card-title">دسته‌بندی‌ها</h2>
        <div class="checkbox-list">
          <?php foreach ($categories as $cat): ?>
          <label class="checkbox-item">
            <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>"
                   <?= in_array($cat['id'], $prodCats) ? 'checked' : '' ?>>
            <span><?= htmlspecialchars($cat['name'], ENT_QUOTES) ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Attributes -->
      <?php if ($attributes): ?>
      <div class="admin-card">
        <h2 class="card-title">ویژگی‌ها</h2>
        <?php foreach ($attributes as $attr): ?>
        <div class="attr-group">
          <div class="attr-group-name"><?= htmlspecialchars($attr['name'], ENT_QUOTES) ?></div>
          <div class="checkbox-list checkbox-list--compact">
            <?php foreach ($attr['values'] as $val): ?>
            <label class="checkbox-item">
              <input type="checkbox" name="attributes[]" value="<?= $val['id'] ?>"
                     <?= in_array($val['value'], $prodAttrs) ? 'checked' : '' ?>>
              <?php if (!empty($val['color_hex'])): ?>
                <span class="color-swatch" style="background:<?= htmlspecialchars($val['color_hex'], ENT_QUOTES) ?>"></span>
              <?php endif; ?>
              <span><?= htmlspecialchars($val['value'], ENT_QUOTES) ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div><!-- /.product-form-sidebar -->
  </div><!-- /.admin-product-form -->
</form>

<script>
(function() {
  // Auto-slug from name
  const nameEl  = document.getElementById('pname');
  const slugEl  = document.getElementById('pslug');
  if (nameEl && slugEl) {
    nameEl.addEventListener('input', function() {
      if (!slugEl.dataset.manual) {
        slugEl.value = nameEl.value
          .toLowerCase()
          .replace(/[؀-ۿݐ-ݿࢠ-ࣿﭐ-﷿ﹰ-﻿]/g, '')
          .replace(/\s+/g, '-')
          .replace(/[^a-z0-9\-]/g, '')
          .replace(/-+/g, '-')
          .replace(/^-|-$/g, '');
      }
    });
    slugEl.addEventListener('input', function() { slugEl.dataset.manual = '1'; });
  }

  // SEO meters
  function updateMeter(inputId, barId, lenId, min, max) {
    const el  = document.getElementById(inputId);
    const bar = document.getElementById(barId);
    const len = document.getElementById(lenId);
    if (!el || !bar || !len) return;
    function refresh() {
      const l = el.tagName === 'TEXTAREA' ? el.value.length : el.value.length;
      len.textContent = l;
      const pct = Math.min(100, (l / max) * 100);
      bar.style.width = pct + '%';
      bar.style.background = (l >= min && l <= max) ? 'var(--clr-ok)' : (l > max ? 'var(--clr-danger)' : 'var(--clr-warn)');
    }
    el.addEventListener('input', refresh);
    refresh();
  }
  updateMeter('meta_title', 'metaTitleBar', 'metaTitleLen', 50, 60);
  updateMeter('meta_desc',  'metaDescBar',  'metaDescLen',  120, 160);

  // SEO live checks
  function updateSeoChecks() {
    const name  = (document.getElementById('pname')?.value    || '').toLowerCase();
    const slug  = (document.getElementById('pslug')?.value    || '').toLowerCase();
    const title = (document.getElementById('meta_title')?.value || '').toLowerCase();
    const desc  = (document.getElementById('meta_desc')?.value  || '').toLowerCase();
    const kw    = name.split(' ')[0];

    const checks = [
      { label: 'عنوان متا 50–60 کاراکتر',     ok: title.length >= 50 && title.length <= 60 },
      { label: 'توضیح متا 120–160 کاراکتر',  ok: desc.length >= 120 && desc.length <= 160 },
      { label: 'کلیدواژه در عنوان متا',        ok: kw && title.includes(kw) },
      { label: 'کلیدواژه در اسلاگ',            ok: kw && slug.includes(kw) },
      { label: 'کلیدواژه در توضیح متا',        ok: kw && desc.includes(kw) },
    ];

    const box = document.getElementById('seoChecks');
    if (!box) return;
    box.innerHTML = checks.map(c =>
      `<div class="seo-check ${c.ok ? 'ok' : 'fail'}">
        <span>${c.ok ? '✓' : '✗'}</span> ${c.label}
      </div>`
    ).join('');
  }
  ['pname','pslug','meta_title','meta_desc'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', updateSeoChecks);
  });
  updateSeoChecks();
})();

function uploadExtraImage(productId) {
  const file = document.getElementById('extraImageFile').files[0];
  if (!file) { alert('فایلی انتخاب نشده.'); return; }
  const fd = new FormData();
  fd.append('image', file);
  fd.append('csrf_token', document.querySelector('[name=csrf_token]').value);
  fd.append('is_main', '0');
  fetch('<?= BASE_URL ?>/admin/products/' + productId + '/images', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      const msg = document.getElementById('uploadMsg');
      if (d.ok) {
        msg.textContent = 'تصویر آپلود شد.';
        msg.style.color = 'var(--clr-ok)';
        location.reload();
      } else {
        msg.textContent = d.msg;
        msg.style.color = 'var(--clr-danger)';
      }
    });
}
</script>
