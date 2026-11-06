<?php
$f = array_merge(array('q'=>'', 'marca'=>0, 'tipo'=>0, 'minimo'=>null, 'maximo'=>null, 'disponibles'=>false, 'orden'=>'nuevos', 'pagina'=>1), is_array($f ?? null) ? $f : array());
$hayFiltros = $f['q'] !== '' || $f['marca'] || $f['tipo'] || $f['minimo'] !== null || $f['maximo'] !== null || $f['disponibles'];
$marcas = is_array($marcas ?? null) ? $marcas : array();
$tipos = is_array($tipos ?? null) ? $tipos : array();
$r = is_array($r ?? null) ? $r : array('items'=>array(), 'total'=>0, 'pagina'=>1, 'paginas'=>1);
$nomMarca = ''; foreach($marcas as $m){ if((int)$m['id'] === $f['marca']){ $nomMarca = $m['texto']; } }
$nomTipo  = ''; foreach($tipos  as $t){ if((int)$t['id'] === $f['tipo'])  { $nomTipo  = $t['texto']; } }
?>
<section class="shop-hero" aria-label="Bienvenida a Movill Store">
    <div class="shop-hero-copy">
        <span class="hero-eyebrow"><span class="sparkle">✦</span> TU UNIVERSO TECNOLÓGICO</span>
        <h1>Tu próxima gran<br><em>conexión</em> está aquí.</h1>
        <p>Celulares, computadores y accesorios. Explora nuestro catálogo, compara productos y registra tus compras en un solo lugar.</p>
        <div class="hero-actions">
            <a href="#productos" class="hero-cta">Explorar productos <span aria-hidden="true">↗</span></a>
            <a href="<?= url('tienda','carrito') ?>" class="hero-secondary">Ver mi carrito <?= icono('carrito',18) ?></a>
        </div>
        <div class="hero-proof"><span>◉</span> Inventario actualizado <i></i> <span>◉</span> Precios en pesos colombianos</div>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="device-orbit orbit-one"></div><div class="device-orbit orbit-two"></div>
        <div class="hero-laptop"><div class="hero-laptop-screen"><div class="screen-top"><i></i><i></i><i></i></div><div class="screen-inner"><span class="screen-pill">MOVILL / TECH</span><div class="screen-wave"></div><div class="screen-bars"><i></i><i></i><i></i></div></div></div><div class="hero-laptop-base"></div></div>
        <div class="hero-phone"><div class="phone-speaker"></div><div class="phone-screen"><span class="phone-time">10:28</span><span class="phone-orb"></span><span class="phone-line"></span><span class="phone-line short"></span></div></div>
        <span class="floating-chip chip-one">NUEVA TECNOLOGÍA <b>✦</b></span><span class="floating-chip chip-two">MOVILL STORE ✦</span>
    </div>
</section>

<div class="shop-benefits" aria-label="Ventajas del catálogo"><div><?= icono('caja',22) ?><span><strong>Inventario visible</strong><small>Consulta disponibilidad</small></span></div><div><?= icono('lupa',22) ?><span><strong>Encuentra lo que buscas</strong><small>Filtros por marca y precio</small></span></div><div><?= icono('carrito',22) ?><span><strong>Compra organizada</strong><small>Registro interno de ventas</small></span></div></div>

<?php if($tipos): ?>
<section class="shop-categorias" aria-label="Categorías destacadas">
    <div class="section-heading"><div><span class="eyebrow">EXPLORA POR CATEGORÍA</span><h2>¿Qué estás buscando?</h2></div><a href="<?= url('tienda') ?>">Ver todo <span aria-hidden="true">→</span></a></div>
    <div class="cat-list">
        <?php $catIconos = array('▣','▤','◈','⌘','✧','▥','▰','⊞'); foreach(array_slice($tipos,0,8) as $idx => $t): ?>
            <a class="cat-pill <?= (int)$t['id'] === $f['tipo'] ? 'selected' : '' ?>" href="<?= url_tienda(array('tipo'=> (int)$t['id'] === $f['tipo'] ? null : $t['id'])) ?>"><span class="cat-icon"><?= $catIconos[$idx] ?></span><span><?= e($t['texto']) ?><small><?= (int)$t['n'] ?> productos</small></span></a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<div class="section-heading catalog-heading" id="productos"><div><span class="eyebrow">CATÁLOGO MOVILL STORE</span><h2>Descubre nuestros productos</h2></div><span class="small-hint">Precios actualizados en el sistema</span></div>
