<?php
/**
 * nav-auth — Auth controls for the main navigation.
 *
 * Rendered in two layout contexts (never both visible at the same time):
 *   $authContext = 'top'    → compact button + dropdown for the desktop top bar
 *   $authContext = 'drawer' → flat links appended to the mobile drawer
 *
 * This is one component with two presentational modes so the auth actions
 * remain reachable at every viewport width.
 */
$authContext = $authContext ?? 'top';

if ($authContext === 'drawer'): ?>
  <li class="nav-auth-drawer">
    <?php if (Auth::check()): ?>
      <?php if (Auth::isAdmin()): ?>
        <a href="<?= BASE_URL ?>/admin" class="dropdown-item">پنل مدیریت</a>
      <?php endif; ?>
      <a href="<?= BASE_URL ?>/account" class="dropdown-item">حساب کاربری</a>
      <a href="<?= BASE_URL ?>/account/orders" class="dropdown-item">سفارش‌های من</a>
      <a href="<?= BASE_URL ?>/account/print-orders" class="dropdown-item">سفارشات چاپ</a>
      <hr class="dropdown-divider">
      <a href="<?= BASE_URL ?>/logout" class="dropdown-item dropdown-item--danger">خروج</a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/login" class="btn btn-ghost btn-block">ورود</a>
      <a href="<?= BASE_URL ?>/register" class="btn btn-primary btn-block">ثبت‌نام</a>
    <?php endif; ?>
  </li>
<?php else: ?>
  <?php if (Auth::check()): ?>
    <div class="nav-user-menu">
      <button class="btn-ghost nav-user-btn" id="userMenuBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span><?= htmlspecialchars(Auth::name(), ENT_QUOTES) ?></span>
      </button>
      <div class="user-dropdown" id="userDropdown" hidden>
        <?php if (Auth::isAdmin()): ?>
          <a href="<?= BASE_URL ?>/admin" class="dropdown-item">پنل مدیریت</a>
          <hr class="dropdown-divider">
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/account" class="dropdown-item">حساب کاربری</a>
        <a href="<?= BASE_URL ?>/account/orders" class="dropdown-item">سفارش‌های من</a>
        <a href="<?= BASE_URL ?>/account/print-orders" class="dropdown-item">سفارشات چاپ</a>
        <hr class="dropdown-divider">
        <a href="<?= BASE_URL ?>/logout" class="dropdown-item dropdown-item--danger">خروج</a>
      </div>
    </div>
  <?php else: ?>
    <a href="<?= BASE_URL ?>/login" class="btn btn-ghost">ورود</a>
    <a href="<?= BASE_URL ?>/register" class="btn btn-primary">ثبت‌نام</a>
  <?php endif; ?>
<?php endif; ?>