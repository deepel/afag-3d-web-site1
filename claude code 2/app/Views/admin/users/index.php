<?php
/**
 * Admin — User Management
 */
$roleMap   = ['admin' => 'مدیر', 'shop_owner' => 'فروشنده', 'customer' => 'مشتری'];
$statusMap = ['active' => 'فعال', 'inactive' => 'غیرفعال', 'banned' => 'مسدود'];
?>
<div class="admin-page-header">
  <h1 class="admin-page-title">مدیریت کاربران</h1>
  <div class="admin-page-actions">
    <span class="text-muted">مجموع: <?= Url::toPersianDigits((string)$total) ?> کاربر</span>
  </div>
</div>

<!-- Filters -->
<div class="admin-card" style="margin-bottom:20px">
  <form method="get" action="<?= BASE_URL ?>/admin/users" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:200px">
      <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:6px">جستجو</label>
      <input type="text" name="search" class="form-control"
             value="<?= htmlspecialchars($search, ENT_QUOTES) ?>"
             placeholder="نام، موبایل یا ایمیل...">
    </div>
    <div style="min-width:160px">
      <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:6px">نقش</label>
      <select name="role" class="form-control">
        <option value="">همه نقش‌ها</option>
        <option value="admin"    <?= $role === 'admin'      ? 'selected' : '' ?>>مدیر</option>
        <option value="customer" <?= $role === 'customer'   ? 'selected' : '' ?>>مشتری</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">اعمال فیلتر</button>
    <?php if ($search || $role): ?>
    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-ghost">پاک کردن</a>
    <?php endif; ?>
  </form>
</div>

<div class="admin-card">
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>نام</th>
          <th>موبایل</th>
          <th>ایمیل</th>
          <th>نقش</th>
          <th>وضعیت</th>
          <th>تاریخ ثبت</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($users)): ?>
          <tr><td colspan="8" class="text-center text-muted">کاربری یافت نشد.</td></tr>
        <?php else: ?>
          <?php foreach ($users as $user): ?>
          <tr>
            <td><span class="admin-id">#<?= Url::toPersianDigits((string)$user['id']) ?></span></td>
            <td><?= htmlspecialchars($user['name'], ENT_QUOTES) ?></td>
            <td dir="ltr"><?= htmlspecialchars($user['mobile'], ENT_QUOTES) ?></td>
            <td dir="ltr" style="font-size:13px"><?= htmlspecialchars($user['email'] ?? '—', ENT_QUOTES) ?></td>
            <td>
              <span class="role-badge role-<?= htmlspecialchars($user['role'], ENT_QUOTES) ?>">
                <?= $roleMap[$user['role']] ?? $user['role'] ?>
              </span>
            </td>
            <td>
              <span class="status-badge status-<?= htmlspecialchars($user['status'], ENT_QUOTES) ?>">
                <?= $statusMap[$user['status']] ?? $user['status'] ?>
              </span>
            </td>
            <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($user['created_at']))) ?></time></td>
            <td>
              <div class="table-actions">
                <a href="<?= BASE_URL ?>/admin/users/<?= (int)$user['id'] ?>" class="btn-icon" title="مشاهده جزئیات">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </a>

                <?php if ($user['id'] !== Auth::id()): ?>
                <!-- Ban/Unban -->
                <form method="post" action="<?= BASE_URL ?>/admin/users/<?= (int)$user['id'] ?>/ban" style="display:inline"
                      onsubmit="return confirm('<?= $user['status'] === 'banned' ? 'این کاربر فعال شود؟' : 'این کاربر مسدود شود؟' ?>')">
                  <?= $csrf ?>
                  <button type="submit" class="btn-icon <?= $user['status'] === 'banned' ? 'text-success' : 'text-danger' ?>"
                          title="<?= $user['status'] === 'banned' ? 'فعال‌سازی' : 'مسدود کردن' ?>">
                    <?php if ($user['status'] === 'banned'): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    <?php endif; ?>
                  </button>
                </form>

                <?php if ($user['role'] !== 'admin'): ?>
                <!-- Make Admin -->
                <form method="post" action="<?= BASE_URL ?>/admin/users/<?= (int)$user['id'] ?>/admin" style="display:inline"
                      onsubmit="return confirm('این کاربر به مدیر ارتقا یابد؟')">
                  <?= $csrf ?>
                  <button type="submit" class="btn-icon" title="ارتقا به مدیر">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                  </button>
                </form>
                <?php endif; ?>
                <?php endif; ?>

              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
  <div style="padding:16px 20px;border-top:1px solid var(--border-color);display:flex;gap:8px;flex-wrap:wrap">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>"
         class="btn <?= $i === $page ? 'btn-primary' : 'btn-ghost' ?>"
         style="padding:4px 12px;font-size:13px"><?= Url::toPersianDigits((string)$i) ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
