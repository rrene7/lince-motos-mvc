<div class="page-heading">
    <div>
        <h1><?= e($moto['codigo_qr']) ?></h1>
        <p><?= e($moto['marca'] . ' ' . $moto['modelo'] . ' · ' . $moto['placa']) ?></p>
    </div>
    <div class="actions no-print">
        <button class="btn" type="button" data-print-ficha>Imprimir ficha</button>
        <a class="btn" href="<?= e(base_url('motos/editar?id=' . $moto['id'])) ?>">Editar</a>
        <a class="btn btn-primary" href="<?= e(base_url('mantenimientos/crear?moto_id=' . $moto['id'])) ?>">Registrar mantenimiento</a>
    </div>
</div>

<section class="identity-grid">
    <article class="panel moto-photo-card">
        <?php if (!empty($moto['foto'])): ?>
            <img src="<?= e(base_url($moto['foto'])) ?>" alt="Fotografía de <?= e($moto['codigo_qr']) ?>" class="moto-photo">
        <?php else: ?>
            <div class="moto-photo-empty"><span>🏍️</span><strong>Sin fotografía</strong><small>Use Editar para agregarla.</small></div>
        <?php endif; ?>
    </article>

    <article class="panel technical-card">
        <h2>Ficha técnica</h2>
        <dl>
            <dt>Estado</dt><dd><span class="badge <?= e(status_badge($moto['estado'])) ?>"><?= e($moto['estado']) ?></span></dd>
            <dt>Unidad</dt><dd><?= e($moto['unidad_asignada']) ?></dd>
            <dt>Kilometraje</dt><dd><?= number_format((int)$moto['kilometraje_actual']) ?> km</dd>
            <dt>Motor</dt><dd><?= e($moto['numero_motor'] ?: 'No registrado') ?></dd>
            <dt>Chasis</dt><dd><?= e($moto['numero_chasis'] ?: 'No registrado') ?></dd>
            <dt>Ingreso al servicio</dt><dd><?= e($moto['fecha_ingreso'] ?: 'No registrada') ?></dd>
            <dt>Plan</dt><dd><?= e($moto['tipo_mantenimiento']) ?></dd>
        </dl>
        <?php if (!empty($moto['observaciones'])): ?>
            <div class="record-note"><strong>Observaciones</strong><p><?= nl2br(e($moto['observaciones'])) ?></p></div>
        <?php endif; ?>
    </article>

    <article class="panel qr-card">
        <h2>Código QR</h2>
        <div
            id="moto-qr"
            class="qr-output"
            data-qr-url="<?= e($qrUrl) ?>"
            data-qr-name="<?= e($moto['codigo_qr']) ?>"
        ></div>
        <p class="qr-status" id="qr-status">Generando código QR…</p>
        <div class="qr-actions no-print">
            <button class="btn btn-small" type="button" data-copy-qr>Copiar enlace</button>
            <button class="btn btn-small" type="button" data-download-qr disabled>Descargar QR</button>
        </div>
        <?php if ($qrLocalOnly): ?>
            <small class="qr-warning">Para escanearlo desde un celular, abra primero el sistema usando la dirección IP del equipo servidor, no <strong>localhost</strong>.</small>
        <?php else: ?>
            <small>Al escanear, el usuario inicia sesión y vuelve directamente a este expediente.</small>
        <?php endif; ?>
    </article>
</section>

<section class="panel">
    <div class="panel-header"><h2>Historial de mantenimiento</h2></div>
    <div class="table-wrap">
    <table>
        <thead><tr><th>Fecha</th><th>Tipo</th><th>Kilometraje</th><th>Responsable</th><th>Próximo servicio</th><th>Estado</th></tr></thead>
        <tbody>
        <?php foreach ($mantenimientos as $item): ?>
            <tr>
                <td><?= e($item['fecha']) ?></td>
                <td><?= e($item['tipo']) ?></td>
                <td><?= number_format((int)$item['kilometraje']) ?> km</td>
                <td><?= e($item['responsable']) ?></td>
                <td><?= $item['proximo_km'] ? number_format((int)$item['proximo_km']) . ' km' : '' ?> <?= e($item['proxima_fecha'] ?: '') ?></td>
                <td><?= e($item['estado']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$mantenimientos): ?><tr><td colspan="6" class="empty">No hay mantenimientos registrados.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</section>

<form class="no-print" method="post" action="<?= e(base_url('motos/eliminar')) ?>" data-confirm="¿Seguro que desea eliminar este expediente? También se eliminará su historial.">
    <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int)$moto['id'] ?>">
    <button class="btn btn-danger" type="submit">Eliminar expediente</button>
</form>

<script src="<?= e(base_url('public/assets/js/qrcode.min.js')) ?>"></script>
<script src="<?= e(base_url('public/assets/js/moto-show.js')) ?>"></script>
