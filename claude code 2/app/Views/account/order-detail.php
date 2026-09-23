<?php
/**
 * Account Order Detail
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

    <div style="margin-bottom:16px">
      <a href="<?= BASE_URL ?>/account/orders" style="color:var(--gray);font-size:14px;display:inline-flex;align-items:center;gap:6px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
        بازگشت به سفارشات
      </a>
    </div>

    <!-- Order Info -->
    <div class="account-card">
      <h2>سفارش #<?= Url::toPersianDigits((string)$order['id']) ?></h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px">
        <div>
          <div style="font-size:12px;color:var(--gray);margin-bottom:4px">تاریخ ثبت</div>
          <div><?= Url::toPersianDigits(date('Y/m/d H:i', strtotime($order['created_at']))) ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:var(--gray);margin-bottom:4px">وضعیت</div>
          <span class="status-badge status-<?= htmlspecialchars($order['status'], ENT_QUOTES) ?>"><?= $statusLabels[$order['status']] ?? $order['status'] ?></span>
        </div>
        <div>
          <div style="font-size:12px;color:var(--gray);margin-bottom:4px">مبلغ کل</div>
          <div style="font-weight:700;color:var(--orange)"><?= Url::toPersianDigits(number_format((int)$order['total'])) ?> تومان</div>
        </div>
        <?php if (!empty($order['ref_id'])): ?>
        <div>
          <div style="font-size:12px;color:var(--gray);margin-bottom:4px">کد رهگیری</div>
          <div dir="ltr"><?= htmlspecialchars($order['ref_id'], ENT_QUOTES) ?></div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Order Items -->
    <div class="account-card">
      <h2>اقلام سفارش</h2>
      <?php if (empty($items)): ?>
        <p style="color:var(--gray)">آیتمی یافت نشد.</p>
      <?php else: ?>
        <div style="overflow-x:auto">
          <table class="data-table">
            <thead>
              <tr>
                <th>نام محصول</th>
                <th>تعداد</th>
                <th>قیمت واحد</th>
                <th>جمع</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['name'], ENT_QUOTES) ?></td>
                <td><?= Url::toPersianDigits((string)$item['qty']) ?></td>
                <td><?= Url::toPersianDigits(number_format((int)$item['price'])) ?> تومان</td>
                <td><strong><?= Url::toPersianDigits(number_format((int)$item['subtotal'])) ?> تومان</strong></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" style="text-align:left;padding-top:14px;font-weight:600">جمع کل:</td>
                <td style="padding-top:14px;font-weight:700;color:var(--orange)"><?= Url::toPersianDigits(number_format((int)$order['total'])) ?> تومان</td>
              </tr>
            </tfoot>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Payment Info -->
    <div class="account-card">
      <h2>اطلاعات پرداخت</h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;font-size:14px">
        <div>
          <div style="color:var(--gray);margin-bottom:4px">جمع محصولات</div>
          <div><?= Url::toPersianDigits(number_format((int)($order['subtotal'] ?? $order['total']))) ?> تومان</div>
        </div>
        <?php if (!empty($order['shipping_cost']) && (int)$order['shipping_cost'] > 0): ?>
        <div>
          <div style="color:var(--gray);margin-bottom:4px">هزینه ارسال</div>
          <div><?= Url::toPersianDigits(number_format((int)$order['shipping_cost'])) ?> تومان</div>
        </div>
        <?php endif; ?>
        <?php if (!empty($order['discount']) && (int)$order['discount'] > 0): ?>
        <div>
          <div style="color:var(--gray);margin-bottom:4px">تخفیف</div>
          <div style="color:#16a34a">− <?= Url::toPersianDigits(number_format((int)$order['discount'])) ?> تومان</div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Shipping Address -->
    <div class="account-card">
      <h2>آدرس ارسال</h2>
      <div style="font-size:14px;line-height:2">
        <div><strong>نام گیرنده:</strong> <?= htmlspecialchars($order['name'] ?? '—', ENT_QUOTES) ?></div>
        <div><strong>موبایل:</strong> <span dir="ltr"><?= htmlspecialchars($order['mobile'] ?? '—', ENT_QUOTES) ?></span></div>
        <?php if (!empty($order['province'])): ?>
        <div><strong>استان / شهر:</strong> <?= htmlspecialchars($order['province'], ENT_QUOTES) ?> — <?= htmlspecialchars($order['city'] ?? '', ENT_QUOTES) ?></div>
        <?php endif; ?>
        <?php if (!empty($order['address'])): ?>
        <div><strong>آدرس:</strong> <?= htmlspecialchars($order['address'], ENT_QUOTES) ?></div>
        <?php endif; ?>
        <?php if (!empty($order['postal_code'])): ?>
        <div><strong>کد پستی:</strong> <span dir="ltr"><?= htmlspecialchars($order['postal_code'], ENT_QUOTES) ?></span></div>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- /.account-main -->
</div><!-- /.account-layout -->
