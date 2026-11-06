<div class="page-header fila">
    <div>
        <span class="eyebrow">GESTIÓN DEL CATÁLOGO</span><h1>Productos e inventario</h1>
        <p><?= count($productos) ?> producto(s) en inventario</p>
    </div>
    <div class="header-actions"><a class="btn-secundario" href="<?= url('productos','exportar') ?>">↓ Exportar inventario</a><a class="btn-primary" href="<?= url('productos', 'crear') ?>">+ Agregar producto</a></div>
</div>

<div class="table-container">
    <div class="barra">
        <input type="search" class="buscador" placeholder="Buscar producto, marca, tipo…" data-filtro="#tabla">
    </div>
    <table id="tabla">
        <thead>
            <tr>
                <th>#</th><th>Imagen</th><th>Producto</th><th>Marca</th><th>Tipo</th><th>Colores</th>
                <th class="num">Precio</th><th class="num">Stock</th><th class="acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($productos as $p): ?>
            <tr>
                <td><?= e($p['cod_producto']) ?></td>
                <td><img class="miniatura" src="<?= e(img_producto($p['imagen'])) ?>" alt="" onerror="this.onerror=null;this.src='assets/img/sin-imagen.svg'"></td>
                <td><a href="<?= url('tienda', 'ver', array('id' => $p['cod_producto'])) ?>"><strong><?= e($p['nom_producto']) ?></strong></a></td>
                <td><?= e($p['nom_marca']) ?></td>
                <td><?= e($p['nom_tipo_p']) ?></td>
                <td><?= e($p['colores']) ?></td>
                <td class="num"><?= dinero($p['precio']) ?></td>
                <td class="num">
                    <span class="etiqueta <?= $p['stock'] <= 0 ? 'rojo' : ($p['stock'] <= 5 ? 'amarillo' : 'verde') ?>">
                        <?= (int)$p['stock'] ?>
                    </span>
                </td>
                <td class="acciones">
                    <?php if($p['stock'] > 0): ?>
                        <a class="btn-sm btn-ok" href="<?= url('ventas', 'crear', array('producto' => $p['cod_producto'])) ?>">Vender</a>
                    <?php endif; ?>
                    <a class="btn-sm" href="<?= url('productos', 'editar', array('id' => $p['cod_producto'])) ?>">Editar</a>
                    <form method="POST" action="<?= url('productos', 'eliminar') ?>" class="inline"
                          data-confirmar="¿Eliminar el producto «<?= e($p['nom_producto']) ?>»?">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= e($p['cod_producto']) ?>">
                        <button class="btn-sm btn-peligro">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if(!$productos): ?>
            <tr><td colspan="9" class="vacio">No hay productos todavía.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
