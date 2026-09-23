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

<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title">سفارش چاپ #<?= $order['id'] ?></h1>
    <span class="badge <?= $statusBadgeClass[$order['status']] ?? 'badge-outline' ?> mt-4">
      <?= htmlspecialchars($statusLabels[$order['status']] ?? $order['status'], ENT_QUOTES) ?>
    </span>
  </div>
  <a href="<?= BASE_URL ?>/admin/print-orders" class="btn btn-ghost">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    بازگشت
  </a>
</div>

<div class="admin-detail-grid">

  <!-- Left col: customer info + options + files -->
  <div class="admin-detail-main">

    <!-- Customer Info -->
    <div class="admin-card mb-24">
      <div class="admin-card-header">
        <h2 class="admin-card-title">اطلاعات مشتری</h2>
      </div>
      <div class="detail-info-grid">
        <div class="detail-info-item">
          <span class="detail-label">نام</span>
          <span class="detail-value"><?= htmlspecialchars($order['customer_name'] ?? '—', ENT_QUOTES) ?></span>
        </div>
        <div class="detail-info-item">
          <span class="detail-label">موبایل</span>
          <span class="detail-value" dir="ltr"><?= htmlspecialchars($order['customer_mobile'] ?? '—', ENT_QUOTES) ?></span>
        </div>
        <div class="detail-info-item">
          <span class="detail-label">ایمیل</span>
          <span class="detail-value"><?= htmlspecialchars($order['customer_email'] ?? '—', ENT_QUOTES) ?></span>
        </div>
        <div class="detail-info-item">
          <span class="detail-label">تاریخ سفارش</span>
          <span class="detail-value"><?= date('Y/m/d H:i', strtotime($order['created_at'])) ?></span>
        </div>
      </div>
    </div>

    <!-- Print Options -->
    <div class="admin-card mb-24">
      <div class="admin-card-header">
        <h2 class="admin-card-title">گزینه‌های انتخابی</h2>
      </div>
      <div class="detail-info-grid">
        <div class="detail-info-item">
          <span class="detail-label">نوع چاپ</span>
          <span class="detail-value"><?= htmlspecialchars($order['print_type_name'] ?? '—', ENT_QUOTES) ?></span>
        </div>
        <div class="detail-info-item">
          <span class="detail-label">جنس مواد</span>
          <span class="detail-value"><?= htmlspecialchars($order['material_name'] ?? '—', ENT_QUOTES) ?></span>
        </div>
        <div class="detail-info-item">
          <span class="detail-label">کیفیت</span>
          <span class="detail-value"><?= htmlspecialchars($order['quality_name'] ?? '—', ENT_QUOTES) ?></span>
        </div>
        <div class="detail-info-item">
          <span class="detail-label">رنگ</span>
          <span class="detail-value"><?= htmlspecialchars($order['color_name'] ?? '—', ENT_QUOTES) ?></span>
        </div>
        <div class="detail-info-item">
          <span class="detail-label">تعداد</span>
          <span class="detail-value"><?= $order['quantity'] ?></span>
        </div>
        <?php if ($order['quoted_price']): ?>
        <div class="detail-info-item">
          <span class="detail-label">قیمت پیشنهادی</span>
          <span class="detail-value text-orange"><?= number_format($order['quoted_price']) ?> تومان</span>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($order['notes']): ?>
      <div class="mt-16">
        <span class="detail-label">توضیحات مشتری:</span>
        <p class="detail-notes mt-4"><?= nl2br(htmlspecialchars($order['notes'], ENT_QUOTES)) ?></p>
      </div>
      <?php endif; ?>
    </div>

    <!-- Files -->
    <div class="admin-card mb-24">
      <div class="admin-card-header">
        <h2 class="admin-card-title">فایل‌های آپلودشده</h2>
      </div>
      <?php if (empty($files)): ?>
      <p class="text-muted px-20 py-16">فایلی آپلود نشده است.</p>
      <?php else: ?>
      <ul class="file-list">
        <?php foreach ($files as $file): ?>
        <li class="file-list__item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <div class="file-list__info">
            <span class="file-list__name"><?= htmlspecialchars($file['original_name'], ENT_QUOTES) ?></span>
            <?php if ($file['size']): ?>
            <span class="file-list__size"><?= round($file['size'] / 1024, 1) ?> KB</span>
            <?php endif; ?>
          </div>
          <a href="<?= BASE_URL ?>/admin/print-orders/file/<?= $file['id'] ?>" class="btn btn-ghost btn-sm" download>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            دانلود
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>

  </div>

  <!-- Right col: update status -->
  <div class="admin-detail-side">
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">بروزرسانی وضعیت</h2>
      </div>
      <form action="<?= BASE_URL ?>/admin/print-orders/<?= $order['id'] ?>/status" method="post">
        <?= $csrf ?>

        <div class="form-group">
          <label class="form-label" for="statusSelect">وضعیت سفارش</label>
          <select name="status" id="statusSelect" class="form-input">
            <?php foreach ($statusLabels as $s => $label): ?>
            <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>>
              <?= htmlspecialchars($label, ENT_QUOTES) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="quotedPrice">قیمت پیشنهادی (تومان)</label>
          <input type="number" name="quoted_price" id="quotedPrice" class="form-input" dir="ltr"
                 value="<?= $order['quoted_price'] ?? '' ?>" min="0" placeholder="مثال: 250000">
        </div>

        <div class="form-group">
          <label class="form-label" for="adminNotes">یادداشت ادمین (برای مشتری)</label>
          <textarea name="admin_notes" id="adminNotes" class="form-input form-textarea" rows="4"
                    placeholder="توضیحات تکمیلی برای مشتری..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-full">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          ذخیره تغییرات
        </button>
      </form>
    </div>
  </div>

</div>
