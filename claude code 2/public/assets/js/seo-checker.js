/**
 * afag3d — Live SEO Checker  (Phase 4)
 * Attaches to admin product/blog forms and provides real-time SEO feedback.
 *
 * Watches: #meta_title, #meta_desc, #slug (or #bf_slug), #focus_keyword,
 *          #description (or #bf_body), #pname (or #bf_title)
 *
 * Outputs to: .seo-live-panel  (injected into .seo-panel or .seo-panel.p-20)
 */
(function () {
  'use strict';

  // ── Field resolvers ───────────────────────────────────────────────────────

  function field(ids) {
    for (var i = 0; i < ids.length; i++) {
      var el = document.getElementById(ids[i]);
      if (el) return el;
    }
    return null;
  }

  var titleEl  = field(['meta_title', 'bf_meta_title']);
  var descEl   = field(['meta_desc',  'bf_meta_desc']);
  var slugEl   = field(['pslug',      'bf_slug', 'slug']);
  var kwEl     = field(['focus_keyword']);
  var bodyEl   = field(['description','bf_body']);
  var nameEl   = field(['pname',      'bf_title']);

  // Bail if no SEO fields present on this page
  if (!titleEl && !descEl) return;

  // ── Panel injection ───────────────────────────────────────────────────────

  // Find the container — could be .seo-panel or .seo-panel.p-20
  var panels = document.querySelectorAll('.seo-panel');
  var container = null;
  for (var p = 0; p < panels.length; p++) {
    // Pick the one that contains an seo-related element, or just the last
    container = panels[p];
  }
  if (!container) return;

  // Build the live panel div and inject it
  var livePanel = document.createElement('div');
  livePanel.className = 'seo-live-panel';
  livePanel.innerHTML = [
    '<div class="seo-live-header">',
    '  <div class="seo-score-circle bad" id="seoScoreCircle">0</div>',
    '  <div class="seo-live-meta">',
    '    <strong>امتیاز سئو</strong>',
    '    <span class="seo-score-label" id="seoScoreLabel">نیاز به بهبود</span>',
    '  </div>',
    '  <div style="clear:both"></div>',
    '</div>',

    '<div class="seo-bars" style="margin:12px 0">',
    '  <div class="seo-bar-row">',
    '    <span class="seo-bar-label">عنوان <span id="titleLenDisplay">0</span>/60</span>',
    '    <div class="seo-bar"><div class="seo-bar-fill bad" id="titleBar" style="width:0%"></div></div>',
    '  </div>',
    '  <div class="seo-bar-row" style="margin-top:6px">',
    '    <span class="seo-bar-label">توضیح <span id="descLenDisplay">0</span>/160</span>',
    '    <div class="seo-bar"><div class="seo-bar-fill bad" id="descBar" style="width:0%"></div></div>',
    '  </div>',
    '</div>',

    '<div id="seoCheckList"></div>',

    '<details class="serp-details" style="margin-top:12px">',
    '  <summary style="cursor:pointer;font-size:12px;color:var(--gray)">پیش‌نمایش گوگل</summary>',
    '  <div class="serp-preview">',
    '    <div class="serp-title" id="serpTitle">عنوان صفحه</div>',
    '    <div class="serp-url"   id="serpUrl">' + (window.location.origin || 'https://afag3d.com') + '/<em>slug</em></div>',
    '    <div class="serp-desc"  id="serpDesc">توضیحات متا در اینجا نمایش داده می‌شود...</div>',
    '  </div>',
    '</details>',
  ].join('\n');

  container.appendChild(livePanel);

  // ── Checks definition ─────────────────────────────────────────────────────

  function getChecks(title, desc, slug, kw, body) {
    var tLen = title.length;
    var dLen = desc.length;
    var tLow = title.toLowerCase();
    var dLow = desc.toLowerCase();
    var sLow = slug.toLowerCase();
    var bLow = body.toLowerCase();
    var kwL  = kw.trim().toLowerCase();

    return [
      {
        label: 'کلمه کلیدی در عنوان',
        pass:  kwL !== '' && tLow.indexOf(kwL) !== -1,
        tip:   'عنوان متا باید کلمه کلیدی اصلی را داشته باشد.',
      },
      {
        label: 'کلمه کلیدی در slug',
        pass:  kwL !== '' && sLow.indexOf(kwL) !== -1,
        tip:   'اسلاگ URL باید کلمه کلیدی اصلی را داشته باشد.',
      },
      {
        label: 'کلمه کلیدی در توضیحات',
        pass:  kwL !== '' && (dLow.indexOf(kwL) !== -1 || bLow.indexOf(kwL) !== -1),
        tip:   'توضیح متا یا محتوا باید کلمه کلیدی را داشته باشند.',
      },
      {
        label: 'طول عنوان مناسب (۵۰–۶۰)',
        pass:  tLen >= 50 && tLen <= 60,
        tip:   'الان: ' + tLen + ' کاراکتر. هدف: ۵۰ تا ۶۰.',
      },
      {
        label: 'طول توضیح مناسب (۱۲۰–۱۶۰)',
        pass:  dLen >= 120 && dLen <= 160,
        tip:   'الان: ' + dLen + ' کاراکتر. هدف: ۱۲۰ تا ۱۶۰.',
      },
      {
        label: 'slug فقط حروف کوچک و خط‌تیره',
        pass:  slug !== '' && /^[a-z0-9][a-z0-9\-]*[a-z0-9]$/.test(slug),
        tip:   'اسلاگ باید فقط a-z، ۰-۹ و خط‌تیره (-) داشته باشد.',
      },
    ];
  }

  // ── Score color ───────────────────────────────────────────────────────────

  function scoreClass(score) {
    if (score >= 70) return 'good';
    if (score >= 40) return 'ok';
    return 'bad';
  }

  function scoreLabel(score) {
    if (score >= 80) return 'عالی';
    if (score >= 60) return 'خوب';
    if (score >= 40) return 'متوسط';
    return 'نیاز به بهبود';
  }

  // ── Bar color ─────────────────────────────────────────────────────────────

  function barClass(len, min, max) {
    if (len === 0) return 'bad';
    if (len >= min && len <= max) return 'good';
    if (len > max) return 'bad';
    return 'ok';
  }

  // ── Render ────────────────────────────────────────────────────────────────

  function render() {
    var title = titleEl ? titleEl.value : '';
    var desc  = descEl  ? descEl.value  : '';
    var slug  = slugEl  ? slugEl.value  : '';
    var kw    = kwEl    ? kwEl.value    : (nameEl ? nameEl.value.split(' ')[0] : '');
    var body  = bodyEl  ? bodyEl.value  : '';

    var checks = getChecks(title, desc, slug, kw, body);
    var passed = checks.filter(function(c) { return c.pass; }).length;
    var score  = Math.round((passed / checks.length) * 100);

    // Score circle
    var circle = document.getElementById('seoScoreCircle');
    if (circle) {
      circle.textContent = score;
      circle.className   = 'seo-score-circle ' + scoreClass(score);
    }

    var lbl = document.getElementById('seoScoreLabel');
    if (lbl) lbl.textContent = scoreLabel(score);

    // Title bar
    var tLen     = title.length;
    var titleBar = document.getElementById('titleBar');
    var titleLenD = document.getElementById('titleLenDisplay');
    if (titleBar) {
      titleBar.style.width     = Math.min(100, Math.round((tLen / 70) * 100)) + '%';
      titleBar.className       = 'seo-bar-fill ' + barClass(tLen, 50, 60);
    }
    if (titleLenD) titleLenD.textContent = tLen;

    // Desc bar
    var dLen    = desc.length;
    var descBar = document.getElementById('descBar');
    var descLenD = document.getElementById('descLenDisplay');
    if (descBar) {
      descBar.style.width = Math.min(100, Math.round((dLen / 180) * 100)) + '%';
      descBar.className   = 'seo-bar-fill ' + barClass(dLen, 120, 160);
    }
    if (descLenD) descLenD.textContent = dLen;

    // Check list
    var list = document.getElementById('seoCheckList');
    if (list) {
      list.innerHTML = checks.map(function(c) {
        return '<div class="seo-check ' + (c.pass ? 'pass' : 'fail') + '">' +
               '<span class="icon"></span>' +
               '<span class="seo-check-label">' + c.label + '</span>' +
               (c.pass ? '' : '<span class="seo-check-tip"> — ' + c.tip + '</span>') +
               '</div>';
      }).join('');
    }

    // SERP preview
    var origin  = window.location.origin || 'https://afag3d.com';
    var serpTitle = document.getElementById('serpTitle');
    var serpUrl   = document.getElementById('serpUrl');
    var serpDesc  = document.getElementById('serpDesc');

    if (serpTitle) {
      serpTitle.textContent = title || (nameEl ? nameEl.value : '(عنوان صفحه)');
      serpTitle.style.color = title.length > 70 ? '#c00' : '#1a0dab';
    }
    if (serpUrl) {
      serpUrl.innerHTML = origin + '/<em>' + (slug || 'slug-url') + '</em>';
    }
    if (serpDesc) {
      var displayDesc = desc || body.substring(0, 160);
      serpDesc.textContent = displayDesc.substring(0, 160) || '(توضیح متا...)';
      serpDesc.style.color = (dLen > 0 && (dLen < 120 || dLen > 160)) ? '#c00' : '#545454';
    }
  }

  // ── Watch fields ──────────────────────────────────────────────────────────

  var watchFields = [titleEl, descEl, slugEl, kwEl, bodyEl, nameEl];
  watchFields.forEach(function(el) {
    if (!el) return;
    el.addEventListener('input', render);
    el.addEventListener('change', render);
  });

  // Initial render
  render();

})();
