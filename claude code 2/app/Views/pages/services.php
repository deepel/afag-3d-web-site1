<?php
/**
 * Services Page
 */
?>
<!-- ░░░ HERO ░░░ -->
<section class="page-hero">
  <div class="container">
    <h1>خدمات <span class="text-accent">ما</span></h1>
    <p>چاپ سه‌بعدی حرفه‌ای با بهترین تکنولوژی‌های روز دنیا — از نمونه اولیه تا تولید انبوه</p>
  </div>
</section>

<!-- ░░░ SERVICE 1 — FDM ░░░ -->
<section class="page-section" id="fdm">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center">
      <div>
        <div style="display:inline-flex;align-items:center;gap:10px;background:rgba(255,90,0,.1);border:1px solid rgba(255,90,0,.2);border-radius:4px;padding:6px 14px;margin-bottom:20px">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="1.5" width="18" height="18"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          <span style="color:var(--orange);font-size:13px;font-weight:600">چاپ FDM</span>
        </div>
        <h2 style="font-size:clamp(24px,3vw,36px);font-weight:900;margin-bottom:16px">چاپ FDM<br><span style="color:var(--orange)">رشته‌ای</span></h2>
        <p style="color:var(--gray);line-height:1.9;margin-bottom:24px">
          فناوری FDM (Fused Deposition Modeling) پرکاربردترین روش چاپ سه‌بعدی است. مواد مذاب لایه به لایه روی هم قرار می‌گیرند و قطعه نهایی شکل می‌گیرد. این روش برای نمونه‌سازی سریع، قطعات کاربردی و تولید کم‌هزینه بسیار مناسب است.
        </p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:28px">
          <div style="background:var(--bg2);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">مواد مصرفی</div>
            <div style="font-size:14px;line-height:1.8">PLA / PETG / ABS<br>TPU / Nylon / Wood</div>
          </div>
          <div style="background:var(--bg2);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">ضخامت لایه</div>
            <div style="font-size:14px;line-height:1.8">0.1 تا 0.3 میلیمتر<br>پیش‌فرض: 0.2mm</div>
          </div>
          <div style="background:var(--bg2);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">حداکثر ابعاد</div>
            <div style="font-size:14px">300 × 300 × 400 mm</div>
          </div>
          <div style="background:var(--bg2);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">زمان تحویل</div>
            <div style="font-size:14px">1 تا 5 روز کاری</div>
          </div>
        </div>

        <div style="margin-bottom:24px">
          <div style="font-weight:600;margin-bottom:10px">کاربردها</div>
          <div style="display:flex;flex-wrap:wrap;gap:8px">
            <?php foreach (['نمونه اولیه','قطعات صنعتی','لوازم خانگی','اسباب‌بازی','پوشیدنی‌ها','جعبه و محفظه'] as $use): ?>
            <span style="background:var(--bg3);border:1px solid var(--line);border-radius:20px;padding:4px 12px;font-size:13px;color:var(--gray)"><?= $use ?></span>
            <?php endforeach; ?>
          </div>
        </div>

        <a href="<?= BASE_URL ?>/print-order" class="btn btn-primary">ثبت سفارش FDM</a>
      </div>

      <!-- Visual -->
      <div style="background:var(--bg2);border:1px solid var(--line);border-radius:8px;padding:40px;text-align:center;min-height:320px;display:flex;align-items:center;justify-content:center">
        <svg viewBox="0 0 200 200" width="180" height="180" aria-hidden="true">
          <rect x="40" y="140" width="120" height="10" rx="2" fill="var(--line)"/>
          <rect x="55" y="110" width="90" height="8" rx="2" fill="var(--line)" opacity=".7"/>
          <rect x="65" y="82" width="70" height="7" rx="2" fill="var(--line)" opacity=".5"/>
          <rect x="72" y="57" width="56" height="7" rx="2" fill="var(--orange)" opacity=".7"/>
          <rect x="80" y="34" width="40" height="6" rx="2" fill="var(--orange)" opacity=".5"/>
          <circle cx="100" cy="25" r="6" fill="var(--orange)"/>
          <line x1="100" y1="31" x2="100" y2="34" stroke="var(--orange)" stroke-width="2"/>
          <text x="100" y="175" text-anchor="middle" font-size="11" fill="var(--gray)" font-family="sans-serif">FDM Printing</text>
        </svg>
      </div>
    </div>
  </div>
</section>

