/**
 * afag3d — Main Frontend JavaScript
 * No dependencies — vanilla ES2020+
 */
'use strict';

/* ─── Digit formatter (site uses Latin/English numerals) ──── */
function fa(n) {
  return String(n);
}

/* ─── Theme ──────────────────────────────────────────────── */
(function initTheme() {
  const stored = localStorage.getItem('afag3d_theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const theme = stored || (prefersDark ? 'dark' : 'dark'); // default dark
  document.documentElement.setAttribute('data-theme', theme);
})();

/* ─── Loader ─────────────────────────────────────────────── */
function hideLoader() {
  const loader = document.getElementById('loader');
  if (!loader) return;
  loader.classList.add('hidden');
  setTimeout(() => loader.remove(), 420);
}

/* ─── DOM Ready ──────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {

  /* ── Loader ──────────────────────────────────────────────── */
  window.addEventListener('load', hideLoader, { once: true });
  setTimeout(hideLoader, 900);

  /* ── Theme toggle ────────────────────────────────────────── */
  document.querySelectorAll('#themeToggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const html  = document.documentElement;
      const theme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      html.classList.add('is-theme-switching');
      html.setAttribute('data-theme', theme);
      localStorage.setItem('afag3d_theme', theme);
      const themeColor = document.querySelector('meta[name="theme-color"]');
      if (themeColor) themeColor.setAttribute('content', theme === 'dark' ? '#181716' : '#eee8df');
      setTimeout(() => html.classList.remove('is-theme-switching'), 450);
    });
  });

  /* ── Nav: solid on scroll ────────────────────────────────── */
  const nav = document.getElementById('nav');
  if (nav) {
    const onScroll = () => {
      nav.classList.toggle('solid', window.scrollY > 40);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ── Hamburger / mobile nav ──────────────────────────────── */
  const hamburger = document.getElementById('hamburger');
  const navLinks  = document.querySelector('.nav-links');
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      const open = navLinks.classList.toggle('open');
      hamburger.classList.toggle('open', open);
      hamburger.setAttribute('aria-expanded', open ? 'true' : 'false');
      document.body.style.overflow = open ? 'hidden' : '';
    });
    // Close on outside click
    document.addEventListener('click', e => {
      if (!nav.contains(e.target)) {
        navLinks.classList.remove('open');
        hamburger.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      }
    });
  }

  /* ── User dropdown ───────────────────────────────────────── */
  const userMenuBtn  = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');
  if (userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const hidden = userDropdown.hidden;
      userDropdown.hidden = !hidden;
    });
    document.addEventListener('click', () => {
      if (userDropdown) userDropdown.hidden = true;
    });
  }

  /* ── Reveal on scroll (IntersectionObserver) ─────────────── */
  const autoRevealSelectors = [
    '.page-hero-content > *',
    '.portfolio-page > .container > .section-header',
    '.blog-page > .container > .section-header',
    '.print-order-page > .container > .section-header',
    '.services-grid > *',
    '.products-grid > *',
    '.product-grid > *',
    '.portfolio-grid > *',
    '.blog-grid > *',
    '.values-grid > *',
    '.team-grid > *',
    '.contact-info-cards > *',
    '.process-steps > .process-step',
    '.form-section',
    '.account-card',
    '.auth-card',
    '.cta-card'
  ];
  document.querySelectorAll(autoRevealSelectors.join(',')).forEach(el => el.classList.add('reveal'));

  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length) {
    if (!('IntersectionObserver' in window)) {
      reveals.forEach(el => el.classList.add('visible'));
    } else {
    const revealObs = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    reveals.forEach((el, i) => {
      const parent = el.parentElement;
      const siblings = parent ? Array.from(parent.children).filter(child => child.classList.contains('reveal')) : [];
      const siblingIndex = Math.max(0, siblings.indexOf(el));
      el.style.setProperty('--reveal-delay', `${Math.min(siblingIndex, 4) * 70}ms`);
      revealObs.observe(el);
    });
    }
  }

  /* ── Hero blueprint parallax (GSAP + ScrollTrigger) ──────── */
  const parallaxHero = document.querySelector('[data-hero-parallax]');
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (parallaxHero && !reduceMotion && window.matchMedia('(pointer: fine)').matches) {
    parallaxHero.addEventListener('pointermove', (event) => {
      const rect = parallaxHero.getBoundingClientRect();
      const x = ((event.clientX - rect.left) / rect.width) * 100;
      const y = ((event.clientY - rect.top) / rect.height) * 100;
      parallaxHero.style.setProperty('--hero-x', `${x.toFixed(2)}%`);
      parallaxHero.style.setProperty('--hero-y', `${y.toFixed(2)}%`);
    }, { passive: true });
  }

  if (parallaxHero && !reduceMotion && window.gsap && window.ScrollTrigger) {
    const printerLayer = parallaxHero.querySelector('[data-parallax-printer]');
    const geometryLayer = parallaxHero.querySelector('[data-parallax-geometry]');

    if (printerLayer && geometryLayer) {
      window.gsap.registerPlugin(window.ScrollTrigger);
      parallaxHero.classList.add('is-parallax-ready');

      window.gsap.to(printerLayer, {
        xPercent: -1.6,
        yPercent: 5,
        ease: 'none',
        force3D: true,
        scrollTrigger: {
          trigger: parallaxHero,
          start: 'top top',
          end: 'bottom top',
          scrub: 1.2,
          invalidateOnRefresh: true,
          onEnter: () => parallaxHero.classList.add('is-parallax-ready'),
          onEnterBack: () => parallaxHero.classList.add('is-parallax-ready'),
          onLeave: () => parallaxHero.classList.remove('is-parallax-ready'),
          onLeaveBack: () => parallaxHero.classList.remove('is-parallax-ready')
        }
      });

      window.gsap.to(geometryLayer, {
        xPercent: 1.2,
        yPercent: -8,
        ease: 'none',
        force3D: true,
        scrollTrigger: {
          trigger: parallaxHero,
          start: 'top top',
          end: 'bottom top',
          scrub: 1.6,
          invalidateOnRefresh: true
        }
      });
    }
  }

  /* ── Counter animation ───────────────────────────────────── */
  const counters = document.querySelectorAll('.stat-num[data-target], .stat-number[data-count]');
  if (counters.length) {
    if (!('IntersectionObserver' in window)) {
      counters.forEach(el => {
        el.textContent = fa(parseInt(el.getAttribute('data-target') || el.getAttribute('data-count'), 10) || 0);
      });
    } else {
    const counterObs = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el     = entry.target;
        const target = parseInt(el.getAttribute('data-target') || el.getAttribute('data-count'), 10) || 0;
        const dur    = 1400;
        const start  = performance.now();
        const tick   = (now) => {
          const elapsed  = now - start;
          const progress = Math.min(elapsed / dur, 1);
          const ease     = 1 - Math.pow(1 - progress, 3); // ease-out cubic
          el.textContent = fa(Math.round(target * ease));
          if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
        counterObs.unobserve(el);
      });
    }, { threshold: 0.3 });

    counters.forEach(el => counterObs.observe(el));
    }
  }

  /* ── Password visibility toggle ──────────────────────────── */
  document.querySelectorAll('.form-input-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      const input    = document.getElementById(targetId);
      if (!input) return;
      const isPass = input.type === 'password';
      input.type   = isPass ? 'text' : 'password';
      btn.querySelector('.eye-open').style.display  = isPass ? 'none'  : '';
      btn.querySelector('.eye-closed').style.display = isPass ? '' : 'none';
    });
  });

  /* ── Password strength indicator ─────────────────────────── */
  const passInput   = document.getElementById('password');
  const strengthEl  = document.getElementById('passwordStrength');
  if (passInput && strengthEl) {
    passInput.addEventListener('input', () => {
      const v = passInput.value;
      let score = 0;
      if (v.length >= 8)  score++;
      if (/[A-Z]/.test(v)) score++;
      if (/[0-9]/.test(v)) score++;
      if (/[^A-Za-z0-9]/.test(v)) score++;

      strengthEl.className = 'password-strength';
      if (!v) { strengthEl.textContent = ''; return; }
      if (score <= 1) { strengthEl.classList.add('weak');   strengthEl.textContent = 'رمز عبور ضعیف'; }
      else if (score === 2) { strengthEl.classList.add('medium'); strengthEl.textContent = 'رمز عبور متوسط'; }
      else { strengthEl.classList.add('strong'); strengthEl.textContent = 'رمز عبور قوی'; }
    });
  }

  /* ── Back to top ─────────────────────────────────────────── */
  const backToTop = document.getElementById('backToTop');
  if (backToTop) {
    backToTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ── Auto-dismiss flash messages ─────────────────────────── */
  const flashContainer = document.querySelector('.flash-container');
  if (flashContainer) {
    setTimeout(() => {
      flashContainer.style.transition = 'opacity .4s ease';
      flashContainer.style.opacity   = '0';
      setTimeout(() => flashContainer.remove(), 400);
    }, 5000);
  }

  /* ── Mobile number normalizer ────────────────────────────── */
  const mobileInputs = document.querySelectorAll('input[name="mobile"]');
  mobileInputs.forEach(input => {
    input.addEventListener('input', () => {
      // Normalize Persian/Arabic digits to ASCII
      input.value = input.value
        .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
        .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
        .replace(/[^\d]/g, '');
    });
  });

  /* ── Mark SVG animation ──────────────────────────────────── */
  const heroMark = document.querySelector('.hero-mark, .auth-mark');
  if (heroMark) {
    let ticking = false;
    document.addEventListener('mousemove', (e) => {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(() => {
        const cx    = window.innerWidth  / 2;
        const cy    = window.innerHeight / 2;
        const dx    = (e.clientX - cx) / cx;
        const dy    = (e.clientY - cy) / cy;
        const rotX  = dy * -8;
        const rotY  = dx *  8;
        heroMark.style.transform = `rotateX(${rotX}deg) rotateY(${rotY}deg)`;
        ticking = false;
      });
    });
  }

  /* ── Atelier card spotlight ─────────────────────────────── */
  if (!reduceMotion && window.matchMedia('(pointer: fine)').matches) {
    const spotlightTargets = document.querySelectorAll([
      '.service-card',
      '.product-card',
      '.portfolio-card',
      '.blog-card',
      '.process-step',
      '.contact-info-card',
      '.value-card',
      '.team-card',
      '.form-section',
      '.account-card'
    ].join(','));

    spotlightTargets.forEach(card => {
      card.addEventListener('pointermove', event => {
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--spot-x', `${event.clientX - rect.left}px`);
        card.style.setProperty('--spot-y', `${event.clientY - rect.top}px`);
      }, { passive: true });
    });
  }

  /* ── Smooth same-origin page exit ───────────────────────── */
  document.querySelectorAll('a[href]').forEach(link => {
    link.addEventListener('click', event => {
      if (
        event.defaultPrevented ||
        event.button !== 0 ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey ||
        link.target === '_blank' ||
        link.hasAttribute('download') ||
        link.hasAttribute('data-no-transition')
      ) return;

      const targetUrl = new URL(link.href, window.location.href);
      if (
        targetUrl.origin !== window.location.origin ||
        targetUrl.href === window.location.href ||
        (targetUrl.pathname === window.location.pathname && targetUrl.hash)
      ) return;

      event.preventDefault();
      document.body.classList.add('is-leaving');
      setTimeout(() => {
        window.location.href = targetUrl.href;
      }, reduceMotion ? 0 : 180);
    });
  });

  window.addEventListener('pageshow', () => {
    document.body.classList.remove('is-leaving');
  });
});

