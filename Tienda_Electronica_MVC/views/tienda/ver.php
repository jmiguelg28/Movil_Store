<?php list($txtStock, $clsStock) = estado_stock($p['stock']); ?>
<nav class="migas" aria-label="Ruta de navegación">
    <a href="<?= url('tienda') ?>">Tienda</a> ›
    <a href="<?= url('tienda', 'index', array('tipo' => $p['cod_tipo_p'])) ?>"><?= e($p['nom_tipo_p']) ?></a> ›
    <a href="<?= url('tienda', 'index', array('marca' => $p['cod_marca'])) ?>"><?= e($p['nom_marca']) ?></a>
</nav>

<div class="detalle-prod">
    <div class="detalle-img">
        <img src="<?= e(img_producto($p['imagen'])) ?>" alt="<?= e($p['nom_producto']) ?>" onerror="this.onerror=null;this.src='assets/img/sin-imagen.svg'">
    </div>

    <div class="detalle-info">
        <span class="card-marca"><?= e($p['nom_marca']) ?> · Código #<?= (int)$p['cod_producto'] ?></span>
        <h1><?= e($p['nom_producto']) ?></h1>
        <div class="detalle-precio"><?= dinero($p['precio']) ?> <small>COP</small></div><p class="detail-intro">Disponible en el catálogo de Movill Store. Agrega al carrito y completa la venta desde el panel.</p>
        <span class="etiqueta <?= $clsStock ?>"><?= e($txtStock) ?></span>

        <?php if($p['colores']): ?>
            <div class="detalle-bloque"><strong>Colores disponibles</strong>
                <div class="checks"><?php foreach($p['colores'] as $c): ?><span class="pildora"><?= e($c) ?></span><?php endforeach; ?></div>
            </div>
        <?php endif; ?>

        <?php if($p['stock'] > 0): ?>
            <div class="detalle-bloque">
                <strong>Cantidad</strong>
                <div class="cantidad-ctl">
                    <button type="button" data-menos aria-label="Menos">−</button>
                    <input type="number" id="cant-detalle" value="1" min="1" max="<?= (int)$p['stock'] ?>">
                    <button type="button" data-mas aria-label="Más">+</button>
                    <small>(<?= (int)$p['stock'] ?> disponibles)</small>
                </div>
            </div>
            <div class="detalle-botones">
                <button type="button" class="btn-primary" data-add="<?= (int)$p['cod_producto'] ?>" data-ir="1" data-cant="#cant-detalle"
                        data-nombre="<?= e($p['nom_producto']) ?>" data-precio="<?= e((float)$p['precio']) ?>" data-stock="<?= (int)$p['stock'] ?>">Comprar ahora</button>
                <button type="button" class="btn-secundario" data-add="<?= (int)$p['cod_producto'] ?>" data-cant="#cant-detalle"
                        data-nombre="<?= e($p['nom_producto']) ?>" data-precio="<?= e((float)$p['precio']) ?>" data-stock="<?= (int)$p['stock'] ?>">Agregar al carrito</button>
            </div>
        <?php else: ?>
            <div class="detalle-botones">
                <a class="btn-primary" href="<?= url('compras', 'crear') ?>">Registrar compra de este producto</a>
            </div>
        <?php endif; ?>

        <div class="detalle-bloque">
            <a class="btn-sm" href="<?= url('productos', 'editar', array('id' => $p['cod_producto'])) ?>">Editar producto</a>
        </div>
    </div>
</div>

<div class="detalle-extra">
    <div class="panel">
        <h3>Sobre este producto</h3>
        <p class="descripcion"><?= $p['descripcion'] ? nl2br(e($p['descripcion'])) : 'Este producto aún no tiene descripción.' ?></p>
    </div>
    <div class="panel">
        <h3>Características</h3>
        <table class="detalle">
            <tbody>
                <tr><td>Marca</td><td><?= e($p['nom_marca']) ?></td></tr>
                <tr><td>Categoría</td><td><?= e($p['nom_tipo_p']) ?></td></tr>
                <tr><td>Colores</td><td><?= $p['colores'] ? e(implode(', ', $p['colores'])) : '—' ?></td></tr>
                <tr><td>Materiales</td><td><?= $p['materiales'] ? e(implode(', ', $p['materiales'])) : '—' ?></td></tr>
                <tr><td>Unidades en stock</td><td><?= (int)$p['stock'] ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php if($relacionados): ?>
    <h3 class="subtitulo">Otros productos de <?= e($p['nom_tipo_p']) ?></h3>
    <div class="grid-prod">
        <?php foreach($relacionados as $p){ require __DIR__ . '/_tarjeta.php'; } ?>
    </div>
<?php endif; ?>
