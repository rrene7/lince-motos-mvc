<?php
declare(strict_types=1);

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'nombre' => $user['nombre'],
            'correo' => $user['correo'],
            'rol' => $user['rol'],
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }
}
