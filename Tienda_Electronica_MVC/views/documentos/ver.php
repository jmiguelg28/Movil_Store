<div class="page-header fila">
    <div>
        <h1><?= e($doc['singular']) ?> #<?= e($cab['id']) ?></h1>
        <p><?= e(fecha_corta($cab['fecha'])) ?></p>
    </div>
    <div>
        <button type="button" class="btn-secundario no-print" onclick="window.print()">⎙ Imprimir comprobante</button> <a class="btn-secundario no-print" href="<?= url($doc['ruta']) ?>">Volver</a>
        <a class="btn-primary" href="<?= url($doc['ruta'], 'editar', array('id' => $cab['id'])) ?>">Editar</a>
    </div>
</div>

<div class="print-only invoice-header"><h1>MOVILL STORE</h1><p>Comprobante de <?= e(mb_strtolower($doc['singular'])) ?> #<?= e($cab['id']) ?> · <?= e(fecha_corta($cab['fecha'])) ?></p></div>
<div class="form-card ancho invoice-card">
    <div class="dos-col resumen">
        <div><span>Tercero · <?= e($doc['tercero']) ?></span><strong><?= e($cab['tercero']) ?></strong></div>
        <div><span>Forma de pago</span><strong><?= e($cab['forma_pago']) ?></strong></div>
    </div>

    <table class="detalle">
        <thead>
            <tr><th>Producto</th><th class="num">Cantidad</th><th class="num"><?= e($doc['precio_label']) ?></th><th class="num">Subtotal</th></tr>
        </thead>
        <tbody>
        <?php foreach($items as $i): ?>
            <tr>
                <td><?= e($i['nom_producto']) ?></td>
                <td class="num"><?= (int)$i['cantidad'] ?></td>
                <td class="num"><?= dinero($i['precio']) ?></td>
                <td class="num"><?= dinero($i['subtotal']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="num"><strong>TOTAL</strong></td><td class="num total"><?= dinero($cab['total']) ?></td></tr>
        </tfoot>
    </table>
</div>