/* ─── Global AJAX helper ─────────────────────────────────── */
window.afag = window.afag || {};
window.afag.get = async function(url) {
  const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
};
window.afag.post = async function(url, data, csrfToken) {
  const body = data instanceof FormData ? data : JSON.stringify(data);
  const headers = { 'X-Requested-With': 'XMLHttpRequest' };
  if (csrfToken) headers['X-CSRF-Token'] = csrfToken;
  if (!(data instanceof FormData)) headers['Content-Type'] = 'application/json';
  const res = await fetch(url, { method: 'POST', headers, body });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
};

/* ============================================================
   Phase 2 — Cart, Gallery, Tabs, Filter JS
   ============================================================ */

/* ─── Cart count badge ────────────────────────────────────── */
function updateCartCount(count) {
  const badge = document.getElementById('cartCount');
  if (!badge) return;
  badge.textContent = fa(count);
  badge.style.display = count > 0 ? 'inline-flex' : 'none';
}

/* ─── Add to cart ─────────────────────────────────────────── */
function addToCart(productId, qty, csrfToken) {
  qty = qty || 1;
  const base = window.AFAG_BASE_URL || '';
  const fd = new FormData();
  fd.append('product_id', productId);
  fd.append('qty', qty);
  fd.append('csrf_token', csrfToken || document.querySelector('[name=csrf_token]')?.value || '');

  fetch(base + '/cart/add', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(d => {
      if (d.ok) {
        updateCartCount(d.count);
        // Flash a brief success toast
        showToast(d.msg || 'به سبد اضافه شد', 'success');
      } else {
        showToast(d.msg || 'خطا', 'error');
      }
    })
    .catch(() => showToast('خطا در اتصال', 'error'));
}

/* ─── Simple toast ────────────────────────────────────────── */
function showToast(msg, type) {
  const t = document.createElement('div');
  t.className = 'alert alert-' + (type === 'error' ? 'error' : 'success');
  t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);z-index:9999;white-space:nowrap;margin-bottom:0;box-shadow:0 10px 30px rgba(0,0,0,.35);animation:none;';
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3000);
}

