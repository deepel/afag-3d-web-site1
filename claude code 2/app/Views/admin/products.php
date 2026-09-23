<div class="admin-page-header">
  <h1 class="admin-page-title">مدیریت محصولات</h1>
  <div class="admin-page-actions">
    <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      محصول جدید
    </a>
  </div>
</div>

<div class="admin-card">
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>نام محصول</th>
          <th>فروشگاه</th>
          <th>قیمت</th>
          <th>موجودی</th>
          <th>وضعیت</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($products)): ?>
          <tr><td colspan="7" class="text-center text-muted">محصولی یافت نشد.</td></tr>
        <?php else: ?>
          <?php
          $statusMap = ['active'=>'فعال','draft'=>'پیش‌نویس','out_of_stock'=>'ناموجود'];
          foreach ($products as $p):
          ?>
          <tr>
            <td><span class="admin-id">#<?= Url::toPersianDigits((string)$p['id']) ?></span></td>
            <td>
              <div class="product-name-cell">
                <strong><?= htmlspecialchars($p['name'], ENT_QUOTES) ?></strong>
                <small class="text-muted" dir="ltr"><?= htmlspecialchars($p['sku'] ?? '', ENT_QUOTES) ?></small>
              </div>
            </td>
            <td><?= htmlspecialchars($p['shop_name'] ?? '—', ENT_QUOTES) ?></td>
            <td><?= Url::price((int)$p['price']) ?></td>
            <td><?= Url::toPersianDigits((string)$p['stock']) ?></td>
            <td><span class="status-badge status-<?= htmlspecialchars($p['status'], ENT_QUOTES) ?>"><?= $statusMap[$p['status']] ?? $p['status'] ?></span></td>
            <td>
              <div class="table-actions">
                <a href="<?= BASE_URL ?>/admin/products/<?= (int)$p['id'] ?>/edit" class="btn-icon" title="ویرایش">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <a href="<?= BASE_URL ?>/product/<?= htmlspecialchars($p['slug'], ENT_QUOTES) ?>" target="_blank" class="btn-icon" title="مشاهده">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
