<?php
declare(strict_types=1);

define('APP_PATH', dirname(__DIR__));
define('ROOT_PATH', dirname(APP_PATH));

define('APP_NAME', 'Gestión de Motocicletas LINCE');

$localConfigFile = __DIR__ . '/config.local.php';
$localConfig = is_file($localConfigFile) ? require $localConfigFile : [];

if (!is_array($localConfig)) {
    throw new RuntimeException('app/config/config.local.php debe retornar un arreglo.');
}

$getConfig = static function (string $key, string $environment, string $default) use ($localConfig): string {
    $environmentValue = getenv($environment);

    if ($environmentValue !== false && $environmentValue !== '') {
        return $environmentValue;
    }

    return isset($localConfig[$key]) ? (string)$localConfig[$key] : $default;
};

define('DB_HOST', $getConfig('db_host', 'LINCE_DB_HOST', '127.0.0.1'));
define('DB_PORT', $getConfig('db_port', 'LINCE_DB_PORT', '3306'));
define('DB_NAME', $getConfig('db_name', 'LINCE_DB_NAME', 'lince_motos'));
define('DB_USER', $getConfig('db_user', 'LINCE_DB_USER', 'root'));
define('DB_PASS', $getConfig('db_pass', 'LINCE_DB_PASS', ''));
