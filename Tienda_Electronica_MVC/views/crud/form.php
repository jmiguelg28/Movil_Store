<?php $accion = $id ? 'actualizar' : 'guardar'; ?>
<div class="page-header fila"><div><span class="eyebrow">ADMINISTRACIÓN / <?= e(mb_strtoupper($cfg['titulo'])) ?></span><h1><?= $id ? 'Editar' : 'Nuevo' ?> <?= e(mb_strtolower($cfg['singular'])) ?></h1><p><?= $id ? 'Actualiza la información del registro seleccionado.' : 'Completa los datos para agregar un nuevo registro al sistema.' ?></p></div><a href="<?= url($cfg['ruta'], 'index', $cfg['extra']) ?>" class="btn-secundario">← Volver al listado</a></div>
<div class="form-shell">
<div class="form-card crud-form">
    <div class="form-step"><span>01</span><div><h3>Información general</h3><small>Los campos marcados con * son obligatorios.</small></div></div>
    <?php if($errores): ?><div class="alerta alerta-error" role="alert"><?= e(implode(' ', array_values($errores))) ?></div><?php endif; ?>
    <form method="POST" action="<?= url($cfg['ruta'], $accion, $cfg['extra']) ?>">
        <?= csrf_field() ?>
        <?php if($id): ?><input type="hidden" name="id" value="<?= e($id) ?>"><?php endif; ?>
        <div class="crud-grid">
        <?php foreach($cfg['campos'] as $campo):
            $n = $campo['name']; $tipo = isset($campo['type']) ? $campo['type'] : 'text';
            $val = isset($valores[$n]) ? $valores[$n] : '';
            $largo = $tipo === 'text' && !in_array($n, array('nom_cliente','nom_proveedor'), true);
        ?>
        <div class="form-field <?= $largo ? '' : '' ?>">
            <label for="<?= e($n) ?>"><?= e($campo['label']) ?><?= !empty($campo['required']) ? ' *' : '' ?></label>
            <?php if($tipo === 'select'): ?>
                <select id="<?= e($n) ?>" name="<?= e($n) ?>" <?= !empty($campo['required']) ? 'required' : '' ?>>
                    <option value="">— Selecciona una opción —</option>
                    <?php foreach($opciones[$n] as $o): ?><option value="<?= e($o['id']) ?>" <?= ((string)$val === (string)$o['id']) ? 'selected' : '' ?>><?= e($o['texto']) ?></option><?php endforeach; ?>
                </select>
            <?php else: ?>
                <input id="<?= e($n) ?>" type="<?= e($tipo) ?>" name="<?= e($n) ?>" value="<?= e($val) ?>" <?= !empty($campo['max']) ? 'maxlength="' . (int)$campo['max'] . '"' : '' ?> <?= !empty($campo['required']) ? 'required' : '' ?> placeholder="Ingresa <?= e(mb_strtolower($campo['label'])) ?>" autocomplete="off">
            <?php endif; ?>
            <?php if(isset($errores[$n])): ?><small class="error"><?= e($errores[$n]) ?></small><?php endif; ?>
        </div>
        <?php endforeach; ?>
        </div>
        <div class="botones"><button class="btn-primary" type="submit"><?= $id ? 'Guardar cambios' : 'Crear ' . e(mb_strtolower($cfg['singular'])) ?> →</button><a class="btn-secundario" href="<?= url($cfg['ruta'], 'index', $cfg['extra']) ?>">Cancelar</a></div>
    </form>
</div>
<aside class="form-helper"><span class="form-helper-icon">✦</span><h3>Todo en orden</h3><p>La información que guardes estará disponible en los módulos relacionados de Movill Store.</p><div class="helper-note">✓ Datos conectados a MySQL<br>✓ Operaciones verificadas<br>✓ Formularios protegidos</div></aside>
</div>
