<!DOCTYPE html>
<html lang="fa" dir="rtl" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#181716">
  <title><?= htmlspecialchars(SEO::title(), ENT_QUOTES) ?></title>

  <?= SEO::metaTags() ?>
  <?= SEO::organizationSchema($siteData ?? [], BASE_URL) ?>

  <link rel="icon" href="<?= BASE_URL ?>/assets/img/favicon.svg" type="image/svg+xml">
  <?php if (!empty($loadHeroParallax)): ?>
  <link rel="preload" as="image" href="<?= BASE_URL ?>/assets/img/hero-blueprint.jpg" fetchpriority="high">
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
  <?php endif; ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/atelier.css">
  <script>
    document.documentElement.classList.add('js');
    try {
      const storedTheme = localStorage.getItem('afag3d_theme');
      const initialTheme = storedTheme === 'light' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', initialTheme);
      document.querySelector('meta[name="theme-color"]').setAttribute('content', initialTheme === 'dark' ? '#181716' : '#eee8df');
    } catch (error) {}
  </script>
  <script>
    window.AFAG_BASE_URL = <?= json_encode(BASE_URL) ?>;
  </script>
</head>
<body>

<a class="skip-link" href="#main-content">رفتن به محتوای اصلی</a>

<!-- ░░░ LOADER ░░░ -->
<div id="loader" class="loader" aria-hidden="true">
  <div class="loader-pyramid">
    <svg class="loader-svg" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
      <!-- Layer 1 — Bottom (largest) -->
      <g class="pyramid-layer" style="--d:0ms">
        <polygon class="face-l" points="20,86 60,106 60,118 20,98"/>
        <polygon class="face-r" points="100,86 60,106 60,118 100,98"/>
        <polygon class="face-t" points="60,66 100,86 60,106 20,86"/>
      </g>
      <!-- Layer 2 — Middle -->
      <g class="pyramid-layer" style="--d:280ms">
        <polygon class="face-l" points="32,74 60,88 60,100 32,86"/>
        <polygon class="face-r" points="88,74 60,88 60,100 88,86"/>
        <polygon class="face-t" points="60,54 88,74 60,88 32,74"/>
      </g>
      <!-- Layer 3 — Top cap -->
      <g class="pyramid-layer" style="--d:560ms">
        <polygon class="face-l" points="44,62 60,70 60,82 44,74"/>
        <polygon class="face-r" points="76,62 60,70 60,82 76,74"/>
        <polygon class="molten" points="60,46 76,62 60,70 44,62"/>
      </g>
    </svg>
    <span class="loader-label">afag<b>3d</b></span>
    <div class="loader-dots"><span></span><span></span><span></span></div>
  </div>
</div>

<!-- ░░░ NAV ░░░ -->
<nav class="nav" id="nav" role="navigation" aria-label="منوی اصلی">
  <div class="nav-inner container">
    <a class="nav-brand" href="<?= BASE_URL ?>/" aria-label="صفحه اصلی افگ تری‌دی">
      <?php
      $logoClass = 'mark';
      include __DIR__ . '/../partials/logo-mark.php';
      ?>
      <span class="brand-name">afag<span class="brand-accent">3d</span></span>
    </a>

    <ul class="nav-links" role="list">
      <li><a href="<?= BASE_URL ?>/" class="nav-link <?= Url::isActive('/') && Url::current() === BASE_URL . '/' ? 'active' : '' ?>">خانه</a></li>
      <li><a href="<?= BASE_URL ?>/services" class="nav-link <?= Url::isActive('/services') ? 'active' : '' ?>">خدمات</a></li>
      <li><a href="<?= BASE_URL ?>/shop/models" class="nav-link <?= Url::isActive('/shop/models') ? 'active' : '' ?>">فروشگاه مدل‌ها</a></li>
      <li><a href="<?= BASE_URL ?>/shop/supplies" class="nav-link <?= Url::isActive('/shop/supplies') ? 'active' : '' ?>">لوازم پرینتر</a></li>
      <li><a href="<?= BASE_URL ?>/print-order" class="nav-link <?= Url::isActive('/print-order') ? 'active' : '' ?>">سفارش چاپ</a></li>
      <li><a href="<?= BASE_URL ?>/portfolio" class="nav-link <?= Url::isActive('/portfolio') ? 'active' : '' ?>">نمونه‌کارها</a></li>
      <li><a href="<?= BASE_URL ?>/blog" class="nav-link <?= Url::isActive('/blog') ? 'active' : '' ?>">وبلاگ</a></li>
    </ul>

    <div class="nav-actions">
      <a class="nav-cart" href="<?= BASE_URL ?>/cart" aria-label="سبد خرید" title="سبد خرید">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="nav-cart-badge" id="cartCount" <?= Cart::count() > 0 ? '' : 'style="display:none"' ?>><?= Cart::count() ?></span>
      </a>

      <button class="theme-toggle" id="themeToggle" aria-label="تغییر پوسته" title="تغییر پوسته">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
      </button>

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

      <button class="hamburger" id="hamburger" aria-label="منو" aria-expanded="false" aria-controls="nav-links">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<!-- ░░░ POPUP ░░░ -->
