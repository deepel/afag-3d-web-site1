<section class="section">
  <div class="container">
    <h1 style="font-size:clamp(24px,3vw,36px);font-weight:700;margin-bottom:32px;">سبد خرید</h1>

    <?php if ($items): ?>
    <div class="cart-layout">

      <!-- ── Cart Table ── -->
      <div>
        <table class="cart-table">
          <thead>
            <tr>
              <th>محصول</th>
              <th>قیمت واحد</th>
              <th>تعداد</th>
              <th>جمع</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): $p = $item['product']; ?>
            <tr data-product-id="<?= $p['id'] ?>">
              <td>
                <div style="display:flex;align-items:center;gap:14px;">
                  <?php if (!empty($p['main_image'])): ?>
                  <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['main_image'], ENT_QUOTES) ?>"
                       alt="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>" class="cart-product-img">
                  <?php else: ?>
                  <img src="<?= BASE_URL ?>/assets/img/placeholder.svg" alt="" class="cart-product-img">
                  <?php endif; ?>
                  <div>
                    <a href="<?= BASE_URL ?>/shop/<?= htmlspecialchars($p['shop_slug'] ?? '', ENT_QUOTES) ?>/<?= htmlspecialchars($p['slug'], ENT_QUOTES) ?>"
                       style="font-size:15px;font-weight:500;"><?= htmlspecialchars($p['name'], ENT_QUOTES) ?></a>
                  </div>
                </div>
              </td>
              <td style="white-space:nowrap;"><?= Url::price((int)$p['price']) ?></td>
              <td>
                <div class="cart-qty-wrap">
                  <button type="button" class="cart-qty-btn qty-btn" data-dir="down">−</button>
                  <input type="number" class="cart-qty-input qty-input" value="<?= $item['qty'] ?>" min="1" max="<?= (int)$p['stock'] ?>">
                  <button type="button" class="cart-qty-btn qty-btn" data-dir="up">+</button>
                </div>
              </td>
              <td style="white-space:nowrap;font-weight:600;"><?= Url::price($item['subtotal']) ?></td>
              <td>
                <button class="cart-remove" data-product-id="<?= $p['id'] ?>" title="حذف" aria-label="حذف محصول">✕</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div style="display:flex;justify-content:space-between;margin-top:20px;flex-wrap:wrap;gap:12px;">
          <a href="<?= BASE_URL ?>/shop/models" class="btn btn-ghost">← ادامه خرید</a>
          <button type="button" onclick="location.reload()" class="btn btn-ghost">بروزرسانی سبد</button>
        </div>
      </div>

      <!-- ── Summary ── -->
      <div class="cart-summary">
        <h3 style="font-size:18px;font-weight:700;margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--line);">خلاصه سفارش</h3>

        <div class="summary-row">
          <span>جمع محصولات</span>
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
          <span>تخفیف <?= $coupon ? '(' . htmlspecialchars($coupon['code'], ENT_QUOTES) . ')' : '' ?></span>
          <span>− <?= Url::price($discount) ?></span>
        </div>
        <?php endif; ?>

        <div class="summary-row" style="padding-top:16px;">
          <span style="font-weight:700;">مجموع</span>
          <span class="summary-total"><?= Url::price($total) ?></span>
        </div>

        <!-- Coupon -->
        <div style="margin-top:20px;">
          <?php if ($coupon): ?>
          <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:2px;padding:10px 14px;font-size:13px;display:flex;align-items:center;justify-content:space-between;">
            <span style="color:#22c55e;">✓ کد تخفیف فعال: <?= htmlspecialchars($coupon['code'], ENT_QUOTES) ?></span>
            <button class="remove-coupon-btn" style="background:none;border:none;color:var(--concrete);cursor:pointer;font-size:12px;">حذف</button>
          </div>
          <?php else: ?>
          <p style="font-size:13px;color:var(--gray);margin-bottom:8px;">کد تخفیف دارید؟</p>
          <div class="coupon-wrap">
            <input type="text" class="coupon-input" placeholder="کد تخفیف را وارد کنید">
            <button type="button" class="coupon-btn">اعمال</button>
          </div>
          <?php endif; ?>
        </div>

        <a href="<?= BASE_URL ?>/checkout" class="btn btn-primary" style="width:100%;margin-top:20px;text-align:center;padding:14px;">
          ادامه و پرداخت ←
        </a>
      </div>

    </div><!-- /.cart-layout -->

    <!-- CSRF token for AJAX -->
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= CSRF::token() ?>">

    <?php else: ?>
    <div style="text-align:center;padding:80px 20px;color:var(--gray);">
      <p style="font-size:64px;margin-bottom:16px;">🛒</p>
      <p style="font-size:20px;margin-bottom:8px;color:var(--bone);">سبد خرید شما خالی است</p>
      <p style="font-size:14px;margin-bottom:28px;">برای خرید به فروشگاه بروید.</p>
      <a href="<?= BASE_URL ?>/shop/models" class="btn btn-primary" style="margin-left:8px;">فروشگاه مدل‌ها</a>
      <a href="<?= BASE_URL ?>/shop/supplies" class="btn btn-ghost">فروشگاه لوازم</a>
    </div>
    <?php endif; ?>
  </div>
</section>

