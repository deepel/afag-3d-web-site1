<?php
// cart/checkout.php — redirects to /checkout
$subtotal     = $subtotal ?? 0;
$discount     = $discount ?? 0;
$shippingCost = $shippingCost ?? 0;
$total        = $total ?? 0;
?>
<section class="section">
  <div class="container">
    <h1 class="page-title">تکمیل خرید</h1>

    <?php if (empty($items)): ?>
      <div class="empty-state">
        <p>سبد خرید شما خالی است.</p>
        <a href="<?= BASE_URL ?>/shop" class="btn btn-primary">بازگشت به فروشگاه</a>
      </div>
    <?php else: ?>
    <div class="checkout-grid">

      <!-- ── Address Form ── -->
      <div class="checkout-form-wrap">
        <h2 class="section-sub-title">اطلاعات ارسال</h2>
        <form method="POST" action="<?= BASE_URL ?>/checkout/process" class="checkout-form" id="checkoutForm">
          <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">

          <div class="form-row two-col">
            <div class="form-group">
              <label class="form-label" for="name">نام و نام خانوادگی *</label>
              <input type="text" id="name" name="name" class="form-control"
                     value="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="mobile">شماره موبایل *</label>
              <input type="tel" id="mobile" name="mobile" class="form-control" dir="ltr"
                     placeholder="09xxxxxxxxx"
                     value="<?= htmlspecialchars($user['mobile'] ?? '', ENT_QUOTES) ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="email">ایمیل (اختیاری)</label>
            <input type="email" id="email" name="email" class="form-control" dir="ltr"
                   value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>">
          </div>

          <div class="form-row two-col">
            <div class="form-group">
              <label class="form-label" for="province">استان *</label>
              <input type="text" id="province" name="province" class="form-control" required>
            </div>
            <div class="form-group">
              <label class="form-label" for="city">شهر *</label>
              <input type="text" id="city" name="city" class="form-control" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="address">آدرس کامل *</label>
            <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
          </div>

          <div class="form-group">
            <label class="form-label" for="postal_code">کد پستی</label>
            <input type="text" id="postal_code" name="postal_code" class="form-control" dir="ltr" maxlength="10">
          </div>

          <div class="form-group">
            <label class="form-label" for="notes">توضیحات سفارش (اختیاری)</label>
            <textarea id="notes" name="notes" class="form-control" rows="2"></textarea>
          </div>

          <!-- ── Shipping method ── -->
          <div class="shipping-info">
            <h3 class="section-sub-title" style="font-size:15px;">روش ارسال</h3>
            <label class="radio-option">
              <input type="radio" name="shipping" value="post" checked>
              <span>پست پیشتاز</span>
              <?php if ($shippingCost === 0): ?>
                <span class="badge-stock-ok">رایگان</span>
              <?php else: ?>
                <span><?= Url::price($shippingCost) ?></span>
              <?php endif; ?>
            </label>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn btn-primary btn-lg w-full" id="payBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18">
              <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            پرداخت <?= Url::price($total) ?> — زرین‌پال
          </button>
        </form>
      </div>

      <!-- ── Order Summary ── -->
      <div class="cart-summary">
        <h2 class="cart-summary-title">خلاصه سفارش</h2>
        <div class="summary-items">
          <?php foreach ($items as $item): ?>
          <div class="summary-item">
            <span class="summary-item-name"><?= htmlspecialchars($item['product']['name'], ENT_QUOTES) ?> × <?= $item['qty'] ?></span>
            <span class="summary-item-price"><?= Url::price($item['subtotal']) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <hr class="summary-divider">
        <div class="summary-row">
          <span>جمع کالاها</span>
          <span><?= Url::price($subtotal) ?></span>
        </div>
        <?php if ($discount > 0): ?>
        <div class="summary-row summary-row--discount">
          <span>تخفیف</span>
          <span>−<?= Url::price($discount) ?></span>
        </div>
        <?php endif; ?>
        <div class="summary-row">
          <span>هزینه ارسال</span>
          <span><?= $shippingCost > 0 ? Url::price($shippingCost) : '<span class="badge-stock-ok">رایگان</span>' ?></span>
        </div>
        <hr class="summary-divider">
        <div class="summary-row summary-row--total">
          <strong>مبلغ قابل پرداخت</strong>
          <strong class="price-accent"><?= Url::price($total) ?></strong>
        </div>
      </div>

    </div><!-- /.checkout-grid -->
    <?php endif; ?>
  </div>
</section>
