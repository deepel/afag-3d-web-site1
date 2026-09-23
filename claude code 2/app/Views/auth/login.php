<section class="auth-section">
  <div class="container">
    <div class="auth-wrap">
      <div class="auth-brand" aria-hidden="true">
        <?php
        $logoClass = 'mark auth-mark';
        include __DIR__ . '/../partials/logo-mark.php';
        ?>
      </div>

      <div class="auth-card">
        <div class="auth-header">
          <h1 class="auth-title">ورود به حساب</h1>
          <p class="auth-sub">خوش برگشتید — با شماره موبایل وارد شوید</p>
        </div>

        <?= Flash::render() ?>

        <form action="<?= BASE_URL ?>/login" method="post" class="auth-form" novalidate>
          <?= $csrf ?>

          <div class="form-group">
            <label class="form-label" for="mobile">شماره موبایل</label>
            <div class="form-input-wrap">
              <svg class="form-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="2" stroke-linecap="round"/></svg>
              <input
                type="tel"
                id="mobile"
                name="mobile"
                class="form-input"
                placeholder="09XXXXXXXXX"
                maxlength="11"
                autocomplete="tel"
                inputmode="numeric"
                required
                dir="ltr"
              >
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="password">
              رمز عبور
              <a href="<?= BASE_URL ?>/forgot-password" class="form-label-link">فراموش کردم</a>
            </label>
            <div class="form-input-wrap">
              <svg class="form-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                placeholder="رمز عبور"
                autocomplete="current-password"
                required
                dir="ltr"
              >
              <button type="button" class="form-input-toggle" data-target="password" aria-label="نمایش/مخفی رمز">
                <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-lg btn-block">
            ورود به حساب
          </button>
        </form>

        <p class="auth-switch">
          حساب کاربری ندارید؟
          <a href="<?= BASE_URL ?>/register">ثبت‌نام کنید</a>
        </p>
      </div>
    </div>
  </div>
</section>
