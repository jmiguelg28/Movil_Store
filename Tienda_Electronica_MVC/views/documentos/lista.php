<div class="page-header fila">
    <div>
        <span class="eyebrow">OPERACIONES / MOVILL STORE</span><h1><?= e($doc['titulo']) ?></h1>
        <p><?= e($doc['subtitulo']) ?></p>
    </div>
    <div class="header-actions"><a class="btn-secundario" href="<?= url($doc['ruta'], 'exportar') ?>">↓ Exportar CSV</a><a class="btn-primary" href="<?= url($doc['ruta'], 'crear') ?>">+ Nueva <?= e(mb_strtolower($doc['singular'])) ?></a></div>
</div>

<div class="table-container">
    <div class="barra">
        <input type="search" class="buscador" placeholder="Buscar por número, <?= e(mb_strtolower($doc['tercero'])) ?>, fecha…" data-filtro="#tabla">
    </div>
    <table id="tabla">
        <thead>
            <tr>
                <th>N°</th><th><?= e($doc['tercero']) ?></th><th>Fecha</th><th>Forma de pago</th>
                <th class="num">Unidades</th><th class="num">Total</th><th class="acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($filas as $f): ?>
            <tr>
                <td><strong>#<?= e($f['id']) ?></strong></td>
                <td><?= e($f['tercero']) ?></td>
                <td><?= e(fecha_corta($f['fecha'])) ?></td>
                <td><?= e($f['forma_pago']) ?></td>
                <td class="num"><?= (int)$f['unidades'] ?></td>
                <td class="num"><strong><?= dinero($f['total']) ?></strong></td>
                <td class="acciones">
                    <a class="btn-sm" href="<?= url($doc['ruta'], 'ver', array('id' => $f['id'])) ?>">Ver</a>
                    <a class="btn-sm" href="<?= url($doc['ruta'], 'editar', array('id' => $f['id'])) ?>">Editar</a>
                    <form method="POST" action="<?= url($doc['ruta'], 'eliminar') ?>" class="inline"
                          data-confirmar="¿Eliminar la <?= e(mb_strtolower($doc['singular'])) ?> #<?= e($f['id']) ?>? El stock se ajustará automáticamente.">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= e($f['id']) ?>">
                        <button class="btn-sm btn-peligro">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if(!$filas): ?>
            <tr><td colspan="7" class="vacio">No hay registros todavía.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
