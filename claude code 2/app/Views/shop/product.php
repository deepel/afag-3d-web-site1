<section class="section">
  <div class="container">

    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="مسیر">
      <a href="<?= BASE_URL ?>/">خانه</a>
      <span>›</span>
      <a href="<?= BASE_URL ?>/shop/<?= htmlspecialchars($shop['slug'], ENT_QUOTES) ?>"><?= htmlspecialchars($shop['name'], ENT_QUOTES) ?></a>
      <span>›</span>
      <span><?= htmlspecialchars($product['name'], ENT_QUOTES) ?></span>
    </nav>

    <!-- Product single -->
    <div class="product-single">

      <!-- ── Gallery ── -->
      <div>
        <div class="gallery-main" id="galleryMain">
          <?php
          $mainImg = '';
          foreach ($images as $img) { if ($img['is_cover']) { $mainImg = $img['path']; break; } }
          if (!$mainImg && $images) $mainImg = $images[0]['path'];
          ?>
          <img id="mainImg"
               src="<?= $mainImg ? BASE_URL . '/uploads/' . htmlspecialchars($mainImg, ENT_QUOTES) : BASE_URL . '/assets/img/placeholder.svg' ?>"
               alt="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>">
        </div>
        <?php if (count($images) > 1): ?>
        <div class="gallery-thumbs">
          <?php foreach ($images as $i => $img): ?>
          <div class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>"
               data-full="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($img['path'], ENT_QUOTES) ?>">
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($img['path'], ENT_QUOTES) ?>"
                 alt="تصویر <?= $i + 1 ?>">
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- ── Product Info ── -->
      <div class="product-info">
        <p class="product-sku">SKU: <?= htmlspecialchars($product['sku'] ?? '—', ENT_QUOTES) ?></p>
        <h1 class="product-info-title"><?= htmlspecialchars($product['name'], ENT_QUOTES) ?></h1>

        <!-- Rating -->
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
          <span class="stars" aria-label="امتیاز <?= $avgRating ?> از 5">
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <?= $i <= round($avgRating) ? '★' : '☆' ?>
            <?php endfor; ?>
          </span>
          <span style="font-size:13px;color:var(--gray);">(<?= Url::toPersianDigits((string)$reviewCount) ?> نظر)</span>
        </div>

        <?php $comingSoon = (($product['lifecycle_status'] ?? '') === 'coming_soon')
                            || ($product['price'] === null || $product['price'] === ''); ?>

        <!-- Price -->
        <div style="margin-bottom:24px;">
          <?php if ($comingSoon): ?>
            <div style="display:flex;align-items:baseline;gap:8px;">
              <span class="price-soon" style="font-size:22px;">به‌زودی — تماس بگیرید</span>
            </div>
          <?php else: ?>
            <?php if (!empty($product['compare_price']) && $product['compare_price'] > $product['price']): ?>
            <div class="product-compare-price"><?= Url::price((int)$product['compare_price']) ?></div>
            <?php endif; ?>
            <div style="display:flex;align-items:baseline;gap:8px;">
              <span class="product-price-big"><?= Url::price((int)$product['price'], false) ?></span>
              <span style="font-size:16px;color:var(--gray);">تومان</span>
            </div>
          <?php endif; ?>
        </div>

        <?php if ($comingSoon): ?>
        <a href="<?= BASE_URL ?>/contact" class="btn-add-to-cart-big" style="display:inline-block;text-decoration:none;text-align:center;margin-bottom:20px;">
          استعلام قیمت و سفارش
        </a>
        <?php endif; ?>

        <!-- Stock -->
        <?php if (!$comingSoon): ?>
          <?php if ($product['stock'] > 0): ?>
          <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(34,197,94,.1);color:#22c55e;padding:6px 14px;border-radius:20px;font-size:13px;margin-bottom:20px;">
            <span>●</span> موجود در انبار
          </div>
          <?php else: ?>
          <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(239,68,68,.1);color:#ef4444;padding:6px 14px;border-radius:20px;font-size:13px;margin-bottom:20px;">
            <span>●</span> ناموجود
          </div>
          <?php endif; ?>
        <?php endif; ?>

        <!-- Attributes -->
        <?php if ($attrs): ?>
        <div style="margin-bottom:24px;">
          <?php foreach ($attrs as $attrName => $values): ?>
          <div style="margin-bottom:12px;">
            <span style="font-size:13px;color:var(--gray);margin-left:8px;"><?= htmlspecialchars($attrName, ENT_QUOTES) ?>:</span>
            <?php foreach ($values as $v): ?>
              <?php if (!empty($v['color_hex'])): ?>
                <span title="<?= htmlspecialchars($v['value'], ENT_QUOTES) ?>"
                      style="display:inline-block;width:20px;height:20px;border-radius:50%;background:<?= htmlspecialchars($v['color_hex'], ENT_QUOTES) ?>;border:1px solid var(--line);margin:0 3px;vertical-align:middle;"></span>
              <?php else: ?>
                <span style="display:inline-block;background:var(--bg3);border:1px solid var(--line);padding:2px 10px;border-radius:2px;font-size:13px;margin:0 3px;"><?= htmlspecialchars($v['value'], ENT_QUOTES) ?></span>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Qty + Add to cart -->
        <?php if (!$comingSoon && $product['stock'] > 0): ?>
        <div style="display:flex;gap:14px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
          <div class="qty-wrap">
            <button type="button" class="qty-btn" data-dir="down">−</button>
            <input type="number" class="qty-input" value="1" min="1" max="<?= (int)$product['stock'] ?>">
            <button type="button" class="qty-btn" data-dir="up">+</button>
          </div>
          <button class="btn-add-to-cart-big btn-add-cart" data-id="<?= $product['id'] ?>" style="flex:1;">
            افزودن به سبد خرید
          </button>
        </div>
        <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">
        <?php endif; ?>

        <!-- Wishlist placeholder -->
        <button class="btn btn-ghost" style="width:100%;margin-bottom:8px;" disabled>
          ♡ افزودن به علاقه‌مندی‌ها
        </button>

        <!-- Share -->
        <div style="font-size:12px;color:var(--concrete);margin-top:16px;">
          کد محصول: <span style="font-family:var(--mono);"><?= htmlspecialchars($product['sku'] ?? '—', ENT_QUOTES) ?></span>
        </div>
      </div><!-- /.product-info -->
    </div><!-- /.product-single -->

    <!-- ── Tabs ── -->
    <div class="tabs" role="tablist">
      <button class="tab-btn active" data-tab="tab-desc" role="tab" aria-selected="true">توضیحات</button>
      <button class="tab-btn" data-tab="tab-specs" role="tab" aria-selected="false">مشخصات فنی</button>
      <button class="tab-btn" data-tab="tab-reviews" role="tab" aria-selected="false">
        نظرات (<?= Url::toPersianDigits((string)$reviewCount) ?>)
      </button>
    </div>

    <div id="tab-desc" class="tab-panel active" role="tabpanel">
      <?php if (!empty($product['description'])): ?>
        <div style="line-height:1.9;color:var(--gray);"><?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES)) ?></div>
      <?php else: ?>
        <p style="color:var(--concrete);">توضیحاتی برای این محصول ثبت نشده است.</p>
      <?php endif; ?>
    </div>

    <div id="tab-specs" class="tab-panel" role="tabpanel">
      <?php if ($attrs): ?>
      <table style="width:100%;border-collapse:collapse;">
        <?php foreach ($attrs as $attrName => $values): ?>
        <tr style="border-bottom:1px solid var(--line);">
          <td style="padding:12px 16px;color:var(--gray);width:30%;font-size:14px;"><?= htmlspecialchars($attrName, ENT_QUOTES) ?></td>
          <td style="padding:12px 16px;font-size:14px;">
            <?= implode('، ', array_map(fn($v) => htmlspecialchars($v['value'], ENT_QUOTES), $values)) ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!empty($product['weight'])): ?>
        <tr style="border-bottom:1px solid var(--line);">
          <td style="padding:12px 16px;color:var(--gray);font-size:14px;">وزن</td>
          <td style="padding:12px 16px;font-size:14px;"><?= Url::toPersianDigits((string)$product['weight']) ?> گرم</td>
        </tr>
        <?php endif; ?>
      </table>
      <?php else: ?>
        <p style="color:var(--concrete);">مشخصات فنی ثبت نشده است.</p>
      <?php endif; ?>
    </div>

    <div id="tab-reviews" class="tab-panel" role="tabpanel">
      <!-- Review list -->
      <?php if ($reviews): ?>
      <div style="margin-bottom:40px;">
        <?php foreach ($reviews as $r): ?>
        <div style="border-bottom:1px solid var(--line);padding:20px 0;">
          <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px;">
            <strong style="font-size:15px;"><?= htmlspecialchars($r['name'], ENT_QUOTES) ?></strong>
            <span class="stars" style="font-size:14px;">
              <?php for ($i=1;$i<=5;$i++) echo $i<=$r['rating']?'★':'☆'; ?>
            </span>
            <span style="font-size:12px;color:var(--concrete);margin-right:auto;"><?= date('Y/m/d', strtotime($r['created_at'])) ?></span>
          </div>
          <p style="font-size:14px;color:var(--gray);line-height:1.7;"><?= nl2br(htmlspecialchars($r['body'], ENT_QUOTES)) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p style="color:var(--concrete);margin-bottom:30px;">هنوز نظری ثبت نشده. اولین نفر باشید!</p>
      <?php endif; ?>

      <!-- Review form -->
      <div style="background:var(--bg2);border:1px solid var(--line);border-radius:4px;padding:28px;">
        <h3 style="font-size:18px;font-weight:600;margin-bottom:20px;">ثبت نظر</h3>
        <form method="POST" action="<?= BASE_URL ?>/product/review">
          <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">
          <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

          <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label">امتیاز</label>
            <div class="star-rating" id="starRating">
              <?php for ($i = 5; $i >= 1; $i--): ?>
              <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>">
              <label for="star<?= $i ?>">★</label>
              <?php endfor; ?>
            </div>
          </div>

          <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label" for="reviewName">نام شما *</label>
            <input type="text" id="reviewName" name="name" class="form-input" required
                   value="<?= Auth::check() ? htmlspecialchars(Auth::name(), ENT_QUOTES) : '' ?>">
          </div>

          <div class="form-group" style="margin-bottom:20px;">
            <label class="form-label" for="reviewBody">متن نظر *</label>
            <textarea id="reviewBody" name="body" class="form-input" rows="4" required style="resize:vertical;"></textarea>
          </div>

          <button type="submit" class="btn btn-primary">ثبت نظر</button>
        </form>
      </div>
    </div>

    <!-- Related Products -->
    <?php if ($related): ?>
    <div style="margin-top:60px;">
      <h2 style="font-size:22px;font-weight:700;margin-bottom:28px;">محصولات مرتبط</h2>
      <div class="product-grid" style="grid-template-columns:repeat(4,1fr);">
        <?php foreach ($related as $rp): ?>
        <article class="product-card">
          <?php if ($rp['stock'] < 1): ?>
            <span class="badge-out-of-stock">ناموجود</span>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>/shop/<?= htmlspecialchars($shop['slug'], ENT_QUOTES) ?>/<?= htmlspecialchars($rp['slug'], ENT_QUOTES) ?>" class="product-card-img">
            <?php if (!empty($rp['main_image'])): ?>
              <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($rp['main_image'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($rp['name'], ENT_QUOTES) ?>" loading="lazy">
            <?php else: ?>
              <img src="<?= BASE_URL ?>/assets/img/placeholder.svg" alt="<?= htmlspecialchars($rp['name'], ENT_QUOTES) ?>" loading="lazy">
            <?php endif; ?>
          </a>
          <div class="product-card-body">
            <a href="<?= BASE_URL ?>/shop/<?= htmlspecialchars($shop['slug'], ENT_QUOTES) ?>/<?= htmlspecialchars($rp['slug'], ENT_QUOTES) ?>">
              <h3 class="product-card-name"><?= htmlspecialchars($rp['name'], ENT_QUOTES) ?></h3>
            </a>
            <div class="product-card-price-wrap">
              <span class="product-card-price"><?= Url::price((int)$rp['price'], false) ?></span>
              <span class="product-card-unit">تومان</span>
            </div>
            <button class="btn-add-cart" data-id="<?= $rp['id'] ?>" <?= $rp['stock'] < 1 ? 'disabled' : '' ?>>
              <?= $rp['stock'] < 1 ? 'ناموجود' : 'افزودن به سبد' ?>
            </button>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</section>

<script>
// Gallery thumbs
document.querySelectorAll('.gallery-thumb').forEach(function(thumb) {
  thumb.addEventListener('click', function() {
    document.querySelectorAll('.gallery-thumb').forEach(function(t) { t.classList.remove('active'); });
    thumb.classList.add('active');
    var mainImg = document.getElementById('mainImg');
    if (mainImg) mainImg.src = thumb.dataset.full;
  });
});
</script>
