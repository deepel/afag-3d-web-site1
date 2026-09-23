<div class="admin-page-header">
  <h1 class="admin-page-title">تنظیمات قیمت‌گذاری</h1>
</div>

<?= Flash::render() ?>

<!-- ─── Coefficients ──────────────────────────────────────── -->
<form action="<?= BASE_URL ?>/admin/pricing" method="post" class="admin-settings-form">
  <?= $csrf ?>

  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        ضرایب فرمول قیمت
      </h2>
    </div>

    <p class="form-hint" style="margin: 0 0 18px; line-height: 2;">
      قیمت تمام‌شده = (وزن گرم × نرخ فیلامنت) + (ساعت چاپ × نرخ دستگاه) + هزینهٔ ثابت<br>
      قیمت فروش = قیمت تمام‌شده × ضریب سود
    </p>

    <div class="settings-grid">
      <div class="form-group">
        <label class="form-label" for="p_filament">نرخ فیلامنت (تومان به ازای هر گرم)</label>
        <input type="number" id="p_filament" name="filament_rate_per_gram" class="form-input" dir="ltr" min="0"
               value="<?= (int) $pricing['filament_rate_per_gram'] ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="p_machine">نرخ دستگاه (تومان به ازای هر ساعت)</label>
        <input type="number" id="p_machine" name="machine_rate_per_hour" class="form-input" dir="ltr" min="0"
               value="<?= (int) $pricing['machine_rate_per_hour'] ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="p_overhead">هزینهٔ ثابت سربار (تومان)</label>
        <input type="number" id="p_overhead" name="fixed_overhead" class="form-input" dir="ltr" min="0"
               value="<?= (int) $pricing['fixed_overhead'] ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="p_profit">ضریب سود</label>
        <input type="number" id="p_profit" name="profit_multiplier" class="form-input" dir="ltr" min="0.1" step="0.1"
               value="<?= htmlspecialchars((string) $pricing['profit_multiplier'], ENT_QUOTES) ?>">
      </div>
    </div>
  </div>

  <div class="form-submit-row">
    <button type="submit" class="btn btn-primary btn-lg">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      ذخیره ضرایب
    </button>
  </div>
</form>

<!-- ─── Recalculate catalog ───────────────────────────────── -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="18" height="18"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
      محاسبهٔ مجدد کل کاتالوگ
    </h2>
  </div>
  <p class="form-hint" style="margin: 0 0 16px; line-height: 2;">
    قیمت همهٔ محصولاتی که <strong>وزن و ساعت چاپ</strong> دارند و قیمتشان <strong>دستی</strong> ثبت نشده، با ضرایب بالا دوباره حساب می‌شود.
    در حال حاضر <strong><?= Url::toPersianDigits((string) $autoCount) ?></strong> محصول واجد شرایط است.
  </p>
  <form action="<?= BASE_URL ?>/admin/pricing/recalc" method="post"
        onsubmit="return confirm('قیمت همهٔ محصولات خودکار دوباره محاسبه شود؟');">
    <?= $csrf ?>
    <button type="submit" class="btn btn-outline">
      محاسبهٔ مجدد قیمت‌ها
    </button>
  </form>
</div>
