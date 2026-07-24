<div class="page-heading">
    <div><h1>Mantenimientos</h1><p>Historial general de intervenciones del taller.</p></div>
    <a class="btn btn-primary" href="<?= e(base_url('mantenimientos/crear')) ?>">+ Registrar mantenimiento</a>
</div>
<div class="table-wrap panel">
<table>
    <thead><tr><th>Fecha</th><th>Motocicleta</th><th>Tipo</th><th>Kilometraje</th><th>Responsable</th><th>Estado</th></tr></thead>
    <tbody>
    <?php foreach ($mantenimientos as $item): ?>
        <tr>
            <td><?= e($item['fecha']) ?></td>
            <td><a href="<?= e(base_url('motos/ver?id=' . $item['moto_id'])) ?>"><strong><?= e($item['codigo_qr']) ?></strong></a><br><small><?= e($item['marca'] . ' ' . $item['modelo']) ?></small></td>
            <td><?= e($item['tipo']) ?></td>
            <td><?= number_format((int)$item['kilometraje']) ?> km</td>
            <td><?= e($item['responsable']) ?></td>
            <td><?= e($item['estado']) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$mantenimientos): ?><tr><td colspan="6" class="empty">No hay mantenimientos registrados.</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
