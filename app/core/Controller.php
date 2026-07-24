<?php
declare(strict_types=1);

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = APP_PATH . '/views/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException('Vista no encontrada: ' . $view);
        }

        require APP_PATH . '/views/layouts/header.php';
        require $viewFile;
        require APP_PATH . '/views/layouts/footer.php';
    }

    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $route = trim((string)($_GET['url'] ?? ''), '/');
            $query = $_GET;
            unset($query['url']);

            if ($route !== '' && $route !== 'login') {
                $_SESSION['_intended_route'] = $route . ($query ? '?' . http_build_query($query) : '');
            }

            flash('error', 'Debe iniciar sesión para continuar.');
            redirect('login');
        }
    }
}
