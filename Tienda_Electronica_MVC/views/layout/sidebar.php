<?php
function activo($ruta, $actual){ return $ruta === $actual ? 'activo' : ''; }
$esCat = ($rutaActual === 'catalogo');
?>
<aside class="sidebar" id="menu-lateral">
    <div class="tienda">
        <span class="marca-logo oscuro">M</span>
        <div class="txt"><strong>Movill Store</strong><small>Panel de administración</small></div>
    </div>
    <p class="sidebar-label txt">EXPLORAR</p>
    <nav aria-label="Navegación principal">
        <a class="<?= activo('tienda', $rutaActual) ?>" href="<?= url('tienda') ?>" title="Explorar tienda"><?= icono('tienda') ?><span class="txt">Explorar tienda</span></a>
        <a class="<?= activo('dashboard', $rutaActual) ?>" href="<?= url('dashboard') ?>" title="Panel general"><?= icono('inicio') ?><span class="txt">Panel general</span></a>
        <p class="sidebar-label txt">OPERACIONES</p>
        <a class="<?= activo('ventas', $rutaActual) ?>" href="<?= url('ventas') ?>" title="Ventas"><?= icono('carrito') ?><span class="txt">Ventas</span></a>
        <a class="<?= activo('compras', $rutaActual) ?>" href="<?= url('compras') ?>" title="Compras"><?= icono('camion') ?><span class="txt">Compras</span></a>
        <a class="<?= activo('productos', $rutaActual) ?>" href="<?= url('productos') ?>" title="Productos"><?= icono('caja') ?><span class="txt">Productos e inventario</span></a>
        <p class="sidebar-label txt">ADMINISTRACIÓN</p>
        <a class="<?= activo('clientes', $rutaActual) ?>" href="<?= url('clientes') ?>" title="Clientes"><?= icono('usuarios') ?><span class="txt">Clientes</span></a>
        <a class="<?= activo('proveedores', $rutaActual) ?>" href="<?= url('proveedores') ?>" title="Proveedores"><?= icono('maletin') ?><span class="txt">Proveedores</span></a>
        <details class="submenu" <?= $esCat ? 'open' : '' ?>>
            <summary title="Catálogos"><?= icono('capas') ?><span class="txt">Catálogos</span></summary>
            <?php require_once BASE_PATH . '/controllers/CatalogoController.php';
            foreach(CatalogoController::$catalogos as $clave => $def): ?>
                <a class="sub <?= ($esCat && $catActual === $clave) ? 'activo' : '' ?>" href="<?= url('catalogo', 'index', array('t' => $clave)) ?>"><?= e($def[0]) ?></a>
            <?php endforeach; ?>
        </details>
    </nav>
    <div class="sidebar-bottom txt"><strong>Todo conectado</strong><small>Catálogo + Inventario + Ventas</small><span class="status-dot">Sistema de gestión</span></div>
</aside>
<button type="button" class="sidebar-overlay" id="cerrar-menu" aria-label="Cerrar menú"></button>
<main class="contenido" id="contenido-principal">
<?php if($mensajeFlash = get_flash()): ?>
    <div class="alerta alerta-<?= e($mensajeFlash['tipo']) ?>" role="status"><?= e($mensajeFlash['msg']) ?></div>
<?php endif; ?>
