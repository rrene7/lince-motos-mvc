<?php
declare(strict_types=1);

final class User
{
    public function findByEmail(string $correo): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM usuarios WHERE correo = :correo AND activo = 1 LIMIT 1');
        $stmt->execute(['correo' => $correo]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
