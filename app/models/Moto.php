<?php
declare(strict_types=1);

final class Moto
{
    public function all(string $search = ''): array
    {
        $sql = 'SELECT * FROM motocicletas';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE codigo_qr LIKE :search OR placa LIKE :search OR marca LIKE :search OR modelo LIKE :search OR unidad_asignada LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY id DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM motocicletas WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $moto = $stmt->fetch();
        return $moto ?: null;
    }

    public function existsByField(string $field, string $value, ?int $excludeId = null): bool
    {
        $allowedFields = ['codigo_qr', 'placa', 'numero_motor', 'numero_chasis'];
        if (!in_array($field, $allowedFields, true)) {
            throw new InvalidArgumentException('Campo de búsqueda no permitido.');
        }

        $sql = "SELECT COUNT(*) FROM motocicletas WHERE {$field} = :value";
        $params = ['value' => $value];

        if ($excludeId !== null && $excludeId > 0) {
            $sql .= ' AND id <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO motocicletas
            (codigo_qr, marca, modelo, anio, placa, numero_motor, numero_chasis, unidad_asignada, foto, fecha_ingreso, kilometraje_actual, tipo_mantenimiento, estado, observaciones)
            VALUES
            (:codigo_qr, :marca, :modelo, :anio, :placa, :numero_motor, :numero_chasis, :unidad_asignada, :foto, :fecha_ingreso, :kilometraje_actual, :tipo_mantenimiento, :estado, :observaciones)';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($data);
        return (int)Database::connection()->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $sql = 'UPDATE motocicletas SET
            codigo_qr = :codigo_qr,
            marca = :marca,
            modelo = :modelo,
            anio = :anio,
            placa = :placa,
            numero_motor = :numero_motor,
            numero_chasis = :numero_chasis,
            unidad_asignada = :unidad_asignada,
            foto = :foto,
            fecha_ingreso = :fecha_ingreso,
            kilometraje_actual = :kilometraje_actual,
            tipo_mantenimiento = :tipo_mantenimiento,
            estado = :estado,
            observaciones = :observaciones
            WHERE id = :id';
        Database::connection()->prepare($sql)->execute($data);
    }

    public function delete(int $id): void
    {
        Database::connection()->prepare('DELETE FROM motocicletas WHERE id = :id')->execute(['id' => $id]);
    }

    public function countsByStatus(): array
    {
        $rows = Database::connection()->query('SELECT estado, COUNT(*) total FROM motocicletas GROUP BY estado')->fetchAll();
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['estado']] = (int)$row['total'];
        }
        return $counts;
    }

    public function total(): int
    {
        return (int)Database::connection()->query('SELECT COUNT(*) FROM motocicletas')->fetchColumn();
    }
}
