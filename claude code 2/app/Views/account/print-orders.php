<?php
/**
 * Account Print Orders
 */
$statusLabels = [
    'pending'   => 'در انتظار بررسی',
    'quoting'   => 'در حال استعلام',
    'confirmed' => 'تایید شده',
    'printing'  => 'در حال چاپ',
    'done'      => 'تکمیل شده',
    'cancelled' => 'لغو شده',
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
      <a href="<?= BASE_URL ?>/account/orders">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        سفارش‌ها
      </a>
      <a href="<?= BASE_URL ?>/account/print-orders" class="active">
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
      <h2>سفارشات چاپ سه‌بعدی
        <a href="<?= BASE_URL ?>/print-order" class="btn btn-primary" style="float:left;padding:6px 16px;font-size:13px">+ سفارش جدید</a>
      </h2>

      <?php if (empty($printOrders)): ?>
        <div class="empty-state">
          <div class="icon">🖨️</div>
          <p>هنوز سفارش چاپی ثبت نکرده‌اید.</p>
          <a href="<?= BASE_URL ?>/print-order" class="btn btn-primary" style="margin-top:12px">ثبت سفارش چاپ</a>
        </div>
      <?php else: ?>
        <div style="overflow-x:auto">
          <table class="data-table">
            <thead>
              <tr>
                <th>شماره</th>
                <th>تاریخ</th>
                <th>نوع چاپ</th>
                <th>جنس</th>
                <th>وضعیت</th>
                <th>قیمت استعلام</th>
                <th>یادداشت ادمین</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($printOrders as $po): ?>
              <tr>
                <td>#<?= Url::toPersianDigits((string)$po['id']) ?></td>
                <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($po['created_at']))) ?></time></td>
                <td><?= htmlspecialchars($po['print_type_label'] ?? '—', ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($po['material_label'] ?? '—', ENT_QUOTES) ?></td>
                <td>
                  <span class="status-badge status-<?= htmlspecialchars($po['status'], ENT_QUOTES) ?>">
                    <?= $statusLabels[$po['status']] ?? htmlspecialchars($po['status'], ENT_QUOTES) ?>
                  </span>
                </td>
                <td>
                  <?php if (!empty($po['quoted_price'])): ?>
                    <span style="color:var(--orange);font-weight:700"><?= Url::toPersianDigits(number_format((int)$po['quoted_price'])) ?> تومان</span>
                  <?php else: ?>
                    <span style="color:var(--gray)">—</span>
                  <?php endif; ?>
                </td>
                <td style="max-width:200px;font-size:13px;color:var(--gray)">
                  <?= htmlspecialchars($po['admin_note'] ?? '—', ENT_QUOTES) ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
