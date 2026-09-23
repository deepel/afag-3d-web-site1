<?php
/**
 * درباره ما — About Page
 */
?>

<!-- ░░░ HERO ░░░ -->
<section class="page-hero">
  <div class="container">
    <div class="page-hero-content">
      <div class="page-hero-badge">درباره ما</div>
      <h1 class="page-hero-title">استودیوی تخصصی چاپ <span class="text-accent">سه‌بعدی</span></h1>
      <p class="page-hero-desc">
        افگ تری‌دی از سال 1400 در زمینه چاپ سه‌بعدی صنعتی، ماکت‌سازی معماری و طراحی پارامتریک فعالیت می‌کند.
        ما با استفاده از فناوری‌های روز دنیا و تیمی متخصص، ایده‌های شما را به واقعیت تبدیل می‌کنیم.
      </p>
    </div>
  </div>
</section>

<!-- ░░░ STORY ░░░ -->
<section class="section about-story">
  <div class="container">
    <div class="about-story-grid">
      <div class="about-story-content">
        <h2 class="section-title">داستان ما</h2>
        <p>
          افگ تری‌دی با هدف ارائه خدمات چاپ سه‌بعدی حرفه‌ای به معماران، طراحان صنعتی و کسب‌وکارها تاسیس شد.
          در ابتدا تنها با یک دستگاه FDM شروع کردیم، اما امروز طیف کاملی از فناوری‌های چاپ سه‌بعدی را در اختیار داریم.
        </p>
        <p>
          ماموریت ما ساده است: کمک به تبدیل ایده‌های خلاقانه به محصولات واقعی با بالاترین کیفیت و در کمترین زمان.
          هر پروژه‌ای که به ما می‌سپارید با دقت، مراقبت و تخصص اجرا می‌شود.
        </p>
        <p>
          با بیش از 1000 پروژه موفق و مشتریان راضی از سراسر کشور، افتخار می‌کنیم که یکی از معتبرترین
          استودیوهای چاپ سه‌بعدی ایران هستیم.
        </p>
        <div class="about-badges">
          <span class="badge badge-brand">FDM</span>
          <span class="badge badge-brand">Resin / SLA</span>
          <span class="badge badge-brand">دقت 50 میکرون</span>
          <span class="badge badge-brand">تحویل سریع</span>
        </div>
      </div>
      <div class="about-story-visual">
        <div class="about-visual-card">
          <svg viewBox="0 0 200 200" class="about-3d-illustration" aria-hidden="true">
            <defs>
              <linearGradient id="gAbout" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="var(--accent)" stop-opacity="0.8"/>
                <stop offset="100%" stop-color="var(--accent2,#6c63ff)" stop-opacity="0.4"/>
              </linearGradient>
            </defs>
            <!-- Isometric cube illustration -->
            <polygon points="100,20 160,55 160,125 100,160 40,125 40,55" fill="none" stroke="url(#gAbout)" stroke-width="1.5" opacity="0.5"/>
            <polygon points="100,40 140,62 140,108 100,130 60,108 60,62" fill="none" stroke="url(#gAbout)" stroke-width="1" opacity="0.4"/>
            <polygon class="face-t" points="100,50 130,67 100,84 70,67" fill="var(--accent)" opacity="0.15"/>
            <polygon class="face-l" points="70,67 100,84 100,120 70,103" fill="var(--accent)" opacity="0.1"/>
            <polygon class="face-r" points="130,67 100,84 100,120 130,103" fill="var(--accent)" opacity="0.08"/>
            <!-- Grid dots -->
            <circle cx="100" cy="100" r="2" fill="var(--accent)" opacity="0.6"/>
            <circle cx="80" cy="80" r="1.5" fill="var(--accent)" opacity="0.4"/>
            <circle cx="120" cy="80" r="1.5" fill="var(--accent)" opacity="0.4"/>
            <circle cx="80" cy="120" r="1.5" fill="var(--accent)" opacity="0.4"/>
            <circle cx="120" cy="120" r="1.5" fill="var(--accent)" opacity="0.4"/>
          </svg>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ MISSION & VALUES ░░░ -->
<section class="section about-values bg-surface">
  <div class="container">
    <div class="section-header text-center">
      <h2 class="section-title">ارزش‌های ما</h2>
      <p class="section-subtitle">اصولی که هر روز با آن‌ها کار می‌کنیم</p>
    </div>
    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </div>
        <h3 class="value-title">کیفیت بی‌توافق</h3>
        <p class="value-desc">هر قطعه‌ای که از استودیوی ما خارج می‌شود، از نظر کیفیت بررسی و تأیید شده است. دقت 50 میکرون استاندارد ماست.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
        <h3 class="value-title">تحویل به موقع</h3>
        <p class="value-desc">به قول‌هایمان پایبندیم. زمان‌بندی دقیق تحویل و اطلاع‌رسانی مداوم از مراحل پیشرفت کار.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <h3 class="value-title">مشتری محوری</h3>
        <p class="value-desc">موفقیت مشتری، موفقیت ماست. از مشاوره تا تحویل، در کنار شما هستیم.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
          </svg>
        </div>
        <h3 class="value-title">نوآوری مستمر</h3>
        <p class="value-desc">همواره در جستجوی بهترین فناوری‌ها و روش‌های نوین چاپ سه‌بعدی هستیم تا بهترین نتیجه را ارائه دهیم.</p>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ TEAM ░░░ -->
