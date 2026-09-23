<section class="print-order-page">
  <div class="container">

    <!-- Page Header -->
    <div class="section-header text-center mb-48">
      <span class="eyebrow">خدمات تخصصی</span>
      <h1 class="section-title">سفارش چاپ سفارشی</h1>
      <p class="section-sub">فایل سه‌بعدی خود را آپلود کنید، گزینه‌ها را انتخاب کنید و منتظر استعلام قیمت ما باشید.</p>
    </div>

    <!-- Info Box -->
    <div class="print-info-box mb-40">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="22" height="22"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <span>پس از ثبت سفارش، تیم ما با شما تماس می‌گیرد و قیمت نهایی را اعلام می‌کند.</span>
    </div>

    <form action="<?= BASE_URL ?>/print-order" method="post" enctype="multipart/form-data" class="print-order-form" id="printOrderForm" novalidate>
      <?= $csrf ?>

      <!-- ── File Upload Zone ──────────────────────────────── -->
      <div class="form-section">
        <h2 class="form-section-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          آپلود فایل مدل سه‌بعدی
        </h2>
        <div class="upload-zone" id="uploadZone">
          <input type="file" name="model_file" id="modelFile" class="upload-zone__input"
                 accept=".stl,.obj,.3mf,.step,.stp,.gcode,.zip,.rar">
          <div class="upload-zone__body" id="uploadZoneBody">
            <svg class="upload-zone__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <p class="upload-zone__text">فایل STL/OBJ/3MF/STEP خود را اینجا رها کنید یا <span class="upload-zone__browse">کلیک کنید</span></p>
            <p class="upload-zone__hint">فرمت‌های مجاز: STL، OBJ، 3MF، STEP، STP، GCODE، ZIP، RAR — حداکثر 50 مگابایت</p>
          </div>
          <div class="upload-zone__preview" id="uploadPreview" hidden>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="28" height="28"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span id="uploadFilename">نام فایل</span>
            <button type="button" class="upload-zone__remove" id="uploadRemove" aria-label="حذف فایل">✕</button>
          </div>
        </div>
      </div>

      <!-- ── Print Options ──────────────────────────────────── -->
      <div class="form-section">
        <h2 class="form-section-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          گزینه‌های چاپ
        </h2>
        <div class="print-options-grid">

          <!-- Color -->
          <div class="option-group">
            <label class="option-group__label">رنگ</label>
            <input type="hidden" name="color_id" id="colorIdInput" value="">
            <div class="color-swatches" role="radiogroup" aria-label="انتخاب رنگ">
              <?php
              $colorMap = [
                'White'   => '#ffffff',
                'Black'   => '#1a1a1a',
                'Gray'    => '#888888',
                'Orange'  => '#f97316',
                'Red'     => '#ef4444',
                'Blue'    => '#3b82f6',
                'Green'   => '#22c55e',
                'Yellow'  => '#eab308',
                'Purple'  => '#a855f7',
                'Pink'    => '#ec4899',
              ];
              ?>
              <?php foreach ($options['color'] ?? [] as $opt): ?>
              <?php
                $hex       = $colorMap[$opt['name']] ?? '#cccccc';
                $suspended = ($opt['status'] == 0);
                $cls       = $suspended ? 'color-swatch color-swatch--disabled' : 'color-swatch';
                $title     = $suspended ? $opt['name_fa'] . ' — موقتاً در دسترس نیست' : $opt['name_fa'];
              ?>
              <button type="button"
                      class="<?= $cls ?>"
                      data-id="<?= $opt['id'] ?>"
                      data-label="<?= htmlspecialchars($opt['name_fa'], ENT_QUOTES) ?>"
                      title="<?= htmlspecialchars($title, ENT_QUOTES) ?>"
                      style="--swatch-color: <?= $hex ?>;"
                      <?= $suspended ? 'disabled aria-disabled="true"' : '' ?>
                      aria-label="<?= htmlspecialchars($title, ENT_QUOTES) ?>">
                <?php if ($suspended): ?>
                <span class="swatch-strikethrough"></span>
                <?php endif; ?>
              </button>
              <?php endforeach; ?>
            </div>
            <p class="color-selected-label" id="colorSelectedLabel">رنگی انتخاب نشده</p>
          </div>

          <!-- Print Type -->
          <div class="option-group">
            <label class="option-group__label">نوع چاپ <span class="required-star">*</span></label>
            <div class="radio-options" role="radiogroup">
              <?php foreach ($options['print_type'] ?? [] as $opt): ?>
              <?php $suspended = ($opt['status'] == 0); ?>
              <label class="radio-option <?= $suspended ? 'radio-option--suspended' : '' ?>">
                <input type="radio" name="print_type_id" value="<?= $opt['id'] ?>"
                       <?= $suspended ? 'disabled' : '' ?> required>
                <span class="radio-option__mark"></span>
                <span class="radio-option__label">
                  <?= htmlspecialchars($opt['name'], ENT_QUOTES) ?>
                  <?php if ($suspended): ?><span class="suspended-badge">موقتاً در دسترس نیست</span><?php endif; ?>
                </span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Material -->
          <div class="option-group">
            <label class="option-group__label">جنس مواد <span class="required-star">*</span></label>
            <div class="radio-options" role="radiogroup">
              <?php foreach ($options['material'] ?? [] as $opt): ?>
              <?php $suspended = ($opt['status'] == 0); ?>
              <label class="radio-option <?= $suspended ? 'radio-option--suspended' : '' ?>">
                <input type="radio" name="material_id" value="<?= $opt['id'] ?>"
                       <?= $suspended ? 'disabled' : '' ?> required>
                <span class="radio-option__mark"></span>
                <span class="radio-option__label">
                  <?= htmlspecialchars($opt['name'], ENT_QUOTES) ?>
                  <?php if ($opt['price_add'] > 0): ?>
                    <span class="option-price">+<?= number_format($opt['price_add']) ?> تومان</span>
                  <?php endif; ?>
                  <?php if ($suspended): ?><span class="suspended-badge">موقتاً در دسترس نیست</span><?php endif; ?>
                </span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Quality -->
          <div class="option-group">
            <label class="option-group__label">کیفیت چاپ <span class="required-star">*</span></label>
            <div class="quality-options" role="radiogroup">
              <?php
              $qualityDesc = [
                'Draft'    => 'سریع‌ترین، مناسب نمونه اولیه',
                'Standard' => 'تعادل سرعت و کیفیت',
                'Fine'     => 'دقت 0.1mm — مناسب برای ماکت',
                'Ultra'    => 'بالاترین دقت — مناسب جواهرات',
              ];
              ?>
              <?php foreach ($options['quality'] ?? [] as $opt): ?>
              <?php $suspended = ($opt['status'] == 0); ?>
              <label class="quality-option <?= $suspended ? 'quality-option--suspended' : '' ?>">
                <input type="radio" name="quality_id" value="<?= $opt['id'] ?>"
                       <?= $suspended ? 'disabled' : '' ?> required>
                <span class="quality-option__inner">
                  <span class="quality-option__name">
                    <?= htmlspecialchars($opt['name_fa'], ENT_QUOTES) ?>
                    <?php if ($opt['price_add'] != 0): ?>
                      <span class="option-price"><?= $opt['price_add'] > 0 ? '+' : '' ?><?= number_format($opt['price_add']) ?> تومان</span>
                    <?php endif; ?>
                  </span>
                  <span class="quality-option__desc"><?= htmlspecialchars($qualityDesc[$opt['name']] ?? '', ENT_QUOTES) ?></span>
                  <?php if ($suspended): ?><span class="suspended-badge">موقتاً در دسترس نیست</span><?php endif; ?>
                </span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

        </div><!-- /.print-options-grid -->

        <!-- Quantity -->
        <div class="option-group option-group--quantity">
          <label class="option-group__label" for="quantityInput">تعداد <span class="required-star">*</span></label>
          <div class="quantity-wrapper">
            <button type="button" class="qty-btn" id="qtyMinus" aria-label="کم کردن">−</button>
            <input type="number" name="quantity" id="quantityInput" class="qty-input"
                   value="1" min="1" max="99" required>
            <button type="button" class="qty-btn" id="qtyPlus" aria-label="افزودن">+</button>
          </div>
        </div>
      </div>

      <!-- ── Customer Info ──────────────────────────────────── -->
      <div class="form-section">
        <h2 class="form-section-title">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="20" height="20"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          اطلاعات تماس
        </h2>
        <div class="contact-grid">
          <div class="form-group">
            <label class="form-label" for="customerName">نام و نام خانوادگی <span class="required-star">*</span></label>
            <input type="text" id="customerName" name="name" class="form-input"
                   placeholder="مثال: علی احمدی" maxlength="120" required
                   value="<?= htmlspecialchars(Auth::name(), ENT_QUOTES) ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="customerMobile">شماره موبایل <span class="required-star">*</span></label>
            <input type="tel" id="customerMobile" name="mobile" class="form-input" dir="ltr"
                   placeholder="09XXXXXXXXX" maxlength="11" pattern="09[0-9]{9}" required>
            <span class="form-hint">شماره 11 رقمی و با 09 شروع شود</span>
          </div>
          <div class="form-group form-group--full">
            <label class="form-label" for="customerNotes">توضیحات و درخواست‌های ویژه</label>
            <textarea id="customerNotes" name="notes" class="form-input form-textarea" rows="4"
                      placeholder="جزئیات بیشتر درباره مدل، کاربرد، یا هر درخواست خاصی را اینجا بنویسید..."></textarea>
          </div>
        </div>
      </div>

      <!-- ── Submit ─────────────────────────────────────────── -->
      <div class="form-submit-row">
        <button type="submit" class="btn btn-primary btn-lg btn-submit-order">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          ثبت سفارش و دریافت استعلام قیمت
        </button>
      </div>

    </form>
  </div>
</section>
