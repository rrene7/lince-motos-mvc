<div class="page-heading">
    <div><h1>Motocicletas</h1><p>Expedientes individuales de la flota LINCE.</p></div>
    <a class="btn btn-primary" href="<?= e(base_url('motos/crear')) ?>">+ Nueva motocicleta</a>
</div>
<form class="search-bar" method="get" action="<?= e(base_url('motos')) ?>">
    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Código, placa, marca, modelo o unidad">
    <button class="btn" type="submit">Buscar</button>
</form>
<div class="table-wrap panel">
<table>
    <thead><tr><th>Foto</th><th>Código</th><th>Motocicleta</th><th>Placa</th><th>Unidad</th><th>Kilometraje</th><th>Estado</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($motos as $moto): ?>
        <tr>
            <td>
                <?php if (!empty($moto['foto'])): ?>
                    <img class="moto-thumb" src="<?= e(base_url($moto['foto'])) ?>" alt="">
                <?php else: ?>
                    <span class="moto-thumb-empty" aria-label="Sin fotografía">🏍️</span>
                <?php endif; ?>
            </td>
            <td><strong><?= e($moto['codigo_qr']) ?></strong></td>
            <td><?= e($moto['marca'] . ' ' . $moto['modelo']) ?><br><small>Año <?= e((string)$moto['anio']) ?></small></td>
            <td><?= e($moto['placa']) ?></td>
            <td><?= e($moto['unidad_asignada']) ?></td>
            <td><?= number_format((int)$moto['kilometraje_actual']) ?> km</td>
            <td><span class="badge <?= e(status_badge($moto['estado'])) ?>"><?= e($moto['estado']) ?></span></td>
            <td class="actions"><a class="btn btn-small" href="<?= e(base_url('motos/ver?id=' . $moto['id'])) ?>">Ver expediente</a></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$motos): ?><tr><td colspan="8" class="empty">No se encontraron motocicletas.</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
