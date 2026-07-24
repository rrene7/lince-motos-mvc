<?php
declare(strict_types=1);

function base_url(string $path = ''): string
{
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $base = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
    return $base . ($path !== '' ? '/' . ltrim($path, '/') : '');
}

function redirect(string $path): never
{
    header('Location: ' . base_url($path));
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

function old(string $key, string $default = ''): string
{
    return e((string)($_SESSION['_old'][$key] ?? $default));
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function status_badge(string $estado): string
{
    return match ($estado) {
        'Operativa' => 'badge-success',
        'En mantenimiento' => 'badge-warning',
        'Reparación' => 'badge-danger',
        'Colisión' => 'badge-dark',
        'Trámite de descarte' => 'badge-muted',
        'Fuera del sistema' => 'badge-black',
        default => 'badge-info',
    };
}
