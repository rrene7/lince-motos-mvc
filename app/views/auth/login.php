<section class="login-card">
    <h1>Acceso al sistema</h1>
    <p>Administración de motocicletas LINCE</p>
    <form method="post" action="<?= e(base_url('login')) ?>" class="form-grid one-column">
        <?= Csrf::field() ?>
        <label>Correo institucional
            <input type="email" name="correo" required value="<?= old('correo', 'admin@lince.local') ?>">
        </label>
        <label>Contraseña
            <input type="password" name="clave" required value="Admin1234">
        </label>
        <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>
    <small>Usuario inicial: admin@lince.local / Admin1234</small>
</section>
