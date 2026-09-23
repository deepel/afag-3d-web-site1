<?php
/**
 * Account Dashboard
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
      <a href="<?= BASE_URL ?>/account" class="active">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        داشبورد
      </a>
      <a href="<?= BASE_URL ?>/account/orders">
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

    <!-- Welcome -->
    <div class="account-card">
      <h2>خوش آمدید، <?= htmlspecialchars(Auth::name(), ENT_QUOTES) ?></h2>
      <p style="color:var(--gray);font-size:14px">از اینجا می‌توانید سفارشات، آدرس‌ها و اطلاعات حساب خود را مدیریت کنید.</p>
    </div>

    <!-- Summary cards -->
    <div class="account-card">
      <h2>خلاصه حساب</h2>
      <div class="summary-grid">
        <div class="summary-item">
          <div class="val"><?= Url::toPersianDigits((string)$totalOrders) ?></div>
          <div class="lbl">کل سفارشات</div>
        </div>
        <div class="summary-item">
          <div class="val"><?= Url::toPersianDigits((string)$pendingOrders) ?></div>
          <div class="lbl">سفارشات در جریان</div>
        </div>
        <div class="summary-item">
          <div class="val"><?= Url::toPersianDigits(number_format($totalSpent)) ?></div>
          <div class="lbl">مجموع خرید (تومان)</div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="account-card">
      <h2>آخرین سفارشات
        <a href="<?= BASE_URL ?>/account/orders" style="font-size:13px;font-weight:400;color:var(--orange);float:left">مشاهده همه</a>
      </h2>
      <?php if (empty($recentOrders)): ?>
        <div class="empty-state">
          <div class="icon">🛒</div>
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
                <th>وضعیت</th>
                <th>جزئیات</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentOrders as $order): ?>
              <tr>
                <td><strong>#<?= Url::toPersianDigits((string)$order['id']) ?></strong></td>
                <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($order['created_at']))) ?></time></td>
                <td><?= Url::toPersianDigits(number_format((int)$order['total'])) ?></td>
                <td><span class="status-badge status-<?= htmlspecialchars($order['status'], ENT_QUOTES) ?>"><?= $statusLabels[$order['status']] ?? $order['status'] ?></span></td>
                <td><a href="<?= BASE_URL ?>/account/orders/<?= (int)$order['id'] ?>" class="btn btn-ghost" style="padding:4px 12px;font-size:12px">مشاهده</a></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Recent Print Orders -->
    <?php if (!empty($recentPrintOrders)): ?>
    <div class="account-card">
      <h2>آخرین سفارشات چاپ
        <a href="<?= BASE_URL ?>/account/print-orders" style="font-size:13px;font-weight:400;color:var(--orange);float:left">مشاهده همه</a>
      </h2>
      <div style="overflow-x:auto">
        <table class="data-table">
          <thead>
            <tr>
              <th>شماره</th>
              <th>تاریخ</th>
              <th>نوع چاپ</th>
              <th>جنس</th>
              <th>وضعیت</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentPrintOrders as $po): ?>
            <tr>
              <td>#<?= Url::toPersianDigits((string)$po['id']) ?></td>
              <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($po['created_at']))) ?></time></td>
              <td><?= htmlspecialchars($po['print_type_label'] ?? '—', ENT_QUOTES) ?></td>
              <td><?= htmlspecialchars($po['material_label'] ?? '—', ENT_QUOTES) ?></td>
              <td><span class="status-badge status-<?= htmlspecialchars($po['status'], ENT_QUOTES) ?>"><?= htmlspecialchars($po['status'], ENT_QUOTES) ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /.account-main -->
</div><!-- /.account-layout -->
