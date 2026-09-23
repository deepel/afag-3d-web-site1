# afag3d — Automation API

این چهار endpoint برای اتصال **n8n** (پل تلگرام/اینستاگرام) به سایت ساخته شده‌اند.
کل منطق کسب‌وکار (قیمت‌گذاری، ساخت کد، وضعیت) داخل خود PHP است؛ n8n فقط پیام‌رسان است.
اگر n8n قطع شود، سایت و سفارش‌ها کامل کار می‌کنند.

## احراز هویت

همهٔ endpointها با هدر `X-API-Key` محافظت می‌شوند. مقدار آن در `config/config.php`
(ثابت `API_KEY`، خارج از `public/`) ذخیره شده است.

```
X-API-Key: <مقدار API_KEY از config.php>
```

اگر هدر نباشد یا اشتباه باشد → پاسخ **401** با بدنهٔ JSON.

> **روی سرور واقعی حتماً مقدار `API_KEY` را به یک رشتهٔ تصادفی بلند تغییر دهید.**

پاسخ‌ها همیشه JSON با کلید `ok` (true/false) هستند. خطاها کلید `error` و کد وضعیت HTTP درست دارند.

Base URL نمونه: `https://yourdomain.com`

---

## 1) ساخت محصول — `POST /api/product/create`

محصول جدید با وضعیت چرخهٔ `coming_soon` می‌سازد و یک **کد یکتا `AFAG-XXXX`** تولید می‌کند.
اگر `weight_grams` و `print_hours` بفرستی، قیمت **خودکار** از روی فرمول حساب می‌شود؛
وگرنه قیمت `null` می‌ماند (محصول «به‌زودی» بدون دکمهٔ خرید).

بدنه (JSON):

| فیلد | نوع | الزامی | توضیح |
|------|-----|--------|-------|
| `name` | string | ✅ | نام محصول |
| `description` یا `caption` | string | — | توضیح/کپشن فارسی |
| `shop_id` | int | — (پیش‌فرض ۱) | شناسهٔ فروشگاه |
| `weight_grams` | int | — | وزن گرم (ورودی قیمت) |
| `print_hours` | number | — | ساعت چاپ (ورودی قیمت) |
| `images` | array | — | آرایهٔ تصاویر base64 (پایین را ببین) |

قالب هر تصویر در `images`: یا رشتهٔ base64 خام، یا `{"filename":"x.jpg","data":"<base64>"}`.
پیشوند `data:image/...;base64,` هم پذیرفته می‌شود. اعتبارسنجی: نوع واقعی تصویر، حجم ≤ سقف آپلود، نام تصادفی.

نمونهٔ درخواست:

```bash
curl -X POST https://yourdomain.com/api/product/create \
  -H "X-API-Key: YOUR_KEY" -H "Content-Type: application/json" \
  -d '{"name":"اژدهای رزینی","description":"کپشن از تلگرام","shop_id":1,"weight_grams":80,"print_hours":4.5}'
```

پاسخ موفق (`201`):

```json
{ "ok": true, "id": 42, "product_code": "AFAG-0042", "price": 481250, "status": "coming_soon" }
```

---

## 2) ثبت/به‌روزرسانی قیمت — `POST /api/product/update-price`

| فیلد | نوع | الزامی | توضیح |
|------|-----|--------|-------|
| `product_code` | string | ✅ | مثل `AFAG-0042` |
| `price` | int | — | قیمت دستی به تومان. اگر بفرستی، **دستی** ثبت می‌شود و با «محاسبهٔ مجدد» تغییر نمی‌کند. |

اگر `price` نفرستی، موتور از روی `weight_grams` و `print_hours` همان محصول قیمت را حساب می‌کند.
اگر نه قیمت بفرستی و نه وزن/ساعت موجود باشد → `422`.

```bash
curl -X POST https://yourdomain.com/api/product/update-price \
  -H "X-API-Key: YOUR_KEY" -H "Content-Type: application/json" \
  -d '{"product_code":"AFAG-0042","price":520000}'
```

پاسخ:

```json
{ "ok": true, "product_code": "AFAG-0042", "price": 520000, "price_is_manual": true }
```

---

## 3) فهرست محصولات منتظر — `GET /api/product/pending`

محصولاتی که هنوز `available` نشده‌اند (`lifecycle_status IN ('pending','coming_soon')`).

```bash
curl https://yourdomain.com/api/product/pending -H "X-API-Key: YOUR_KEY"
```

پاسخ:

```json
{
  "ok": true,
  "count": 2,
  "products": [
    { "id": 42, "product_code": "AFAG-0042", "name": "اژدهای رزینی",
      "price": "481250", "weight_grams": 80, "print_hours": "4.50",
      "lifecycle_status": "coming_soon", "shop_id": 1, "created_at": "2026-06-26 13:59:26" }
  ]
}
```

---

## 4) انتشار محصول — `POST /api/product/publish`

وضعیت را به `available` تغییر می‌دهد (و `status='active'` تا در فروشگاه دیده شود).
محصول **باید قیمت داشته باشد**، وگرنه `409`.

| فیلد | نوع | الزامی |
|------|-----|--------|
| `product_code` | string | ✅ |

```bash
curl -X POST https://yourdomain.com/api/product/publish \
  -H "X-API-Key: YOUR_KEY" -H "Content-Type: application/json" \
  -d '{"product_code":"AFAG-0042"}'
```

پاسخ:

```json
{ "ok": true, "product_code": "AFAG-0042", "status": "available" }
```

---

## فرمول قیمت‌گذاری

```
قیمت تمام‌شده = (weight_grams × filament_rate_per_gram)
              + (print_hours  × machine_rate_per_hour)
              + fixed_overhead
قیمت فروش    = round(قیمت تمام‌شده × profit_multiplier)
```

ضرایب در جدول `pricing_settings` و قابل ویرایش از **پنل ادمین → تنظیمات قیمت** هستند.
دکمهٔ «محاسبهٔ مجدد کل کاتالوگ» قیمت همهٔ محصولاتِ دارای وزن/ساعت را که **دستی** نیستند، دوباره حساب می‌کند.

## کدهای وضعیت

| کد | معنی |
|----|------|
| 200 / 201 | موفق |
| 401 | کلید API نامعتبر |
| 404 | محصول با این کد یافت نشد |
| 409 | انتشار بدون قیمت |
| 422 | ورودی ناقص/نامعتبر |

## جریان معمول در n8n

1. تلگرام → `POST /api/product/create` (نام، کپشن، عکس‌ها) → گرفتن `product_code`.
2. (اختیاری) `POST /api/product/update-price` برای ثبت قیمت دستی.
3. `GET /api/product/pending` برای مرور صف.
4. بعد از تأیید مالک → `POST /api/product/publish`.
