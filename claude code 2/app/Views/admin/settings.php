<div class="admin-page-header">
  <h1 class="admin-page-title">تنظیمات سایت</h1>
</div>

<?= Flash::render() ?>

<form action="<?= BASE_URL ?>/admin/settings" method="post" class="admin-settings-form">
  <?= $csrf ?>

  <!-- ─── General ────────────────────────────────────────── -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
        اطلاعات عمومی
      </h2>
    </div>
    <div class="settings-grid">
      <div class="form-group">
        <label class="form-label" for="s_site_name">نام سایت</label>
        <input type="text" id="s_site_name" name="site_name" class="form-input"
               value="<?= htmlspecialchars($settings['site_name'] ?? '', ENT_QUOTES) ?>" maxlength="120">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_site_tagline">شعار سایت</label>
        <input type="text" id="s_site_tagline" name="site_tagline" class="form-input"
               value="<?= htmlspecialchars($settings['site_tagline'] ?? '', ENT_QUOTES) ?>" maxlength="200">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_site_email">ایمیل سایت</label>
        <input type="email" id="s_site_email" name="site_email" class="form-input" dir="ltr"
               value="<?= htmlspecialchars($settings['site_email'] ?? '', ENT_QUOTES) ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_site_phone">تلفن تماس</label>
        <input type="tel" id="s_site_phone" name="site_phone" class="form-input" dir="ltr"
               value="<?= htmlspecialchars($settings['site_phone'] ?? '', ENT_QUOTES) ?>">
      </div>
      <div class="form-group form-group--full">
        <label class="form-label" for="s_site_address">آدرس</label>
        <input type="text" id="s_site_address" name="site_address" class="form-input"
               value="<?= htmlspecialchars($settings['site_address'] ?? '', ENT_QUOTES) ?>">
      </div>
      <div class="form-group form-group--full">
        <label class="form-label" for="s_seo_meta_desc">توضیحات متا (SEO)</label>
        <textarea id="s_seo_meta_desc" name="seo_meta_desc" class="form-input form-textarea" rows="3" maxlength="400"><?= htmlspecialchars($settings['seo_meta_desc'] ?? '', ENT_QUOTES) ?></textarea>
      </div>
      <div class="form-group">
        <label class="check-label">
          <input type="checkbox" name="maintenance_mode" class="check-input"
                 <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
          <span class="checkmark"></span>
          حالت تعمیر (سایت برای کاربران عادی بسته می‌شود)
        </label>
      </div>
    </div>
  </div>

  <!-- ─── Commerce ──────────────────────────────────────── -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        تجاری
      </h2>
    </div>
    <div class="settings-grid">
      <div class="form-group">
        <label class="form-label" for="s_order_prefix">پیشوند شماره سفارش</label>
        <input type="text" id="s_order_prefix" name="order_prefix" class="form-input" dir="ltr" maxlength="10"
               value="<?= htmlspecialchars($settings['order_prefix'] ?? 'AFG', ENT_QUOTES) ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_shipping_fee">هزینه ارسال (تومان)</label>
        <input type="number" id="s_shipping_fee" name="shipping_fee" class="form-input" dir="ltr" min="0"
               value="<?= htmlspecialchars($settings['shipping_fee'] ?? '35000', ENT_QUOTES) ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_free_shipping_from">ارسال رایگان از (تومان)</label>
        <input type="number" id="s_free_shipping_from" name="free_shipping_from" class="form-input" dir="ltr" min="0"
               value="<?= htmlspecialchars($settings['free_shipping_from'] ?? '500000', ENT_QUOTES) ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_tax_rate">نرخ مالیات (%)</label>
        <input type="number" id="s_tax_rate" name="tax_rate" class="form-input" dir="ltr" min="0" max="100"
               value="<?= htmlspecialchars($settings['tax_rate'] ?? '9', ENT_QUOTES) ?>">
      </div>
    </div>
  </div>

  <!-- ─── Social ────────────────────────────────────────── -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
        شبکه‌های اجتماعی
      </h2>
    </div>
    <div class="settings-grid">
      <div class="form-group">
        <label class="form-label" for="s_instagram">اینستاگرام (URL)</label>
        <input type="url" id="s_instagram" name="instagram" class="form-input" dir="ltr"
               placeholder="https://instagram.com/..."
               value="<?= htmlspecialchars($settings['instagram'] ?? '', ENT_QUOTES) ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_telegram">تلگرام (URL)</label>
        <input type="url" id="s_telegram" name="telegram" class="form-input" dir="ltr"
               placeholder="https://t.me/..."
               value="<?= htmlspecialchars($settings['telegram'] ?? '', ENT_QUOTES) ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_whatsapp">واتساپ (شماره)</label>
        <input type="tel" id="s_whatsapp" name="whatsapp" class="form-input" dir="ltr"
               placeholder="09XXXXXXXXX"
               value="<?= htmlspecialchars($settings['whatsapp'] ?? '', ENT_QUOTES) ?>">
      </div>
    </div>
  </div>

  <!-- ─── Analytics ─────────────────────────────────────── -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        آنالیتیکس
      </h2>
    </div>
    <div class="settings-grid">
      <div class="form-group form-group--full">
        <label class="form-label" for="s_google_analytics">Google Analytics Measurement ID</label>
        <input type="text" id="s_google_analytics" name="google_analytics" class="form-input" dir="ltr"
               placeholder="G-XXXXXXXXXX"
               value="<?= htmlspecialchars($settings['google_analytics'] ?? '', ENT_QUOTES) ?>">
      </div>
    </div>
  </div>

  <!-- ─── Popup ──────────────────────────────────────── -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6M9 12h6M9 15h4"/></svg>
        پاپ‌آپ سایت
      </h2>
    </div>
    <div class="settings-grid">
      <div class="form-group form-group--full">
        <label class="check-label">
          <input type="checkbox" name="popup_enabled" class="check-input"
                 value="1" <?= ($settings['popup_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
          <span class="checkmark"></span>
          فعال‌سازی پاپ‌آپ (هر بار بازدید جدید)
        </label>
      </div>
      <div class="form-group">
        <label class="form-label" for="s_popup_title">عنوان پاپ‌آپ</label>
        <input type="text" id="s_popup_title" name="popup_title" class="form-input"
               value="<?= htmlspecialchars($settings['popup_title'] ?? '', ENT_QUOTES) ?>" maxlength="200">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_popup_image">تصویر پاپ‌آپ (نام فایل در uploads/)</label>
        <input type="text" id="s_popup_image" name="popup_image" class="form-input" dir="ltr"
               value="<?= htmlspecialchars($settings['popup_image'] ?? '', ENT_QUOTES) ?>"
               placeholder="popup/banner.jpg">
      </div>
      <div class="form-group form-group--full">
        <label class="form-label" for="s_popup_content">متن پاپ‌آپ</label>
        <textarea id="s_popup_content" name="popup_content" class="form-input form-textarea" rows="3"><?= htmlspecialchars($settings['popup_content'] ?? '', ENT_QUOTES) ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label" for="s_popup_btn_text">متن دکمه</label>
        <input type="text" id="s_popup_btn_text" name="popup_btn_text" class="form-input"
               value="<?= htmlspecialchars($settings['popup_btn_text'] ?? '', ENT_QUOTES) ?>"
               placeholder="اطلاعات بیشتر">
      </div>
      <div class="form-group">
        <label class="form-label" for="s_popup_btn_url">URL دکمه</label>
        <input type="text" id="s_popup_btn_url" name="popup_btn_url" class="form-input" dir="ltr"
               value="<?= htmlspecialchars($settings['popup_btn_url'] ?? '', ENT_QUOTES) ?>"
               placeholder="/shop">
      </div>
    </div>
  </div>

  <div class="form-submit-row">
    <button type="submit" class="btn btn-primary btn-lg">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      ذخیره تنظیمات
    </button>
  </div>
</form>
