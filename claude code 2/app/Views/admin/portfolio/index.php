<div class="admin-page-header">
  <h1 class="admin-page-title">نمونه کارها</h1>
  <a href="<?= BASE_URL ?>/admin/portfolio/create" class="btn btn-primary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    افزودن نمونه کار
  </a>
</div>

<div class="admin-card">
  <?php if (empty($items)): ?>
  <div class="empty-state py-48">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="48" height="48"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
    <p>هیچ نمونه کاری وجود ندارد.</p>
    <a href="<?= BASE_URL ?>/admin/portfolio/create" class="btn btn-primary mt-16">افزودن اولین نمونه کار</a>
  </div>
  <?php else: ?>
  <div class="portfolio-admin-grid">
    <?php foreach ($items as $item): ?>
    <div class="portfolio-admin-card">
      <div class="portfolio-admin-card__thumb">
        <?php if ($item['cover']): ?>
        <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($item['cover'], ENT_QUOTES) ?>"
             alt="<?= htmlspecialchars($item['title'], ENT_QUOTES) ?>" loading="lazy">
        <?php else: ?>
        <div class="portfolio-admin-card__placeholder">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="32" height="32"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
        <?php endif; ?>
        <div class="portfolio-admin-card__badges">
          <span class="badge <?= $item['status'] ? 'badge-success' : 'badge-danger' ?>">
            <?= $item['status'] ? 'فعال' : 'غیرفعال' ?>
          </span>
          <?php if ($item['is_featured']): ?>
          <span class="badge badge-warning">ویژه</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="portfolio-admin-card__body">
        <h3 class="portfolio-admin-card__title"><?= htmlspecialchars($item['title'], ENT_QUOTES) ?></h3>
        <?php if ($item['client']): ?>
        <p class="portfolio-admin-card__client"><?= htmlspecialchars($item['client'], ENT_QUOTES) ?></p>
        <?php endif; ?>
        <p class="portfolio-admin-card__meta"><?= $item['image_count'] ?> تصویر</p>
        <div class="portfolio-admin-card__actions">
          <a href="<?= BASE_URL ?>/admin/portfolio/<?= $item['id'] ?>/edit" class="btn btn-ghost btn-sm">ویرایش</a>
          <form method="post" action="<?= BASE_URL ?>/admin/portfolio/<?= $item['id'] ?>/delete"
                onsubmit="return confirm('آیا مطمئن هستید؟')">
            <?= CSRF::field() ?>
            <button type="submit" class="btn btn-ghost btn-sm btn-danger">حذف</button>
          </form>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
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
