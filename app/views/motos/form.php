<?php
$isEdit = is_array($moto);
$value = function (string $field, string $default = '') use ($moto): string {
    return old($field, $moto[$field] ?? $default);
};
$currentPhoto = $isEdit ? ($moto['foto'] ?? null) : null;
?>
<div class="page-heading">
    <div>
        <h1><?= $isEdit ? 'Editar motocicleta' : 'Registrar motocicleta' ?></h1>
        <p>Complete los datos principales. La fotografía puede tomarse directamente desde el celular.</p>
    </div>
</div>
<form method="post" enctype="multipart/form-data" action="<?= e(base_url($isEdit ? 'motos/actualizar' : 'motos/guardar')) ?>" class="panel form-grid" id="moto-form">
    <?= Csrf::field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$moto['id'] ?>"><?php endif; ?>

    <section class="photo-field full">
        <div class="photo-preview-wrap">
            <img
                id="photo-preview"
                class="photo-preview <?= $currentPhoto ? '' : 'is-hidden' ?>"
                src="<?= $currentPhoto ? e(base_url($currentPhoto)) : '' ?>"
                alt="Vista previa de la motocicleta"
            >
            <div id="photo-placeholder" class="photo-placeholder <?= $currentPhoto ? 'is-hidden' : '' ?>">
                <span>🏍️</span>
                <strong>Sin fotografía</strong>
            </div>
        </div>
        <div class="photo-controls">
            <label for="foto">Fotografía de la motocicleta</label>
            <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp" capture="environment">
            <small>Formatos JPG, PNG o WEBP. Tamaño máximo: 5 MB.</small>
            <?php if ($currentPhoto): ?>
                <label class="check-label"><input type="checkbox" name="remove_photo" value="1"> Eliminar la fotografía actual</label>
            <?php endif; ?>
        </div>
    </section>

    <div class="section-title full"><h2>Identificación</h2><p>Datos visibles para reconocer la motocicleta.</p></div>
    <label>Código institucional *<input name="codigo_qr" required value="<?= $value('codigo_qr') ?>" placeholder="LINCE-M-001"></label>
    <label>Placa *<input name="placa" required value="<?= $value('placa') ?>"></label>
    <label>Marca *<input name="marca" required value="<?= $value('marca', 'Suzuki') ?>"></label>
    <label>Modelo *<input name="modelo" required value="<?= $value('modelo') ?>" placeholder="DR 150"></label>
    <label>Año<input type="number" name="anio" min="1990" max="2100" value="<?= $value('anio') ?>"></label>
    <label>Unidad asignada *<input name="unidad_asignada" required value="<?= $value('unidad_asignada', 'LINCE San Miguelito') ?>"></label>

    <div class="section-title full"><h2>Control técnico</h2><p>Información utilizada por el taller y el mantenimiento preventivo.</p></div>
    <label>Número de motor<input name="numero_motor" value="<?= $value('numero_motor') ?>"></label>
    <label>Número de chasis<input name="numero_chasis" value="<?= $value('numero_chasis') ?>"></label>
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
    <label class="full">Observaciones<textarea name="observaciones" rows="4" placeholder="Anote condiciones especiales, daños visibles o información útil para el taller."><?= $value('observaciones') ?></textarea></label>
    <div class="full form-actions sticky-actions">
        <a class="btn" href="<?= e(base_url($isEdit ? 'motos/ver?id=' . $moto['id'] : 'motos')) ?>">Cancelar</a>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Guardar cambios' : 'Registrar motocicleta' ?></button>
    </div>
</form>
<script src="<?= e(base_url('public/assets/js/moto-form.js')) ?>"></script>