<hr style="border:none;border-top:1px solid var(--line)">

<!-- ░░░ SERVICE 2 — RESIN ░░░ -->
<section class="page-section" id="resin" style="background:var(--bg2)">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center">

      <!-- Visual -->
      <div style="background:var(--bg3);border:1px solid var(--line);border-radius:8px;padding:40px;text-align:center;min-height:320px;display:flex;align-items:center;justify-content:center">
        <svg viewBox="0 0 200 200" width="180" height="180" aria-hidden="true">
          <ellipse cx="100" cy="160" rx="55" ry="10" fill="rgba(255,90,0,.15)"/>
          <path d="M75 160 Q70 100 100 50 Q130 100 125 160 Z" fill="rgba(255,90,0,.25)" stroke="var(--orange)" stroke-width="1.5"/>
          <path d="M85 155 Q82 105 100 65 Q118 105 115 155 Z" fill="rgba(255,90,0,.35)"/>
          <circle cx="100" cy="50" r="4" fill="var(--orange)"/>
          <text x="100" y="185" text-anchor="middle" font-size="11" fill="var(--gray)" font-family="sans-serif">Resin Printing</text>
        </svg>
      </div>

      <div>
        <div style="display:inline-flex;align-items:center;gap:10px;background:rgba(255,90,0,.1);border:1px solid rgba(255,90,0,.2);border-radius:4px;padding:6px 14px;margin-bottom:20px">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="1.5" width="18" height="18"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
          <span style="color:var(--orange);font-size:13px;font-weight:600">چاپ رزینی</span>
        </div>
        <h2 style="font-size:clamp(24px,3vw,36px);font-weight:900;margin-bottom:16px">چاپ رزینی<br><span style="color:var(--orange)">با دقت بالا</span></h2>
        <p style="color:var(--gray);line-height:1.9;margin-bottom:24px">
          چاپ رزینی (SLA/MSLA) با استفاده از نور UV رزین مایع را سخت می‌کند و قطعاتی با دقت و کیفیت سطحی بسیار بالا تولید می‌کند. این روش برای مدل‌های دقیق، جواهرات، دندانپزشکی و مجسمه‌سازی ایده‌آل است.
        </p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:28px">
          <div style="background:var(--bg3);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">دقت لایه</div>
            <div style="font-size:14px;line-height:1.8">0.02 تا 0.1 میلیمتر<br>تا 50 میکرون</div>
          </div>
          <div style="background:var(--bg3);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">مواد مصرفی</div>
            <div style="font-size:14px;line-height:1.8">Standard / Tough<br>Flexible / Castable</div>
          </div>
          <div style="background:var(--bg3);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">حداکثر ابعاد</div>
            <div style="font-size:14px">192 × 120 × 245 mm</div>
          </div>
          <div style="background:var(--bg3);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">پرداخت نهایی</div>
            <div style="font-size:14px">رنگ‌آمیزی / پولیش</div>
          </div>
        </div>

        <div style="margin-bottom:24px">
          <div style="font-weight:600;margin-bottom:10px">کاربردها</div>
          <div style="display:flex;flex-wrap:wrap;gap:8px">
            <?php foreach (['فیگورین','جواهرات','دندانپزشکی','اکشن فیگر','مجسمه','مدل دقیق'] as $use): ?>
            <span style="background:var(--bg3);border:1px solid var(--line);border-radius:20px;padding:4px 12px;font-size:13px;color:var(--gray)"><?= $use ?></span>
            <?php endforeach; ?>
          </div>
        </div>

        <a href="<?= BASE_URL ?>/print-order" class="btn btn-primary">ثبت سفارش رزینی</a>
      </div>
    </div>
  </div>
</section>

<hr style="border:none;border-top:1px solid var(--line)">