/* ─── Product gallery ─────────────────────────────────────── */
(function initGallery() {
  const main = document.getElementById('galleryMain');
  if (!main) return;
  const mainImg = main.querySelector('img');
  if (!mainImg) return;

  document.querySelectorAll('.gallery-thumb').forEach(thumb => {
    thumb.addEventListener('click', function() {
      const src = this.querySelector('img')?.src;
      if (src && mainImg) mainImg.src = src;
      document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
    });
  });
})();

/* ─── Product tabs ────────────────────────────────────────── */
(function initTabs() {
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const target = this.dataset.tab;
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      const panel = document.getElementById('tab-' + target);
      if (panel) panel.classList.add('active');
    });
  });
  // Activate first tab
  const firstBtn = document.querySelector('.tab-btn');
  if (firstBtn) firstBtn.click();
})();

/* ─── Faceted filter ──────────────────────────────────────── */
(function initFilter() {
  const filterForm = document.getElementById('filterForm');
  if (!filterForm) return;

  filterForm.addEventListener('change', function() {
    const params = new URLSearchParams(new FormData(this));
    const url = window.location.pathname + '?' + params.toString();
    window.location.href = url;
  });
})();

/* ─── Price range display ─────────────────────────────────── */
(function initPriceRange() {
  const minInput = document.getElementById('minPriceRange');
  const maxInput = document.getElementById('maxPriceRange');
  const minLabel = document.getElementById('minPriceLabel');
  const maxLabel = document.getElementById('maxPriceLabel');
  if (!minInput || !maxInput) return;

  function update() {
    if (minLabel) minLabel.textContent = fa(Number(minInput.value).toLocaleString('en'));
    if (maxLabel) maxLabel.textContent = fa(Number(maxInput.value).toLocaleString('en'));
  }
  minInput.addEventListener('input', update);
  maxInput.addEventListener('input', update);
  update();
})();

