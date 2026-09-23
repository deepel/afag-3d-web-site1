<?php
declare(strict_types=1);
/**
 * afag3d — Base Controller
 */
abstract class BaseController
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Render a view inside a layout.
     *
     * @param string $view   Dot-separated path relative to Views/, e.g. 'home.index'
     * @param array  $data   Variables to extract into the view scope
     * @param string $layout Layout file name without .php, e.g. 'main' | 'admin'
     */
    protected function render(string $view, array $viewData = [], string $layout = 'main'): void
    {
        $__viewsDir   = dirname(__DIR__) . '/Views/';
        $__viewFile   = $__viewsDir . str_replace('.', '/', $view) . '.php';
        $__layoutFile = $__viewsDir . 'layouts/' . $layout . '.php';

        if (!file_exists($__viewFile)) {
            http_response_code(500);
            die('View not found: ' . htmlspecialchars($__viewFile));
        }
        if (!file_exists($__layoutFile)) {
            http_response_code(500);
            die('Layout not found: ' . htmlspecialchars($__layoutFile));
        }

        // Make data available in view. Prefixed locals above avoid clobbering
        // view keys (e.g. a 'data' key would otherwise collide and be skipped).
        extract($viewData, EXTR_OVERWRITE);

        // Capture view output
        ob_start();
        include $__viewFile;
        $content = ob_get_clean();

        // Render layout (which embeds $content)
        include $__layoutFile;
    }

    /**
     * Redirect to a path (relative to BASE_URL).
     */
    protected function redirect(string $path): never
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    /**
     * Return a 404 response.
     */
    public function error404(): void
    {
        http_response_code(404);
        $this->render('errors.404', ['title' => 'صفحه یافت نشد']);
    }

    /**
     * Return a 500 response.
     */
    public function error500(string $message = ''): never
    {
        http_response_code(500);
        $this->render('errors.500', ['title' => 'خطای سرور', 'message' => $message]);
        exit;
    }

    /**
     * Require the user to be logged in; redirect to /login otherwise.
     */
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            Flash::set('error', 'برای دسترسی به این صفحه باید وارد شوید.');
            $this->redirect('/login');
        }
    }

    /**
     * Require the user to have admin role.
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            $this->render('errors.404', ['title' => 'دسترسی ممنوع']);
            exit;
        }
    }

    /**
     * Return JSON (for AJAX endpoints).
     */
    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Get sanitised POST value.
     */
    protected function post(string $key, string $default = ''): string
    {
        return trim(htmlspecialchars($_POST[$key] ?? $default, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Get raw POST value (for passwords etc. — do not HTML-escape before hashing).
     */
    protected function postRaw(string $key, string $default = ''): string
    {
        return $_POST[$key] ?? $default;
    }
}
