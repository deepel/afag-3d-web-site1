# راهنمای نصب afag3d روی cPanel

## پیش‌نیازها

- PHP 8.1 یا بالاتر (با افزونه‌های: PDO، pdo_mysql، mbstring، fileinfo، zip، curl)
- MySQL 5.7+ یا MariaDB 10.3+
- Apache با mod_rewrite فعال
- حداقل ۵۰۰ مگابایت فضای هاست

---

## مرحله ۱ — ساخت فایل ZIP

قبل از آپلود، فایل ZIP را بسازید:

```
php database/make_zip.php
```

این دستور فایل `afag3d_deploy.zip` را در ریشه پروژه می‌سازد.

---

## مرحله ۲ — آپلود فایل‌ها

1. وارد cPanel شوید
2. به **File Manager** بروید
3. فایل `afag3d_deploy.zip` را در مسیر دلخواه آپلود کنید (مثلاً `/home/username/`)
4. روی فایل کلیک راست کرده و **Extract** کنید
5. محتوا باید داخل پوشه‌ای مثل `/home/username/afag3d/` قرار گیرد

---

## مرحله ۳ — تنظیم Document Root

در cPanel، بخش **Domains** یا **Subdomains**:

- Document Root را روی پوشه `public` تنظیم کنید
- مثال: `/home/username/afag3d/public`

> **نکته:** اگر از ساب‌دامین استفاده می‌کنید، در بخش Subdomains، Document Root را مستقیماً روی پوشه public بگذارید.

---

## مرحله ۴ — ساخت دیتابیس

1. در cPanel به **MySQL Databases** بروید
2. یک دیتابیس جدید بسازید — مثلاً: `username_afag3d`
3. یک کاربر جدید با رمز قوی بسازید
4. کاربر را به دیتابیس اضافه کنید و **ALL PRIVILEGES** بدهید
5. نام دیتابیس، نام کاربری و رمز را یادداشت کنید

---

## مرحله ۵ — ایمپورت دیتابیس

1. در cPanel به **phpMyAdmin** بروید
2. دیتابیس `username_afag3d` را از پانل چپ انتخاب کنید
3. تب **Import** را کلیک کنید
4. فایل `database/install.sql` را انتخاب کنید
5. روی **Go** کلیک کنید
6. پیام موفقیت باید نمایش داده شود

---

## مرحله ۶ — تنظیم config.php

فایل `config/config.php` را در File Manager ویرایش کنید:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'username_afag3d');    // نام دیتابیس
define('DB_USER', 'username_dbuser');    // نام کاربری دیتابیس
define('DB_PASS', 'YourStrongPass!');    // رمز دیتابیس
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', 'https://yourdomain.com');  // بدون / انتهایی
define('APP_KEY', 'یک-رشته-۳۲-کاراکتری-تصادفی-تغییر-دهید');

define('ZARINPAL_MERCHANT', 'your-zarinpal-merchant-id');
define('ZARINPAL_SANDBOX', false);  // در محیط واقعی false

define('API_KEY', 'یک-رشتهٔ-تصادفی-بلند');  // کلید اتصال n8n (هدر X-API-Key)

define('ENV', 'production');
define('SESSION_NAME', 'afag3d_sess');
define('SESSION_LIFETIME', 7200);
```

> **مهم:** مقدار `APP_KEY` را حتماً به یک رشته تصادفی ۳۲+ کاراکتری تغییر دهید.

---

## مرحله ۷ — ساخت حساب ادمین

در مرورگر یک بار به این آدرس بروید:

```
https://yourdomain.com/database/seed_admin.php
```

این صفحه حساب ادمین پیش‌فرض را می‌سازد:
- موبایل: `09000000000`
- رمز عبور: `Admin@1234`

**بلافاصله پس از ورود، رمز عبور را تغییر دهید و این فایل را حذف کنید!**

---

## مرحله ۸ — مجوزهای فایل

در File Manager یا از طریق SSH:

```bash
# پوشه‌ها
find /home/username/afag3d -type d -exec chmod 755 {} \;

# فایل‌های PHP/CSS/JS
find /home/username/afag3d -type f -exec chmod 644 {} \;

