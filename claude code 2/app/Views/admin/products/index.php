<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title">محصولات</h1>
    <p class="admin-page-sub">مجموعاً <?= number_format($total) ?> محصول</p>
  </div>
  <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-primary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    افزودن محصول
  </a>
</div>

<!-- Filters -->
<form method="GET" action="<?= BASE_URL ?>/admin/products" class="admin-filter-bar">
  <input type="text" name="search" class="form-control" placeholder="جستجو در نام یا SKU…"
         value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES) ?>">
  <select name="shop_id" class="form-control">
    <option value="">همه فروشگاه‌ها</option>
    <?php foreach ($shops as $sh): ?>
    <option value="<?= $sh['id'] ?>" <?= ($filters['shop_id'] ?? '') == $sh['id'] ? 'selected' : '' ?>>
      <?= htmlspecialchars($sh['name'], ENT_QUOTES) ?>
    </option>
    <?php endforeach; ?>
  </select>
  <select name="status" class="form-control">
    <option value="">همه وضعیت‌ها</option>
    <option value="active"   <?= ($filters['status'] ?? '') === 'active'   ? 'selected' : '' ?>>فعال</option>
    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>غیرفعال</option>
    <option value="draft"    <?= ($filters['status'] ?? '') === 'draft'    ? 'selected' : '' ?>>پیش‌نویس</option>
  </select>
  <button type="submit" class="btn btn-ghost">جستجو</button>
  <a href="<?= BASE_URL ?>/admin/products" class="btn btn-ghost">پاک</a>
</form>

<!-- Table -->
<div class="admin-card">
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width:40px">#</th>
          <th style="width:60px">تصویر</th>
          <th>نام محصول</th>
          <th>فروشگاه</th>
          <th>قیمت</th>
          <th>موجودی</th>
          <th>وضعیت</th>
          <th style="width:120px">عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($products): ?>
        <?php foreach ($products as $p): ?>
        <tr>
          <td class="text-muted"><?= $p['id'] ?></td>
          <td>
            <?php if (!empty($p['main_image'])): ?>
              <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['main_image'], ENT_QUOTES) ?>"
                   alt="" style="width:44px;height:44px;object-fit:cover;border-radius:6px;">
            <?php else: ?>
              <div style="width:44px;height:44px;background:var(--surface-2);border-radius:6px;display:flex;align-items:center;justify-content:center;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <a href="<?= BASE_URL ?>/admin/products/<?= $p['id'] ?>/edit" class="table-link">
              <?= htmlspecialchars($p['name'], ENT_QUOTES) ?>
            </a>
            <?php if ($p['sku']): ?>
              <small class="text-muted d-block"><?= htmlspecialchars($p['sku'], ENT_QUOTES) ?></small>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($p['shop_name'] ?? '—', ENT_QUOTES) ?></td>
          <td><?= Url::price((int)$p['price']) ?></td>
          <td>
            <?php if ((int)$p['stock'] === 0): ?>
              <span class="badge-out">ناموجود</span>
            <?php elseif ((int)$p['stock'] <= 5): ?>
              <span class="badge-stock-low"><?= $p['stock'] ?></span>
            <?php else: ?>
              <span class="badge-stock-ok"><?= $p['stock'] ?></span>
            <?php endif; ?>
          </td>
          <td>
            <?php $statusMap = ['active'=>'فعال','inactive'=>'غیرفعال','draft'=>'پیش‌نویس']; ?>
            <span class="badge badge-<?= $p['status'] === 'active' ? 'success' : ($p['status'] === 'draft' ? 'warning' : 'danger') ?>">
              <?= $statusMap[$p['status']] ?? $p['status'] ?>
            </span>
          </td>
          <td>
            <div class="table-actions">
              <a href="<?= BASE_URL ?>/admin/products/<?= $p['id'] ?>/edit" class="btn-icon" title="ویرایش">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <form method="POST" action="<?= BASE_URL ?>/admin/products/<?= $p['id'] ?>/delete"
                    onsubmit="return confirm('آیا مطمئن هستید؟')" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">
                <button type="submit" class="btn-icon btn-icon--danger" title="حذف">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr><td colspan="8" class="text-center text-muted" style="padding:32px">محصولی یافت نشد.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
  <div class="admin-pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a href="?page=<?= $i ?>&search=<?= urlencode($filters['search'] ?? '') ?>&shop_id=<?= $filters['shop_id'] ?? '' ?>&status=<?= $filters['status'] ?? '' ?>"
         class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
