<section class="error-section">
  <div class="container error-inner">
    <div class="error-visual" aria-hidden="true">
      <?php
      $logoClass = 'mark error-mark';
      $logoOpacity = 0.8;
      include __DIR__ . '/../partials/logo-mark.php';
      ?>
      <span class="error-code">500</span>
    </div>
    <h1 class="error-title">خطای سرور</h1>
    <p class="error-desc">مشکلی در سرور پیش آمده. تیم ما در حال بررسی است. لطفاً دقایقی دیگر تلاش کنید.</p>
    <?php if (!empty($message) && ENV === 'development'): ?>
      <div class="error-debug">
        <pre><?= htmlspecialchars($message, ENT_QUOTES) ?></pre>
      </div>
    <?php endif; ?>
    <div class="error-actions">
      <a href="<?= BASE_URL ?>/" class="btn btn-primary">بازگشت به خانه</a>
    </div>
  </div>
</section>
