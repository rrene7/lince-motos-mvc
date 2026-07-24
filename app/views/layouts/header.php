<?php $pageTitle = isset($title) ? $title . ' | ' . APP_NAME : APP_NAME; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= e(base_url('public/assets/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_url('public/assets/css/moto.css')) ?>">
</head>
<body>
<?php if (Auth::check()): ?>
<header class="topbar">
    <div>
        <strong><?= e(APP_NAME) ?></strong>
        <span class="subtitle">Taller de la Policía Nacional</span>
    </div>
    <div class="user-box">
        <?= e(Auth::user()['nombre']) ?> · <?= e(Auth::user()['rol']) ?>
        <a href="<?= e(base_url('logout')) ?>">Salir</a>
    </div>
</header>
<nav class="nav">
    <a href="<?= e(base_url('dashboard')) ?>">Panel</a>
    <a href="<?= e(base_url('motos')) ?>">Motocicletas</a>
    <a href="<?= e(base_url('mantenimientos')) ?>">Mantenimientos</a>
</nav>
<?php endif; ?>
<main class="container <?= Auth::check() ? '' : 'login-container' ?>">
    <?php if ($message = flash('success')): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>
    <?php if ($message = flash('error')): ?><div class="alert alert-danger"><?= e($message) ?></div><?php endif; ?>