/* ─── Coupon apply ────────────────────────────────────────── */
(function initCoupon() {
  const btn = document.getElementById('applyCouponBtn');
  if (!btn) return;
  btn.addEventListener('click', function() {
    const code = document.getElementById('couponCode')?.value?.trim();
    const csrf = document.querySelector('[name=csrf_token]')?.value || '';
    if (!code) return;

    const fd = new FormData();
    fd.append('code', code);
    fd.append('csrf_token', csrf);

    fetch('/cart/coupon', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(d => {
        const msg = document.getElementById('couponMsg');
        if (msg) {
          msg.textContent = d.msg || '';
          msg.style.color = d.ok ? 'var(--clr-ok, #22c55e)' : 'var(--clr-danger, #ef4444)';
        }
        if (d.ok) setTimeout(() => location.reload(), 800);
      });
  });
})();

/* ─── Cart update/remove ──────────────────────────────────── */
(function initCartActions() {
  const base = window.AFAG_BASE_URL || '';
  const tokenEl = document.getElementById('csrf_token') || document.querySelector('[name=csrf_token]');
  const token = () => (tokenEl ? tokenEl.value : '');

  function post(url, fd) {
    return fetch(base + url, { method: 'POST', body: fd }).then(r => r.json());
  }

  function updateQty(productId, qty) {
    const fd = new FormData();
    fd.append('product_id', productId);
    fd.append('qty', qty);
    fd.append('csrf_token', token());
    post('/cart/update', fd).then(d => { if (d.ok) { updateCartCount(d.count); location.reload(); } });
  }

  // Qty +/- (cart page)
  document.querySelectorAll('.cart-qty-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const wrap = this.closest('.cart-qty-wrap');
      const input = wrap ? wrap.querySelector('.cart-qty-input') : null;
      if (!input) return;
      const dir = this.dataset.dir;
      const min = parseInt(input.min, 10) || 1;
      const max = parseInt(input.max, 10) || 99;
      let v = parseInt(input.value, 10) || min;
      v = dir === 'up' ? v + 1 : v - 1;
      v = Math.max(min, Math.min(v, max));
      input.value = v;
      const row = this.closest('tr[data-product-id]');
      if (row) updateQty(row.dataset.productId, v);
    });
  });

  // Qty input change (cart page)
  document.querySelectorAll('.cart-qty-input').forEach(input => {
    input.addEventListener('change', function() {
      const row = this.closest('tr[data-product-id]');
      if (row) updateQty(row.dataset.productId, Math.max(1, parseInt(this.value, 10) || 1));
    });
  });

  // Remove (cart page)
  document.querySelectorAll('.cart-remove').forEach(btn => {
    btn.addEventListener('click', function() {
      const fd = new FormData();
      fd.append('product_id', this.dataset.productId);
      fd.append('csrf_token', token());
      post('/cart/remove', fd).then(d => { if (d.ok) { updateCartCount(d.count); location.reload(); } });
    });
  });

  // Coupon apply
  const couponBtn = document.querySelector('.coupon-btn');
  const couponInput = document.querySelector('.coupon-input');
  if (couponBtn && couponInput) {
    const applyCoupon = function() {
      const code = couponInput.value.trim();
      if (!code) return;
      const fd = new FormData();
      fd.append('code', code);
      fd.append('csrf_token', token());
      post('/cart/coupon', fd).then(d => { if (d.ok) location.reload(); });
    };
    couponBtn.addEventListener('click', applyCoupon);
    couponInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') { e.preventDefault(); applyCoupon(); }
    });
  }

  // Remove coupon
  document.querySelectorAll('.remove-coupon-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const fd = new FormData();
      fd.append('csrf_token', token());
      post('/cart/coupon/remove', fd).then(() => location.reload());
    });
  });
})();

