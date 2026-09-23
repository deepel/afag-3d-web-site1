<?php
/**
 * Privacy Policy Page
 */
?>
<section class="page-hero">
  <div class="container">
    <h1>حریم <span class="text-accent">خصوصی</span></h1>
    <p>نحوه جمع‌آوری، استفاده و حفاظت از اطلاعات شما در سایت افگ تری‌دی</p>
  </div>
</section>

<section class="page-section">
  <div class="container">
    <div style="max-width:800px;margin:0 auto">

      <p style="color:var(--gray);font-size:14px;margin-bottom:40px">آخرین بروزرسانی: <?= Url::toPersianDigits(date('Y/m/d')) ?></p>

      <div style="display:flex;flex-direction:column;gap:40px">

        <div>
          <h2 style="font-size:20px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--line)">1. اطلاعات جمع‌آوری شده</h2>
          <p style="color:var(--gray);line-height:1.9;margin-bottom:12px">ما اطلاعات زیر را جمع‌آوری می‌کنیم:</p>
          <ul style="color:var(--gray);line-height:2.2;padding-right:20px">
            <li><strong>اطلاعات ثبت‌نام:</strong> نام، شماره موبایل و ایمیل</li>
            <li><strong>اطلاعات سفارش:</strong> آدرس، کد پستی، جزئیات خرید</li>
            <li><strong>فایل‌های سه‌بعدی:</strong> فایل‌های آپلود شده برای چاپ</li>
            <li><strong>اطلاعات فنی:</strong> آدرس IP، نوع مرورگر، صفحات بازدید شده</li>
            <li><strong>پرداخت:</strong> اطلاعات کارت بانکی از طریق درگاه امن (ذخیره نمی‌شود)</li>
          </ul>
        </div>

        <div>
          <h2 style="font-size:20px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--line)">2. نحوه استفاده از اطلاعات</h2>
          <p style="color:var(--gray);line-height:1.9;margin-bottom:12px">اطلاعات جمع‌آوری شده برای موارد زیر استفاده می‌شود:</p>
          <ul style="color:var(--gray);line-height:2.2;padding-right:20px">
            <li>پردازش و مدیریت سفارشات</li>
            <li>ارتباط با شما درباره سفارش و خدمات</li>
            <li>بهبود کیفیت خدمات و تجربه کاربری</li>
            <li>ارسال اطلاعیه‌ها و پیشنهادات (با رضایت شما)</li>
            <li>رعایت تعهدات قانونی</li>
          </ul>
        </div>

        <div>
          <h2 style="font-size:20px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--line)">3. امنیت اطلاعات</h2>
          <p style="color:var(--gray);line-height:1.9">
            ما از تدابیر امنیتی مناسب برای حفاظت از اطلاعات شما استفاده می‌کنیم، از جمله رمزنگاری SSL/TLS، هش امن رمزهای عبور (bcrypt) و کنترل دسترسی محدود. با این حال، هیچ سیستم انتقال داده‌ای کاملاً امن نیست و ما نمی‌توانیم امنیت مطلق را تضمین کنیم.
          </p>
        </div>

        <div>
          <h2 style="font-size:20px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--line)">4. اشتراک‌گذاری اطلاعات</h2>
          <p style="color:var(--gray);line-height:1.9;margin-bottom:12px">
            ما اطلاعات شخصی شما را بدون رضایت شما به اشخاص ثالث نمی‌فروشیم یا اجاره نمی‌دهیم. در موارد زیر ممکن است اطلاعات را به اشتراک بگذاریم:
          </p>
          <ul style="color:var(--gray);line-height:2.2;padding-right:20px">
            <li>شرکت‌های پستی و لجستیک برای ارسال سفارش</li>
            <li>درگاه‌های پرداخت بانکی برای تراکنش مالی</li>
            <li>مراجع قانونی در صورت درخواست قانونی</li>
          </ul>
        </div>

        <div>
          <h2 style="font-size:20px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--line)">5. کوکی‌ها</h2>
          <p style="color:var(--gray);line-height:1.9">
            سایت ما از کوکی‌های ضروری برای مدیریت نشست (session) و سبد خرید استفاده می‌کند. این کوکی‌ها برای عملکرد صحیح سایت الزامی هستند. ما از کوکی‌های تبلیغاتی یا ردیابی شخص ثالث استفاده نمی‌کنیم.
          </p>
        </div>

        <div>
          <h2 style="font-size:20px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--line)">6. حقوق شما</h2>
          <p style="color:var(--gray);line-height:1.9;margin-bottom:12px">شما حق دارید:</p>
          <ul style="color:var(--gray);line-height:2.2;padding-right:20px">
            <li>به اطلاعات ذخیره شده از شما دسترسی داشته باشید</li>
            <li>اطلاعات نادرست را تصحیح کنید</li>
            <li>درخواست حذف اطلاعات خود را بدهید (در حدود قوانین)</li>
            <li>از دریافت ایمیل‌های تبلیغاتی انصراف دهید</li>
          </ul>
        </div>

        <div>
          <h2 style="font-size:20px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--line)">7. نگهداری اطلاعات</h2>
          <p style="color:var(--gray);line-height:1.9">
            اطلاعات حساب کاربری تا زمانی که حساب شما فعال است نگهداری می‌شود. پس از بستن حساب، اطلاعات تا 6 ماه برای پیگیری امور قانونی نگهداری و سپس حذف می‌شود. فایل‌های سه‌بعدی آپلود شده تا 6 ماه پس از تکمیل سفارش نگهداری می‌شوند.
          </p>
        </div>

        <div>
          <h2 style="font-size:20px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--line)">8. تماس با ما</h2>
          <p style="color:var(--gray);line-height:1.9">
            برای هرگونه سوال درباره حریم خصوصی یا اعمال حقوق خود، از طریق
            <a href="<?= BASE_URL ?>/contact" style="color:var(--orange)">صفحه تماس</a>
            با ما در ارتباط باشید. تیم ما ظرف 72 ساعت پاسخ خواهد داد.
          </p>
        </div>

      </div>
    </div>
  </div>
</section>
