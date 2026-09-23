<?php
$typeLabels = [
    'color'      => 'رنگ‌ها',
    'print_type' => 'نوع چاپ',
    'material'   => 'جنس مواد',
    'quality'    => 'کیفیت چاپ',
];
$colorMap = [
    'White'   => '#ffffff',
    'Black'   => '#1a1a1a',
    'Gray'    => '#888888',
    'Orange'  => '#f97316',
    'Red'     => '#ef4444',
    'Blue'    => '#3b82f6',
    'Green'   => '#22c55e',
    'Yellow'  => '#eab308',
    'Purple'  => '#a855f7',
    'Pink'    => '#ec4899',
];
?>

<div class="admin-page-header">
  <h1 class="admin-page-title">گزینه‌های چاپ</h1>
</div>

<form method="post" action="<?= BASE_URL ?>/admin/print-options" id="optionsForm">
  <?= $csrf ?>

  <?php foreach ($typeLabels as $type => $typeLabel): ?>
  <?php $opts = $options[$type] ?? []; ?>
  <div class="admin-card mb-24">
    <div class="admin-card-header">
      <h2 class="admin-card-title"><?= htmlspecialchars($typeLabel, ENT_QUOTES) ?></h2>
      <button type="button" class="btn btn-ghost btn-sm"
              data-add-option-type="<?= $type ?>"
              onclick="openAddModal('<?= $type ?>', '<?= htmlspecialchars($typeLabel, ENT_QUOTES) ?>')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        افزودن گزینه
      </button>
    </div>

    <?php if (empty($opts)): ?>
    <p class="text-muted px-20 py-16">هیچ گزینه‌ای تعریف نشده است.</p>
    <?php else: ?>
    <div class="options-list">
      <?php foreach ($opts as $opt): ?>
      <div class="option-row">
        <?php if ($type === 'color'): ?>
        <span class="color-swatch-sm" style="background: <?= $colorMap[$opt['name']] ?? '#ccc' ?>;" title="<?= htmlspecialchars($opt['name'], ENT_QUOTES) ?>"></span>
        <?php else: ?>
        <span class="option-row__icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        </span>
        <?php endif; ?>

        <div class="option-row__info">
          <span class="option-row__name"><?= htmlspecialchars($opt['name_fa'], ENT_QUOTES) ?></span>
          <span class="option-row__name-en"><?= htmlspecialchars($opt['name'], ENT_QUOTES) ?></span>
          <?php if ($opt['price_add'] != 0): ?>
          <span class="option-row__price"><?= $opt['price_add'] > 0 ? '+' : '' ?><?= number_format($opt['price_add']) ?> تومان</span>
          <?php endif; ?>
        </div>

        <div class="option-row__controls">
          <label class="toggle-label" title="وضعیت">
            <input type="checkbox" name="status[<?= $opt['id'] ?>]" value="1"
                   <?= $opt['status'] ? 'checked' : '' ?> class="toggle-check">
            <span class="toggle-switch"></span>
            <span class="sr-only">فعال</span>
          </label>
        </div>

        <div class="option-row__actions">
          <form method="post" action="<?= BASE_URL ?>/admin/print-options/<?= $opt['id'] ?>/delete"
                onsubmit="return confirm('آیا مطمئن هستید؟')" class="d-inline">
            <?= $csrf ?>
            <button type="submit" class="btn-icon btn-icon--danger" title="حذف">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>

  <div class="form-submit-row">
    <button type="submit" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      ذخیره وضعیت‌ها
    </button>
  </div>
</form>

<!-- Add Option Modal -->
<div id="addOptionModal" class="modal-overlay" hidden>
  <div class="modal-box">
    <div class="modal-header">
      <h3 class="modal-title" id="addModalTitle">افزودن گزینه</h3>
      <button type="button" class="modal-close" onclick="closeAddModal()">✕</button>
    </div>
    <form method="post" action="<?= BASE_URL ?>/admin/print-options/new">
      <?= $csrf ?>
      <input type="hidden" name="type" id="addOptionType" value="">
      <div class="form-group">
        <label class="form-label">نام فارسی <span class="required-star">*</span></label>
        <input type="text" name="name_fa" class="form-input" placeholder="مثال: آبی" required>
      </div>
      <div class="form-group">
        <label class="form-label">نام انگلیسی</label>
        <input type="text" name="name" class="form-input" dir="ltr" placeholder="مثال: Blue">
      </div>
      <div class="form-group">
        <label class="form-label">اضافه قیمت (تومان)</label>
        <input type="number" name="price_add" class="form-input" value="0" dir="ltr">
      </div>
      <div class="form-group">
        <label class="form-label">ترتیب نمایش</label>
        <input type="number" name="sort_order" class="form-input" value="0" dir="ltr">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeAddModal()">انصراف</button>
        <button type="submit" class="btn btn-primary">افزودن</button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddModal(type, label) {
  document.getElementById('addOptionType').value = type;
  document.getElementById('addModalTitle').textContent = 'افزودن گزینه — ' + label;
  document.getElementById('addOptionModal').hidden = false;
}
function closeAddModal() {
  document.getElementById('addOptionModal').hidden = true;
}
document.getElementById('addOptionModal').addEventListener('click', function(e) {
  if (e.target === this) closeAddModal();
});
</script>
