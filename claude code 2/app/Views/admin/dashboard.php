<div class="admin-page-header">
  <h1 class="admin-page-title">داشبورد</h1>
  <div class="admin-page-actions">
    <span class="admin-date"><?= Url::toPersianDigits(date('Y/m/d')) ?></span>
  </div>
</div>

<!-- ░░░ STAT CARDS ░░░ -->
<div class="admin-stats-grid">
  <div class="admin-stat-card">
    <div class="asc-icon asc-icon--blue">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <div class="asc-body">
      <div class="asc-value"><?= Url::toPersianDigits((string)$stats['total_users']) ?></div>
      <div class="asc-label">کل کاربران</div>
    </div>
    <a href="<?= BASE_URL ?>/admin/users" class="asc-link" aria-label="مشاهده کاربران"></a>
  </div>

  <div class="admin-stat-card">
    <div class="asc-icon asc-icon--orange">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
    </div>
    <div class="asc-body">
      <div class="asc-value"><?= Url::toPersianDigits((string)$stats['total_products']) ?></div>
      <div class="asc-label">کل محصولات</div>
    </div>
    <a href="<?= BASE_URL ?>/admin/products" class="asc-link" aria-label="مشاهده محصولات"></a>
  </div>

  <div class="admin-stat-card">
    <div class="asc-icon asc-icon--green">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
    </div>
    <div class="asc-body">
      <div class="asc-value"><?= Url::toPersianDigits((string)$stats['total_orders']) ?></div>
      <div class="asc-label">کل سفارشات</div>
    </div>
    <a href="<?= BASE_URL ?>/admin/orders" class="asc-link" aria-label="مشاهده سفارشات"></a>
  </div>

  <div class="admin-stat-card">
    <div class="asc-icon asc-icon--purple">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div class="asc-body">
      <div class="asc-value"><?= Url::price($stats['total_revenue'], false) ?></div>
      <div class="asc-label">درآمد کل (تومان)</div>
    </div>
  </div>
</div>

<!-- ░░░ CHARTS ROW ░░░ -->
<div class="admin-row">
  <!-- Recent Orders -->
  <div class="admin-card admin-card--lg">
    <div class="admin-card-header">
      <h2 class="admin-card-title">سفارشات اخیر</h2>
      <a href="<?= BASE_URL ?>/admin/orders" class="admin-card-action">مشاهده همه</a>
    </div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>#</th>
            <th>مشتری</th>
            <th>مبلغ</th>
            <th>وضعیت</th>
            <th>تاریخ</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentOrders)): ?>
            <tr><td colspan="5" class="text-center text-muted">سفارشی ثبت نشده است.</td></tr>
          <?php else: ?>
            <?php foreach ($recentOrders as $order): ?>
            <tr>
              <td><span class="admin-id">#<?= Url::toPersianDigits((string)$order['id']) ?></span></td>
              <td><?= htmlspecialchars($order['user_name'] ?? '—', ENT_QUOTES) ?></td>
              <td><?= Url::price((int)$order['total']) ?></td>
              <td>
                <span class="status-badge status-<?= htmlspecialchars($order['status'], ENT_QUOTES) ?>">
                  <?php $statusMap = ['pending'=>'در انتظار','processing'=>'در حال پردازش','shipped'=>'ارسال شده','delivered'=>'تحویل داده شده','cancelled'=>'لغو شده','refunded'=>'بازگشت وجه']; ?>
                  <?= $statusMap[$order['status']] ?? $order['status'] ?>
                </span>
              </td>
              <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($order['created_at']))) ?></time></td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Order status breakdown -->
  <div class="admin-card admin-card--sm">
    <div class="admin-card-header">
      <h2 class="admin-card-title">وضعیت سفارشات</h2>
    </div>
    <ul class="admin-status-list">
      <?php
      $statusColors = ['pending'=>'orange','processing'=>'blue','shipped'=>'purple','delivered'=>'green','cancelled'=>'red','refunded'=>'gray'];
      $statusNames  = ['pending'=>'در انتظار','processing'=>'در پردازش','shipped'=>'ارسال شده','delivered'=>'تحویل','cancelled'=>'لغو شده','refunded'=>'بازگشت'];
      foreach ($statusColors as $st => $color):
        $cnt = $orderStats[$st] ?? 0;
      ?>
      <li class="asl-item">
        <span class="asl-dot asl-dot--<?= $color ?>"></span>
        <span class="asl-name"><?= $statusNames[$st] ?></span>
        <span class="asl-count"><?= Url::toPersianDigits((string)$cnt) ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<!-- ░░░ RECENT USERS ░░░ -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title">آخرین کاربران</h2>
    <a href="<?= BASE_URL ?>/admin/users" class="admin-card-action">مشاهده همه</a>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>نام</th>
          <th>موبایل</th>
          <th>نقش</th>
          <th>وضعیت</th>
          <th>تاریخ ثبت</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentUsers)): ?>
          <tr><td colspan="6" class="text-center text-muted">کاربری ثبت نشده است.</td></tr>
        <?php else: ?>
          <?php foreach ($recentUsers as $user): ?>
          <tr>
            <td><span class="admin-id">#<?= Url::toPersianDigits((string)$user['id']) ?></span></td>
            <td><?= htmlspecialchars($user['name'], ENT_QUOTES) ?></td>
            <td dir="ltr"><?= htmlspecialchars($user['mobile'], ENT_QUOTES) ?></td>
            <td>
              <?php $roleMap = ['admin'=>'مدیر','shop_owner'=>'فروشنده','customer'=>'مشتری']; ?>
              <span class="role-badge role-<?= htmlspecialchars($user['role'], ENT_QUOTES) ?>"><?= $roleMap[$user['role']] ?? $user['role'] ?></span>
            </td>
            <td>
              <?php $statusMap2 = ['active'=>'فعال','inactive'=>'غیرفعال','banned'=>'مسدود']; ?>
              <span class="status-badge status-<?= htmlspecialchars($user['status'], ENT_QUOTES) ?>"><?= $statusMap2[$user['status']] ?? $user['status'] ?></span>
            </td>
            <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($user['created_at']))) ?></time></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
