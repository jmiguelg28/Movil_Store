<?php list($txtStock, $clsStock) = estado_stock($p['stock']); ?>
<article class="card-prod<?= $p['stock'] <= 0 ? ' agotado' : '' ?>">
    <a class="card-img" href="<?= url('tienda', 'ver', array('id' => $p['cod_producto'])) ?>" aria-label="Ver <?= e($p['nom_producto']) ?>">
        <img src="<?= e(img_producto($p['imagen'])) ?>" alt="<?= e($p['nom_producto']) ?>" loading="lazy" onerror="this.onerror=null;this.src='assets/img/sin-imagen.svg'">
        <?php if($p['stock'] > 0 && $p['stock'] <= 5): ?><span class="cinta">¡Quedan pocos!</span><?php endif; ?>
        <?php if($p['stock'] <= 0): ?><span class="cinta cinta-agotado">Agotado</span><?php endif; ?>
    </a>
    <div class="card-body">
        <span class="card-marca"><?= e($p['nom_marca']) ?> <span>•</span> <?= e($p['nom_tipo_p']) ?></span>
        <a class="card-nombre" href="<?= url('tienda', 'ver', array('id' => $p['cod_producto'])) ?>"><?= e($p['nom_producto']) ?></a>
        <div class="card-precio"><?= dinero($p['precio']) ?> <small>COP</small></div>
        <span class="stock-hint <?= e($clsStock) ?>"><i></i><?= e($txtStock) ?></span>
        <?php if($p['stock'] > 0): ?>
            <div class="card-actions">
                <button type="button" class="btn-primary" data-add="<?= (int)$p['cod_producto'] ?>" data-ir="1" data-nombre="<?= e($p['nom_producto']) ?>" data-precio="<?= e((float)$p['precio']) ?>" data-stock="<?= (int)$p['stock'] ?>">Comprar ahora</button>
                <button type="button" class="btn-add-cart" aria-label="Agregar <?= e($p['nom_producto']) ?> al carrito" title="Agregar al carrito" data-add="<?= (int)$p['cod_producto'] ?>" data-nombre="<?= e($p['nom_producto']) ?>" data-precio="<?= e((float)$p['precio']) ?>" data-stock="<?= (int)$p['stock'] ?>"><?= icono('carrito',19) ?></button>
            </div>
        <?php else: ?>
            <button type="button" class="btn-secundario btn-bloque" disabled>Sin existencias</button>
        <?php endif; ?>
    </div>
</article>
