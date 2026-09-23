<?php
$isEdit = $coupon !== null;
$action = $isEdit
    ? BASE_URL . '/admin/coupons/' . $coupon['id']
    : BASE_URL . '/admin/coupons';
?>
<div class="admin-page-header">
  <h1 class="admin-page-title"><?= $isEdit ? 'ویرایش کوپن' : 'افزودن کوپن' ?></h1>
  <a href="<?= BASE_URL ?>/admin/coupons" class="btn btn-ghost">← برگشت</a>
</div>

<div class="admin-card" style="max-width:640px">
  <form method="POST" action="<?= $action ?>">
    <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">

    <div class="form-row two-col">
      <div class="form-group">
        <label class="form-label" for="code">کد تخفیف *</label>
        <input type="text" id="code" name="code" class="form-control" dir="ltr"
               style="text-transform:uppercase" required
               value="<?= htmlspecialchars($coupon['code'] ?? '', ENT_QUOTES) ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="type">نوع</label>
        <select id="couponType" name="type" class="form-control">
          <option value="percent" <?= ($coupon['type'] ?? 'percent') === 'percent' ? 'selected' : '' ?>>درصدی (%)</option>
          <option value="fixed"   <?= ($coupon['type'] ?? '') === 'fixed'   ? 'selected' : '' ?>>مقدار ثابت (تومان)</option>
        </select>
      </div>
    </div>

    <div class="form-row two-col">
      <div class="form-group">
        <label class="form-label" for="value">مقدار تخفیف *</label>
        <input type="number" id="value" name="value" class="form-control" step="0.01" min="0" required
               value="<?= $coupon['value'] ?? 0 ?>">
        <small class="form-hint" id="valueHint">درصد (0–100)</small>
      </div>
      <div class="form-group">
        <label class="form-label" for="max_discount">حداکثر تخفیف (تومان)</label>
        <input type="number" name="max_discount" class="form-control" min="0"
               value="<?= $coupon['max_discount'] ?? '' ?>">
        <small class="form-hint">برای کوپن‌های درصدی</small>
      </div>
    </div>

    <div class="form-row two-col">
      <div class="form-group">
        <label class="form-label" for="min_order">حداقل مبلغ خرید (تومان)</label>
        <input type="number" name="min_order" class="form-control" min="0"
               value="<?= $coupon['min_order'] ?? '' ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="usage_limit">سقف دفعات استفاده</label>
        <input type="number" name="usage_limit" class="form-control" min="0"
               placeholder="خالی = نامحدود"
               value="<?= $coupon['usage_limit'] ?? '' ?>">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="expires_at">تاریخ انقضا</label>
      <input type="date" name="expires_at" class="form-control" dir="ltr"
             value="<?= isset($coupon['expires_at']) ? substr($coupon['expires_at'], 0, 10) : '' ?>">
    </div>

    <div class="form-group">
      <label class="toggle-label">
        <input type="checkbox" name="is_active" value="1" <?= !$isEdit || !empty($coupon['is_active']) ? 'checked' : '' ?>>
        <span>کوپن فعال باشد</span>
      </label>
    </div>

    <button type="submit" class="btn btn-primary">ذخیره</button>
  </form>
</div>

<script>
document.getElementById('couponType').addEventListener('change', function(){
  const hint = document.getElementById('valueHint');
  hint.textContent = this.value === 'percent' ? 'درصد (0–100)' : 'مبلغ به تومان';
});
</script>
