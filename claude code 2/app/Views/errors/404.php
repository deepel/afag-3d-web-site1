<div class="error-page">
  <div>
    <!-- afag3d mark -->
    <div style="display:flex;justify-content:center;margin-bottom:8px">
      <?php
      $logoClass = 'mark';
      $logoWidth = 72;
      $logoHeight = 72;
      $logoStyle = 'opacity:.35';
      include __DIR__ . '/../partials/logo-mark.php';
      ?>
    </div>

    <div class="error-code">404</div>
    <h1 class="error-msg">صفحه مورد نظر یافت نشد</h1>
    <p class="error-sub">صفحه‌ای که دنبالش بودید وجود ندارد، جابجا یا حذف شده است.</p>

    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
      <a href="<?= BASE_URL ?>/" class="btn btn-primary btn-lg">بازگشت به خانه</a>
      <a href="<?= BASE_URL ?>/shop" class="btn btn-ghost btn-lg">مشاهده فروشگاه</a>
    </div>
  </div>
</div>
