<?php
$accion = $id ? 'actualizar' : 'guardar';
$jsProductos = array();
foreach($productos as $p){
    $jsProductos[] = array('id' => (int)$p['cod_producto'], 'nombre' => $p['nom_producto'],
                           'precio' => (float)$p['precio'], 'stock' => (int)$p['stock']);
}
?>
<div class="page-header">
    <h1><?= $id ? 'Editar' : 'Nueva' ?> <?= e(mb_strtolower($doc['singular'])) ?><?= $id ? ' #' . e($id) : '' ?></h1>
</div>

<div class="form-card ancho">
    <?php if($errores): ?>
        <div class="alerta alerta-error"><?= e(implode(' ', $errores)) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= url($doc['ruta'], $accion) ?>" id="form-documento">
        <?= csrf_field() ?>
        <?php if($id): ?><input type="hidden" name="id" value="<?= e($id) ?>"><?php endif; ?>

        <div class="form-step"><span>01</span><div><h3>Información de la operación</h3><small>Selecciona <?= e(mb_strtolower($doc['tercero'])) ?>, fecha y método de pago.</small></div></div>
        <div class="tres-col">
            <div>
                <label for="tercero_id"><?= e($doc['tercero']) ?> *</label>
                <select id="tercero_id" name="tercero_id" required>
                    <option value="">— Seleccione —</option>
                    <?php foreach($terceros as $t): ?>
                        <option value="<?= e($t['id']) ?>" <?= (isset($v['tercero_id']) && (string)$v['tercero_id'] === (string)$t['id']) ? 'selected' : '' ?>><?= e($t['texto']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fecha">Fecha *</label>
                <input id="fecha" type="date" name="fecha" required value="<?= e(isset($v['fecha']) ? $v['fecha'] : date('Y-m-d')) ?>">
            </div>
            <div>
                <label for="cod_f_pago">Forma de pago *</label>
                <select id="cod_f_pago" name="cod_f_pago" required>
                    <option value="">— Seleccione —</option>
                    <?php foreach($pagos as $t): ?>
                        <option value="<?= e($t['id']) ?>" <?= (isset($v['cod_f_pago']) && (string)$v['cod_f_pago'] === (string)$t['id']) ? 'selected' : '' ?>><?= e($t['texto']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <p class="aviso" id="aviso-borrador" hidden>
            Estos productos están en tu carrito de <?= e(mb_strtolower($doc['ruta'])) ?>.
            <button type="button" class="enlace" id="vaciar">Vaciar carrito</button>
        </p>

        <div class="form-step"><span>02</span><div><h3 class="subtitulo">Productos de la <?= e(mb_strtolower($doc['singular'])) ?></h3><small>Agrega las referencias y ajusta cantidades. El total se calcula automáticamente.</small></div></div>
        <div class="tabla-scroll">
        <table class="detalle" id="tabla-lineas">
            <thead>
                <tr><th>Producto</th><th class="num">Cantidad</th><th class="num"><?= e($doc['precio_label']) ?></th><th class="num">Subtotal</th><th></th></tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr><td colspan="3" class="num"><strong>TOTAL</strong></td><td class="num total" id="total">$0</td><td></td></tr>
            </tfoot>
        </table>
        </div>
        <button type="button" class="btn-secundario" id="agregar-linea">+ Agregar producto</button>

        <div class="botones">
            <button class="btn-primary">Confirmar y guardar <?= e(mb_strtolower($doc['singular'])) ?> →</button>
            <a class="btn-secundario" href="<?= url($doc['ruta']) ?>" data-limpiar="<?= e($doc['ruta']) ?>">Cancelar</a>
        </div>
    </form>
</div>

<script>
window.DOC = {
    id:           <?= $id ? (int)$id : 'null' ?>,
    ruta:         <?= json_encode($doc['ruta']) ?>,
    pre:          <?= json_encode($pre, JSON_HEX_TAG | JSON_HEX_AMP) ?>,
    precioAuto:   <?= $doc['precio_auto'] ? 'true' : 'false' ?>,
    muestraStock: <?= $doc['muestra_stock'] ? 'true' : 'false' ?>,
    productos:    <?= json_encode($jsProductos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>,
    lineas:       <?= json_encode($lineas, JSON_HEX_TAG | JSON_HEX_AMP) ?>
};
</script>
