<section class="section">
  <div class="container">

    <nav class="breadcrumb" aria-label="مسیر">
      <a href="<?= BASE_URL ?>/">خانه</a>
      <span>›</span>
      <span>نتایج جستجو</span>
    </nav>

    <h1 style="font-size:clamp(22px,3vw,32px);font-weight:700;margin-bottom:8px;">
      نتایج جستجو برای: <span style="color:var(--orange);"><?= htmlspecialchars($query, ENT_QUOTES) ?></span>
    </h1>
    <p style="color:var(--gray);margin-bottom:32px;"><?= Url::toPersianDigits((string)$total) ?> محصول یافت شد</p>

    <!-- Search form -->
    <form method="GET" action="<?= BASE_URL ?>/search" style="margin-bottom:32px;display:flex;gap:10px;max-width:500px;">
      <input type="search" name="q" value="<?= htmlspecialchars($query, ENT_QUOTES) ?>"
             class="form-input" placeholder="جستجو در محصولات..." style="flex:1;">
      <button type="submit" class="btn btn-primary">جستجو</button>
    </form>

    <?php if ($products): ?>
    <div class="product-grid">
      <?php foreach ($products as $p): ?>
      <article class="product-card">
        <?php if ($p['stock'] < 1): ?>
          <span class="badge-out-of-stock">ناموجود</span>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/shop/<?= htmlspecialchars($p['shop_slug'] ?? '', ENT_QUOTES) ?>/<?= htmlspecialchars($p['slug'], ENT_QUOTES) ?>" class="product-card-img">
          <?php if (!empty($p['main_image'])): ?>
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['main_image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>" loading="lazy">
          <?php else: ?>
            <img src="<?= BASE_URL ?>/assets/img/placeholder.svg" alt="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>" loading="lazy">
          <?php endif; ?>
        </a>
        <div class="product-card-body">
          <a href="<?= BASE_URL ?>/shop/<?= htmlspecialchars($p['shop_slug'] ?? '', ENT_QUOTES) ?>/<?= htmlspecialchars($p['slug'], ENT_QUOTES) ?>">
            <h3 class="product-card-name"><?= htmlspecialchars($p['name'], ENT_QUOTES) ?></h3>
          </a>
          <?php if (!empty($p['shop_name'])): ?>
          <p style="font-size:12px;color:var(--concrete);margin-bottom:8px;"><?= htmlspecialchars($p['shop_name'], ENT_QUOTES) ?></p>
          <?php endif; ?>
          <div class="product-card-price-wrap">
            <?php if (!empty($p['compare_price']) && $p['compare_price'] > $p['price']): ?>
              <span class="product-card-compare"><?= Url::price((int)$p['compare_price'], false) ?></span>
            <?php endif; ?>
            <span class="product-card-price"><?= Url::price((int)$p['price'], false) ?></span>
            <span class="product-card-unit">تومان</span>
          </div>
          <button class="btn-add-cart" data-id="<?= $p['id'] ?>" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>
            <?= $p['stock'] < 1 ? 'ناموجود' : 'افزودن به سبد' ?>
          </button>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- CSRF for AJAX -->
    <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <nav class="pagination" style="margin-top:40px;">
      <?php if ($page > 1): ?>
        <a href="?q=<?= urlencode($query) ?>&page=<?= $page - 1 ?>">‹</a>
      <?php endif; ?>
      <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
        <?php if ($i === $page): ?>
          <span class="active"><?= Url::toPersianDigits((string)$i) ?></span>
        <?php else: ?>
          <a href="?q=<?= urlencode($query) ?>&page=<?= $i ?>"><?= Url::toPersianDigits((string)$i) ?></a>
        <?php endif; ?>
      <?php endfor; ?>
      <?php if ($page < $pages): ?>
        <a href="?q=<?= urlencode($query) ?>&page=<?= $page + 1 ?>">›</a>
      <?php endif; ?>
    </nav>
    <?php endif; ?>

    <?php else: ?>
    <div style="text-align:center;padding:80px 20px;color:var(--gray);">
      <p style="font-size:48px;margin-bottom:16px;">🔍</p>
      <p style="font-size:18px;margin-bottom:16px;">نتیجه‌ای یافت نشد.</p>
      <p style="font-size:14px;margin-bottom:24px;">عبارت دیگری را امتحان کنید یا محصولات فروشگاه را مرور کنید.</p>
      <a href="<?= BASE_URL ?>/shop/models" class="btn btn-primary" style="margin-left:8px;">فروشگاه مدل‌ها</a>
      <a href="<?= BASE_URL ?>/shop/supplies" class="btn btn-ghost">فروشگاه لوازم</a>
    </div>
    <?php endif; ?>

  </div>
</section>
