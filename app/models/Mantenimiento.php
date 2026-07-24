<?php
declare(strict_types=1);

final class Mantenimiento
{
    public function all(): array
    {
        $sql = 'SELECT m.*, mo.codigo_qr, mo.placa, mo.marca, mo.modelo
                FROM mantenimientos m
                INNER JOIN motocicletas mo ON mo.id = m.moto_id
                ORDER BY m.fecha DESC, m.id DESC';
        return Database::connection()->query($sql)->fetchAll();
    }

    public function byMoto(int $motoId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM mantenimientos WHERE moto_id = :moto_id ORDER BY fecha DESC, id DESC');
        $stmt->execute(['moto_id' => $motoId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $sql = 'INSERT INTO mantenimientos
                (moto_id, fecha, kilometraje, tipo, diagnostico, trabajos_realizados, repuestos_utilizados, responsable, proximo_km, proxima_fecha, estado, creado_por)
                VALUES
                (:moto_id, :fecha, :kilometraje, :tipo, :diagnostico, :trabajos_realizados, :repuestos_utilizados, :responsable, :proximo_km, :proxima_fecha, :estado, :creado_por)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            $id = (int)$pdo->lastInsertId();

            $pdo->prepare('UPDATE motocicletas SET kilometraje_actual = GREATEST(kilometraje_actual, :km), estado = :estado WHERE id = :id')
                ->execute([
                    'km' => $data['kilometraje'],
                    'estado' => $data['estado'] === 'Finalizado' ? 'Operativa' : 'En mantenimiento',
                    'id' => $data['moto_id'],
                ]);

            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public function upcomingAlerts(): array
    {
        $sql = "SELECT m.*, mo.codigo_qr, mo.kilometraje_actual, mo.placa
                FROM mantenimientos m
                INNER JOIN motocicletas mo ON mo.id = m.moto_id
                WHERE m.id IN (SELECT MAX(id) FROM mantenimientos GROUP BY moto_id)
                  AND (
                    (m.proximo_km IS NOT NULL AND mo.kilometraje_actual >= m.proximo_km - 200)
                    OR
                    (m.proxima_fecha IS NOT NULL AND m.proxima_fecha <= DATE_ADD(CURDATE(), INTERVAL 15 DAY))
                  )
                ORDER BY m.proxima_fecha ASC, m.proximo_km ASC";
        return Database::connection()->query($sql)->fetchAll();
    }
}
