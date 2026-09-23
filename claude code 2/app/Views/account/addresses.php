<?php
/**
 * Account Addresses
 */
$editing = $editAddress ?? null;
?>
<div class="account-layout">

  <!-- ░░░ SIDEBAR ░░░ -->
  <aside class="account-sidebar">
    <nav aria-label="منوی حساب کاربری">
      <a href="<?= BASE_URL ?>/account">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        داشبورد
      </a>
      <a href="<?= BASE_URL ?>/account/orders">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        سفارش‌ها
      </a>
      <a href="<?= BASE_URL ?>/account/print-orders">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        سفارش چاپ
      </a>
      <a href="<?= BASE_URL ?>/account/addresses" class="active">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        آدرس‌ها
      </a>
      <a href="<?= BASE_URL ?>/account/profile">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        پروفایل
      </a>
      <a href="<?= BASE_URL ?>/account/password">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        تغییر رمز
      </a>
      <a href="<?= BASE_URL ?>/logout" style="color:var(--red,#ef4444)">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        خروج
      </a>
    </nav>
  </aside>

  <!-- ░░░ MAIN ░░░ -->
  <div class="account-main">

    <!-- Saved Addresses List -->
    <div class="account-card">
      <h2>آدرس‌های ذخیره شده</h2>

      <?php if (empty($addresses)): ?>
        <div class="empty-state">
          <div class="icon">📍</div>
          <p>هنوز آدرسی ثبت نکرده‌اید.</p>
        </div>
      <?php else: ?>
        <div style="display:grid;gap:16px">
          <?php foreach ($addresses as $addr): ?>
          <div style="background:var(--bg3);border:1px solid var(--line);border-radius:4px;padding:20px;position:relative">
            <?php if ($addr['is_default']): ?>
            <span class="status-badge status-completed" style="position:absolute;top:14px;left:14px">پیش‌فرض</span>
            <?php endif; ?>

            <div style="font-weight:600;margin-bottom:8px"><?= htmlspecialchars($addr['title'] ?: 'آدرس', ENT_QUOTES) ?></div>
            <div style="font-size:14px;color:var(--gray);line-height:1.8">
              <div><?= htmlspecialchars($addr['name'], ENT_QUOTES) ?> — <span dir="ltr"><?= htmlspecialchars($addr['mobile'], ENT_QUOTES) ?></span></div>
              <?php if ($addr['province']): ?>
              <div><?= htmlspecialchars($addr['province'], ENT_QUOTES) ?><?= $addr['city'] ? ' — ' . htmlspecialchars($addr['city'], ENT_QUOTES) : '' ?></div>
              <?php endif; ?>
              <div><?= htmlspecialchars($addr['address'], ENT_QUOTES) ?></div>
              <?php if ($addr['postal_code']): ?>
              <div>کد پستی: <span dir="ltr"><?= htmlspecialchars($addr['postal_code'], ENT_QUOTES) ?></span></div>
              <?php endif; ?>
            </div>

            <div style="display:flex;gap:10px;margin-top:14px;flex-wrap:wrap">
              <?php if (!$addr['is_default']): ?>
              <form method="post" action="<?= BASE_URL ?>/account/addresses/<?= (int)$addr['id'] ?>/default" style="display:inline">
                <?= $csrf ?>
                <button type="submit" class="btn btn-ghost" style="padding:5px 12px;font-size:12px">تنظیم پیش‌فرض</button>
              </form>
              <?php endif; ?>

              <a href="<?= BASE_URL ?>/account/addresses/<?= (int)$addr['id'] ?>/edit" class="btn btn-ghost" style="padding:5px 12px;font-size:12px">ویرایش</a>

              <form method="post" action="<?= BASE_URL ?>/account/addresses/<?= (int)$addr['id'] ?>/delete" style="display:inline"
                    onsubmit="return confirm('آیا مطمئن هستید؟')">
                <?= $csrf ?>
                <button type="submit" class="btn btn-ghost" style="padding:5px 12px;font-size:12px;color:var(--red,#ef4444)">حذف</button>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Add / Edit Address Form -->
    <div class="account-card">
      <h2><?= $editing ? 'ویرایش آدرس' : 'افزودن آدرس جدید' ?></h2>

      <form method="post"
            action="<?= $editing ? BASE_URL . '/account/addresses/' . (int)$editing['id'] : BASE_URL . '/account/addresses' ?>"
            novalidate>
        <?= $csrf ?>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div class="form-group">
            <label class="form-label" for="title">عنوان آدرس</label>
            <input type="text" id="title" name="title" class="form-input"
                   value="<?= htmlspecialchars($editing['title'] ?? '', ENT_QUOTES) ?>"
                   placeholder="مثلاً: خانه، محل کار">
          </div>
          <div class="form-group">
            <label class="form-label" for="name">نام گیرنده <span style="color:var(--orange)">*</span></label>
            <input type="text" id="name" name="name" class="form-input" required
                   value="<?= htmlspecialchars($editing['name'] ?? '', ENT_QUOTES) ?>"
                   placeholder="نام و نام خانوادگی">
          </div>
          <div class="form-group">
            <label class="form-label" for="mobile">موبایل گیرنده <span style="color:var(--orange)">*</span></label>
            <input type="tel" id="mobile" name="mobile" class="form-input" required dir="ltr"
                   value="<?= htmlspecialchars($editing['mobile'] ?? '', ENT_QUOTES) ?>"
                   placeholder="09xxxxxxxxx">
          </div>
          <div class="form-group">
            <label class="form-label" for="postal_code">کد پستی</label>
            <input type="text" id="postal_code" name="postal_code" class="form-input" dir="ltr"
                   value="<?= htmlspecialchars($editing['postal_code'] ?? '', ENT_QUOTES) ?>"
                   placeholder="10 رقم">
          </div>
          <div class="form-group">
            <label class="form-label" for="province">استان</label>
            <input type="text" id="province" name="province" class="form-input"
                   value="<?= htmlspecialchars($editing['province'] ?? '', ENT_QUOTES) ?>"
                   placeholder="استان">
          </div>
          <div class="form-group">
            <label class="form-label" for="city">شهر</label>
            <input type="text" id="city" name="city" class="form-input"
                   value="<?= htmlspecialchars($editing['city'] ?? '', ENT_QUOTES) ?>"
                   placeholder="شهر">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="address">آدرس کامل <span style="color:var(--orange)">*</span></label>
          <textarea id="address" name="address" class="form-textarea" required
                    placeholder="خیابان، کوچه، پلاک، واحد..."><?= htmlspecialchars($editing['address'] ?? '', ENT_QUOTES) ?></textarea>
        </div>

        <?php if (!$editing): ?>
        <div class="form-group" style="display:flex;align-items:center;gap:10px">
          <input type="checkbox" id="is_default" name="is_default" value="1" style="width:auto">
          <label for="is_default" style="margin:0;cursor:pointer">به عنوان آدرس پیش‌فرض تنظیم شود</label>
        </div>
        <?php endif; ?>

        <div style="display:flex;gap:12px;margin-top:8px">
          <button type="submit" class="btn btn-primary"><?= $editing ? 'ذخیره ویرایش' : 'افزودن آدرس' ?></button>
          <?php if ($editing): ?>
          <a href="<?= BASE_URL ?>/account/addresses" class="btn btn-ghost">انصراف</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

  </div><!-- /.account-main -->
</div><!-- /.account-layout -->
