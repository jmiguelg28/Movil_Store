<div class="page-header fila">
    <div>
        <span class="eyebrow">DIRECTORIO / CONFIGURACIÓN</span><h1><?= e($cfg['titulo']) ?></h1>
        <p>Organiza y administra tus <?= e(mb_strtolower($cfg['titulo'])) ?> · <?= count($filas) ?> registros</p>
    </div>
    <a class="btn-primary" href="<?= url($cfg['ruta'], 'crear', $cfg['extra']) ?>">+ Agregar <?= e(mb_strtolower($cfg['singular'])) ?></a>
</div>

<div class="table-container">
    <div class="barra">
        <input type="search" class="buscador" placeholder="Buscar en <?= e(mb_strtolower($cfg['titulo'])) ?>…" data-filtro="#tabla">
    </div>
    <table id="tabla">
        <thead>
            <tr>
                <?php foreach($cfg['columnas'] as $titulo): ?><th><?= e($titulo) ?></th><?php endforeach; ?>
                <th class="acciones">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($filas as $fila): $id = $fila[$cfg['pk']]; ?>
            <tr>
                <?php foreach($cfg['columnas'] as $col => $t): ?><td><?= e($fila[$col]) ?></td><?php endforeach; ?>
                <td class="acciones">
                    <a class="btn-sm" href="<?= url($cfg['ruta'], 'editar', array_merge($cfg['extra'], array('id' => $id))) ?>">Editar</a>
                    <form method="POST" action="<?= url($cfg['ruta'], 'eliminar', $cfg['extra']) ?>" class="inline"
                          data-confirmar="¿Eliminar este registro?">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= e($id) ?>">
                        <button class="btn-sm btn-peligro">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if(!$filas): ?>
            <tr><td colspan="<?= count($cfg['columnas']) + 1 ?>" class="vacio">No hay registros todavía.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
