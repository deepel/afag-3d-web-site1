<?php
$success = $success ?? false;
$order   = $order   ?? null;
$refId   = $refId   ?? '';
?>
<section class="section">
  <div class="container">
    <div class="payment-result <?= $success ? 'payment-result--success' : 'payment-result--fail' ?>">
      <?php if ($success): ?>
        <div class="payment-result-icon payment-result-icon--ok">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="48" height="48">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <h1 class="payment-result-title">پرداخت موفق!</h1>
        <p class="payment-result-msg">سفارش شما با موفقیت ثبت شد. از خرید شما سپاسگزاریم.</p>
        <?php if ($order): ?>
        <div class="payment-result-details">
          <div class="detail-row">
            <span>شماره سفارش:</span>
            <strong>#<?= $order['id'] ?></strong>
          </div>
          <?php if ($refId): ?>
          <div class="detail-row">
            <span>کد پیگیری:</span>
            <strong dir="ltr"><?= htmlspecialchars($refId, ENT_QUOTES) ?></strong>
          </div>
          <?php endif; ?>
          <div class="detail-row">
            <span>مبلغ پرداختی:</span>
            <strong><?= Url::price((int)$order['total']) ?></strong>
          </div>
        </div>
        <?php endif; ?>
        <div class="payment-result-actions">
          <a href="<?= BASE_URL ?>/orders" class="btn btn-primary">مشاهده سفارشات من</a>
          <a href="<?= BASE_URL ?>/shop" class="btn btn-ghost">ادامه خرید</a>
        </div>

      <?php else: ?>
        <div class="payment-result-icon payment-result-icon--fail">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="48" height="48">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
        </div>
        <h1 class="payment-result-title">پرداخت ناموفق</h1>
        <p class="payment-result-msg">متاسفانه پرداخت شما با مشکل روبرو شد یا لغو شد.<br>مبلغی از حساب شما کسر نشده است.</p>
        <div class="payment-result-actions">
          <a href="<?= BASE_URL ?>/checkout" class="btn btn-primary">تلاش مجدد</a>
          <a href="<?= BASE_URL ?>/cart" class="btn btn-ghost">بازگشت به سبد خرید</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
