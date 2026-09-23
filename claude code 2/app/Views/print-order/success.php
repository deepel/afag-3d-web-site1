<section class="success-page">
  <div class="container">
    <div class="success-card">

      <!-- Checkmark -->
      <div class="success-icon">
        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg" width="80" height="80" aria-hidden="true">
          <circle cx="40" cy="40" r="38" stroke="var(--orange)" stroke-width="3" fill="rgba(249,115,22,.08)"/>
          <path d="M22 40l12 14 24-26" stroke="var(--orange)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>

      <h1 class="success-title">سفارش شما با موفقیت ثبت شد!</h1>
      <p class="success-body">
        تیم ما سفارش شما را دریافت کرد و در اسرع وقت با شماره‌ای که وارد کردید تماس می‌گیرد تا قیمت نهایی را اعلام کند.
      </p>
      <p class="success-note">
        معمولاً ظرف 24 ساعت کاری با شما در تماس خواهیم بود.
      </p>

      <div class="success-actions">
        <a href="<?= BASE_URL ?>/" class="btn btn-ghost">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          بازگشت به صفحه اصلی
        </a>
        <a href="<?= BASE_URL ?>/print-order" class="btn btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          ثبت سفارش جدید
        </a>
      </div>

    </div>
  </div>
</section>
