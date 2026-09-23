<!DOCTYPE html>
<html lang="fa" dir="rtl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'مدیریت', ENT_QUOTES) ?> — پنل مدیریت افگ تری‌دی</title>
  <link rel="icon" href="<?= BASE_URL ?>/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">

<!-- ░░░ ADMIN SIDEBAR ░░░ -->
<aside class="admin-sidebar" id="adminSidebar" role="navigation" aria-label="منوی مدیریت">
  <div class="sidebar-header">
    <a href="<?= BASE_URL ?>/" class="sidebar-brand" aria-label="افگ تری‌دی">
      <?php
      $logoClass = 'mark';
      include __DIR__ . '/../partials/logo-mark.php';
      ?>
      <span>afag<b>3d</b></span>
    </a>
    <button class="sidebar-close" id="sidebarClose" aria-label="بستن منو">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>

  <nav class="sidebar-nav">
    <?php
    // Count pending orders for sidebar badges
    $pendingOrders = 0;
    $pendingPrint  = 0;
    $_sidebarDb = $GLOBALS['db'] ?? null;
    if ($_sidebarDb instanceof PDO) {
        try {
            $pendingOrders = (int)$_sidebarDb->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
            $pendingPrint  = (int)$_sidebarDb->query("SELECT COUNT(*) FROM print_orders WHERE status='pending'")->fetchColumn();
        } catch(\Throwable $e) {
            // Ignore — badges just won't show
        }
    }
    unset($_sidebarDb);
    ?>
    <div class="sidebar-section-label">اصلی</div>
    <ul role="list">
      <li>
        <a href="<?= BASE_URL ?>/admin" class="sidebar-link <?= Url::isActive('/admin') && Url::current() === BASE_URL.'/admin' ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          داشبورد
        </a>
      </li>
    </ul>

    <div class="sidebar-section-label">فروشگاه</div>
    <ul role="list">
      <li>
        <a href="<?= BASE_URL ?>/admin/orders" class="sidebar-link <?= Url::isActive('/admin/orders') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          سفارش‌های فروشگاه
          <?php if ($pendingOrders > 0): ?>
          <span class="sidebar-badge"><?= $pendingOrders ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/products" class="sidebar-link <?= Url::isActive('/admin/products') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          محصولات
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/categories" class="sidebar-link <?= Url::isActive('/admin/categories') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
          دسته‌بندی‌ها
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/coupons" class="sidebar-link <?= Url::isActive('/admin/coupons') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          کوپن‌ها
        </a>
      </li>
    </ul>

    <div class="sidebar-section-label">چاپ</div>
    <ul role="list">
      <li>
        <a href="<?= BASE_URL ?>/admin/print-orders" class="sidebar-link <?= Url::isActive('/admin/print-orders') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          سفارش‌های چاپ
          <?php if ($pendingPrint > 0): ?>
          <span class="sidebar-badge"><?= $pendingPrint ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/print-options" class="sidebar-link <?= Url::isActive('/admin/print-options') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          تنظیمات چاپ
        </a>
      </li>
    </ul>

    <div class="sidebar-section-label">محتوا</div>
    <ul role="list">
      <li>
        <a href="<?= BASE_URL ?>/admin/portfolio" class="sidebar-link <?= Url::isActive('/admin/portfolio') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          نمونه‌کارها
        </a>
      </li>
      <li>
        <details class="sidebar-group" <?= Url::isActive('/admin/blog') ? 'open' : '' ?>>
          <summary class="sidebar-link sidebar-link--expandable">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            وبلاگ
            <svg class="sidebar-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="6 9 12 15 18 9"/></svg>
          </summary>
          <ul class="sidebar-sub">
            <li><a href="<?= BASE_URL ?>/admin/blog" class="sidebar-link sidebar-link--sub <?= Url::isActive('/admin/blog') && !Url::isActive('/admin/blog/categories') ? 'active' : '' ?>">مقالات</a></li>
            <li><a href="<?= BASE_URL ?>/admin/blog/categories" class="sidebar-link sidebar-link--sub <?= Url::isActive('/admin/blog/categories') ? 'active' : '' ?>">دسته‌بندی‌ها</a></li>
          </ul>
        </details>
      </li>
    </ul>

    <div class="sidebar-section-label">مدیریت</div>
    <ul role="list">
      <li>
        <a href="<?= BASE_URL ?>/admin/users" class="sidebar-link <?= Url::isActive('/admin/users') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          کاربران
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/pricing" class="sidebar-link <?= Url::isActive('/admin/pricing') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          تنظیمات قیمت
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/admin/settings" class="sidebar-link <?= Url::isActive('/admin/settings') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/><path d="M10.05 2.06a10 10 0 0 1 3.9 0M10.05 21.94a10 10 0 0 0 3.9 0M2.06 13.95a10 10 0 0 1 0-3.9M21.94 13.95a10 10 0 0 0 0-3.9"/></svg>
          تنظیمات
        </a>
      </li>
    </ul>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-user-avatar"><?= mb_substr(Auth::name(), 0, 1) ?></div>
      <div>
        <div class="sidebar-user-name"><?= htmlspecialchars(Auth::name(), ENT_QUOTES) ?></div>
        <div class="sidebar-user-role">مدیر سیستم</div>
      </div>
    </div>
    <a href="<?= BASE_URL ?>/logout" class="sidebar-logout" title="خروج">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </a>
  </div>
</aside>

<!-- ░░░ ADMIN MAIN ░░░ -->
<div class="admin-main" id="adminMain">
  <!-- Top bar -->
  <header class="admin-topbar">
    <div class="admin-topbar-right">
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="باز/بستن منو">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <nav aria-label="مسیر" class="admin-breadcrumb">
        <a href="<?= BASE_URL ?>/admin">خانه</a>
        <span aria-hidden="true">/</span>
        <span><?= htmlspecialchars($title ?? '', ENT_QUOTES) ?></span>
      </nav>
    </div>
    <div class="admin-topbar-left">
      <button class="theme-toggle" id="themeToggle" aria-label="تغییر پوسته">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
      </button>
      <a href="<?= BASE_URL ?>/" target="_blank" class="btn btn-ghost btn-sm" title="مشاهده سایت">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        سایت
      </a>
    </div>
  </header>

  <!-- Flash messages -->
  <?php if (Flash::has()): ?>
  <div class="admin-flash container-fluid" role="alert">
    <?= Flash::render() ?>
  </div>
  <?php endif; ?>

  <!-- Page content -->
  <div class="admin-content">
    <?= $content ?>
  </div>
</div>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script src="<?= BASE_URL ?>/assets/js/admin.js"></script>
</body>
</html>
