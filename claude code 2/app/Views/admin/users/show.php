<?php
/**
 * Admin — User Detail
 */
$roleMap   = ['admin' => 'مدیر', 'shop_owner' => 'فروشنده', 'customer' => 'مشتری'];
$statusMap = ['active' => 'فعال', 'inactive' => 'غیرفعال', 'banned' => 'مسدود'];

$orderStatusLabels = [
    'pending'    => 'در انتظار پرداخت',
    'paid'       => 'پرداخت شده',
    'processing' => 'در حال پردازش',
    'shipped'    => 'ارسال شده',
    'completed'  => 'تکمیل شده',
    'cancelled'  => 'لغو شده',
];
$printStatusLabels = [
    'pending'   => 'در انتظار بررسی',
    'quoting'   => 'در حال استعلام',
    'confirmed' => 'تایید شده',
    'printing'  => 'در حال چاپ',
    'done'      => 'تکمیل شده',
    'cancelled' => 'لغو شده',
];
?>
<div class="admin-page-header">
  <h1 class="admin-page-title">جزئیات کاربر</h1>
  <div class="admin-page-actions">
    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-ghost">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
      بازگشت به لیست
    </a>
  </div>
</div>

<!-- User Info Card -->
<div class="admin-card" style="margin-bottom:24px">
  <div class="admin-card-header">
    <h2 class="admin-card-title">اطلاعات کاربر #<?= Url::toPersianDigits((string)$user['id']) ?></h2>
  </div>
  <div class="admin-card-body">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;font-size:14px">
      <div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">نام</div>
        <div style="font-weight:500"><?= htmlspecialchars($user['name'], ENT_QUOTES) ?></div>
      </div>
      <div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">موبایل</div>
        <div dir="ltr"><?= htmlspecialchars($user['mobile'], ENT_QUOTES) ?></div>
      </div>
      <div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">ایمیل</div>
        <div dir="ltr"><?= htmlspecialchars($user['email'] ?? '—', ENT_QUOTES) ?></div>
      </div>
      <div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">نقش</div>
        <span class="role-badge role-<?= htmlspecialchars($user['role'], ENT_QUOTES) ?>">
          <?= $roleMap[$user['role']] ?? $user['role'] ?>
        </span>
      </div>
      <div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">وضعیت</div>
        <span class="status-badge status-<?= htmlspecialchars($user['status'], ENT_QUOTES) ?>">
          <?= $statusMap[$user['status']] ?? $user['status'] ?>
        </span>
      </div>
      <div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">تاریخ عضویت</div>
        <time><?= Url::toPersianDigits(date('Y/m/d H:i', strtotime($user['created_at']))) ?></time>
      </div>
      <?php if (!empty($user['last_login_at'])): ?>
      <div>
        <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">آخرین ورود</div>
        <time><?= Url::toPersianDigits(date('Y/m/d H:i', strtotime($user['last_login_at']))) ?></time>
      </div>
      <?php endif; ?>
    </div>

    <?php if ($user['id'] !== Auth::id()): ?>
    <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);display:flex;gap:12px;flex-wrap:wrap">
      <!-- Toggle Status -->
      <form method="post" action="<?= BASE_URL ?>/admin/users/<?= (int)$user['id'] ?>/toggle"
            onsubmit="return confirm('<?= $user['status'] === 'banned' ? 'این کاربر فعال شود؟' : 'این کاربر مسدود شود؟' ?>')">
        <?= $csrf ?>
        <button type="submit" class="btn <?= $user['status'] === 'banned' ? 'btn-success' : 'btn-danger' ?>">
          <?= $user['status'] === 'banned' ? 'فعال‌سازی کاربر' : 'مسدود کردن کاربر' ?>
        </button>
      </form>

      <?php if ($user['role'] !== 'admin'): ?>
      <!-- Make Admin -->
      <form method="post" action="<?= BASE_URL ?>/admin/users/<?= (int)$user['id'] ?>/admin"
            onsubmit="return confirm('این کاربر به مدیر ارتقا یابد؟')">
        <?= $csrf ?>
        <button type="submit" class="btn btn-ghost">ارتقا به مدیر</button>
      </form>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Orders -->
<div class="admin-card" style="margin-bottom:24px">
  <div class="admin-card-header">
    <h2 class="admin-card-title">سفارشات فروشگاه</h2>
  </div>
  <?php if (empty($orders)): ?>
    <div class="admin-card-body" style="color:var(--text-muted);font-size:14px">سفارشی ثبت نشده است.</div>
  <?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>شماره</th>
          <th>تاریخ</th>
          <th>مبلغ</th>
          <th>وضعیت</th>
          <th>جزئیات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
          <td><span class="admin-id">#<?= Url::toPersianDigits((string)$order['id']) ?></span></td>
          <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($order['created_at']))) ?></time></td>
          <td><?= Url::toPersianDigits(number_format((int)$order['total'])) ?> تومان</td>
          <td>
            <span class="status-badge status-<?= htmlspecialchars($order['status'], ENT_QUOTES) ?>">
              <?= $orderStatusLabels[$order['status']] ?? $order['status'] ?>
            </span>
          </td>
          <td>
            <a href="<?= BASE_URL ?>/admin/orders/<?= (int)$order['id'] ?>" class="btn-icon" title="مشاهده">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- Print Orders -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title">سفارشات چاپ</h2>
  </div>
  <?php if (empty($printOrders)): ?>
    <div class="admin-card-body" style="color:var(--text-muted);font-size:14px">سفارش چاپی ثبت نشده است.</div>
  <?php else: ?>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>شماره</th>
          <th>تاریخ</th>
          <th>نوع چاپ</th>
          <th>جنس</th>
          <th>وضعیت</th>
          <th>قیمت استعلام</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($printOrders as $po): ?>
        <tr>
          <td><span class="admin-id">#<?= Url::toPersianDigits((string)$po['id']) ?></span></td>
          <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($po['created_at']))) ?></time></td>
          <td><?= htmlspecialchars($po['print_type_label'] ?? '—', ENT_QUOTES) ?></td>
          <td><?= htmlspecialchars($po['material_label'] ?? '—', ENT_QUOTES) ?></td>
          <td>
            <span class="status-badge status-<?= htmlspecialchars($po['status'], ENT_QUOTES) ?>">
              <?= $printStatusLabels[$po['status']] ?? $po['status'] ?>
            </span>
          </td>
          <td>
            <?php if (!empty($po['quoted_price'])): ?>
              <strong><?= Url::toPersianDigits(number_format((int)$po['quoted_price'])) ?> تومان</strong>
            <?php else: ?>
              <span style="color:var(--text-muted)">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
