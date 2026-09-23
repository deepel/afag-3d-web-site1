<div class="admin-page-header">
  <h1 class="admin-page-title">مدیریت سفارشات</h1>
  <div class="admin-page-actions">
    <span class="text-muted">مجموع: <?= Url::toPersianDigits((string)$total) ?> سفارش</span>
  </div>
</div>

<div class="admin-card">
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>مشتری</th>
          <th>مبلغ کل</th>
          <th>وضعیت</th>
          <th>پرداخت</th>
          <th>تاریخ</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($orders)): ?>
          <tr><td colspan="7" class="text-center text-muted">سفارشی ثبت نشده است.</td></tr>
        <?php else: ?>
          <?php
          $statusMap  = ['pending'=>'در انتظار','processing'=>'در پردازش','shipped'=>'ارسال شده','delivered'=>'تحویل','cancelled'=>'لغو','refunded'=>'بازگشت'];
          $payMap     = ['unpaid'=>'پرداخت نشده','paid'=>'پرداخت شده','refunded'=>'بازگشت'];
          foreach ($orders as $order):
          ?>
          <tr>
            <td><span class="admin-id">#<?= Url::toPersianDigits((string)$order['id']) ?></span></td>
            <td><?= htmlspecialchars($order['user_name'] ?? '—', ENT_QUOTES) ?></td>
            <td><?= Url::price((int)$order['total']) ?></td>
            <td><span class="status-badge status-<?= htmlspecialchars($order['status'], ENT_QUOTES) ?>"><?= $statusMap[$order['status']] ?? $order['status'] ?></span></td>
            <td><span class="status-badge status-<?= htmlspecialchars($order['payment_status'], ENT_QUOTES) ?>"><?= $payMap[$order['payment_status']] ?? $order['payment_status'] ?></span></td>
            <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($order['created_at']))) ?></time></td>
            <td>
              <div class="table-actions">
                <a href="<?= BASE_URL ?>/admin/orders/<?= (int)$order['id'] ?>" class="btn-icon" title="جزئیات">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
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
