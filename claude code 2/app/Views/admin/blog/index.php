<div class="admin-page-header">
  <h1 class="admin-page-title">مقالات بلاگ</h1>
  <div class="header-actions">
    <a href="<?= BASE_URL ?>/admin/blog/categories" class="btn btn-ghost">دسته‌بندی‌ها</a>
    <a href="<?= BASE_URL ?>/admin/blog/create" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      مقاله جدید
    </a>
  </div>
</div>

<div class="admin-card">
  <?php if (empty($posts)): ?>
  <div class="empty-state py-48">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="48" height="48"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
    <p>هیچ مقاله‌ای وجود ندارد.</p>
    <a href="<?= BASE_URL ?>/admin/blog/create" class="btn btn-primary mt-16">نوشتن اولین مقاله</a>
  </div>
  <?php else: ?>
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>تصویر</th>
          <th>عنوان</th>
          <th>دسته‌بندی</th>
          <th>وضعیت</th>
          <th>بازدید</th>
          <th>تاریخ</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($posts as $post): ?>
        <tr>
          <td><?= $post['id'] ?></td>
          <td>
            <?php if ($post['cover']): ?>
            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($post['cover'], ENT_QUOTES) ?>"
                 alt="" width="48" height="36" style="object-fit:cover;border-radius:4px;">
            <?php else: ?>
            <div style="width:48px;height:36px;background:var(--surface-2);border-radius:4px;"></div>
            <?php endif; ?>
          </td>
          <td>
            <a href="<?= BASE_URL ?>/blog/<?= htmlspecialchars($post['slug'], ENT_QUOTES) ?>"
               target="_blank" class="table-link">
              <?= htmlspecialchars(mb_substr($post['title'], 0, 50), ENT_QUOTES) ?>
              <?= mb_strlen($post['title']) > 50 ? '…' : '' ?>
            </a>
          </td>
          <td><?= htmlspecialchars($post['category_name'] ?? '—', ENT_QUOTES) ?></td>
          <td>
            <span class="badge <?= $post['status'] === 'published' ? 'badge-success' : 'badge-outline' ?>">
              <?= $post['status'] === 'published' ? 'منتشرشده' : 'پیش‌نویس' ?>
            </span>
          </td>
          <td><?= number_format($post['views']) ?></td>
          <td><?= date('Y/m/d', strtotime($post['created_at'])) ?></td>
          <td>
            <div class="table-actions">
              <a href="<?= BASE_URL ?>/admin/blog/<?= $post['id'] ?>/edit" class="btn btn-ghost btn-sm">ویرایش</a>
              <form method="post" action="<?= BASE_URL ?>/admin/blog/<?= $post['id'] ?>/delete"
                    onsubmit="return confirm('آیا مطمئن هستید؟')">
                <?= CSRF::field() ?>
                <button type="submit" class="btn btn-ghost btn-sm btn-danger">حذف</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
  <div class="admin-pagination">
    <?php for ($p = 1; $p <= $pages; $p++): ?>
    <a href="?page=<?= $p ?>" class="page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>
