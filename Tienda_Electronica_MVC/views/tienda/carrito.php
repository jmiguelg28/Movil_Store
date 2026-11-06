<?php
$carritoProductos = array();
foreach($productos as $p){
    $carritoProductos[] = array('id'=>(int)$p['cod_producto'], 'nombre'=>$p['nom_producto'], 'precio'=>(float)$p['precio'], 'stock'=>(int)$p['stock'], 'imagen'=>img_producto($p['imagen']));
}
?>
<nav class="migas"><a href="<?= url('tienda') ?>">Tienda</a> › Mi carrito</nav>
<div class="page-header"><span class="eyebrow">TU SELECCIÓN</span><h1>Mi carrito <span class="carrito-conteo" id="carrito-conteo"></span></h1><p>Verifica las cantidades y registra tu venta cuando esté lista.</p></div>
<div class="cart-layout" id="cart-layout">
    <section class="cart-items panel" aria-label="Productos del carrito">
        <div class="cart-heading"><h2>Productos seleccionados</h2><button type="button" class="enlace" id="cart-clear">Vaciar carrito</button></div>
        <div id="cart-list" aria-live="polite"></div>
        <div id="cart-empty" class="cart-empty" hidden><div class="cart-empty-icon"><?= icono('carrito',42) ?></div><h2>Tu carrito está vacío</h2><p>Encuentra productos y agrégalos aquí para comenzar.</p><a href="<?= url('tienda') ?>" class="btn-primary">Explorar el catálogo</a></div>
    </section>
    <aside class="cart-summary panel"><span class="eyebrow">RESUMEN DE COMPRA</span><h2>Tu pedido</h2><div class="cart-summary-line"><span>Productos</span><strong id="cart-units">0</strong></div><div class="cart-summary-line"><span>Subtotal</span><strong id="cart-subtotal">$0</strong></div><div class="cart-summary-total"><span>Total estimado</span><strong id="cart-total">$0</strong></div><p class="cart-disclaimer">Esta tienda funciona como sistema interno. El cobro se registra en el módulo de ventas; no hay pago en línea.</p><a class="btn-primary cart-checkout" id="cart-checkout" href="<?= url('ventas','crear') ?>">Continuar a la venta →</a><a class="cart-continue" href="<?= url('tienda') ?>">← Seguir comprando</a></aside>
</div>
<script>window.CARRITO_PRODUCTOS = <?= json_encode($carritoProductos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