<!-- ░░░ SERVICE 3 — ARCHITECTURAL ░░░ -->
<section class="page-section" id="architecture">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center">
      <div>
        <div style="display:inline-flex;align-items:center;gap:10px;background:rgba(255,90,0,.1);border:1px solid rgba(255,90,0,.2);border-radius:4px;padding:6px 14px;margin-bottom:20px">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--orange)" stroke-width="1.5" width="18" height="18"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <span style="color:var(--orange);font-size:13px;font-weight:600">ماکت معماری</span>
        </div>
        <h2 style="font-size:clamp(24px,3vw,36px);font-weight:900;margin-bottom:16px">ماکت <span style="color:var(--orange)">معماری</span></h2>
        <p style="color:var(--gray);line-height:1.9;margin-bottom:24px">
          تخصصی‌ترین خدمت ما: ساخت ماکت‌های معماری دقیق برای معماران، دفاتر طراحی و دانشجویان معماری. از فایل‌های Revit، ArchiCAD یا SketchUp خود ماکت‌های دقیق با مقیاس دلخواه دریافت کنید.
        </p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:28px">
          <div style="background:var(--bg2);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">مقیاس‌های رایج</div>
            <div style="font-size:14px;line-height:1.8" dir="ltr">1:50 / 1:100<br>1:200 / 1:500</div>
          </div>
          <div style="background:var(--bg2);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">مواد ماکت</div>
            <div style="font-size:14px;line-height:1.8">PLA سفید / رزین<br>رنگ‌آمیزی سفارشی</div>
          </div>
          <div style="background:var(--bg2);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">فرمت فایل</div>
            <div style="font-size:14px;line-height:1.8">STL / OBJ / 3DS<br>Revit / SketchUp</div>
          </div>
          <div style="background:var(--bg2);border:1px solid var(--line);border-radius:4px;padding:16px">
            <div style="font-size:12px;color:var(--gray);margin-bottom:6px">زمان تحویل</div>
            <div style="font-size:14px">3 تا 10 روز کاری</div>
          </div>
        </div>

        <div style="background:rgba(255,90,0,.08);border-right:3px solid var(--orange);padding:14px 18px;border-radius:0 4px 4px 0;margin-bottom:24px;font-size:14px;color:var(--gray)">
          <strong>فرآیند کار:</strong> ارسال فایل → بررسی و استعلام قیمت → تایید → چاپ → پرداخت → ارسال
        </div>

        <a href="<?= BASE_URL ?>/print-order" class="btn btn-primary">ثبت سفارش ماکت</a>
      </div>

      <!-- Visual -->
      <div style="background:var(--bg2);border:1px solid var(--line);border-radius:8px;padding:40px;text-align:center;min-height:320px;display:flex;align-items:center;justify-content:center">
        <svg viewBox="0 0 200 200" width="180" height="180" aria-hidden="true">
          <!-- Building silhouette -->
          <rect x="60" y="80" width="80" height="90" fill="none" stroke="var(--line)" stroke-width="1.5"/>
          <rect x="70" y="90" width="12" height="15" fill="var(--orange)" opacity=".4"/>
          <rect x="90" y="90" width="12" height="15" fill="var(--orange)" opacity=".4"/>
          <rect x="110" y="90" width="12" height="15" fill="var(--orange)" opacity=".4"/>
          <rect x="70" y="115" width="12" height="15" fill="var(--orange)" opacity=".4"/>
          <rect x="90" y="115" width="12" height="15" fill="var(--orange)" opacity=".4"/>
          <rect x="110" y="115" width="12" height="15" fill="var(--orange)" opacity=".4"/>
          <rect x="85" y="140" width="30" height="30" fill="var(--orange)" opacity=".3"/>
          <!-- Roof -->
          <polygon points="55,80 100,50 145,80" fill="none" stroke="var(--orange)" stroke-width="1.5"/>
          <line x1="40" y1="170" x2="160" y2="170" stroke="var(--line)" stroke-width="1.5"/>
          <text x="100" y="190" text-anchor="middle" font-size="11" fill="var(--gray)" font-family="sans-serif">Architectural Model</text>
        </svg>
      </div>
    </div>
  </div>
</section>

<!-- ░░░ CTA ░░░ -->
<section style="background:var(--bg2);padding:80px 6vw;text-align:center">
  <div class="container">
    <h2 style="font-size:clamp(24px,4vw,48px);font-weight:900;margin-bottom:16px">پروژه‌ای دارید؟</h2>
    <p style="color:var(--gray);font-size:18px;margin-bottom:32px;max-width:40em;margin-inline:auto">
      فایل سه‌بعدی خود را آپلود کنید یا با ما تماس بگیرید تا بهترین راه‌حل را با هم پیدا کنیم.
    </p>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
      <a href="<?= BASE_URL ?>/print-order" class="btn btn-primary btn-lg">ثبت سفارش چاپ</a>
      <a href="<?= BASE_URL ?>/contact" class="btn btn-ghost btn-lg">تماس با ما</a>
    </div>
  </div>
</section>