<section class="section about-team">
  <div class="container">
    <div class="section-header text-center">
      <h2 class="section-title">تیم ما</h2>
      <p class="section-subtitle">متخصصانی که رویاهای شما را چاپ می‌کنند</p>
    </div>
    <div class="team-grid">
      <div class="team-card">
        <div class="team-avatar" aria-hidden="true">
          <svg viewBox="0 0 80 80" width="80" height="80">
            <circle cx="40" cy="40" r="40" fill="var(--accent)" opacity="0.15"/>
            <path d="M40 44a14 14 0 1 0 0-28 14 14 0 0 0 0 28zm-24 20c0-12 10.7-21 24-21s24 9 24 21" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <h4 class="team-name">علیرضا کریمی</h4>
        <p class="team-role">مدیر فنی و بنیان‌گذار</p>
        <p class="team-bio">کارشناس ارشد مهندسی مواد با 8 سال تجربه در صنعت چاپ سه‌بعدی.</p>
      </div>
      <div class="team-card">
        <div class="team-avatar" aria-hidden="true">
          <svg viewBox="0 0 80 80" width="80" height="80">
            <circle cx="40" cy="40" r="40" fill="var(--accent)" opacity="0.12"/>
            <path d="M40 44a14 14 0 1 0 0-28 14 14 0 0 0 0 28zm-24 20c0-12 10.7-21 24-21s24 9 24 21" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <h4 class="team-name">مریم احمدی</h4>
        <p class="team-role">طراح ارشد سه‌بعدی</p>
        <p class="team-bio">معمار و طراح صنعتی با تخصص در نرم‌افزارهای CAD و طراحی پارامتریک.</p>
      </div>
      <div class="team-card">
        <div class="team-avatar" aria-hidden="true">
          <svg viewBox="0 0 80 80" width="80" height="80">
            <circle cx="40" cy="40" r="40" fill="var(--accent)" opacity="0.10"/>
            <path d="M40 44a14 14 0 1 0 0-28 14 14 0 0 0 0 28zm-24 20c0-12 10.7-21 24-21s24 9 24 21" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </div>
        <h4 class="team-name">محمد نصیری</h4>
        <p class="team-role">مسئول تولید و کنترل کیفیت</p>
        <p class="team-bio">متخصص در بهینه‌سازی پارامترهای چاپ و کنترل کیفیت محصولات نهایی.</p>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ STATS ░░░ -->
<section class="section stats-section bg-surface">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item">
        <span class="stat-number" data-count="<?= (int)($stats['products'] ?? 0) ?>">0</span>
        <span class="stat-label">محصول فعال</span>
      </div>
      <div class="stat-item">
        <span class="stat-number" data-count="<?= (int)($stats['orders'] ?? 0) ?>">0</span>
        <span class="stat-label">سفارش موفق</span>
      </div>
      <div class="stat-item">
        <span class="stat-number" data-count="<?= (int)($stats['clients'] ?? 0) ?>">0</span>
        <span class="stat-label">مشتری راضی</span>
      </div>
      <div class="stat-item">
        <span class="stat-number" data-count="<?= (int)($stats['portfolio'] ?? 0) ?>">0</span>
        <span class="stat-label">نمونه کار</span>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ WHY US ░░░ -->
<section class="section why-us">
  <div class="container">
    <div class="section-header text-center">
      <h2 class="section-title">چرا افگ تری‌دی؟</h2>
    </div>
    <div class="why-list">
      <div class="why-item">
        <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20" class="why-check" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>دقت چاپ تا 50 میکرون با فناوری SLA/DLP</span>
      </div>
      <div class="why-item">
        <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20" class="why-check" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>مشاوره رایگان قبل از هر پروژه</span>
      </div>
      <div class="why-item">
        <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20" class="why-check" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>پشتیبانی از فایل‌های STL، OBJ، 3MF و STEP</span>
      </div>
      <div class="why-item">
        <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20" class="why-check" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>ارسال سراسری با بسته‌بندی ایمن</span>
      </div>
      <div class="why-item">
        <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20" class="why-check" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>قیمت شفاف و بدون هزینه پنهان</span>
      </div>
      <div class="why-item">
        <svg viewBox="0 0 20 20" fill="currentColor" width="20" height="20" class="why-check" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <span>گارانتی کیفیت و بازپرداخت در صورت نارضایتی</span>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ CTA ░░░ -->
<section class="section cta-section bg-accent-soft">
  <div class="container text-center">
    <h2 class="cta-title">آماده‌اید پروژه‌تان را شروع کنیم؟</h2>
    <p class="cta-desc">همین حالا فایل خود را آپلود کنید یا با ما تماس بگیرید تا بهترین راه‌حل را پیدا کنیم.</p>
    <div class="cta-actions">
      <a href="<?= BASE_URL ?>/print-order" class="btn btn-primary btn-lg">سفارش چاپ</a>
      <a href="<?= BASE_URL ?>/contact"     class="btn btn-ghost btn-lg">تماس با ما</a>
    </div>
  </div>
</section>
