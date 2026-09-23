<section class="blog-page">
  <div class="container">

    <!-- Page Header -->
    <div class="section-header text-center mb-40">
      <span class="eyebrow">دانش و تجربه</span>
      <h1 class="section-title"><?= !empty($category) ? htmlspecialchars($category['name'], ENT_QUOTES) : 'بلاگ' ?></h1>
      <p class="section-sub">مقالات آموزشی، معرفی مواد و نمونه پروژه‌های چاپ سه‌بعدی</p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="blog-filter-bar mb-32">
      <!-- Search -->
      <form action="<?= BASE_URL ?>/blog/search" method="get" class="blog-search-form" role="search">
        <div class="search-input-wrap">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="search" name="q" class="blog-search-input" placeholder="جستجو در مقالات..."
                 value="<?= htmlspecialchars($search, ENT_QUOTES) ?>" id="blogSearch">
        </div>
        <button type="submit" class="btn btn-primary">جستجو</button>
      </form>

      <!-- Category Tabs -->
      <div class="category-tabs" role="navigation" aria-label="دسته‌بندی مقالات">
        <a href="<?= BASE_URL ?>/blog" class="category-tab <?= $activeCat === 0 && $search === '' ? 'active' : '' ?>">
          همه
        </a>
        <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_URL ?>/blog/category/<?= htmlspecialchars($cat['slug'], ENT_QUOTES) ?>"
           class="category-tab <?= $activeCat === (int)$cat['id'] ? 'active' : '' ?>">
          <?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>
          <?php if ($cat['post_count'] > 0): ?>
          <span class="tab-count"><?= $cat['post_count'] ?></span>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if (!empty($search)): ?>
    <p class="search-result-info mb-24">
      <?= $total ?> نتیجه برای «<?= htmlspecialchars($search, ENT_QUOTES) ?>»
    </p>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="56" height="56"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
      <p>هیچ مقاله‌ای یافت نشد.</p>
      <a href="<?= BASE_URL ?>/blog" class="btn btn-ghost mt-16">مشاهده همه مقالات</a>
    </div>
    <?php else: ?>

    <!-- Blog Grid -->
    <div class="blog-grid">
      <?php foreach ($posts as $post): ?>
      <?php
        $pubDate = $post['published_at'] ?: $post['created_at'];
        $dateLabel = date('Y/m/d', strtotime($pubDate));
        $readTime  = Blog::readTime($post['body'] ?? '');
      ?>
      <article class="blog-card">
        <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>" class="blog-card__img-link" tabindex="-1" aria-hidden="true">
          <?php if ($post['cover']): ?>
          <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($post['cover'], ENT_QUOTES) ?>"
               alt="<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>"
               class="blog-card__img" loading="lazy">
          <?php else: ?>
          <div class="blog-card__img-placeholder">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="40" height="40"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          </div>
          <?php endif; ?>
        </a>
        <div class="blog-card__body">
          <?php if ($post['category_name']): ?>
          <a href="<?= BASE_URL ?>/blog/category/<?= htmlspecialchars($post['category_slug'] ?? '', ENT_QUOTES) ?>" class="blog-card__badge">
            <?= htmlspecialchars($post['category_name'], ENT_QUOTES) ?>
          </a>
          <?php endif; ?>
          <h2 class="blog-card__title">
            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>">
              <?= htmlspecialchars($post['title'], ENT_QUOTES) ?>
            </a>
          </h2>
          <?php if ($post['excerpt']): ?>
          <p class="blog-card__excerpt"><?= htmlspecialchars(mb_substr($post['excerpt'], 0, 120), ENT_QUOTES) ?>…</p>
          <?php endif; ?>
          <div class="blog-card__meta">
            <span class="meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <?= $dateLabel ?>
            </span>
            <span class="meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <?= $readTime ?> دقیقه مطالعه
            </span>
            <span class="meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <?= number_format($post['views']) ?> بازدید
            </span>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <div class="pagination mt-48">
      <?php for ($p = 1; $p <= $pages; $p++): ?>
      <?php
        $qp = http_build_query(array_filter(['page' => $p, 'cat' => $activeCat ?: null, 'q' => $search ?: null]));
      ?>
      <a href="?<?= $qp ?>" class="page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>
  </div>
</section>