/* ─── Add to cart buttons (product + listings) ─────────────── */
(function initAddToCartButtons() {
  const base = window.AFAG_BASE_URL || '';
  const tokenEl = document.querySelector('[name=csrf_token]');
  const csrf = () => (tokenEl ? tokenEl.value : '');

  document.querySelectorAll('.btn-add-cart[data-id]').forEach(btn => {
    btn.addEventListener('click', function() {
      if (this.disabled) return;
      const productId = this.dataset.id;
      // Read qty from the nearest quantity input (product detail) if present
      let qty = 1;
      const container = this.closest('.product-info, .product-card, .product-grid, .products-grid');
      if (container) {
        const qtyInput = container.querySelector('.qty-input');
        if (qtyInput) qty = Math.max(1, parseInt(qtyInput.value, 10) || 1);
      }

      const originalText = this.textContent;
      this.disabled = true;
      this.classList.add('is-added');
      this.textContent = '✓ اضافه شد';
      setTimeout(() => {
        this.disabled = false;
        this.classList.remove('is-added');
        this.textContent = originalText;
      }, 1500);

      addToCart(productId, qty, csrf());
    });
  });
})();

/* ─── Quantity +/- (product detail) ───────────────────────── */
(function initQtySteppers() {
  document.querySelectorAll('.qty-wrap .qty-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const wrap = this.closest('.qty-wrap');
      const input = wrap ? wrap.querySelector('.qty-input') : null;
      if (!input) return;
      const dir = this.dataset.dir;
      const min = parseInt(input.min, 10) || 1;
      const max = parseInt(input.max, 10) || 99;
      let v = parseInt(input.value, 10) || min;
      v = dir === 'up' ? v + 1 : v - 1;
      v = Math.max(min, Math.min(v, max));
      input.value = v;
    });
  });
})();

/* ─── Filter collapsible groups ──────────────────────────── */
(function initFilterGroups() {
  document.querySelectorAll('.filter-group-title').forEach(title => {
    title.addEventListener('click', function() {
      this.closest('.filter-group').classList.toggle('collapsed');
    });
  });
})();

/* ============================================================
   PHASE 3 JS
   ============================================================ */

