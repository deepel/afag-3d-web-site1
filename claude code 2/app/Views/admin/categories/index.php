<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title">دسته‌بندی‌ها</h1>
  </div>
  <a href="<?= BASE_URL ?>/admin/categories/create" class="btn btn-primary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    افزودن دسته‌بندی
  </a>
</div>

<div class="admin-card">
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>نام</th>
          <th>فروشگاه</th>
          <th>والد</th>
          <th>ترتیب</th>
          <th style="width:100px">عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($categories): ?>
        <?php
        $shopMap = [];
        foreach ($shops as $sh) $shopMap[$sh['id']] = $sh['name'];
        $catMap  = [];
        foreach ($categories as $c) $catMap[$c['id']] = $c['name'];
        ?>
        <?php foreach ($categories as $cat): ?>
        <tr>
          <td class="text-muted"><?= $cat['id'] ?></td>
          <td><strong><?= htmlspecialchars($cat['name'], ENT_QUOTES) ?></strong><br>
            <small class="text-muted" dir="ltr"><?= htmlspecialchars($cat['slug'] ?? '', ENT_QUOTES) ?></small>
          </td>
          <td><?= htmlspecialchars($shopMap[$cat['shop_id'] ?? ''] ?? '—', ENT_QUOTES) ?></td>
          <td><?= htmlspecialchars($catMap[$cat['parent_id'] ?? 0] ?? '—', ENT_QUOTES) ?></td>
          <td><?= $cat['sort_order'] ?? 0 ?></td>
          <td>
            <div class="table-actions">
              <a href="<?= BASE_URL ?>/admin/categories/<?= $cat['id'] ?>/edit" class="btn-icon" title="ویرایش">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <form method="POST" action="<?= BASE_URL ?>/admin/categories/<?= $cat['id'] ?>/delete"
                    onsubmit="return confirm('حذف شود؟')" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= CSRF::token() ?>">
                <button type="submit" class="btn-icon btn-icon--danger" title="حذف">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted" style="padding:32px">دسته‌بندی‌ای یافت نشد.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
