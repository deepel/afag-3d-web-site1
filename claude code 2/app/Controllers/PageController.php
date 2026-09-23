<?php
declare(strict_types=1);
/**
 * afag3d — Page Controller
 * Handles static informational pages: about, contact, privacy, terms.
 */
class PageController extends BaseController
{
    private function siteData(): array
    {
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    // ── GET /about ───────────────────────────────────────────────────────────

    public function about(): void
    {
        $siteData = $this->siteData();

        Seo::set([
            'title'       => 'درباره ما — استودیوی چاپ سه‌بعدی افگ',
            'description' => 'آشنایی با استودیوی چاپ سه‌بعدی افگ، تیم متخصص و ماموریت ما در ارائه خدمات چاپ سه‌بعدی صنعتی با دقت ۵۰ میکرون.',
            'canonical'   => (defined('BASE_URL') ? BASE_URL : '') . '/about',
        ]);

        $schema = Seo::schemaOrganization($siteData);
        $breadcrumbSchema = Seo::schemaBreadcrumb([
            ['name' => 'خانه',    'url' => (defined('BASE_URL') ? BASE_URL : '') . '/'],
            ['name' => 'درباره ما', 'url' => (defined('BASE_URL') ? BASE_URL : '') . '/about'],
        ]);

        // Stats
        $stats = [
            'products'  => (int) $this->db->query('SELECT COUNT(*) FROM products WHERE status="active"')->fetchColumn(),
            'orders'    => (int) $this->db->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
            'clients'   => (int) $this->db->query('SELECT COUNT(*) FROM users WHERE role="customer"')->fetchColumn(),
            'portfolio' => (int) $this->db->query('SELECT COUNT(*) FROM portfolio WHERE status=1')->fetchColumn(),
        ];

        $this->render('pages.about', [
            'title'            => 'درباره ما',
            'siteData'         => $siteData,
            'stats'            => $stats,
            'schema'           => $schema,
            'breadcrumbSchema' => $breadcrumbSchema,
        ]);
    }

    // ── GET /contact ─────────────────────────────────────────────────────────

    public function contact(): void
    {
        $siteData = $this->siteData();

        Seo::set([
            'title'       => 'تماس با ما — افگ تری‌دی',
            'description' => 'با استودیوی چاپ سه‌بعدی افگ تماس بگیرید. تلفن، ایمیل، آدرس و ساعات کاری ما.',
            'canonical'   => (defined('BASE_URL') ? BASE_URL : '') . '/contact',
        ]);

        $breadcrumbSchema = Seo::schemaBreadcrumb([
            ['name' => 'خانه',      'url' => (defined('BASE_URL') ? BASE_URL : '') . '/'],
            ['name' => 'تماس با ما', 'url' => (defined('BASE_URL') ? BASE_URL : '') . '/contact'],
        ]);

        $this->render('pages.contact', [
            'title'            => 'تماس با ما',
            'siteData'         => $siteData,
            'csrf'             => CSRF::field(),
            'breadcrumbSchema' => $breadcrumbSchema,
        ]);
    }

    // ── POST /contact ────────────────────────────────────────────────────────

    public function contactSubmit(): void
    {
        CSRF::check();

        $name    = trim(htmlspecialchars($_POST['name']    ?? '', ENT_QUOTES, 'UTF-8'));
        $mobile  = preg_replace('/\D/', '', $_POST['mobile'] ?? '');
        $subject = trim(htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES, 'UTF-8'));
        $message = trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'));

        // Validation
        $errors = [];
        if (mb_strlen($name) < 2)    $errors[] = 'نام باید حداقل ۲ حرف باشد.';
        if (strlen($mobile) !== 11)  $errors[] = 'شماره موبایل ۱۱ رقمی وارد کنید.';
        if (mb_strlen($subject) < 3) $errors[] = 'موضوع را وارد کنید.';
        if (mb_strlen($message) < 10)$errors[] = 'پیام باید حداقل ۱۰ کاراکتر باشد.';

        if (!empty($errors)) {
            foreach ($errors as $e) Flash::error($e);
            $this->redirect('/contact');
        }

        // Save to contact_messages table (create if not exists)
        try {
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS contact_messages (
                    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name       VARCHAR(120) NOT NULL,
                    mobile     VARCHAR(20)  NOT NULL,
                    subject    VARCHAR(255) NOT NULL,
                    message    TEXT         NOT NULL,
                    ip         VARCHAR(45)  NOT NULL,
                    is_read    TINYINT(1)   NOT NULL DEFAULT 0,
                    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );

            $stmt = $this->db->prepare(
                "INSERT INTO contact_messages (name, mobile, subject, message, ip)
                 VALUES (:name, :mobile, :subject, :message, :ip)"
            );
            $stmt->execute([
                'name'    => $name,
                'mobile'  => $mobile,
                'subject' => $subject,
                'message' => $message,
                'ip'      => RateLimit::clientIp(),
            ]);
        } catch (PDOException $e) {
            Flash::error('خطایی رخ داد. لطفاً دوباره تلاش کنید.');
            $this->redirect('/contact');
        }

        Flash::success('پیام شما با موفقیت ارسال شد. در اسرع وقت با شما تماس خواهیم گرفت.');
        $this->redirect('/contact');
    }