/* ─── Upload Zone drag-and-drop ──────────────────────────── */
(function initUploadZone() {
  const zone     = document.getElementById('uploadZone');
  const input    = document.getElementById('modelFile');
  const body     = document.getElementById('uploadZoneBody');
  const preview  = document.getElementById('uploadPreview');
  const fname    = document.getElementById('uploadFilename');
  const removeBtn = document.getElementById('uploadRemove');
  if (!zone || !input) return;

  const ALLOWED = ['stl','obj','3mf','step','stp','gcode','zip','rar'];

  function showFile(file) {
    if (!file) return;
    const ext = file.name.split('.').pop().toLowerCase();
    if (!ALLOWED.includes(ext)) {
      alert('فرمت فایل قابل قبول نیست. فرمت‌های مجاز: ' + ALLOWED.join(', ').toUpperCase());
      input.value = '';
      return;
    }
    fname.textContent = file.name;
    body.hidden    = true;
    preview.hidden = false;
    zone.style.borderColor = 'var(--orange)';
  }

  function clearFile() {
    input.value    = '';
    body.hidden    = false;
    preview.hidden = true;
    zone.style.borderColor = '';
  }

  zone.addEventListener('click', function(e) {
    if (!e.target.closest('#uploadRemove')) input.click();
  });
  zone.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ' ') input.click();
  });

  input.addEventListener('change', function() {
    showFile(this.files[0]);
  });

  zone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('drag-over');
  });
  zone.addEventListener('dragleave', function() {
    this.classList.remove('drag-over');
  });
  zone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file) {
      // Set file to input via DataTransfer
      const dt = new DataTransfer();
      dt.items.add(file);
      input.files = dt.files;
      showFile(file);
    }
  });

  if (removeBtn) {
    removeBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      clearFile();
    });
  }
})();

/* ─── Color Swatch Selection ─────────────────────────────── */
(function initColorSwatches() {
  const swatches   = document.querySelectorAll('.color-swatch:not([disabled])');
  const hiddenInput = document.getElementById('colorIdInput');
  const labelEl    = document.getElementById('colorSelectedLabel');
  if (!swatches.length) return;

  swatches.forEach(swatch => {
    swatch.addEventListener('click', function() {
      const wasSelected = this.classList.contains('selected');
      swatches.forEach(s => s.classList.remove('selected'));
      if (!wasSelected) {
        this.classList.add('selected');
        if (hiddenInput) hiddenInput.value = this.dataset.id;
        if (labelEl)     labelEl.textContent = 'رنگ انتخابی: ' + this.dataset.label;
      } else {
        if (hiddenInput) hiddenInput.value = '';
        if (labelEl)     labelEl.textContent = 'رنگی انتخاب نشده';
      }
    });
  });
})();

/* ─── Quantity Input ─────────────────────────────────────── */
(function initQtyButtons() {
  const qtyInput = document.getElementById('quantityInput');
  const minus    = document.getElementById('qtyMinus');
  const plus     = document.getElementById('qtyPlus');
  if (!qtyInput) return;

  if (minus) {
    minus.addEventListener('click', function() {
      const v = parseInt(qtyInput.value, 10) || 1;
      if (v > 1) qtyInput.value = v - 1;
    });
  }
  if (plus) {
    plus.addEventListener('click', function() {
      const v = parseInt(qtyInput.value, 10) || 1;
      if (v < 99) qtyInput.value = v + 1;
    });
  }
})();

