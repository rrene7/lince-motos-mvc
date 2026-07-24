<?php
$isEdit = is_array($moto);
$value = function (string $field, string $default = '') use ($moto): string {
    return old($field, $moto[$field] ?? $default);
};
?>
<div class="page-heading"><div><h1><?= $isEdit ? 'Editar motocicleta' : 'Registrar motocicleta' ?></h1><p>Complete la ficha técnica y operativa.</p></div></div>
<form method="post" action="<?= e(base_url($isEdit ? 'motos/actualizar' : 'motos/guardar')) ?>" class="panel form-grid">
    <?= Csrf::field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$moto['id'] ?>"><?php endif; ?>
    <label>Código QR *<input name="codigo_qr" required value="<?= $value('codigo_qr') ?>" placeholder="LINCE-M-001"></label>
    <label>Placa *<input name="placa" required value="<?= $value('placa') ?>"></label>
    <label>Marca *<input name="marca" required value="<?= $value('marca', 'Suzuki') ?>"></label>
    <label>Modelo *<input name="modelo" required value="<?= $value('modelo') ?>" placeholder="DR 150"></label>
    <label>Año<input type="number" name="anio" min="1990" max="2100" value="<?= $value('anio') ?>"></label>
    <label>Número de motor<input name="numero_motor" value="<?= $value('numero_motor') ?>"></label>
    <label>Número de chasis<input name="numero_chasis" value="<?= $value('numero_chasis') ?>"></label>
    <label>Unidad asignada *<input name="unidad_asignada" required value="<?= $value('unidad_asignada', 'LINCE San Miguelito') ?>"></label>
    <label>Fecha de ingreso<input type="date" name="fecha_ingreso" value="<?= $value('fecha_ingreso') ?>"></label>
    <label>Kilometraje actual<input type="number" name="kilometraje_actual" min="0" value="<?= $value('kilometraje_actual', '0') ?>"></label>
    <label>Programación de mantenimiento
        <select name="tipo_mantenimiento">
            <?php foreach (['Por kilometraje', 'Por tiempo', 'Mixto'] as $tipo): ?>
                <option <?= $value('tipo_mantenimiento', 'Mixto') === $tipo ? 'selected' : '' ?>><?= e($tipo) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Estado *
        <select name="estado" required>
            <?php foreach (['Operativa', 'En mantenimiento', 'Reparación', 'Colisión', 'Trámite de descarte', 'Fuera del sistema'] as $estado): ?>
                <option <?= $value('estado', 'Operativa') === $estado ? 'selected' : '' ?>><?= e($estado) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label class="full">Observaciones<textarea name="observaciones" rows="4"><?= $value('observaciones') ?></textarea></label>
    <div class="full form-actions">
        <a class="btn" href="<?= e(base_url('motos')) ?>">Cancelar</a>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Guardar cambios' : 'Registrar motocicleta' ?></button>
    </div>
</form>
