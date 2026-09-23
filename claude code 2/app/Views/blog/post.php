<?php
$pubDate  = $post['published_at'] ?: $post['created_at'];
$dateLabel = date('Y/m/d', strtotime($pubDate));
$tags      = array_filter(array_map('trim', explode(',', $post['tags'] ?? '')));
?>

<!-- Hero -->
<div class="blog-post-hero">
  <?php if ($post['cover']): ?>
  <div class="blog-post-hero__img" style="background-image: url('<?= BASE_URL ?>/uploads/<?= htmlspecialchars($post['cover'], ENT_QUOTES) ?>');" role="img" aria-label="<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>">
    <div class="blog-post-hero__overlay">
      <div class="container">
        <div class="blog-post-hero__content">
          <?php if ($post['category_name']): ?>
          <a href="<?= BASE_URL ?>/blog/category/<?= htmlspecialchars($post['category_slug'] ?? '', ENT_QUOTES) ?>" class="blog-card__badge">
            <?= htmlspecialchars($post['category_name'], ENT_QUOTES) ?>
          </a>
          <?php endif; ?>
          <h1 class="blog-post-hero__title"><?= htmlspecialchars($post['title'], ENT_QUOTES) ?></h1>
          <div class="blog-post-hero__meta">
            <span class="meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <?= htmlspecialchars($post['author_name'] ?? 'تیم افگ', ENT_QUOTES) ?>
            </span>
            <span class="meta-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
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
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="container pt-40 pb-24">
    <?php if ($post['category_name']): ?>
    <a href="<?= BASE_URL ?>/blog/category/<?= htmlspecialchars($post['category_slug'] ?? '', ENT_QUOTES) ?>" class="blog-card__badge">
      <?= htmlspecialchars($post['category_name'], ENT_QUOTES) ?>
    </a>
    <?php endif; ?>
    <h1 class="blog-post-title mt-16"><?= htmlspecialchars($post['title'], ENT_QUOTES) ?></h1>
    <div class="blog-post-meta mt-12">
      <span class="meta-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <?= htmlspecialchars($post['author_name'] ?? 'تیم افگ', ENT_QUOTES) ?>
      </span>
      <span class="meta-item"><?= $dateLabel ?></span>
      <span class="meta-item"><?= $readTime ?> دقیقه مطالعه</span>
      <span class="meta-item"><?= number_format($post['views']) ?> بازدید</span>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Content -->
