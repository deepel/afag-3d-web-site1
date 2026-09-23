<?php
/**
 * تماس با ما — Contact Page
 */
?>

<!-- ░░░ HERO ░░░ -->
<section class="page-hero page-hero--compact">
  <div class="container">
    <div class="page-hero-content">
      <div class="page-hero-badge">تماس با ما</div>
      <h1 class="page-hero-title">در تماس <span class="text-accent">باشید</span></h1>
      <p class="page-hero-desc">سوالی دارید؟ پروژه‌ای برای مشاوره؟ خوشحال می‌شویم بشنویم.</p>
    </div>
  </div>
</section>

<!-- ░░░ CONTACT CONTENT ░░░ -->
<section class="section contact-section">
  <div class="container">
    <div class="contact-grid">

      <!-- Contact Form -->
      <div class="contact-form-wrap">
        <div class="admin-card">
          <h2 class="card-title">ارسال پیام</h2>

          <?php if (isset($_GET['sent'])): ?>
          <div class="alert alert-success">پیام شما با موفقیت ارسال شد. به زودی با شما تماس می‌گیریم.</div>
          <?php endif; ?>

          <form method="POST" action="<?= BASE_URL ?>/contact" class="contact-form" novalidate>
            <?= $csrf ?>

            <div class="form-row two-col">
              <div class="form-group">
                <label class="form-label" for="c-name">نام و نام خانوادگی *</label>
                <input type="text" id="c-name" name="name" class="form-control"
                       placeholder="مثال: علی احمدی" required
                       value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES) ?>">
              </div>
              <div class="form-group">
                <label class="form-label" for="c-mobile">شماره موبایل *</label>
                <input type="tel" id="c-mobile" name="mobile" class="form-control"
                       placeholder="09xxxxxxxxx" dir="ltr" required
                       value="<?= htmlspecialchars($_POST['mobile'] ?? '', ENT_QUOTES) ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="c-subject">موضوع *</label>
              <input type="text" id="c-subject" name="subject" class="form-control"
                     placeholder="مثال: استعلام قیمت چاپ سه‌بعدی" required
                     value="<?= htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="c-message">پیام *</label>
              <textarea id="c-message" name="message" class="form-control" rows="6"
                        placeholder="پیام خود را اینجا بنویسید..." required><?= htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-full">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true">
                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
              </svg>
              ارسال پیام
            </button>
          </form>
        </div>
      </div>

      <!-- Contact Info + Map -->
      <div class="contact-info-wrap">

        <!-- Info cards -->
        <div class="contact-info-cards">
          <?php if (!empty($siteData['site_phone'])): ?>
          <div class="contact-info-card">
            <div class="contact-info-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.58 1.22h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.77a16 16 0 0 0 6.29 6.29l1.42-1.42a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
              </svg>
            </div>
            <div class="contact-info-body">
              <span class="contact-info-label">تلفن</span>
              <a href="tel:<?= htmlspecialchars($siteData['site_phone'], ENT_QUOTES) ?>" class="contact-info-value" dir="ltr">
                <?= htmlspecialchars($siteData['site_phone'], ENT_QUOTES) ?>
              </a>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($siteData['site_email'])): ?>
          <div class="contact-info-card">
            <div class="contact-info-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24" aria-hidden="true">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
            </div>
            <div class="contact-info-body">
              <span class="contact-info-label">ایمیل</span>
              <a href="mailto:<?= htmlspecialchars($siteData['site_email'], ENT_QUOTES) ?>" class="contact-info-value">
                <?= htmlspecialchars($siteData['site_email'], ENT_QUOTES) ?>
              </a>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($siteData['site_address'])): ?>
          <div class="contact-info-card">
            <div class="contact-info-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24" aria-hidden="true">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
              </svg>
            </div>
            <div class="contact-info-body">
              <span class="contact-info-label">آدرس</span>
              <span class="contact-info-value"><?= htmlspecialchars($siteData['site_address'], ENT_QUOTES) ?></span>
            </div>
          </div>
          <?php endif; ?>

          <div class="contact-info-card">
            <div class="contact-info-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
              </svg>
            </div>
            <div class="contact-info-body">
              <span class="contact-info-label">ساعات کاری</span>
              <span class="contact-info-value">شنبه تا چهارشنبه، 9 صبح تا 6 بعد از ظهر</span>
            </div>
          </div>
        </div>

        <!-- Social links -->
        <?php if (!empty($siteData['instagram']) || !empty($siteData['telegram'])): ?>
        <div class="contact-social">
          <p class="contact-social-label">شبکه‌های اجتماعی</p>
          <div class="contact-social-links">
            <?php if (!empty($siteData['instagram'])): ?>
            <a href="<?= htmlspecialchars($siteData['instagram'], ENT_QUOTES) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="اینستاگرام">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="22" height="22">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <circle cx="12" cy="12" r="4.5"/>
                <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
              </svg>
              اینستاگرام
            </a>
            <?php endif; ?>
            <?php if (!empty($siteData['telegram'])): ?>
            <a href="<?= htmlspecialchars($siteData['telegram'], ENT_QUOTES) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="تلگرام">
              <svg viewBox="0 0 24 24" fill="currentColor" width="22" height="22">
                <path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.89 8.14-2.04 9.62c-.15.66-.55.82-1.12.51l-3.08-2.27-1.49 1.43c-.17.17-.31.31-.62.31l.22-3.1 5.63-5.09c.24-.22-.05-.34-.37-.12L6.44 14.04 3.4 13.09c-.65-.2-.66-.65.14-.96l11.55-4.45c.54-.2 1.02.13.8.96z"/>
              </svg>
              تلگرام
            </a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Branded SVG map placeholder -->
        <div class="contact-map" aria-label="موقعیت روی نقشه (تصویری)">
          <svg viewBox="0 0 400 220" xmlns="http://www.w3.org/2000/svg" class="contact-map-svg" role="img" aria-label="نقشه موقعیت استودیو">
            <title>موقعیت استودیوی افگ تری‌دی</title>
            <!-- Background -->
            <rect width="400" height="220" rx="12" fill="var(--bg2)"/>
            <!-- Grid lines -->
            <line x1="0" y1="55"  x2="400" y2="55"  stroke="var(--line)" stroke-width="0.5"/>
            <line x1="0" y1="110" x2="400" y2="110" stroke="var(--line)" stroke-width="0.5"/>
            <line x1="0" y1="165" x2="400" y2="165" stroke="var(--line)" stroke-width="0.5"/>
            <line x1="80"  y1="0" x2="80"  y2="220" stroke="var(--line)" stroke-width="0.5"/>
            <line x1="160" y1="0" x2="160" y2="220" stroke="var(--line)" stroke-width="0.5"/>
            <line x1="240" y1="0" x2="240" y2="220" stroke="var(--line)" stroke-width="0.5"/>
            <line x1="320" y1="0" x2="320" y2="220" stroke="var(--line)" stroke-width="0.5"/>
            <!-- "Roads" -->
            <rect x="0" y="98" width="400" height="24" rx="0" fill="var(--bg3)" opacity="0.5"/>
            <rect x="148" y="0" width="24" height="220" rx="0" fill="var(--bg3)" opacity="0.5"/>
            <!-- Road center lines -->
            <line x1="0" y1="110" x2="400" y2="110" stroke="var(--accent)" stroke-width="1" stroke-dasharray="12 8" opacity="0.4"/>
            <line x1="160" y1="0" x2="160" y2="220" stroke="var(--accent)" stroke-width="1" stroke-dasharray="12 8" opacity="0.4"/>
            <!-- "Blocks" -->
            <rect x="10"  y="10"  width="60" height="80" rx="4" fill="var(--bg3)" opacity="0.6"/>
            <rect x="90"  y="10"  width="50" height="80" rx="4" fill="var(--bg3)" opacity="0.6"/>
            <rect x="10"  y="130" width="130" height="80" rx="4" fill="var(--bg3)" opacity="0.6"/>
            <rect x="190" y="10"  width="80" height="80" rx="4" fill="var(--bg3)" opacity="0.6"/>
            <rect x="290" y="10"  width="100" height="80" rx="4" fill="var(--bg3)" opacity="0.5"/>
            <rect x="190" y="130" width="60" height="80" rx="4" fill="var(--bg3)" opacity="0.6"/>
            <rect x="270" y="130" width="120" height="80" rx="4" fill="var(--bg3)" opacity="0.5"/>
            <!-- Pin -->
            <circle cx="200" cy="110" r="22" fill="var(--accent)" opacity="0.18"/>
            <circle cx="200" cy="110" r="10" fill="var(--accent)"/>
            <circle cx="200" cy="110" r="4"  fill="var(--bg)"/>
            <!-- Label -->
            <rect x="120" y="140" width="160" height="36" rx="8" fill="var(--bg)" opacity="0.92"/>
            <text x="200" y="162" text-anchor="middle" fill="var(--fg)" font-family="Vazirmatn,Tahoma,sans-serif" font-size="13">استودیوی افگ تری‌دی</text>
          </svg>
        </div>

      </div>
    </div>
  </div>