    // ── GET /privacy ─────────────────────────────────────────────────────────

    public function privacy(): void
    {
        $siteData = $this->siteData();

        Seo::set([
            'title'       => 'حریم خصوصی — افگ تری‌دی',
            'description' => 'سیاست حریم خصوصی و نحوه استفاده از اطلاعات کاربران در سایت افگ تری‌دی.',
            'canonical'   => (defined('BASE_URL') ? BASE_URL : '') . '/privacy',
            'robots'      => 'noindex,follow',
        ]);

        $breadcrumbSchema = Seo::schemaBreadcrumb([
            ['name' => 'خانه',       'url' => (defined('BASE_URL') ? BASE_URL : '') . '/'],
            ['name' => 'حریم خصوصی', 'url' => (defined('BASE_URL') ? BASE_URL : '') . '/privacy'],
        ]);

        $this->render('pages.privacy', [
            'title'            => 'حریم خصوصی',
            'siteData'         => $siteData,
            'breadcrumbSchema' => $breadcrumbSchema,
        ]);
    }

    // ── GET /terms ───────────────────────────────────────────────────────────

    public function terms(): void
    {
        $siteData = $this->siteData();

        Seo::set([
            'title'       => 'قوانین و مقررات — افگ تری‌دی',
            'description' => 'قوانین و شرایط استفاده از خدمات استودیوی چاپ سه‌بعدی افگ.',
            'canonical'   => (defined('BASE_URL') ? BASE_URL : '') . '/terms',
            'robots'      => 'noindex,follow',
        ]);

        $breadcrumbSchema = Seo::schemaBreadcrumb([
            ['name' => 'خانه',   'url' => (defined('BASE_URL') ? BASE_URL : '') . '/'],
            ['name' => 'قوانین', 'url' => (defined('BASE_URL') ? BASE_URL : '') . '/terms'],
        ]);

        $this->render('pages.terms', [
            'title'            => 'قوانین و مقررات',
            'siteData'         => $siteData,
            'breadcrumbSchema' => $breadcrumbSchema,
        ]);
    }

    // ── GET /services ────────────────────────────────────────────────────────

    public function services(): void
    {
        $siteData = $this->siteData();

        Seo::set([
            'title'       => 'خدمات چاپ سه‌بعدی — افگ تری‌دی',
            'description' => 'خدمات چاپ سه‌بعدی FDM، رزینی و ماکت معماری با دقت بالا و قیمت مناسب.',
            'canonical'   => (defined('BASE_URL') ? BASE_URL : '') . '/services',
        ]);

        $breadcrumbSchema = Seo::schemaBreadcrumb([
            ['name' => 'خانه',   'url' => (defined('BASE_URL') ? BASE_URL : '') . '/'],
            ['name' => 'خدمات',  'url' => (defined('BASE_URL') ? BASE_URL : '') . '/services'],
        ]);

        $this->render('pages.services', [
            'title'            => 'خدمات ما',
            'siteData'         => $siteData,
            'breadcrumbSchema' => $breadcrumbSchema,
        ]);
    }

    // ── POST /contact (alias for contactSubmit) ───────────────────────────────

    public function submitContact(): void
    {
        $this->contactSubmit();
    }
}
