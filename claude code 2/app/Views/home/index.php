<!-- ░░░ HERO ░░░ -->
<section class="hero" aria-label="معرفی" data-hero-parallax>
  <div class="hero-bg" aria-hidden="true">
    <div class="hero-parallax-layer hero-printer" data-parallax-printer></div>
    <div class="hero-parallax-layer hero-geometry" data-parallax-geometry></div>
    <div class="hero-readability"></div>
  </div>
  <div class="container hero-inner">
    <div class="hero-copy">
      <div class="hero-badge reveal">
        <span class="badge-dot"></span>
        استودیوی تخصصی چاپ سه‌بعدی
      </div>
      <h1 class="hero-title reveal">
        از ایده تا<br>
        <span class="hero-accent">واقعیت سه‌بعدی</span>
      </h1>
      <p class="hero-sub reveal">
        طراحی، چاپ و فروش محصولات سه‌بعدی با بالاترین کیفیت.<br>
        از ماکت معماری تا جواهرات سفارشی.
      </p>
      <div class="hero-actions reveal">
        <a href="<?= BASE_URL ?>/print" class="btn btn-primary btn-lg">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          سفارش چاپ
        </a>
        <a href="<?= BASE_URL ?>/shop" class="btn btn-ghost btn-lg">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          مشاهده فروشگاه
        </a>
      </div>
    </div>
  </div>
  <div class="hero-scroll" aria-hidden="true">
    <span>اسکرول کنید</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="6 9 12 15 18 9"/></svg>
  </div>
</section>

<!-- ░░░ STATS ░░░ -->
<section class="stats section" aria-label="آمار">
  <div class="container stats-grid">
    <div class="stat reveal">
      <span class="stat-num" data-target="<?= $stats['products'] ?>">0</span>
      <span class="stat-label">محصول فعال</span>
    </div>
    <div class="stat reveal">
      <span class="stat-num" data-target="<?= $stats['orders'] ?>">0</span>
      <span class="stat-label">سفارش انجام شده</span>
    </div>
    <div class="stat reveal">
      <span class="stat-num" data-target="<?= max($stats['clients'], 120) ?>">0</span>
      <span class="stat-label">مشتری راضی</span>
    </div>
    <div class="stat reveal">
      <span class="stat-num" data-target="<?= max($stats['portfolio'], 50) ?>">0</span>
      <span class="stat-label">پروژه موفق</span>
    </div>
  </div>
</section>

<!-- ░░░ SERVICES ░░░ -->
<section class="services section" aria-label="خدمات">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-tag">چه می‌کنیم</span>
      <h2 class="section-title">خدمات ما</h2>
      <p class="section-sub">از ایده تا محصول نهایی، در کنار شما هستیم</p>
    </div>

    <div class="services-grid">
      <article class="service-card reveal">
        <div class="service-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        </div>
        <h3 class="service-title">چاپ سه‌بعدی</h3>
        <p class="service-desc">چاپ با تکنولوژی‌های FDM، SLA و رزین با بالاترین دقت و کیفیت.</p>
        <a href="<?= BASE_URL ?>/print" class="service-link">
          سفارش دهید
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
      </article>

      <article class="service-card reveal">
        <div class="service-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32"><circle cx="12" cy="12" r="10"/><polygon points="10,8 16,12 10,16 10,8"/></svg>
        </div>
        <h3 class="service-title">طراحی سه‌بعدی</h3>
        <p class="service-desc">مدل‌سازی دیجیتال با نرم‌افزارهای حرفه‌ای برای هر نوع پروژه.</p>
        <a href="<?= BASE_URL ?>/design" class="service-link">
          بیشتر بدانید
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
      </article>

      <article class="service-card reveal">
        <div class="service-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </div>
        <h3 class="service-title">فروشگاه آنلاین</h3>
        <p class="service-desc">خرید محصولات چاپ‌شده آماده با تنوع بالا و ارسال سریع.</p>
        <a href="<?= BASE_URL ?>/shop" class="service-link">
          ورود به فروشگاه
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
      </article>

      <article class="service-card reveal">
        <div class="service-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32"><path d="M21 10H7"/><path d="M21 6H3"/><path d="M21 14H3"/><path d="M21 18H7"/></svg>
        </div>
        <h3 class="service-title">فایل‌های دیجیتال</h3>
        <p class="service-desc">دانلود فایل‌های STL آماده چاپ با ضمانت کیفیت.</p>
        <a href="<?= BASE_URL ?>/shop/digital" class="service-link">
          مشاهده فایل‌ها
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
      </article>
    </div>
  </div>
</section>

