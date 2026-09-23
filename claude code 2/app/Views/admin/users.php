<div class="admin-page-header">
  <h1 class="admin-page-title">مدیریت کاربران</h1>
  <div class="admin-page-actions">
    <span class="text-muted">مجموع: <?= Url::toPersianDigits((string)$total) ?> کاربر</span>
  </div>
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
          <?php
          $roleMap   = ['admin'=>'مدیر','shop_owner'=>'فروشنده','customer'=>'مشتری'];
          $statusMap = ['active'=>'فعال','inactive'=>'غیرفعال','banned'=>'مسدود'];
          foreach ($users as $user):
          ?>
          <tr>
            <td><span class="admin-id">#<?= Url::toPersianDigits((string)$user['id']) ?></span></td>
            <td><?= htmlspecialchars($user['name'], ENT_QUOTES) ?></td>
            <td dir="ltr"><?= htmlspecialchars($user['mobile'], ENT_QUOTES) ?></td>
            <td dir="ltr"><?= htmlspecialchars($user['email'] ?? '—', ENT_QUOTES) ?></td>
            <td><span class="role-badge role-<?= htmlspecialchars($user['role'], ENT_QUOTES) ?>"><?= $roleMap[$user['role']] ?? $user['role'] ?></span></td>
            <td><span class="status-badge status-<?= htmlspecialchars($user['status'], ENT_QUOTES) ?>"><?= $statusMap[$user['status']] ?? $user['status'] ?></span></td>
            <td><time><?= Url::toPersianDigits(date('Y/m/d', strtotime($user['created_at']))) ?></time></td>
            <td>
              <div class="table-actions">
                <a href="<?= BASE_URL ?>/admin/users/<?= (int)$user['id'] ?>/edit" class="btn-icon" title="ویرایش">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
