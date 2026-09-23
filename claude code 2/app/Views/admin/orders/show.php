<?php
$sMap = ['pending'=>['label'=>'در انتظار','cls'=>'warning'],
         'processing'=>['label'=>'در حال پردازش','cls'=>'info'],
         'paid'=>['label'=>'پرداخت شده','cls'=>'success'],
         'shipped'=>['label'=>'ارسال شده','cls'=>'info'],
         'completed'=>['label'=>'تکمیل شده','cls'=>'success'],
         'cancelled'=>['label'=>'لغو شده','cls'=>'danger'],
         'failed'=>['label'=>'ناموفق','cls'=>'danger'],
         'refunded'=>['label'=>'بازگشت وجه','cls'=>'warning']];
$cs = $sMap[$order['status']] ?? ['label'=>$order['status'],'cls'=>'default'];
?>
<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title">سفارش #<?= $order['id'] ?></h1>
    <span class="badge badge-<?= $cs['cls'] ?>"><?= $cs['label'] ?></span>
  </div>
  <a href="<?= BASE_URL ?>/admin/orders" class="btn btn-ghost">← برگشت</a>
</div>

<div class="order-detail-grid">

  <!-- ── Order Items ── -->
  <div>
    <div class="admin-card">
      <h2 class="card-title">اقلام سفارش</h2>
      <table class="admin-table">
        <thead>
          <tr><th>محصول</th><th>قیمت</th><th>تعداد</th><th>جمع</th></tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr>
            <td>
              <?= htmlspecialchars($item['name'], ENT_QUOTES) ?>
              <?php if (!empty($item['product_code']) || !empty($item['archive_folder'])): ?>
              <div style="margin-top:5px;font-family:var(--mono,monospace);font-size:12px;line-height:1.9;color:var(--cyan,#2DD4BF)">
                <?php if (!empty($item['product_code'])): ?>
                  <span title="کد محصول">⬡ <?= htmlspecialchars($item['product_code'], ENT_QUOTES) ?></span>
                <?php endif; ?>
                <?php if (!empty($item['archive_folder'])): ?>
                  <span title="پوشهٔ آرشیو فایل STL" style="color:var(--gray,#9a8)">📁 <?= htmlspecialchars($item['archive_folder'], ENT_QUOTES) ?></span>
                <?php endif; ?>
              </div>
              <?php endif; ?>
            </td>
            <td><?= Url::price((int)$item['price']) ?></td>
            <td><?= $item['qty'] ?></td>
            <td><?= Url::price((int)$item['subtotal']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align:left"><strong>جمع کالاها</strong></td>
            <td><?= Url::price((int)$order['subtotal']) ?></td>
          </tr>
          <?php if ((int)$order['discount'] > 0): ?>
          <tr>
            <td colspan="3" style="text-align:left">تخفیف</td>
            <td style="color:var(--clr-ok)">−<?= Url::price((int)$order['discount']) ?></td>
          </tr>
          <?php endif; ?>
          <tr>
            <td colspan="3" style="text-align:left">هزینه ارسال</td>
            <td><?= (int)$order['shipping_cost'] > 0 ? Url::price((int)$order['shipping_cost']) : 'رایگان' ?></td>
          </tr>
          <tr style="font-size:16px;font-weight:700">
            <td colspan="3" style="text-align:left">مبلغ کل</td>
            <td><?= Url::price((int)$order['total']) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- ── Sidebar ── -->
  <div>
    <!-- Customer Info -->
    <div class="admin-card">
      <h2 class="card-title">اطلاعات مشتری</h2>
      <dl class="detail-list">
        <dt>نام</dt><dd><?= htmlspecialchars($order['name'], ENT_QUOTES) ?></dd>
        <dt>موبایل</dt><dd dir="ltr"><?= htmlspecialchars($order['mobile'] ?? '', ENT_QUOTES) ?></dd>
        <dt>ایمیل</dt><dd><?= htmlspecialchars($order['email'] ?? $order['user_email'] ?? '—', ENT_QUOTES) ?></dd>
        <dt>استان</dt><dd><?= htmlspecialchars($order['province'] ?? '—', ENT_QUOTES) ?></dd>
        <dt>شهر</dt><dd><?= htmlspecialchars($order['city'] ?? '—', ENT_QUOTES) ?></dd>
        <dt>آدرس</dt><dd><?= htmlspecialchars($order['address'] ?? '—', ENT_QUOTES) ?></dd>
        <dt>کد پستی</dt><dd dir="ltr"><?= htmlspecialchars($order['postal_code'] ?? '—', ENT_QUOTES) ?></dd>
      </dl>
    </div>

    <!-- Payment Info -->
    <div class="admin-card">
      <h2 class="card-title">اطلاعات پرداخت</h2>
      <dl class="detail-list">
        <dt>کد پیگیری</dt><dd dir="ltr"><?= htmlspecialchars($order['ref_id'] ?? '—', ENT_QUOTES) ?></dd>
        <dt>شناسه Authority</dt><dd dir="ltr" style="font-size:12px"><?= htmlspecialchars($order['authority'] ?? '—', ENT_QUOTES) ?></dd>
        <dt>تاریخ</dt><dd><?= substr($order['created_at'] ?? '', 0, 16) ?></dd>
      </dl>
    </div>

    <!-- Change Status -->
    <div class="admin-card">
      <h2 class="card-title">تغییر وضعیت</h2>
      <form method="POST" action="<?= BASE_URL ?>/admin/orders/<?= $order['id'] ?>/status">
        <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">
        <div class="form-group">
          <select name="status" class="form-control">
            <?php foreach ($sMap as $val => $info): ?>
            <option value="<?= $val ?>" <?= $order['status'] === $val ? 'selected' : '' ?>>
              <?= $info['label'] ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary w-full">به‌روزرسانی</button>
      </form>
    </div>

    <?php if (!empty($order['notes'])): ?>
    <div class="admin-card">
      <h2 class="card-title">یادداشت مشتری</h2>
      <p><?= nl2br(htmlspecialchars($order['notes'], ENT_QUOTES)) ?></p>
    </div>
    <?php endif; ?>
  </div>

</div><!-- /.order-detail-grid -->
