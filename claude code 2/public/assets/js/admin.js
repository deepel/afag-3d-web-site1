/**
 * afag3d — Admin Panel JavaScript
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {

  /* ── Sidebar toggle (mobile) ─────────────────────────────── */
  const sidebarToggle  = document.getElementById('sidebarToggle');
  const sidebarClose   = document.getElementById('sidebarClose');
  const adminSidebar   = document.getElementById('adminSidebar');
  const sidebarOverlay = document.getElementById('sidebarOverlay');

  function openSidebar() {
    adminSidebar.classList.add('open');
    sidebarOverlay.classList.add('visible');
    document.body.style.overflow = 'hidden';
  }
  function closeSidebar() {
    adminSidebar.classList.remove('open');
    sidebarOverlay.classList.remove('visible');
    document.body.style.overflow = '';
  }

  if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
  if (sidebarClose)  sidebarClose.addEventListener('click', closeSidebar);
  if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

  /* ── Confirm dangerous actions ───────────────────────────── */
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', (e) => {
      const msg = el.getAttribute('data-confirm') || 'آیا مطمئن هستید؟';
      if (!confirm(msg)) e.preventDefault();
    });
  });

  /* ── Admin table: row click navigation ───────────────────── */
  document.querySelectorAll('[data-href]').forEach(row => {
    row.style.cursor = 'pointer';
    row.addEventListener('click', (e) => {
      if (e.target.closest('a, button, input')) return;
      window.location.href = row.getAttribute('data-href');
    });
  });

  /* ── Image preview on file input ─────────────────────────── */
  document.querySelectorAll('.file-preview-input').forEach(input => {
    input.addEventListener('change', function () {
      const previewId = this.getAttribute('data-preview');
      const preview   = document.getElementById(previewId);
      if (!preview) return;
      const file = this.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => { preview.src = e.target.result; };
      reader.readAsDataURL(file);
    });
  });

  /* ── Inline search filter on admin tables ────────────────── */
  const searchInput = document.getElementById('tableSearch');
  const tableBody   = document.querySelector('.admin-table tbody');
  if (searchInput && tableBody) {
    searchInput.addEventListener('input', function () {
      const q = this.value.toLowerCase();
      tableBody.querySelectorAll('tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }

  /* ── Toast helper (for future AJAX responses) ────────────── */
  window.adminToast = function(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.cssText = 'position:fixed;top:24px;left:24px;z-index:9999;min-width:280px;animation:fadeInUp .3s ease';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity .3s';
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  };

  /* ── Settings form: unsaved changes warning ──────────────── */
  const settingsForm = document.querySelector('.admin-settings-form');
  if (settingsForm) {
    let changed = false;
    settingsForm.addEventListener('change', () => { changed = true; });
    settingsForm.addEventListener('submit', () => { changed = false; });
    window.addEventListener('beforeunload', (e) => {
      if (changed) { e.preventDefault(); e.returnValue = ''; }
    });
  }

  /* ── Bulk select ─────────────────────────────────────────── */
  const selectAll = document.getElementById('selectAll');
  if (selectAll) {
    selectAll.addEventListener('change', function () {
      document.querySelectorAll('.row-select').forEach(cb => {
        cb.checked = this.checked;
      });
    });
  }
});
