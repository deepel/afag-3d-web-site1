<?php
// Helper: build URL preserving current filters but changing one param
function filterUrl(array $override = [], array $remove = []): string {
    $params = $_GET;
    foreach ($override as $k => $v) $params[$k] = $v;
    foreach ($remove  as $k)        unset($params[$k]);
    unset($params['page']); // reset page on filter change
    $qs = http_build_query($params);
    return BASE_URL . '/shop/' . ($shop['slug'] ?? '') . ($qs ? '?' . $qs : '');
}
?>
<section class="section">
  <div class="container">

    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="مسیر">
      <a href="<?= BASE_URL ?>/">خانه</a>
      <span>›</span>
      <span><?= htmlspecialchars($shop['name'], ENT_QUOTES) ?></span>
      <?php if ($activeCategory): ?>
        <span>›</span>
        <span><?= htmlspecialchars($activeCategory['name'], ENT_QUOTES) ?></span>
      <?php endif; ?>
    </nav>

    <div class="shop-layout">

      <!-- ── Sidebar ── -->
      <aside class="shop-sidebar">
        <form method="GET" action="<?= BASE_URL ?>/shop/<?= htmlspecialchars($shop['slug'], ENT_QUOTES) ?>" class="filter-form" id="filterForm">

          <!-- Shop info -->
          <div style="margin-bottom:28px;">
            <h2 style="font-size:20px;font-weight:700;margin-bottom:8px;"><?= htmlspecialchars($shop['name'], ENT_QUOTES) ?></h2>
            <?php if (!empty($shop['description'])): ?>
              <p style="font-size:13px;color:var(--gray);line-height:1.6;"><?= nl2br(htmlspecialchars($shop['description'], ENT_QUOTES)) ?></p>
            <?php endif; ?>
          </div>

          <!-- Categories -->
          <?php if ($categories): ?>
          <div class="filter-section">
            <div class="filter-title">دسته‌بندی‌ها</div>
            <div class="filter-options">
              <label class="filter-check">
                <input type="radio" name="cat" value="" <?= empty($_GET['cat']) ? 'checked' : '' ?>>
                <span>همه محصولات</span>
                <span style="margin-right:auto;font-size:12px;color:var(--concrete);">(<?= Url::toPersianDigits((string)$totalProducts) ?>)</span>
              </label>
              <?php foreach ($categories as $cat): ?>
              <label class="filter-check">
                <input type="radio" name="cat" value="<?= $cat['id'] ?>" <?= (isset($_GET['cat']) && (int)$_GET['cat'] === $cat['id']) ? 'checked' : '' ?>>
                <span><?= htmlspecialchars($cat['name'], ENT_QUOTES) ?></span>
                <span style="margin-right:auto;font-size:12px;color:var(--concrete);">(<?= Url::toPersianDigits((string)$cat['product_count']) ?>)</span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <!-- Price Range -->
          <div class="filter-section">
            <div class="filter-title">محدوده قیمت (تومان)</div>
            <div class="price-range">
              <input type="number" name="min" placeholder="از" value="<?= (int)($_GET['min'] ?? 0) ?: '' ?>" min="0" step="1000">
              <span style="color:var(--gray);">—</span>
              <input type="number" name="max" placeholder="تا" value="<?= (int)($_GET['max'] ?? 0) ?: '' ?>" min="0" step="1000">
            </div>
            <button type="submit" class="btn btn-ghost" style="margin-top:12px;width:100%;font-size:13px;">اعمال قیمت</button>
          </div>

          <!-- Attribute Filters -->
          <?php foreach ($attrFilters as $attr): ?>
          <div class="filter-section">
            <div class="filter-title"><?= htmlspecialchars($attr['name'], ENT_QUOTES) ?></div>
            <?php if ($attr['type'] === 'color'): ?>
            <div class="color-swatches">
              <?php foreach ($attr['values'] as $val): ?>
              <label title="<?= htmlspecialchars($val['value'], ENT_QUOTES) ?>">
                <input type="checkbox" name="av[]" value="<?= $val['id'] ?>" style="display:none;" <?= in_array($val['id'], (array)($_GET['av'] ?? [])) ? 'checked' : '' ?>>
                <span class="color-swatch <?= in_array($val['id'], (array)($_GET['av'] ?? [])) ? 'active' : '' ?>"
                      style="background:<?= htmlspecialchars($val['color_hex'] ?? '#ccc', ENT_QUOTES) ?>;"></span>
              </label>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="filter-options">
              <?php foreach ($attr['values'] as $val): ?>
              <label class="filter-check">
                <input type="checkbox" name="av[]" value="<?= $val['id'] ?>" <?= in_array($val['id'], (array)($_GET['av'] ?? [])) ? 'checked' : '' ?>>
                <span><?= htmlspecialchars($val['value'], ENT_QUOTES) ?></span>
                <span style="margin-right:auto;font-size:11px;color:var(--concrete);"><?= Url::toPersianDigits((string)$val['product_count']) ?></span>
              </label>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>

          <!-- Sort (hidden — controlled from sort bar) -->
          <input type="hidden" name="sort" id="sortInput" value="<?= htmlspecialchars($_GET['sort'] ?? 'newest', ENT_QUOTES) ?>">

          <button type="button" onclick="window.location='<?= BASE_URL ?>/shop/<?= htmlspecialchars($shop['slug'], ENT_QUOTES) ?>'"
                  class="btn btn-ghost" style="width:100%;margin-top:8px;font-size:13px;color:var(--concrete);">
            ✕ پاک کردن فیلترها
          </button>
        </form>
      </aside>

      <!-- ── Main ── -->
      <div class="shop-main">

        <!-- Active filters as removable tags -->
        <?php
        $activeTags = [];
        if (!empty($_GET['cat']) && $activeCategory) {
            $activeTags[] = ['label' => 'دسته: ' . $activeCategory['name'], 'remove' => filterUrl([], ['cat'])];
        }
        if (!empty($_GET['min'])) $activeTags[] = ['label' => 'از ' . Url::price((int)$_GET['min']), 'remove' => filterUrl([], ['min'])];
        if (!empty($_GET['max'])) $activeTags[] = ['label' => 'تا ' . Url::price((int)$_GET['max']), 'remove' => filterUrl([], ['max'])];
        if (!empty($_GET['av'])) {
            foreach ($attrFilters as $attr) {
                foreach ($attr['values'] as $val) {
                    if (in_array($val['id'], (array)$_GET['av'])) {
                        $newAv = array_diff((array)$_GET['av'], [$val['id']]);
                        $p = array_merge($_GET, ['av' => $newAv]);
                        unset($p['page']);
                        $url = BASE_URL . '/shop/' . $shop['slug'] . '?' . http_build_query($p);
                        $activeTags[] = ['label' => $attr['name'] . ': ' . $val['value'], 'remove' => $url];
                    }
                }
            }
        }
        ?>
        <?php if ($activeTags): ?>
        <div class="active-filters">
          <?php foreach ($activeTags as $tag): ?>
          <a href="<?= htmlspecialchars($tag['remove'], ENT_QUOTES) ?>" class="active-filter-tag">
            <?= htmlspecialchars($tag['label'], ENT_QUOTES) ?>
            <button type="button">✕</button>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Sort bar -->
        <div class="sort-bar">
          <div class="result-count">
            <?= Url::toPersianDigits((string)$totalProducts) ?> محصول یافت شد
          </div>
          <select class="sort-select" onchange="document.getElementById('sortInput').value=this.value;document.getElementById('filterForm').submit();">
            <option value="newest"    <?= ($_GET['sort'] ?? '') === 'newest'    ? 'selected' : '' ?>>جدیدترین</option>
            <option value="cheapest"  <?= ($_GET['sort'] ?? '') === 'cheapest'  ? 'selected' : '' ?>>ارزان‌ترین</option>
            <option value="expensive" <?= ($_GET['sort'] ?? '') === 'expensive' ? 'selected' : '' ?>>گران‌ترین</option>
            <option value="popular"   <?= ($_GET['sort'] ?? '') === 'popular'   ? 'selected' : '' ?>>محبوب‌ترین</option>
          </select>
        </div>

        <!-- Product Grid -->
        <?php if ($products): ?>
        <div class="product-grid">
          <?php foreach ($products as $p): ?>
          <?php $comingSoon = (($p['lifecycle_status'] ?? '') === 'coming_soon')
                              || ($p['price'] === null || $p['price'] === ''); ?>
          <article class="product-card">
            <?php if ($comingSoon): ?>
              <span class="badge-out-of-stock" style="background:var(--orange);color:#fff">به‌زودی</span>
            <?php elseif ($p['stock'] < 1): ?>
              <span class="badge-out-of-stock">ناموجود</span>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/shop/<?= htmlspecialchars($shop['slug'], ENT_QUOTES) ?>/<?= htmlspecialchars($p['slug'], ENT_QUOTES) ?>" class="product-card-img">
              <?php if (!empty($p['main_image'])): ?>
                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['main_image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>" loading="lazy">
              <?php else: ?>
                <img src="<?= BASE_URL ?>/assets/img/placeholder.svg" alt="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>" loading="lazy">
              <?php endif; ?>
            </a>
            <div class="product-card-body">
              <a href="<?= BASE_URL ?>/shop/<?= htmlspecialchars($shop['slug'], ENT_QUOTES) ?>/<?= htmlspecialchars($p['slug'], ENT_QUOTES) ?>">
                <h3 class="product-card-name"><?= htmlspecialchars($p['name'], ENT_QUOTES) ?></h3>
              </a>
              <?php if ($comingSoon): ?>
                <div class="product-card-price-wrap"><span class="price-soon">به‌زودی — تماس بگیرید</span></div>
                <a href="<?= BASE_URL ?>/contact" class="btn-add-cart" style="text-align:center;text-decoration:none">استعلام قیمت</a>
              <?php else: ?>
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
              <?php endif; ?>
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <!-- CSRF for AJAX -->
        <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">

        <!-- Pagination -->
        <?php if ($pages > 1): ?>
        <nav class="pagination" aria-label="صفحه‌بندی">
          <?php if ($page > 1): ?>
            <a href="<?= htmlspecialchars(filterUrl(['page' => $page - 1]), ENT_QUOTES) ?>">‹</a>
          <?php endif; ?>
          <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
            <?php if ($i === $page): ?>
              <span class="active"><?= Url::toPersianDigits((string)$i) ?></span>
            <?php else: ?>
              <a href="<?= htmlspecialchars(filterUrl(['page' => $i]), ENT_QUOTES) ?>"><?= Url::toPersianDigits((string)$i) ?></a>
            <?php endif; ?>
          <?php endfor; ?>
          <?php if ($page < $pages): ?>
            <a href="<?= htmlspecialchars(filterUrl(['page' => $page + 1]), ENT_QUOTES) ?>">›</a>
          <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div style="text-align:center;padding:80px 20px;color:var(--gray);">
          <p style="font-size:18px;margin-bottom:16px;">محصولی یافت نشد.</p>
          <a href="<?= BASE_URL ?>/shop/<?= htmlspecialchars($shop['slug'], ENT_QUOTES) ?>" class="btn btn-ghost">نمایش همه محصولات</a>
        </div>
        <?php endif; ?>
      </div><!-- /.shop-main -->
    </div><!-- /.shop-layout -->
  </div>
</section>