<!-- ░░░ FEATURED PRODUCTS ░░░ -->
<?php if (!empty($featuredProducts)): ?>
<section class="products-section section" aria-label="محصولات ویژه">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-tag">فروشگاه</span>
      <h2 class="section-title">محصولات ویژه</h2>
      <p class="section-sub">برترین محصولات چاپ سه‌بعدی ما</p>
    </div>

    <div class="products-grid">
      <?php foreach ($featuredProducts as $product): ?>
      <?php $comingSoon = (($product['lifecycle_status'] ?? '') === 'coming_soon')
                          || ($product['price'] === null || $product['price'] === ''); ?>
      <article class="product-card reveal">
        <a href="<?= BASE_URL ?>/product/<?= htmlspecialchars($product['slug'], ENT_QUOTES) ?>" class="product-img-wrap">
          <?php if (!empty($product['cover_image'])): ?>
            <img src="<?= Url::upload($product['cover_image']) ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>" loading="lazy">
          <?php else: ?>
            <img src="<?= BASE_URL ?>/assets/img/placeholder-product.svg" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>" loading="lazy">
          <?php endif; ?>
          <?php if ($comingSoon): ?>
            <span class="product-badge product-badge--soon">به‌زودی</span>
          <?php elseif ($product['sale_price']): ?>
            <span class="product-badge">تخفیف</span>
          <?php endif; ?>
        </a>
        <div class="product-info">
          <h3 class="product-name">
            <a href="<?= BASE_URL ?>/product/<?= htmlspecialchars($product['slug'], ENT_QUOTES) ?>">
              <?= htmlspecialchars($product['name'], ENT_QUOTES) ?>
            </a>
          </h3>
          <?php if ($comingSoon): ?>
            <div class="product-price"><span class="price-soon">به‌زودی — تماس بگیرید</span></div>
            <a href="<?= BASE_URL ?>/contact" class="btn btn-outline btn-sm btn-block">استعلام قیمت</a>
          <?php else: ?>
            <div class="product-price">
              <?php if ($product['sale_price']): ?>
                <span class="price-sale"><?= Url::price((int)$product['sale_price']) ?></span>
                <span class="price-original"><?= Url::price((int)$product['price']) ?></span>
              <?php else: ?>
                <span class="price-current"><?= Url::price((int)$product['price']) ?></span>
              <?php endif; ?>
            </div>
            <button type="button" class="btn btn-primary btn-sm btn-block btn-add-cart" data-id="<?= (int)$product['id'] ?>">
              افزودن به سبد
            </button>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <div class="section-cta reveal">
      <a href="<?= BASE_URL ?>/shop" class="btn btn-outline">مشاهده همه محصولات</a>
    </div>
  </div>
  <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">
</section>
<?php endif; ?>

<!-- ░░░ PRINT CTA ░░░ -->
<section class="print-cta section" aria-label="سفارش چاپ">
  <div class="container">
    <div class="cta-card reveal">
      <div class="cta-text">
        <h2 class="cta-title">مدل سه‌بعدی دارید؟</h2>
        <p class="cta-desc">فایل STL یا OBJ خود را آپلود کنید، قیمت آنی دریافت کنید و سفارش ثبت کنید.</p>
        <ul class="cta-features">
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="20 6 9 17 4 12"/></svg>
            پشتیبانی از STL، OBJ، 3MF
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="20 6 9 17 4 12"/></svg>
            انتخاب رنگ، ماده و کیفیت
          </li>
          <li>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="20 6 9 17 4 12"/></svg>
            تحویل سریع در سراسر ایران
          </li>
        </ul>
        <a href="<?= BASE_URL ?>/print" class="btn btn-primary btn-lg">شروع سفارش</a>
      </div>
      <div class="cta-visual" aria-hidden="true">
        <?php
        $logoClass = 'mark cta-mark';
        include __DIR__ . '/../partials/logo-mark.php';
        ?>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ PORTFOLIO ░░░ -->
