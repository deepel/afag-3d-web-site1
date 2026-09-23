<div class="admin-page-header">
  <div>
    <h1 class="admin-page-title">سفارشات</h1>
    <p class="admin-page-sub">مجموعاً <?= number_format($total) ?> سفارش</p>
  </div>
</div>

<!-- Status filter -->
<div class="admin-filter-bar" style="margin-bottom:20px">
  <?php
  $statuses = ['' => 'همه', 'pending' => 'در انتظار', 'processing' => 'در حال پردازش',
                'shipped' => 'ارسال شده', 'completed' => 'تکمیل شده',
                'cancelled' => 'لغو شده', 'paid' => 'پرداخت شده', 'refunded' => 'بازگشت وجه'];
  foreach ($statuses as $val => $label):
  ?>
  <a href="?status=<?= $val ?>" class="badge <?= $status === $val ? 'badge-primary' : 'badge-outline' ?>"><?= $label ?></a>
  <?php endforeach; ?>
</div>

<div class="admin-card">
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>#</th>
          <th>مشتری</th>
          <th>موبایل</th>
          <th>مبلغ</th>
          <th>وضعیت</th>
          <th>تاریخ</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($orders): ?>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><a href="<?= BASE_URL ?>/admin/orders/<?= $o['id'] ?>" class="table-link">#<?= $o['id'] ?></a></td>
          <td><?= htmlspecialchars($o['name'], ENT_QUOTES) ?></td>
          <td dir="ltr"><?= htmlspecialchars($o['mobile'] ?? '—', ENT_QUOTES) ?></td>
          <td><?= Url::price((int)$o['total']) ?></td>
          <td>
            <?php
            $sMap = ['pending'=>['label'=>'در انتظار','cls'=>'warning'],
                     'processing'=>['label'=>'پردازش','cls'=>'info'],
                     'paid'=>['label'=>'پرداخت شده','cls'=>'success'],
                     'shipped'=>['label'=>'ارسال شده','cls'=>'info'],
                     'completed'=>['label'=>'تکمیل','cls'=>'success'],
                     'cancelled'=>['label'=>'لغو','cls'=>'danger'],
                     'failed'=>['label'=>'ناموفق','cls'=>'danger'],
                     'refunded'=>['label'=>'بازگشت وجه','cls'=>'warning']];
            $s = $sMap[$o['status']] ?? ['label'=>$o['status'],'cls'=>'default'];
            ?>
            <span class="badge badge-<?= $s['cls'] ?>"><?= $s['label'] ?></span>
          </td>
          <td><?= substr($o['created_at'] ?? '', 0, 10) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/admin/orders/<?= $o['id'] ?>" class="btn btn-ghost btn-sm">جزئیات</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr><td colspan="7" class="text-center text-muted" style="padding:32px">سفارشی یافت نشد.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pages > 1): ?>
  <div class="admin-pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <a href="?page=<?= $i ?>&status=<?= urlencode($status) ?>"
         class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