</section>

<style>
.contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
@media(max-width:768px){ .contact-grid { grid-template-columns: 1fr; } }

.contact-info-cards { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem; }
.contact-info-card  { display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: var(--bg2); border: 1px solid var(--line); border-radius: var(--radius); }
.contact-info-icon  { flex-shrink: 0; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: var(--accent-soft, color-mix(in srgb, var(--accent) 12%, transparent)); border-radius: 50%; color: var(--accent); }
.contact-info-body  { display: flex; flex-direction: column; gap: 2px; }
.contact-info-label { font-size: .75rem; color: var(--gray); }
.contact-info-value { font-size: .95rem; color: var(--fg); text-decoration: none; }
a.contact-info-value:hover { color: var(--accent); }

.contact-social { margin-bottom: 1.5rem; }
.contact-social-label { font-size: .8rem; color: var(--gray); margin-bottom: .5rem; }
.contact-social-links { display: flex; gap: .75rem; flex-wrap: wrap; }
.social-link { display: flex; align-items: center; gap: .4rem; padding: .5rem 1rem; border-radius: var(--radius); border: 1px solid var(--line); background: var(--bg2); color: var(--fg); text-decoration: none; font-size: .875rem; transition: border-color .2s, color .2s; }
.social-link:hover { border-color: var(--accent); color: var(--accent); }

.contact-map { border-radius: var(--radius); overflow: hidden; border: 1px solid var(--line); }
.contact-map-svg { width: 100%; height: auto; display: block; }

.contact-form .form-row.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media(max-width:500px){ .contact-form .form-row.two-col { grid-template-columns: 1fr; } }
</style>