<?php if (!empty($portfolio)): ?>
<section class="portfolio-section section" aria-label="نمونه کارها">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-tag">پروژه‌ها</span>
      <h2 class="section-title">نمونه کارهای ما</h2>
      <p class="section-sub">برخی از پروژه‌های موفق ما</p>
    </div>

    <div class="portfolio-grid">
      <?php foreach ($portfolio as $item): ?>
      <article class="portfolio-card reveal">
        <a href="<?= BASE_URL ?>/portfolio/<?= htmlspecialchars($item['slug'], ENT_QUOTES) ?>" class="portfolio-img-wrap">
          <?php if (!empty($item['cover'])): ?>
            <img src="<?= Url::upload($item['cover']) ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES) ?>" loading="lazy">
          <?php else: ?>
            <img src="<?= BASE_URL ?>/assets/img/placeholder-portfolio.svg" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES) ?>" loading="lazy">
          <?php endif; ?>
          <div class="portfolio-overlay">
            <span class="portfolio-view">مشاهده</span>
          </div>
        </a>
        <div class="portfolio-info">
          <h3 class="portfolio-title">
            <a href="<?= BASE_URL ?>/portfolio/<?= htmlspecialchars($item['slug'], ENT_QUOTES) ?>">
              <?= htmlspecialchars($item['title'], ENT_QUOTES) ?>
            </a>
          </h3>
          <?php if (!empty($item['client'])): ?>
            <span class="portfolio-client"><?= htmlspecialchars($item['client'], ENT_QUOTES) ?></span>
          <?php endif; ?>
          <?php if (!empty($item['tags'])): ?>
            <div class="portfolio-tags">
              <?php foreach (explode(',', $item['tags']) as $tag): ?>
                <span class="tag"><?= htmlspecialchars(trim($tag), ENT_QUOTES) ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <div class="section-cta reveal">
      <a href="<?= BASE_URL ?>/portfolio" class="btn btn-outline">مشاهده همه پروژه‌ها</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ░░░ BLOG ░░░ -->
<?php if (!empty($blogPosts)): ?>
<section class="blog-section section" aria-label="بلاگ">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-tag">مقالات</span>
      <h2 class="section-title">آخرین مطالب</h2>
      <p class="section-sub">راهنماها، آموزش‌ها و اخبار دنیای چاپ سه‌بعدی</p>
    </div>

    <div class="blog-grid">
      <?php foreach ($blogPosts as $post): ?>
      <article class="blog-card reveal">
        <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>" class="blog-img-wrap">
          <?php if (!empty($post['cover'])): ?>
            <img src="<?= Url::upload($post['cover']) ?>" alt="<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>" loading="lazy">
          <?php else: ?>
            <img src="<?= BASE_URL ?>/assets/img/placeholder-blog.svg" alt="" loading="lazy">
          <?php endif; ?>
        </a>
        <div class="blog-info">
          <?php if (!empty($post['category_name'])): ?>
            <span class="blog-cat"><?= htmlspecialchars($post['category_name'], ENT_QUOTES) ?></span>
          <?php endif; ?>
          <h3 class="blog-title">
            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>">
              <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
            </a>
          </h3>
          <?php if (!empty($post['excerpt'])): ?>
            <p class="blog-excerpt"><?= htmlspecialchars(mb_substr($post['excerpt'], 0, 100), ENT_QUOTES) ?>…</p>
          <?php endif; ?>
          <div class="blog-meta">
            <span class="blog-author"><?= htmlspecialchars($post['author_name'] ?? '', ENT_QUOTES) ?></span>
            <?php if (!empty($post['published_at'])): ?>
              <time datetime="<?= date('Y-m-d', strtotime($post['published_at'])) ?>">
                <?= Url::toPersianDigits(date('Y/m/d', strtotime($post['published_at']))) ?>
              </time>
            <?php endif; ?>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <div class="section-cta reveal">
      <a href="<?= BASE_URL ?>/blog" class="btn btn-outline">مشاهده همه مطالب</a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ░░░ PROCESS ░░░ -->
<section class="process section" aria-label="فرایند">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-tag">چگونه</span>
      <h2 class="section-title">فرایند کار ما</h2>
    </div>
    <div class="process-steps">
      <div class="process-step reveal">
        <div class="step-num">1</div>
        <h3 class="step-title">آپلود فایل</h3>
        <p class="step-desc">فایل سه‌بعدی خود را آپلود کنید یا از نمونه‌های آماده انتخاب کنید.</p>
      </div>
      <div class="process-arrow" aria-hidden="true">←</div>
      <div class="process-step reveal">
        <div class="step-num">2</div>
        <h3 class="step-title">انتخاب تنظیمات</h3>
        <p class="step-desc">ماده، رنگ، کیفیت و سایر تنظیمات چاپ را انتخاب کنید.</p>
      </div>
      <div class="process-arrow" aria-hidden="true">←</div>
      <div class="process-step reveal">
        <div class="step-num">3</div>
        <h3 class="step-title">تأیید و پرداخت</h3>
        <p class="step-desc">قیمت نهایی را تأیید کنید و پرداخت امن انجام دهید.</p>
      </div>
      <div class="process-arrow" aria-hidden="true">←</div>
      <div class="process-step reveal">
        <div class="step-num">4</div>
        <h3 class="step-title">تحویل سریع</h3>
        <p class="step-desc">محصول چاپ‌شده را در کمترین زمان تحویل بگیرید.</p>
      </div>
    </div>
  </div>
</section>