<div class="container">
  <div class="blog-post-layout">

    <!-- Article -->
    <main class="blog-post">
      <?php if ($post['excerpt']): ?>
      <p class="blog-post__lead"><?= htmlspecialchars($post['excerpt'], ENT_QUOTES) ?></p>
      <?php endif; ?>

      <article class="blog-post__body prose">
        <?= $post['body'] ?>
      </article>

      <!-- Tags -->
      <?php if ($tags): ?>
      <div class="blog-post__tags mt-32">
        <span class="tags-label">برچسب‌ها:</span>
        <?php foreach ($tags as $tag): ?>
        <a href="<?= BASE_URL ?>/blog/search?q=<?= urlencode($tag) ?>" class="tag-pill"><?= htmlspecialchars($tag, ENT_QUOTES) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Share -->
      <div class="blog-post__share mt-32">
        <span class="share-label">اشتراک‌گذاری:</span>
        <button type="button" class="share-btn" data-share="copy" title="کپی لینک">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
          کپی لینک
        </button>
        <a href="https://telegram.me/share/url?url=<?= urlencode(Url::abs('/blog/' . $post['slug'])) ?>&text=<?= urlencode($post['title']) ?>"
           target="_blank" rel="noopener" class="share-btn share-btn--tg">
          <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M12 0C5.37 0 0 5.37 0 12s5.37 12 12 12 12-5.37 12-12S18.63 0 12 0zm5.89 8.14-2.04 9.62c-.15.66-.55.82-1.12.51l-3.08-2.27-1.49 1.43c-.17.17-.31.31-.62.31l.22-3.1 5.63-5.09c.24-.22-.05-.34-.37-.12L6.44 14.04 3.4 13.09c-.65-.2-.66-.65.14-.96l11.55-4.45c.54-.2 1.02.13.8.96z"/></svg>
          تلگرام
        </a>
      </div>
    </main>

    <!-- Sidebar -->
    <aside class="blog-sidebar">

      <!-- Author -->
      <div class="sidebar-card">
        <h3 class="sidebar-card__title">نویسنده</h3>
        <div class="author-widget">
          <div class="author-widget__avatar"><?= mb_substr($post['author_name'] ?? 'ت', 0, 1) ?></div>
          <div>
            <div class="author-widget__name"><?= htmlspecialchars($post['author_name'] ?? 'تیم افگ', ENT_QUOTES) ?></div>
            <div class="author-widget__role">تیم افگ تری‌دی</div>
          </div>
        </div>
      </div>

      <!-- Recent Posts -->
      <?php if ($recent): ?>
      <div class="sidebar-card">
        <h3 class="sidebar-card__title">مقالات اخیر</h3>
        <ul class="recent-posts-list">
          <?php foreach ($recent as $rp): ?>
          <?php if ((int)$rp['id'] === (int)$post['id']) continue; ?>
          <li class="recent-post-item">
            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($rp['slug'], ENT_QUOTES) ?>" class="recent-post-link">
              <?php if ($rp['cover']): ?>
              <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($rp['cover'], ENT_QUOTES) ?>" alt="" class="recent-post-thumb">
              <?php else: ?>
              <div class="recent-post-thumb recent-post-thumb--placeholder"></div>
              <?php endif; ?>
              <span><?= htmlspecialchars($rp['title'], ENT_QUOTES) ?></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <!-- Categories -->
      <?php if ($categories): ?>
      <div class="sidebar-card">
        <h3 class="sidebar-card__title">دسته‌بندی‌ها</h3>
        <ul class="sidebar-cat-list">
          <?php foreach ($categories as $cat): ?>
          <li>
            <a href="<?= BASE_URL ?>/blog/category/<?= htmlspecialchars($cat['slug'], ENT_QUOTES) ?>" class="sidebar-cat-link">
              <?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>
              <span class="sidebar-cat-count"><?= $cat['post_count'] ?></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

    </aside>
  </div>

  <!-- Related Posts -->
  <?php if ($related): ?>
  <section class="related-posts mt-64">
    <h2 class="section-title-sm mb-32">مقالات مرتبط</h2>
    <div class="blog-grid blog-grid--3">
      <?php foreach ($related as $rp): ?>
      <?php $rpDate = date('Y/m/d', strtotime($rp['published_at'] ?: $rp['created_at'])); ?>
      <article class="blog-card">
        <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($rp['slug'], ENT_QUOTES) ?>" class="blog-card__img-link" tabindex="-1">
          <?php if ($rp['cover']): ?>
          <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($rp['cover'], ENT_QUOTES) ?>" alt="" class="blog-card__img" loading="lazy">
          <?php else: ?>
          <div class="blog-card__img-placeholder"></div>
          <?php endif; ?>
        </a>
        <div class="blog-card__body">
          <?php if ($rp['category_name']): ?>
          <span class="blog-card__badge"><?= htmlspecialchars($rp['category_name'], ENT_QUOTES) ?></span>
          <?php endif; ?>
          <h3 class="blog-card__title">
            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($rp['slug'], ENT_QUOTES) ?>">
              <?= htmlspecialchars($rp['title'], ENT_QUOTES) ?>
            </a>
          </h3>
          <div class="blog-card__meta">
            <span class="meta-item"><?= $rpDate ?></span>
            <span class="meta-item"><?= Blog::readTime($rp['body'] ?? '') ?> دقیقه مطالعه</span>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

</div>
