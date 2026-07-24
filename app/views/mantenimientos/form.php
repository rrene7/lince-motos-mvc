<div class="page-heading"><div><h1>Registrar mantenimiento</h1><p>Documente la intervención y programe el próximo servicio.</p></div></div>
<form method="post" action="<?= e(base_url('mantenimientos/guardar')) ?>" class="panel form-grid">
    <?= Csrf::field() ?>
    <label class="full">Motocicleta *
        <select name="moto_id" required>
            <option value="">Seleccione</option>
            <?php foreach ($motos as $moto): ?>
                <option value="<?= (int)$moto['id'] ?>" <?= $motoId === (int)$moto['id'] ? 'selected' : '' ?>><?= e($moto['codigo_qr'] . ' · ' . $moto['placa'] . ' · ' . $moto['marca'] . ' ' . $moto['modelo']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Fecha *<input type="date" name="fecha" required value="<?= date('Y-m-d') ?>"></label>
    <label>Kilometraje *<input type="number" name="kilometraje" min="0" required></label>
    <label>Tipo
        <select name="tipo"><option>Preventivo</option><option>Correctivo</option><option>Reparación por daño</option><option>Colisión</option><option>Inspección</option></select>
    </label>
    <label>Estado
        <select name="estado"><option>En proceso</option><option>Finalizado</option><option>Pendiente de repuesto</option><option>Pendiente de presupuesto</option></select>
    </label>
    <label class="full">Diagnóstico<textarea name="diagnostico" rows="3"></textarea></label>
    <label class="full">Trabajos realizados<textarea name="trabajos_realizados" rows="4" placeholder="Cambio de aceite, ajuste de cadena..."></textarea></label>
    <label class="full">Repuestos utilizados<textarea name="repuestos_utilizados" rows="3" placeholder="Aceite, filtro, pastillas..."></textarea></label>
    <label>Responsable *<input name="responsable" required></label>
    <label>Próximo mantenimiento (km)<input type="number" name="proximo_km" min="0"></label>
    <label>Próximo mantenimiento (fecha)<input type="date" name="proxima_fecha"></label>
    <div class="full form-actions"><a class="btn" href="<?= e(base_url('mantenimientos')) ?>">Cancelar</a><button class="btn btn-primary" type="submit">Guardar mantenimiento</button></div>
</form>
