<section class="section">
  <div class="container">
    <h1 style="font-size:clamp(24px,3vw,36px);font-weight:700;margin-bottom:32px;">تکمیل خرید</h1>

    <div class="checkout-layout">

      <!-- ── Form ── -->
      <div>
        <form method="POST" action="<?= BASE_URL ?>/checkout/process">
          <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">

          <!-- Contact Info -->
          <div class="checkout-form-section">
            <h3>اطلاعات تماس</h3>
            <div class="form-grid-2">
              <div class="form-group">
                <label class="form-label" for="name">نام و نام خانوادگی *</label>
                <input type="text" id="name" name="name" class="form-input" required
                       value="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="mobile">شماره موبایل *</label>
                <input type="tel" id="mobile" name="mobile" class="form-input" required
                       placeholder="09xxxxxxxxx" dir="ltr"
                       value="<?= htmlspecialchars($user['mobile'] ?? '', ENT_QUOTES) ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="email">ایمیل (اختیاری)</label>
                <input type="email" id="email" name="email" class="form-input" dir="ltr"
                       value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>">
              </div>
            </div>
          </div>

          <!-- Shipping Address -->
          <div class="checkout-form-section">
            <h3>آدرس تحویل</h3>
            <div class="form-grid-2" style="margin-bottom:16px;">
              <div class="form-group">
                <label class="form-label" for="province">استان</label>
                <input type="text" id="province" name="province" class="form-input"
                       value="<?= htmlspecialchars($_POST['province'] ?? '', ENT_QUOTES) ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="city">شهر</label>
                <input type="text" id="city" name="city" class="form-input"
                       value="<?= htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES) ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="postal_code">کد پستی</label>
                <input type="text" id="postal_code" name="postal_code" class="form-input" dir="ltr"
                       value="<?= htmlspecialchars($_POST['postal_code'] ?? '', ENT_QUOTES) ?>">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="address">آدرس کامل</label>
              <textarea id="address" name="address" class="form-input" rows="3" style="resize:vertical;"><?= htmlspecialchars($_POST['address'] ?? '', ENT_QUOTES) ?></textarea>
            </div>
          </div>

          <!-- Notes -->
          <div class="checkout-form-section">
            <h3>توضیحات سفارش</h3>
            <div class="form-group">
              <textarea name="notes" class="form-input" rows="3" placeholder="توضیحات اختیاری برای سفارش..." style="resize:vertical;"></textarea>
            </div>
          </div>

          <!-- Shipping Method -->
          <div class="checkout-form-section">
            <h3>روش ارسال</h3>
            <label class="filter-check" style="font-size:15px;">
              <input type="radio" name="shipping_method" value="standard" checked>
              <span>ارسال استاندارد پستی</span>
              <span style="margin-right:auto;color:var(--orange);">
                <?= $shippingCost > 0 ? Url::price($shippingCost) : 'رایگان' ?>
              </span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%;padding:16px;font-size:16px;">
            پرداخت و تکمیل سفارش ←
          </button>
        </form>
      </div>

      <!-- ── Order Summary ── -->
      <div>
        <div class="cart-summary" style="position:sticky;top:90px;">
          <h3 style="font-size:18px;font-weight:700;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--line);">خلاصه سفارش</h3>

          <?php foreach ($items as $item): $p = $item['product']; ?>
          <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--line);">
            <?php if (!empty($p['main_image'])): ?>
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['main_image'], ENT_QUOTES) ?>"
                 alt="" style="width:48px;height:48px;object-fit:cover;border-radius:2px;">
            <?php else: ?>
            <img src="<?= BASE_URL ?>/assets/img/placeholder.svg" alt="" style="width:48px;height:48px;border-radius:2px;">
            <?php endif; ?>
            <div style="flex:1;min-width:0;">
              <div style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($p['name'], ENT_QUOTES) ?></div>
              <div style="font-size:12px;color:var(--gray);">× <?= Url::toPersianDigits((string)$item['qty']) ?></div>
            </div>
            <div style="font-size:13px;font-weight:600;white-space:nowrap;"><?= Url::price($item['subtotal'], false) ?></div>
          </div>
          <?php endforeach; ?>

          <div class="summary-row" style="margin-top:8px;">
            <span>جمع</span>
            <span><?= Url::price($subtotal) ?></span>
          </div>
          <?php if ($shippingCost > 0): ?>
          <div class="summary-row">
            <span>هزینه ارسال</span>
            <span><?= Url::price($shippingCost) ?></span>
          </div>
          <?php else: ?>
          <div class="summary-row">
            <span>هزینه ارسال</span>
            <span style="color:#22c55e;">رایگان</span>
          </div>
          <?php endif; ?>
          <?php if ($discount > 0): ?>
          <div class="summary-row" style="color:#22c55e;">
            <span>تخفیف</span>
            <span>− <?= Url::price($discount) ?></span>
          </div>
          <?php endif; ?>
          <div class="summary-row" style="font-weight:700;font-size:17px;">
            <span>مجموع قابل پرداخت</span>
            <span class="summary-total"><?= Url::price($total) ?></span>
          </div>

          <div style="margin-top:20px;padding:14px;background:var(--bg3);border-radius:4px;font-size:12px;color:var(--gray);text-align:center;line-height:1.7;">
            پس از کلیک روی دکمه پرداخت، به درگاه امن بانکی زرین‌پال منتقل خواهید شد.
          </div>
        </div>
      </div>

    </div><!-- /.checkout-layout -->
  </div>
</section>
