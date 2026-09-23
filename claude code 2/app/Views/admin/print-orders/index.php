<div class="admin-page-header">
  <h1 class="admin-page-title">سفارش‌های چاپ</h1>
</div>

<!-- Status Tabs -->
<div class="admin-tabs mb-24">
  <a href="<?= BASE_URL ?>/admin/print-orders"
     class="admin-tab <?= $activeStatus === '' ? 'active' : '' ?>">
    همه
    <span class="tab-badge"><?= array_sum($statusCounts) ?></span>
  </a>
  <?php foreach ($statusLabels as $s => $label): ?>
  <a href="<?= BASE_URL ?>/admin/print-orders?status=<?= $s ?>"
     class="admin-tab <?= $activeStatus === $s ? 'active' : '' ?>">
    <?= htmlspecialchars($label, ENT_QUOTES) ?>
    <?php if (($statusCounts[$s] ?? 0) > 0): ?>
    <span class="tab-badge <?= $s === 'pending' ? 'tab-badge--danger' : '' ?>"><?= $statusCounts[$s] ?></span>
    <?php endif; ?>
  </a>
  <?php endforeach; ?>
</div>

<?php
$statusBadgeClass = [
    'pending'   => 'badge-warning',
    'quoting'   => 'badge-info',
    'confirmed' => 'badge-success',
    'printing'  => 'badge-primary',
    'done'      => 'badge-success',
    'cancelled' => 'badge-danger',
];
?>

<div class="admin-card">
  <?php if (empty($orders)): ?>
  <div class="empty-state py-48">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="48" height="48"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    <p>هیچ سفارشی یافت نشد.</p>
  </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>مشتری</th>
          <th>موبایل</th>
          <th>نوع چاپ</th>
          <th>جنس</th>
          <th>فایل‌ها</th>
          <th>تاریخ</th>
          <th>وضعیت</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
          <td><?= $order['id'] ?></td>
          <td><?= htmlspecialchars($order['customer_name'] ?? '—', ENT_QUOTES) ?></td>
          <td dir="ltr"><?= htmlspecialchars($order['customer_mobile'] ?? '—', ENT_QUOTES) ?></td>
          <td><?= htmlspecialchars($order['print_type_name'] ?? '—', ENT_QUOTES) ?></td>
          <td><?= htmlspecialchars($order['material_name'] ?? '—', ENT_QUOTES) ?></td>
          <td><?= $order['file_count'] ?> فایل</td>
          <td><?= date('Y/m/d', strtotime($order['created_at'])) ?></td>
          <td>
            <span class="badge <?= $statusBadgeClass[$order['status']] ?? 'badge-outline' ?>">
              <?= htmlspecialchars($statusLabels[$order['status']] ?? $order['status'], ENT_QUOTES) ?>
            </span>
          </td>
          <td>
            <a href="<?= BASE_URL ?>/admin/print-orders/<?= $order['id'] ?>" class="btn btn-ghost btn-sm">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              مشاهده
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
  <div class="admin-pagination">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
    <a href="?status=<?= $activeStatus ?>&page=<?= $p ?>" class="page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>