/* ─── Lightbox ───────────────────────────────────────────── */
(function initLightbox() {
  let lbItems = [], lbIdx = 0;
  const lb      = document.getElementById('lightbox');
  const lbImg   = document.getElementById('lbMainImg');
  const lbCap   = document.getElementById('lbCaption');
  const lbClose = document.getElementById('lbClose');
  const lbPrev  = document.getElementById('lbPrev');
  const lbNext  = document.getElementById('lbNext');
  const lbThumbs = document.getElementById('lbThumbs');
  const lbCounter = document.getElementById('lbCounter');

  if (!lb) return;

  function renderLightbox() {
    const item = lbItems[lbIdx];
    if (!item) return;
    lbImg.src = item.src;
    lbImg.alt = item.caption || '';
    if (lbCap) lbCap.textContent = item.caption || '';
    if (lbCounter) lbCounter.textContent = (lbIdx + 1) + ' / ' + lbItems.length;
    // Thumbs
    if (lbThumbs) {
      lbThumbs.innerHTML = '';
      if (lbItems.length > 1) {
        lbItems.forEach(function(it, i) {
          const t = document.createElement('img');
          t.src = it.src;
          t.alt = '';
          t.className = 'lightbox-thumb' + (i === lbIdx ? ' active' : '');
          t.addEventListener('click', function() {
            lbIdx = i;
            renderLightbox();
          });
          lbThumbs.appendChild(t);
        });
      }
    }
    if (lbPrev) lbPrev.style.display = lbItems.length > 1 ? '' : 'none';
    if (lbNext) lbNext.style.display = lbItems.length > 1 ? '' : 'none';
  }

  function openLightbox(items, idx) {
    lbItems = items;
    lbIdx   = idx || 0;
    renderLightbox();
    lb.hidden = false;
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    lb.hidden = true;
    document.body.style.overflow = '';
    lbItems = [];
  }

  function prevItem() {
    lbIdx = (lbIdx - 1 + lbItems.length) % lbItems.length;
    renderLightbox();
  }
  function nextItem() {
    lbIdx = (lbIdx + 1) % lbItems.length;
    renderLightbox();
  }

  if (lbClose) lbClose.addEventListener('click', closeLightbox);
  if (lbPrev)  lbPrev.addEventListener('click', prevItem);
  if (lbNext)  lbNext.addEventListener('click', nextItem);
  lb.addEventListener('click', function(e) {
    if (e.target === lb) closeLightbox();
  });

  document.addEventListener('keydown', function(e) {
    if (lb.hidden) return;
    if (e.key === 'Escape')     closeLightbox();
    if (e.key === 'ArrowRight') prevItem();   // RTL: right = prev
    if (e.key === 'ArrowLeft')  nextItem();
  });

  // Wire up portfolio cards
  document.querySelectorAll('.portfolio-card[data-lb-items]').forEach(function(card) {
    function open() {
      try {
        const items = JSON.parse(card.dataset.lbItems);
        const idx   = parseInt(card.dataset.lbIndex || '0', 10);
        openLightbox(items, idx);
      } catch (err) {}
    }
    card.addEventListener('click', open);
    card.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
    });
  });

  // Expose globally for external use
  window.openLightbox  = openLightbox;
  window.closeLightbox = closeLightbox;
})();

/* ─── Popup ──────────────────────────────────────────────── */
(function initPopup() {
  const POPUP_KEY = 'afag3d_popup_seen';
  window.addEventListener('load', function() {
    const popup = document.getElementById('site-popup');
    if (!popup) return;

    if (!sessionStorage.getItem(POPUP_KEY)) {
      setTimeout(function() {
        popup.style.display = 'flex';
      }, 1500);
    }

    const closeBtn = popup.querySelector('.popup-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', function() {
        popup.style.display = 'none';
        sessionStorage.setItem(POPUP_KEY, '1');
      });
    }

    popup.addEventListener('click', function(e) {
      if (e.target === popup) {
        popup.style.display = 'none';
        sessionStorage.setItem(POPUP_KEY, '1');
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && popup.style.display !== 'none') {
        popup.style.display = 'none';
        sessionStorage.setItem(POPUP_KEY, '1');
      }
    });
  });
})();

/* ─── Blog Search debounce ───────────────────────────────── */
(function initBlogSearch() {
  const searchInput = document.getElementById('blogSearch');
  if (!searchInput) return;
  let debounceTimer = null;

  searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    const val = this.value.trim();
    debounceTimer = setTimeout(function() {
      if (val.length >= 2) {
        const form = searchInput.closest('form');
        if (form) form.submit();
      }
    }, 600);
  });
})();

/* ─── Share button: copy link ─────────────────────────────── */
(function initShareCopy() {
  document.querySelectorAll('[data-share="copy"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      navigator.clipboard.writeText(window.location.href).then(function() {
        const orig = btn.textContent;
        btn.textContent = 'کپی شد!';
        setTimeout(function() { btn.textContent = orig; }, 2000);
      }).catch(function() {
        // Fallback
        const ta = document.createElement('textarea');
        ta.value = window.location.href;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        const orig = btn.textContent;
        btn.textContent = 'کپی شد!';
        setTimeout(function() { btn.textContent = orig; }, 2000);
      });
    });
  });
})();
