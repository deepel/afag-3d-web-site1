<?php
$tags = array_filter(array_map('trim', explode(',', $item['tags'] ?? '')));
?>
<section class="portfolio-page">
  <div class="container">

    <div class="section-header text-center mb-40">
      <span class="eyebrow">نمونه کارها</span>
      <h1 class="section-title"><?= htmlspecialchars($item['title'], ENT_QUOTES) ?></h1>
      <?php if ($item['client']): ?>
      <p class="section-sub">کارفرما: <?= htmlspecialchars($item['client'], ENT_QUOTES) ?></p>
      <?php endif; ?>
    </div>

    <?php if ($images): ?>
    <!-- Main image -->
    <div class="portfolio-show-cover mb-32">
      <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($images[0]['path'] ?? $item['cover'], ENT_QUOTES) ?>"
           alt="<?= htmlspecialchars($item['title'], ENT_QUOTES) ?>"
           style="max-width:100%; border-radius:12px; display:block; margin:0 auto;">
    </div>

    <!-- Image gallery -->
    <?php if (count($images) > 1): ?>
    <div class="portfolio-grid mb-40" id="portfolioGrid">
      <?php
      $lbItems = [];
      foreach ($images as $img) {
          $lbItems[] = ['src' => BASE_URL . '/uploads/' . $img['path'], 'caption' => $img['caption'] ?? $item['title']];
      }
      $lbJson = htmlspecialchars(json_encode($lbItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES);
      ?>
      <?php foreach ($images as $idx => $img): ?>
      <div class="portfolio-card" data-lb-items="<?= $lbJson ?>" data-lb-index="<?= $idx ?>"
           tabindex="0" role="button">
        <div class="portfolio-card__img">
          <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($img['path'], ENT_QUOTES) ?>"
               alt="<?= htmlspecialchars($img['caption'] ?? $item['title'], ENT_QUOTES) ?>" loading="lazy">
        </div>
        <div class="portfolio-card__overlay">
          <div class="portfolio-card__zoom" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Description -->
    <?php if ($item['description']): ?>
    <div class="portfolio-show-desc mb-40" style="max-width:760px; margin:0 auto;">
      <p style="font-size:16px; line-height:1.9; color:var(--fg);"><?= nl2br(htmlspecialchars($item['description'], ENT_QUOTES)) ?></p>
    </div>
    <?php endif; ?>

    <!-- Tags -->
    <?php if ($tags): ?>
    <div style="text-align:center; margin-bottom:48px;">
      <?php foreach ($tags as $tag): ?>
      <span class="tag-pill"><?= htmlspecialchars($tag, ENT_QUOTES) ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="text-center">
      <a href="<?= BASE_URL ?>/portfolio" class="btn btn-ghost">مشاهده همه نمونه کارها</a>
    </div>
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
