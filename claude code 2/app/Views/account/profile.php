<?php
/**
 * Account Profile Edit
 */
?>
<div class="account-layout">

  <!-- ░░░ SIDEBAR ░░░ -->
  <aside class="account-sidebar">
    <nav aria-label="منوی حساب کاربری">
      <a href="<?= BASE_URL ?>/account">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        داشبورد
      </a>
      <a href="<?= BASE_URL ?>/account/orders">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        سفارش‌ها
      </a>
      <a href="<?= BASE_URL ?>/account/print-orders">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        سفارش چاپ
      </a>
      <a href="<?= BASE_URL ?>/account/addresses">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        آدرس‌ها
      </a>
      <a href="<?= BASE_URL ?>/account/profile" class="active">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        پروفایل
      </a>
      <a href="<?= BASE_URL ?>/account/password">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        تغییر رمز
      </a>
      <a href="<?= BASE_URL ?>/logout" style="color:var(--red,#ef4444)">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        خروج
      </a>
    </nav>
  </aside>

  <!-- ░░░ MAIN ░░░ -->
  <div class="account-main">
    <div class="account-card">
      <h2>ویرایش پروفایل</h2>

      <form method="post" action="<?= BASE_URL ?>/account/profile" novalidate>
        <?= $csrf ?>

        <div class="form-group">
          <label class="form-label" for="name">نام و نام خانوادگی <span style="color:var(--orange)">*</span></label>
          <input
            type="text"
            id="name"
            name="name"
            class="form-input"
            value="<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?>"
            required
            minlength="2"
            placeholder="نام کامل خود را وارد کنید"
          >
        </div>

        <div class="form-group">
          <label class="form-label" for="email">آدرس ایمیل</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>"
            placeholder="example@email.com"
            dir="ltr"
          >
        </div>

        <div class="form-group">
          <label class="form-label">شماره موبایل</label>
          <input
            type="text"
            class="form-input"
            value="<?= htmlspecialchars($user['mobile'] ?? '', ENT_QUOTES) ?>"
            readonly
            disabled
            dir="ltr"
            style="opacity:.6;cursor:not-allowed"
          >
          <small style="color:var(--gray);font-size:12px;margin-top:4px;display:block">شماره موبایل قابل تغییر نیست. برای تغییر با پشتیبانی تماس بگیرید.</small>
        </div>

        <div style="margin-top:24px">
          <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
        </div>
      </form>
    </div>
  </div>
</div>
