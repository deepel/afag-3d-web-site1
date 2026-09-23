<section class="portfolio-page">
  <div class="container">

    <!-- Page Header -->
    <div class="section-header text-center mb-48">
      <span class="eyebrow">دستاوردهای ما</span>
      <h1 class="section-title">نمونه کارها</h1>
      <p class="section-sub">نگاهی به پروژه‌های چاپ سه‌بعدی که با افتخار برای مشتریان‌مان تکمیل کرده‌ایم.</p>
    </div>

    <?php if (empty($items)): ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="64" height="64"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      <p>هنوز نمونه کاری ثبت نشده است.</p>
    </div>
    <?php else: ?>

    <!-- Portfolio Grid -->
    <div class="portfolio-grid" id="portfolioGrid">
      <?php foreach ($items as $idx => $item): ?>
      <?php
        // Collect images for lightbox
        $imgs      = $item['images'] ?? [];
        $coverPath = $item['cover'] ?: ($imgs[0]['path'] ?? null);
        // Build JSON for lightbox
        $lbItems = [];
        if ($coverPath) {
            $lbItems[] = [
                'src'     => BASE_URL . '/uploads/' . $coverPath,
                'caption' => $item['title'],
            ];
        }
        foreach ($imgs as $img) {
            $lbItems[] = [
                'src'     => BASE_URL . '/uploads/' . $img['path'],
                'caption' => $img['caption'] ?? $item['title'],
            ];
        }
        $lbJson = htmlspecialchars(json_encode($lbItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES);
        $tags   = array_filter(array_map('trim', explode(',', $item['tags'] ?? '')));
      ?>
      <div class="portfolio-card" data-lb-items="<?= $lbJson ?>" data-lb-index="0" tabindex="0"
           role="button" aria-label="مشاهده <?= htmlspecialchars($item['title'], ENT_QUOTES) ?>">
        <div class="portfolio-card__img">
          <?php if ($coverPath): ?>
          <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($coverPath, ENT_QUOTES) ?>"
               alt="<?= htmlspecialchars($item['title'], ENT_QUOTES) ?>"
               loading="lazy">
          <?php else: ?>
          <div class="portfolio-card__placeholder">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="48" height="48"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
          <?php endif; ?>
          <?php if (count($lbItems) > 1): ?>
          <span class="portfolio-card__count"><?= count($lbItems) ?> تصویر</span>
          <?php endif; ?>
        </div>
        <div class="portfolio-card__overlay">
          <div class="portfolio-card__info">
            <h3 class="portfolio-card__title"><?= htmlspecialchars($item['title'], ENT_QUOTES) ?></h3>
            <?php if ($item['client']): ?>
            <p class="portfolio-card__client"><?= htmlspecialchars($item['client'], ENT_QUOTES) ?></p>
            <?php endif; ?>
            <?php if ($tags): ?>
            <div class="portfolio-card__tags">
              <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
              <span class="tag-pill"><?= htmlspecialchars($tag, ENT_QUOTES) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
          <div class="portfolio-card__zoom" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <div class="pagination mt-48">
      <?php for ($p = 1; $p <= $pages; $p++): ?>
      <a href="?page=<?= $p ?>" class="page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>
  </div>
</section>

<!-- Lightbox -->
<div id="lightbox" class="lightbox-overlay" role="dialog" aria-modal="true" aria-label="مشاهده تصاویر" hidden>
  <button class="lightbox-close" id="lbClose" aria-label="بستن">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="22" height="22"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
  <button class="lightbox-nav lightbox-nav--prev" id="lbPrev" aria-label="قبلی">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="22" height="22"><polyline points="15 18 9 12 15 6"/></svg>
  </button>
  <div class="lightbox-box">
    <img id="lbMainImg" class="lightbox-img" src="" alt="">
    <p id="lbCaption" class="lightbox-caption"></p>
  </div>
  <button class="lightbox-nav lightbox-nav--next" id="lbNext" aria-label="بعدی">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="22" height="22"><polyline points="9 18 15 12 9 6"/></svg>
  </button>
  <div class="lightbox-thumbs" id="lbThumbs"></div>
  <div class="lightbox-counter" id="lbCounter"></div>
</div>