<?php if (!empty($siteData['popup_enabled']) && $siteData['popup_enabled'] === '1'): ?>
<div id="site-popup" class="popup-overlay" role="dialog" aria-modal="true" aria-label="اطلاعیه" style="display:none">
  <div class="popup-box">
    <button class="popup-close" aria-label="بستن">✕</button>
    <?php if (!empty($siteData['popup_image'])): ?>
    <img src="<?= Url::upload(htmlspecialchars($siteData['popup_image'], ENT_QUOTES)) ?>" alt="<?= htmlspecialchars($siteData['popup_title'] ?? '', ENT_QUOTES) ?>" class="popup-img">
    <?php endif; ?>
    <?php if (!empty($siteData['popup_title'])): ?>
    <h3 class="popup-title"><?= htmlspecialchars($siteData['popup_title'], ENT_QUOTES) ?></h3>
    <?php endif; ?>
    <?php if (!empty($siteData['popup_content'])): ?>
    <p class="popup-body"><?= nl2br(htmlspecialchars($siteData['popup_content'], ENT_QUOTES)) ?></p>
    <?php endif; ?>
    <?php if (!empty($siteData['popup_btn_text']) && !empty($siteData['popup_btn_url'])): ?>
    <a href="<?= htmlspecialchars($siteData['popup_btn_url'], ENT_QUOTES) ?>" class="btn btn-primary popup-btn"><?= htmlspecialchars($siteData['popup_btn_text'], ENT_QUOTES) ?></a>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<!-- ░░░ FLASH MESSAGES ░░░ -->
<?php if (Flash::has()): ?>
<div class="flash-container" role="alert" aria-live="polite">
  <div class="container">
    <?= Flash::render() ?>
  </div>
</div>
<?php endif; ?>

<!-- ░░░ MAIN CONTENT ░░░ -->
<main id="main-content" tabindex="-1">
  <?= $content ?>
</main>

<!-- ░░░ FOOTER ░░░ -->
<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="<?= BASE_URL ?>/" class="footer-logo" aria-label="افگ تری‌دی">
          <?php
          $logoClass = 'mark';
          include __DIR__ . '/../partials/logo-mark.php';
          ?>
          <span>afag<b>3d</b></span>
        </a>
        <p class="footer-desc">استودیوی تخصصی چاپ سه‌بعدی — طراحی، چاپ و فروش محصولات سه‌بعدی با کیفیت حرفه‌ای.</p>
        <div class="footer-social">
          <?php if (!empty($siteData['instagram'])): ?>
          <a href="<?= htmlspecialchars($siteData['instagram'], ENT_QUOTES) ?>" target="_blank" rel="noopener" aria-label="اینستاگرام">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4.5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
          </a>
          <?php endif; ?>
          <?php if (!empty($siteData['telegram'])): ?>
          <a href="<?= htmlspecialchars($siteData['telegram'], ENT_QUOTES) ?>" target="_blank" rel="noopener" aria-label="تلگرام">
            <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.89 8.14-2.04 9.62c-.15.66-.55.82-1.12.51l-3.08-2.27-1.49 1.43c-.17.17-.31.31-.62.31l.22-3.1 5.63-5.09c.24-.22-.05-.34-.37-.12L6.44 14.04 3.4 13.09c-.65-.2-.66-.65.14-.96l11.55-4.45c.54-.2 1.02.13.8.96z"/></svg>
          </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="footer-col">
        <h4 class="footer-heading">خدمات</h4>
        <ul class="footer-links">
          <li><a href="<?= BASE_URL ?>/print">سفارش چاپ</a></li>
          <li><a href="<?= BASE_URL ?>/shop">فروشگاه</a></li>
          <li><a href="<?= BASE_URL ?>/portfolio">نمونه کارها</a></li>
          <li><a href="<?= BASE_URL ?>/design">طراحی سفارشی</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 class="footer-heading">اطلاعات</h4>
        <ul class="footer-links">
          <li><a href="<?= BASE_URL ?>/about">درباره ما</a></li>
          <li><a href="<?= BASE_URL ?>/blog">بلاگ</a></li>
          <li><a href="<?= BASE_URL ?>/terms">قوانین</a></li>
          <li><a href="<?= BASE_URL ?>/contact">تماس با ما</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 class="footer-heading">تماس</h4>
        <ul class="footer-contact">
          <?php if (!empty($siteData['site_phone'])): ?>
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.58 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.77a16 16 0 0 0 6.29 6.29l1.42-1.42a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <span dir="ltr"><?= htmlspecialchars($siteData['site_phone'], ENT_QUOTES) ?></span>
          </li>
          <?php endif; ?>
          <?php if (!empty($siteData['site_email'])): ?>
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <span><?= htmlspecialchars($siteData['site_email'], ENT_QUOTES) ?></span>
          </li>
          <?php endif; ?>
          <?php if (!empty($siteData['site_address'])): ?>
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span><?= htmlspecialchars($siteData['site_address'], ENT_QUOTES) ?></span>
          </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p class="footer-copy">© <?= date('Y') ?> افگ تری‌دی — تمام حقوق محفوظ است.</p>
      <a href="#" class="back-to-top" id="backToTop" aria-label="بازگشت به بالا">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><polyline points="18 15 12 9 6 15"/></svg>
      </a>
    </div>
  </div>
</footer>

<?php if (!empty($loadHeroParallax)): ?>
<script defer src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script>
<?php endif; ?>
<script defer src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