<div class="tienda-wrap">

    <aside class="filtros">
        <div class="filter-title"><h3>Filtrar productos</h3><a href="<?= url('tienda') ?>" title="Borrar filtros">↺</a></div>

        <?php if($hayFiltros): ?>
            <div class="chips">
                <?php if($f['q'] !== ''): ?><a class="chip-f" href="<?= url_tienda(array('q' => null)) ?>">“<?= e($f['q']) ?>” ✕</a><?php endif; ?>
                <?php if($nomMarca): ?><a class="chip-f" href="<?= url_tienda(array('marca' => null)) ?>"><?= e($nomMarca) ?> ✕</a><?php endif; ?>
                <?php if($nomTipo): ?><a class="chip-f" href="<?= url_tienda(array('tipo' => null)) ?>"><?= e($nomTipo) ?> ✕</a><?php endif; ?>
                <?php if($f['minimo'] !== null || $f['maximo'] !== null): ?>
                    <a class="chip-f" href="<?= url_tienda(array('minimo' => null, 'maximo' => null)) ?>">Precio ✕</a>
                <?php endif; ?>
                <?php if($f['disponibles']): ?><a class="chip-f" href="<?= url_tienda(array('disponibles' => null)) ?>">Solo disponibles ✕</a><?php endif; ?>
            </div>
            <a class="limpiar" href="<?= url('tienda') ?>">Limpiar todos los filtros</a>
        <?php endif; ?>

        <div class="filtro">
            <h4>Marca</h4>
            <ul>
            <?php foreach($marcas as $m): $act = ((int)$m['id'] === $f['marca']); ?>
                <li><a class="<?= $act ? 'act' : '' ?>" href="<?= url_tienda(array('marca' => $act ? null : $m['id'])) ?>">
                    <?= e($m['texto']) ?> <small>(<?= (int)$m['n'] ?>)</small></a></li>
            <?php endforeach; ?>
            </ul>
        </div>

        <div class="filtro">
            <h4>Categoría</h4>
            <ul>
            <?php foreach($tipos as $t): $act = ((int)$t['id'] === $f['tipo']); ?>
                <li><a class="<?= $act ? 'act' : '' ?>" href="<?= url_tienda(array('tipo' => $act ? null : $t['id'])) ?>">
                    <?= e($t['texto']) ?> <small>(<?= (int)$t['n'] ?>)</small></a></li>
            <?php endforeach; ?>
            </ul>
        </div>

        <div class="filtro">
            <h4>Precio</h4>
            <form method="GET" action="index.php" class="precio-form">
                <input type="hidden" name="c" value="tienda">
                <?php foreach($_GET as $k => $val): if(in_array($k, array('c','a','minimo','maximo','pagina'), true) || is_array($val)){ continue; } ?>
                    <input type="hidden" name="<?= e($k) ?>" value="<?= e($val) ?>">
                <?php endforeach; ?>
                <input type="number" name="minimo" min="0" placeholder="Mínimo" value="<?= $f['minimo'] !== null ? e($f['minimo']) : '' ?>">
                <span>–</span>
                <input type="number" name="maximo" min="0" placeholder="Máximo" value="<?= $f['maximo'] !== null ? e($f['maximo']) : '' ?>">
                <button class="btn-secundario">Aplicar</button>
            </form>
        </div>

        <div class="filtro">
            <h4>Disponibilidad</h4>
            <ul><li><a class="<?= $f['disponibles'] ? 'act' : '' ?>" href="<?= url_tienda(array('disponibles' => $f['disponibles'] ? null : 1)) ?>">Solo con existencias</a></li></ul>
        </div>
    </aside>

    <section class="resultados">
        <div class="res-head">
            <div>
                <h3><?= $f['q'] !== '' ? 'Resultados para “' . e($f['q']) . '”' : 'Todos los productos' ?></h3>
                <p><?= (int)$r['total'] ?> resultados disponibles</p>
            </div>
            <form method="GET" action="index.php" class="orden-form">
                <input type="hidden" name="c" value="tienda">
                <?php foreach($_GET as $k => $val): if(in_array($k, array('c','a','orden','pagina'), true) || is_array($val)){ continue; } ?>
                    <input type="hidden" name="<?= e($k) ?>" value="<?= e($val) ?>">
                <?php endforeach; ?>
                <label for="orden">Ordenar por</label>
                <select id="orden" name="orden" data-autosubmit>
                    <?php foreach(array('nuevos' => 'Más recientes', 'menor' => 'Menor precio', 'mayor' => 'Mayor precio', 'nombre' => 'Nombre (A-Z)') as $k => $t): ?>
                        <option value="<?= $k ?>" <?= $f['orden'] === $k ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if($r['items']): ?>
            <div class="grid-prod">
                <?php foreach($r['items'] as $p){ require __DIR__ . '/_tarjeta.php'; } ?>
            </div>

            <?php if($r['paginas'] > 1): ?>
            <nav class="paginas">
                <?php if($r['pagina'] > 1): ?><a href="<?= url_tienda(array('pagina' => $r['pagina'] - 1)) ?>">‹ Anterior</a><?php endif; ?>
                <?php for($i = 1; $i <= $r['paginas']; $i++): ?>
                    <a class="<?= $i === $r['pagina'] ? 'act' : '' ?>" href="<?= url_tienda(array('pagina' => $i)) ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if($r['pagina'] < $r['paginas']): ?><a href="<?= url_tienda(array('pagina' => $r['pagina'] + 1)) ?>">Siguiente ›</a><?php endif; ?>
            </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="sin-resultados">
                <img src="assets/img/sin-imagen.svg" alt="" width="120">
                <h3>No encontramos productos</h3>
                <p>Revisa la ortografía o quita algunos filtros.</p>
                <a class="btn-primary" href="<?= url('tienda') ?>">Ver todos los productos</a>
            </div>
        <?php endif; ?>
    </section>
</div>
