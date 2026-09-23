<div class="admin-content">
  <div class="admin-header">
    <h1 class="admin-title">سفارش #<?= Url::toPersianDigits((string)$order['id']) ?></h1>
    <a href="<?= BASE_URL ?>/admin/orders" class="btn btn-ghost">← بازگشت به سفارشات</a>
  </div>

  <?php
  $statusLabels = [
    'pending'    => ['label' => 'در انتظار پرداخت', 'color' => '#f59e0b'],
    'paid'       => ['label' => 'پرداخت شده',       'color' => '#22c55e'],
    'processing' => ['label' => 'در حال پردازش',   'color' => '#3b82f6'],
    'shipped'    => ['label' => 'ارسال شده',         'color' => '#8b5cf6'],
    'delivered'  => ['label' => 'تحویل داده شده',   'color' => '#22c55e'],
    'cancelled'  => ['label' => 'لغو شده',          'color' => '#ef4444'],
    'failed'     => ['label' => 'ناموفق',            'color' => '#ef4444'],
  ];
  $s = $statusLabels[$order['status']] ?? ['label' => $order['status'], 'color' => 'var(--gray)'];
  ?>

  <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">

    <div>
      <!-- Order Items -->
      <div class="admin-card" style="margin-bottom:20px;">
        <h3 class="admin-card-title">آیتم‌های سفارش</h3>
        <table style="width:100%;border-collapse:collapse;">
          <thead>
            <tr style="border-bottom:1px solid var(--line);">
              <th style="padding:10px 12px;text-align:right;font-size:13px;color:var(--gray);font-weight:400;">محصول</th>
              <th style="padding:10px 12px;text-align:right;font-size:13px;color:var(--gray);font-weight:400;">قیمت</th>
              <th style="padding:10px 12px;text-align:right;font-size:13px;color:var(--gray);font-weight:400;">تعداد</th>
              <th style="padding:10px 12px;text-align:right;font-size:13px;color:var(--gray);font-weight:400;">جمع</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
            <tr style="border-bottom:1px solid var(--line);">
              <td style="padding:12px;"><?= htmlspecialchars($item['name'], ENT_QUOTES) ?></td>
              <td style="padding:12px;"><?= Url::price((int)$item['price']) ?></td>
              <td style="padding:12px;"><?= Url::toPersianDigits((string)$item['qty']) ?></td>
              <td style="padding:12px;font-weight:600;"><?= Url::price((int)$item['subtotal']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" style="padding:12px;text-align:left;color:var(--gray);">جمع محصولات</td>
              <td style="padding:12px;"><?= Url::price((int)$order['subtotal']) ?></td>
            </tr>
            <?php if ($order['shipping_cost'] > 0): ?>
            <tr>
              <td colspan="3" style="padding:12px;text-align:left;color:var(--gray);">هزینه ارسال</td>
              <td style="padding:12px;"><?= Url::price((int)$order['shipping_cost']) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($order['discount'] > 0): ?>
            <tr>
              <td colspan="3" style="padding:12px;text-align:left;color:#22c55e;">تخفیف</td>
              <td style="padding:12px;color:#22c55e;">− <?= Url::price((int)$order['discount']) ?></td>
            </tr>
            <?php endif; ?>
            <tr style="border-top:2px solid var(--line);">
              <td colspan="3" style="padding:14px 12px;text-align:left;font-weight:700;">مجموع</td>
              <td style="padding:14px 12px;font-weight:700;color:var(--orange);"><?= Url::price((int)$order['total']) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Customer Info -->
      <div class="admin-card" style="margin-bottom:20px;">
        <h3 class="admin-card-title">اطلاعات مشتری</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">نام</p>
            <p style="font-weight:500;"><?= htmlspecialchars($order['name'], ENT_QUOTES) ?></p>
          </div>
          <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">موبایل</p>
            <p dir="ltr"><?= htmlspecialchars($order['mobile'], ENT_QUOTES) ?></p>
          </div>
          <?php if ($order['email']): ?>
          <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">ایمیل</p>
            <p><?= htmlspecialchars($order['email'], ENT_QUOTES) ?></p>
          </div>
          <?php endif; ?>
          <?php if ($order['province']): ?>
          <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">استان / شهر</p>
            <p><?= htmlspecialchars($order['province'] . ' / ' . $order['city'], ENT_QUOTES) ?></p>
          </div>
          <?php endif; ?>
          <?php if ($order['address']): ?>
          <div style="grid-column:1/-1;">
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">آدرس</p>
            <p style="font-size:14px;"><?= htmlspecialchars($order['address'], ENT_QUOTES) ?></p>
          </div>
          <?php endif; ?>
          <?php if ($order['postal_code']): ?>
          <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">کد پستی</p>
            <p dir="ltr" style="font-family:var(--mono);"><?= htmlspecialchars($order['postal_code'], ENT_QUOTES) ?></p>
          </div>
          <?php endif; ?>
        </div>
        <?php if ($order['notes']): ?>
        <div style="margin-top:16px;padding:12px;background:var(--bg3);border-radius:4px;">
          <p style="font-size:12px;color:var(--gray);margin-bottom:6px;">یادداشت مشتری</p>
          <p style="font-size:14px;"><?= nl2br(htmlspecialchars($order['notes'], ENT_QUOTES)) ?></p>
        </div>
        <?php endif; ?>
      </div>

      <!-- Payment Info -->
      <?php if (!empty($order['ref_id'])): ?>
      <div class="admin-card">
        <h3 class="admin-card-title">اطلاعات پرداخت</h3>
        <div style="display:flex;gap:24px;flex-wrap:wrap;">
          <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">شماره مرجع</p>
            <p style="font-family:var(--mono);color:var(--orange);"><?= htmlspecialchars($order['ref_id'], ENT_QUOTES) ?></p>
          </div>
          <?php if (!empty($order['authority'])): ?>
          <div>
            <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">Authority</p>
            <p style="font-family:var(--mono);font-size:12px;"><?= htmlspecialchars(substr($order['authority'], 0, 30) . '...', ENT_QUOTES) ?></p>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- ── Sidebar ── -->
    <div>
      <div class="admin-card" style="margin-bottom:16px;">
        <h3 class="admin-card-title">وضعیت سفارش</h3>
        <div style="margin-bottom:16px;padding:12px;border-radius:4px;background:var(--bg3);text-align:center;">
          <span style="font-weight:600;color:<?= $s['color'] ?>;"><?= $s['label'] ?></span>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/admin/orders/<?= $order['id'] ?>">
          <?= $csrf ?>
          <div class="form-group" style="margin-bottom:12px;">
            <label class="form-label">تغییر وضعیت</label>
            <select name="status" class="form-input">
              <option value="pending"    <?= $order['status'] === 'pending'    ? 'selected' : '' ?>>در انتظار پرداخت</option>
              <option value="paid"       <?= $order['status'] === 'paid'       ? 'selected' : '' ?>>پرداخت شده</option>
              <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>در حال پردازش</option>
              <option value="shipped"    <?= $order['status'] === 'shipped'    ? 'selected' : '' ?>>ارسال شده</option>
              <option value="delivered"  <?= $order['status'] === 'delivered'  ? 'selected' : '' ?>>تحویل داده شده</option>
              <option value="cancelled"  <?= $order['status'] === 'cancelled'  ? 'selected' : '' ?>>لغو شده</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;">بروزرسانی وضعیت</button>
        </form>
      </div>

      <div class="admin-card">
        <h3 class="admin-card-title">جزئیات</h3>
        <div style="font-size:13px;color:var(--gray);line-height:2.2;">
          <div style="display:flex;justify-content:space-between;">
            <span>شماره سفارش</span>
            <span style="color:var(--bone);font-family:var(--mono);">#<?= $order['id'] ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span>تاریخ ثبت</span>
            <span style="color:var(--bone);"><?= date('Y/m/d H:i', strtotime($order['created_at'])) ?></span>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span>مجموع</span>
            <span style="color:var(--orange);font-weight:600;"><?= Url::price((int)$order['total']) ?></span>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
