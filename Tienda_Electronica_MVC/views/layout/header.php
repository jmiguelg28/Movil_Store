<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#142341">
    <meta name="description" content="Movill Store: catálogo de tecnología, ventas, clientes e inventario en un solo lugar.">
    <title><?= e(isset($tituloPagina) && $tituloPagina ? $tituloPagina . ' · ' : '') ?>Movill Store</title>
    <link rel="stylesheet" href="assets/css/style.css?v=20261103">
    <link rel="stylesheet" href="assets/css/premium.css?v=20261103">
</head>
<body>
<script>try{if(localStorage.getItem('menuCompacto')==='1'&&innerWidth>1000)document.body.classList.add('menu-compacto');if(localStorage.getItem('movill-tema')==='oscuro')document.body.classList.add('tema-oscuro')}catch(e){}</script>
<div class="utility-strip"><span>Movill <strong>Store</strong> / Tecnología para todos</span><span class="utility-right">Gestión de tienda · Precios en COP · <?= e(date('Y')) ?></span></div>
<header class="topbar">
    <button type="button" id="btn-menu" class="topbar-btn" aria-label="Abrir o contraer menú" aria-expanded="false"><?= icono('menu', 22) ?></button>
    <a class="marca" href="<?= url('tienda') ?>"><span class="marca-logo">M<span class="marca-punto">.</span></span><span>movill<span class="marca-acento">store</span></span></a>
    <form class="topbar-buscar" method="GET" action="index.php" role="search">
        <input type="hidden" name="c" value="tienda">
        <input type="search" name="q" placeholder="Busca celulares, laptops, accesorios..." value="<?= e(isset($_GET['q']) && ($_GET['c'] ?? '') === 'tienda' && is_string($_GET['q']) ? $_GET['q'] : '') ?>" aria-label="Buscar productos">
        <button aria-label="Buscar productos"><?= icono('lupa', 19) ?><span>Buscar</span></button>
    </form>
    <div class="topbar-der">
        <a class="topbar-tienda" href="<?= url('tienda') ?>" title="Ver catálogo">Catálogo</a>
        <button class="topbar-tema topbar-btn" type="button" id="btn-tema" title="Cambiar tema" aria-label="Cambiar tema">◐</button>
        <a class="topbar-carrito" href="<?= url('tienda','carrito') ?>" title="Ver carrito" aria-label="Ver carrito">
            <?= icono('carrito', 21) ?><span class="badge-carrito" id="badge-carrito" hidden>0</span>
        </a>
        <?php
            $uSesion = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : array('nombre' => 'Invitado', 'usuario' => '');
            $partes = preg_split('/\s+/', trim($uSesion['nombre']));
            $iniciales = mb_strtoupper(mb_substr($partes[0], 0, 1) . (isset($partes[1]) ? mb_substr($partes[1], 0, 1) : ''));
        ?>
        <details class="usuario-menu">
            <summary class="avatar" title="<?= e($uSesion['nombre']) ?>" aria-label="Menú de usuario"><?= e($iniciales) ?></summary>
            <div class="usuario-panel">
                <div class="usuario-datos"><strong><?= e($uSesion['nombre']) ?></strong><small><?= e($uSesion['usuario']) ?></small></div>
                <a href="<?= url('auth', 'cuenta') ?>">Mi cuenta y seguridad</a>
                <form method="POST" action="<?= url('auth', 'salir') ?>">
                    <?= csrf_field() ?>
                    <button class="salir" type="submit">Cerrar sesión</button>
                </form>
            </div>
        </details>
    </div>
</header>
<div class="layout">
