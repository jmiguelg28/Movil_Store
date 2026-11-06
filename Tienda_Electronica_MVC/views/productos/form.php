<?php $accion = $id ? 'actualizar' : 'guardar'; $g = function($k, $d = '') use ($v){ return isset($v[$k]) ? $v[$k] : $d; }; ?>
<div class="page-header">
    <span class="eyebrow">CATÁLOGO / INVENTARIO</span><h1><?= $id ? 'Editar producto' : 'Agregar producto' ?></h1><p>Completa la ficha de tu producto con imágenes, precio y existencias.</p>
</div>

<div class="form-card ancho">
    <?php if($errores): ?>
        <div class="alerta alerta-error"><?= e(implode(' ', $errores)) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= url('productos', $accion) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?php if($id): ?><input type="hidden" name="id" value="<?= e($id) ?>"><?php endif; ?>

        <div class="form-step"><span>01</span><div><h3>Información del producto</h3><small>Nombre, fotografía y descripción para la tienda.</small></div></div><label for="nom_producto">Nombre del producto *</label>
        <input id="nom_producto" name="nom_producto" maxlength="100" required value="<?= e($g('nom_producto')) ?>">

        <label>Imagen del producto</label>
        <div class="subir-img">
            <img id="vista-previa" src="<?= e(img_producto($g('imagen', null))) ?>" alt="Vista previa" onerror="this.onerror=null;this.src='assets/img/sin-imagen.svg'">
            <div>
                <input id="imagen" type="file" name="imagen" accept="image/jpeg,image/png,image/webp,image/gif">
                <small>JPG, PNG, WEBP o GIF · máximo 4 MB · se ve mejor cuadrada.</small>
                <?php if($g('imagen', null)): ?>
                    <label class="check"><input type="checkbox" name="quitar_imagen" value="1"> Quitar la imagen actual</label>
                <?php endif; ?>
                <?php if(isset($errores['imagen'])): ?><small class="error"><?= e($errores['imagen']) ?></small><?php endif; ?>
            </div>
        </div>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="4" maxlength="2000" placeholder="Características principales, qué incluye la caja, garantía..."><?= e($g('descripcion')) ?></textarea>
        <?php if(isset($errores['descripcion'])): ?><small class="error"><?= e($errores['descripcion']) ?></small><?php endif; ?>

        <div class="form-step"><span>02</span><div><h3>Precio y clasificación</h3><small>Define el valor de venta, cantidad y atributos.</small></div></div><div class="dos-col">
            <div>
                <label for="precio">Precio de venta *</label>
                <input id="precio" type="number" name="precio" min="0" step="0.01" required value="<?= e($g('precio')) ?>">
            </div>
            <div>
                <label for="stock">Stock *</label>
                <input id="stock" type="number" name="stock" min="0" step="1" required value="<?= e($g('stock', 0)) ?>">
            </div>
        </div>

        <div class="dos-col">
            <div>
                <label for="cod_marca">Marca *</label>
                <select id="cod_marca" name="cod_marca" required>
                    <option value="">— Seleccione —</option>
                    <?php foreach($marcas as $o): ?>
                        <option value="<?= e($o['id']) ?>" <?= (string)$g('cod_marca') === (string)$o['id'] ? 'selected' : '' ?>><?= e($o['texto']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="cod_tipo_p">Tipo de producto *</label>
                <select id="cod_tipo_p" name="cod_tipo_p" required>
                    <option value="">— Seleccione —</option>
                    <?php foreach($tipos as $o): ?>
                        <option value="<?= e($o['id']) ?>" <?= (string)$g('cod_tipo_p') === (string)$o['id'] ? 'selected' : '' ?>><?= e($o['texto']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <label>Colores disponibles</label>
        <div class="checks">
            <?php foreach($colores as $o): ?>
                <label class="check"><input type="checkbox" name="colores[]" value="<?= e($o['id']) ?>"
                    <?= in_array((int)$o['id'], $g('colores', array()), true) ? 'checked' : '' ?>> <?= e($o['texto']) ?></label>
            <?php endforeach; ?>
            <?php if(!$colores): ?><small>No hay colores. Créalos en Catálogos → Colores.</small><?php endif; ?>
        </div>

        <label>Materiales</label>
        <div class="checks">
            <?php foreach($materiales as $o): ?>
                <label class="check"><input type="checkbox" name="materiales[]" value="<?= e($o['id']) ?>"
                    <?= in_array((int)$o['id'], $g('materiales', array()), true) ? 'checked' : '' ?>> <?= e($o['texto']) ?></label>
            <?php endforeach; ?>
            <?php if(!$materiales): ?><small>No hay materiales. Créalos en Catálogos → Materiales.</small><?php endif; ?>
        </div>

        <div class="botones">
            <button class="btn-primary">Guardar</button>
            <a class="btn-secundario" href="<?= url('productos') ?>">Cancelar</a>
        </div>
    </form>
</div>
