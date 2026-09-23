<?php
/**
 * Account Orders List
 */
$statusLabels = [
    'pending'    => 'در انتظار پرداخت',
    'paid'       => 'پرداخت شده',
    'processing' => 'در حال پردازش',
    'shipped'    => 'ارسال شده',
    'completed'  => 'تکمیل شده',
    'cancelled'  => 'لغو شده',
];
?>
<div class="account-layout">

  <!-- ░░░ SIDEBAR ░░░ -->
  <aside class="account-sidebar">
    <nav aria-label="منوی حساب کاربری">
      <a href="<?= BASE_URL ?>/account">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        داشبورد
      </a>
      <a href="<?= BASE_URL ?>/account/orders" class="active">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        سفارش‌ها
      </a>
      <a href="<?= BASE_URL ?>/account/print-orders">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        سفارش چاپ
      </a>
      <a href="<?= BASE_URL ?>/account/addresses">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        آدرس‌ها
      </a>
      <a href="<?= BASE_URL ?>/account/profile">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        پروفایل
      </a>
      <a href="<?= BASE_URL ?>/account/password">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        تغییر رمز
      </a>
      <a href="<?= BASE_URL ?>/logout" style="color:var(--red,#ef4444)">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        خروج
      </a>
    </nav>
  </aside>

  <!-- ░░░ MAIN ░░░ -->
  <div class="account-main">
    <div class="account-card">
      <h2>سفارش‌های من
        <span style="font-size:13px;font-weight:400;color:var(--gray);float:left">مجموع: <?= Url::toPersianDigits((string)$total) ?> سفارش</span>
      </h2>

      <?php if (empty($orders)): ?>
        <div class="empty-state">
          <div class="icon">📦</div>
          <p>هنوز سفارشی ثبت نکرده‌اید.</p>
          <a href="<?= BASE_URL ?>/shop" class="btn btn-primary" style="margin-top:12px">مشاهده فروشگاه</a>
        </div>
      <?php else: ?>
        <div style="overflow-x:auto">
          <table class="data-table">
            <thead>
              <tr>
                <th>شماره سفارش</th>
                <th>تاریخ</th>
                <th>مبلغ (تومان)</th>
                <th>روش پرداخت</th>
                <th>وضعیت</th>
                <th>جزئیات</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order): ?>
              <tr>
                <td><strong>#<?= Url::toPersianDigits((string)$order['id']) ?></strong></td>
                <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($order['created_at']))) ?></time></td>
                <td><?= Url::toPersianDigits(number_format((int)$order['total'])) ?></td>
                <td><?= htmlspecialchars($order['payment_method'] ?? 'آنلاین', ENT_QUOTES) ?></td>
                <td><span class="status-badge status-<?= htmlspecialchars($order['status'], ENT_QUOTES) ?>"><?= $statusLabels[$order['status']] ?? $order['status'] ?></span></td>
                <td>
                  <a href="<?= BASE_URL ?>/account/orders/<?= (int)$order['id'] ?>" class="btn btn-ghost" style="padding:4px 12px;font-size:12px">جزئیات</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <div class="pagination" style="margin-top:20px;display:flex;gap:8px;justify-content:center">
          <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?page=<?= $i ?>"
               class="btn <?= $i === $page ? 'btn-primary' : 'btn-ghost' ?>"
               style="padding:6px 14px;font-size:13px"><?= Url::toPersianDigits((string)$i) ?></a>
          <?php endfor; ?>
        </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