# پوشه‌های قابل نوشتن
chmod 777 /home/username/afag3d/storage/uploads/
chmod 777 /home/username/afag3d/storage/cache/
chmod 777 /home/username/afag3d/storage/logs/
```

---

## مرحله ۹ — نصب فونت‌ها

فایل‌های woff2 زیر را دانلود و در `public/assets/fonts/` آپلود کنید:

**Vazirmatn** (منبع: https://github.com/rastikerdar/vazirmatn/releases):
- Vazirmatn-Light.woff2
- Vazirmatn-Regular.woff2
- Vazirmatn-Medium.woff2
- Vazirmatn-Bold.woff2
- Vazirmatn-Black.woff2

**Space Grotesk & Space Mono** (منبع: https://fonts.google.com — دانلود و آپلود لوکال):
- SpaceGrotesk-Medium.woff2
- SpaceGrotesk-Bold.woff2
- SpaceMono-Regular.woff2
- SpaceMono-Bold.woff2

> بدون این فونت‌ها سایت از فونت‌های سیستمی استفاده می‌کند و ظاهر متفاوت خواهد داشت.

---

## مرحله ۱۰ — بررسی نهایی

- [ ] سایت در آدرس اصلی باز می‌شود و بدون خطا لود می‌شود
- [ ] ورود به پنل ادمین کار می‌کند: `/login`
- [ ] **رمز ادمین را فوراً تغییر دهید** (موبایل: 09000000000، رمز: Admin@1234)
- [ ] فایل `database/seed_admin.php` را حذف یا تغییر نام دهید
- [ ] `ZARINPAL_SANDBOX` را `false` کنید (بعد از تست درگاه)
- [ ] `ENV` را `'production'` کنید
- [ ] `.htaccess` در `storage/cache/` و `storage/uploads/` وجود دارد

---

## عیب‌یابی رایج

| مشکل | راه‌حل |
|------|---------|
| خطای 500 | بررسی مجوز فایل‌ها، فعال بودن PHP 8.1، بررسی error_log |
| صفحه سفید | در config.php تنظیم `ENV='development'` کنید تا خطاها نمایش داده شود |
| خطای دیتابیس | بررسی اطلاعات config.php و اینکه دیتابیس ایمپورت شده است |
| تصاویر نمایش داده نمی‌شود | بررسی مجوز `storage/uploads/` (باید 777 باشد) |
| فونت‌ها لود نمی‌شوند | بررسی وجود فایل‌های woff2 در `public/assets/fonts/` |
| mod_rewrite کار نمی‌کند | با پشتیبانی هاست تماس بگیرید یا AllowOverride All را فعال کنید |
| خطای CSRF | مطمئن شوید session کار می‌کند؛ بررسی session.save_path |
| پرداخت کار نمی‌کند | ZARINPAL_MERCHANT را با merchant-id واقعی پر کنید؛ ZARINPAL_SANDBOX=false |

---

## ساختار پوشه‌ها

```
afag3d/
├── app/
│   ├── Controllers/     # کنترلرها
│   ├── Models/          # مدل‌ها
│   ├── Views/           # ویوها
│   ├── Helpers/         # کلاس‌های کمکی
│   └── Services/        # سرویس‌ها (ZarinPal و ...)
├── config/
│   └── config.php       # تنظیمات (باید دستی پر شود)
├── database/
│   ├── install.sql      # اسکریپت ساخت جداول
│   ├── seed_admin.php   # ساخت ادمین اولیه (بعد از نصب حذف شود)
│   └── make_zip.php     # ساخت ZIP برای استقرار
├── public/              # Document Root — این پوشه را Document Root کنید
│   ├── index.php        # Front Controller
│   ├── .htaccess        # URL Rewrite
│   └── assets/          # CSS، JS، تصاویر، فونت‌ها
└── storage/
    ├── uploads/         # تصاویر آپلود شده (chmod 777)
    ├── cache/           # کش (chmod 777)
    └── logs/            # لاگ‌ها (chmod 777)
```

---

## پشتیبانی

برای مشاوره فنی، ارتباط با تیم توسعه از طریق ایمیل پروژه.
