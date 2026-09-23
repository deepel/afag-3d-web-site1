<section class="section">
  <div class="container" style="max-width:600px;margin:0 auto;text-align:center;padding:60px 20px;">

    <div style="width:80px;height:80px;background:rgba(34,197,94,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 28px;font-size:40px;">
      ✓
    </div>

    <h1 style="font-size:28px;font-weight:700;color:#22c55e;margin-bottom:12px;">پرداخت موفق!</h1>
    <p style="font-size:16px;color:var(--gray);margin-bottom:32px;line-height:1.7;">
      سفارش شما با موفقیت ثبت و پرداخت آن تأیید شد. از خرید شما متشکریم.
    </p>

    <?php if ($order): ?>
    <div style="background:var(--bg2);border:1px solid var(--line);border-radius:8px;padding:28px;text-align:right;margin-bottom:32px;">
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--line);">
        <span style="color:var(--gray);">شماره سفارش</span>
        <span style="font-family:var(--mono);font-weight:600;">#<?= Url::toPersianDigits((string)$order['id']) ?></span>
      </div>
      <?php if ($refId): ?>
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--line);">
        <span style="color:var(--gray);">شماره پیگیری</span>
        <span style="font-family:var(--mono);font-weight:600;color:var(--orange);"><?= htmlspecialchars($refId, ENT_QUOTES) ?></span>
      </div>
      <?php endif; ?>
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--line);">
        <span style="color:var(--gray);">نام</span>
        <span><?= htmlspecialchars($order['name'], ENT_QUOTES) ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--line);">
        <span style="color:var(--gray);">موبایل</span>
        <span dir="ltr"><?= htmlspecialchars($order['mobile'], ENT_QUOTES) ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;padding:10px 0;">
        <span style="color:var(--gray);">مبلغ پرداختی</span>
        <span style="font-weight:700;color:var(--orange);"><?= Url::price((int)$order['total']) ?></span>
      </div>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a href="<?= BASE_URL ?>/" class="btn btn-ghost">بازگشت به خانه</a>
      <a href="<?= BASE_URL ?>/shop/models" class="btn btn-primary">ادامه خرید</a>
    </div>
  </div>
</section>
