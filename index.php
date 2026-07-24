<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/app/config/config.php';
require_once APP_PATH . '/core/helpers.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/core/Csrf.php';

spl_autoload_register(function (string $class): void {
    foreach ([APP_PATH . '/controllers/', APP_PATH . '/models/'] as $directory) {
        $file = $directory . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

$url = trim((string)($_GET['url'] ?? ''), '/');
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$routes = [
    'GET' => [
        '' => [DashboardController::class, 'index'],
        'login' => [AuthController::class, 'showLogin'],
        'logout' => [AuthController::class, 'logout'],
        'dashboard' => [DashboardController::class, 'index'],
        'motos' => [MotosController::class, 'index'],
        'motos/crear' => [MotosController::class, 'create'],
        'motos/ver' => [MotosController::class, 'show'],
        'motos/editar' => [MotosController::class, 'edit'],
        'mantenimientos' => [MantenimientosController::class, 'index'],
        'mantenimientos/crear' => [MantenimientosController::class, 'create'],
    ],
    'POST' => [
        'login' => [AuthController::class, 'login'],
        'motos/guardar' => [MotosController::class, 'store'],
        'motos/actualizar' => [MotosController::class, 'update'],
        'motos/eliminar' => [MotosController::class, 'destroy'],
        'mantenimientos/guardar' => [MantenimientosController::class, 'store'],
    ],
];

$route = $routes[$method][$url] ?? null;

if ($route === null) {
    http_response_code(404);
    require APP_PATH . '/views/errors/404.php';
    exit;
}

[$controllerClass, $action] = $route;
$controller = new $controllerClass();
$controller->$action();
