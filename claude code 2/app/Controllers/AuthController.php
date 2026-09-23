<?php
declare(strict_types=1);
/**
 * afag3d — Auth Controller
 */
class AuthController extends BaseController
{
    private User $userModel;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->userModel = new User($db);
    }

    /** GET /login */
    public function loginForm(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? '/admin' : '/');
        }
        $this->render('auth.login', ['title' => 'ورود به حساب کاربری', 'csrf' => CSRF::field()]);
    }

    /** POST /login */
    public function login(): void
    {
        CSRF::check();

        $mobile   = preg_replace('/\D/', '', $this->post('mobile'));
        $password = $this->postRaw('password');
        $ip       = RateLimit::getIp();

        // Rate limit check
        if (!RateLimit::check($this->db, 'login', $ip, 5, 900)) {
            Flash::error('تعداد تلاش‌های ناموفق زیاد است. ۱۵ دقیقه صبر کنید.');
            $this->redirect('/login');
            return;
        }

        // Validate inputs
        if (strlen($mobile) !== 11 || !str_starts_with($mobile, '09')) {
            Flash::error('شماره موبایل وارد شده صحیح نیست.');
            $this->redirect('/login');
            return;
        }

        if (empty($password)) {
            Flash::error('رمز عبور را وارد کنید.');
            $this->redirect('/login');
            return;
        }

        // Fetch user
        $user = $this->userModel->findByMobile($mobile);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            RateLimit::hit($this->db, 'login', $ip);
            Flash::error('شماره موبایل یا رمز عبور اشتباه است.');
            $this->redirect('/login');
            return;
        }

        if ($user['status'] !== 'active') {
            Flash::error('حساب کاربری شما غیرفعال یا مسدود شده است.');
            $this->redirect('/login');
            return;
        }

        // Success
        RateLimit::clear($this->db, 'login', $ip);
        $this->userModel->updateLastLogin((int) $user['id']);
        Auth::login($user);

        Flash::success('خوش آمدید، ' . $user['name'] . '!');
        $this->redirect(Auth::isAdmin() ? '/admin' : '/');
    }

    /** GET /register */
    public function registerForm(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->render('auth.register', ['title' => 'ثبت‌نام', 'csrf' => CSRF::field()]);
    }

    /** POST /register */
    public function register(): void
    {
        CSRF::check();

        $name     = $this->post('name');
        $mobile   = preg_replace('/\D/', '', $this->post('mobile'));
        $password = $this->postRaw('password');
        $confirm  = $this->postRaw('password_confirm');

        // Validation
        $errors = [];

        if (mb_strlen($name) < 2) {
            $errors[] = 'نام باید حداقل ۲ حرف باشد.';
        }
        if (strlen($mobile) !== 11 || !str_starts_with($mobile, '09')) {
            $errors[] = 'شماره موبایل وارد شده صحیح نیست.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'رمز عبور باید حداقل ۸ کاراکتر باشد.';
        }
        if ($password !== $confirm) {
            $errors[] = 'رمز عبور و تکرار آن مطابقت ندارند.';
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'رمز عبور باید شامل حداقل یک حرف بزرگ انگلیسی و یک عدد باشد.';
        }

        if (!empty($errors)) {
            foreach ($errors as $err) Flash::error($err);
            $this->redirect('/register');
        }

        // Check duplicate
        if ($this->userModel->mobileExists($mobile)) {
            Flash::error('این شماره موبایل قبلاً ثبت شده است.');
            $this->redirect('/register');
        }

        // Create user
        $userId = $this->userModel->create($name, $mobile, $password);
        $user   = $this->userModel->find($userId);

        Auth::login($user);
        Flash::success('ثبت‌نام با موفقیت انجام شد. خوش آمدید!');
        $this->redirect('/');
    }

    /** GET|POST /logout */
    public function logout(): void
    {
        if ($this->post('_csrf') !== '' || isset($_SERVER['HTTP_REFERER'])) {
            // POST logout with CSRF or GET from nav link
        }
        Auth::logout();
        Flash::info('از حساب کاربری خارج شدید.');
        $this->redirect('/login');
    }
}
