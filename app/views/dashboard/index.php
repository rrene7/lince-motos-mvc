<div class="page-heading">
    <div>
        <h1>Disponibilidad operativa</h1>
        <p>Resumen actualizado del parque de motocicletas.</p>
    </div>
    <a class="btn btn-primary" href="<?= e(base_url('motos/crear')) ?>">+ Registrar moto</a>
</div>

<section class="cards">
    <article class="card"><span>Total</span><strong><?= (int)$total ?></strong></article>
    <article class="card success"><span>Operativas</span><strong><?= (int)($counts['Operativa'] ?? 0) ?></strong></article>
    <article class="card warning"><span>Mantenimiento</span><strong><?= (int)($counts['En mantenimiento'] ?? 0) ?></strong></article>
    <article class="card danger"><span>Reparación</span><strong><?= (int)($counts['Reparación'] ?? 0) ?></strong></article>
    <article class="card dark"><span>Colisión</span><strong><?= (int)($counts['Colisión'] ?? 0) ?></strong></article>
    <article class="card muted"><span>Descarte</span><strong><?= (int)($counts['Trámite de descarte'] ?? 0) ?></strong></article>
</section>

<section class="panel">
    <div class="panel-header"><h2>Alertas de mantenimiento</h2></div>
    <?php if (!$alerts): ?>
        <p class="empty">No existen alertas próximas.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Moto</th><th>Actual</th><th>Próximo km</th><th>Próxima fecha</th><th>Acción</th></tr></thead>
                <tbody>
                <?php foreach ($alerts as $alert): ?>
                    <tr>
                        <td><strong><?= e($alert['codigo_qr']) ?></strong><br><small><?= e($alert['placa']) ?></small></td>
                        <td><?= number_format((int)$alert['kilometraje_actual']) ?> km</td>
                        <td><?= $alert['proximo_km'] ? number_format((int)$alert['proximo_km']) . ' km' : '—' ?></td>
                        <td><?= e($alert['proxima_fecha'] ?: '—') ?></td>
                        <td><a class="btn btn-small" href="<?= e(base_url('mantenimientos/crear?moto_id=' . $alert['moto_id'])) ?>">Atender</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
