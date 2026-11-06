<div class="page-header">
    <span class="eyebrow">CENTRO DE CONTROL</span><h1>Panel general</h1>
    <p>Tu negocio, de un vistazo. Consulta indicadores y gestiona tus operaciones.</p><div class="dashboard-quick"><a class="btn-primary" href="<?= url('ventas','crear') ?>">+ Registrar venta</a><a class="btn-secundario" href="<?= url('productos','crear') ?>">+ Nuevo producto</a><a class="btn-secundario" href="<?= url('tienda') ?>">Explorar tienda →</a></div>
</div>

<details class="seccion" open>
    <summary>Resumen</summary>
    <div class="seccion-cuerpo">
        <div class="kpis">
            <div class="kpi">
                <div class="kpi-icono verde"><?= icono('subida', 20) ?></div>
                <div><span>Ventas totales</span><strong><?= dinero($kpi['total_ventas']) ?></strong><small><?= $kpi['ventas'] ?> ventas registradas</small></div>
            </div>
            <div class="kpi">
                <div class="kpi-icono azul"><?= icono('calendario', 20) ?></div>
                <div><span>Ventas de este mes</span><strong><?= dinero($kpi['ventas_mes']) ?></strong><small>mes en curso</small></div>
            </div>
            <div class="kpi">
                <div class="kpi-icono naranja"><?= icono('camion', 20) ?></div>
                <div><span>Compras totales</span><strong><?= dinero($kpi['total_compras']) ?></strong><small><?= $kpi['compras'] ?> compras registradas</small></div>
            </div>
            <div class="kpi">
                <div class="kpi-icono morado"><?= icono('caja', 20) ?></div>
                <div><span>Valor del inventario</span><strong><?= dinero($kpi['valor_inventario']) ?></strong><small><?= $kpi['productos'] ?> productos</small></div>
            </div>
            <div class="kpi">
                <div class="kpi-icono rosa"><?= icono('usuarios', 20) ?></div>
                <div><span>Clientes</span><strong><?= $kpi['clientes'] ?></strong><small>registrados</small></div>
            </div>
            <div class="kpi">
                <div class="kpi-icono gris"><?= icono('maletin', 20) ?></div>
                <div><span>Proveedores</span><strong><?= $kpi['proveedores'] ?></strong><small>registrados</small></div>
            </div>
        </div>
    </div>
</details>

<div class="grid-graficos">
    <div class="panel span2">
        <h3>Ventas y compras de los últimos 12 meses</h3>
        <div class="lienzo"><canvas id="gMensual"></canvas></div>
    </div>
    <div class="panel">
        <h3>Clientes que más compran</h3>
        <div class="lienzo"><canvas id="gClientes"></canvas></div>
    </div>
    <div class="panel">
        <h3>Ventas por forma de pago</h3>
        <div class="lienzo"><canvas id="gPagos"></canvas></div>
    </div>
    <div class="panel">
        <h3>Productos más vendidos (unidades)</h3>
        <div class="lienzo"><canvas id="gProductos"></canvas></div>
    </div>
    <div class="panel">
        <h3>Compras por proveedor</h3>
        <div class="lienzo"><canvas id="gProveedores"></canvas></div>
    </div>
</div>

<div class="grid-graficos">
    <div class="panel">
        <h3>Últimas ventas</h3>
        <table class="detalle">
            <thead><tr><th>N°</th><th>Cliente</th><th>Fecha</th><th class="num">Total</th></tr></thead>
            <tbody>
            <?php foreach($ultimas as $u): ?>
                <tr>
                    <td><a href="<?= url('ventas', 'ver', array('id' => $u['nro_f_venta'])) ?>">#<?= e($u['nro_f_venta']) ?></a></td>
                    <td><?= e($u['nom_cliente']) ?></td>
                    <td><?= e(fecha_corta($u['fecha'])) ?></td>
                    <td class="num"><?= dinero($u['total']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if(!$ultimas): ?><tr><td colspan="4" class="vacio">Todavía no hay ventas.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="panel">
        <h3>Productos con poco stock</h3>
        <table class="detalle">
            <thead><tr><th>Producto</th><th class="num">Stock</th></tr></thead>
            <tbody>
            <?php foreach($stockBajo as $s): ?>
                <tr>
                    <td><?= e($s['nom_producto']) ?></td>
                    <td class="num"><span class="etiqueta <?= $s['stock'] <= 0 ? 'rojo' : 'amarillo' ?>"><?= (int)$s['stock'] ?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if(!$stockBajo): ?><tr><td colspan="2" class="vacio">Todo el inventario está bien surtido.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
    if(typeof Chart === 'undefined'){
        document.querySelectorAll('.lienzo').forEach(function(l){
            l.innerHTML = '<p class="vacio">No se pudieron cargar los gráficos (se necesita conexión a internet).</p>';
        });
        return;
    }
    Chart.defaults.font.family = '"Segoe UI", system-ui, Arial, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7280';

    var dinero = function(v){ return '$' + Math.round(v).toLocaleString('es-CO'); };
    var paleta = ['#6a3de8','#f2305f','#14b8a6','#f59e0b','#3b82f6','#9ca3af'];
    var cuadricula = { color:'#eef0f5' };

    var s  = <?= json_encode($series, JSON_UNESCAPED_UNICODE) ?>;
    var tc = <?= json_encode($topClientes, JSON_UNESCAPED_UNICODE) ?>;
    var tp = <?= json_encode($topProductos, JSON_UNESCAPED_UNICODE) ?>;
    var pr = <?= json_encode($topProveedores, JSON_UNESCAPED_UNICODE) ?>;
    var pg = <?= json_encode($pagos, JSON_UNESCAPED_UNICODE) ?>;

    function barras(id, datos, campo, color, enDinero){
        new Chart(document.getElementById(id), {
            type:'bar',
            data:{ labels:datos.map(function(x){ return x.nombre; }),
                   datasets:[{ data:datos.map(function(x){ return +x[campo]; }), backgroundColor:color, borderRadius:4, barThickness:18 }] },
            options:{ indexAxis:'y', responsive:true, maintainAspectRatio:false,
                plugins:{ legend:{ display:false },
                          tooltip:{ callbacks:{ label:function(c){ return enDinero ? dinero(c.parsed.x) : c.parsed.x + ' unidades'; } } } },
                scales:{ x:{ grid:cuadricula, ticks:{ callback:function(v){ return enDinero ? dinero(v) : v; } } },
                         y:{ grid:{ display:false } } } }
        });
    }

    new Chart(document.getElementById('gMensual'), {
        type:'line',
        data:{ labels:s.labels, datasets:[
            { label:'Ventas',  data:s.ventas,  borderColor:'#6a3de8', backgroundColor:'rgba(106,61,232,.08)', fill:true, tension:.3, pointRadius:3 },
            { label:'Compras', data:s.compras, borderColor:'#f59e0b', backgroundColor:'transparent', tension:.3, pointRadius:3 }
        ]},
        options:{ responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{ position:'top', align:'end', labels:{ boxWidth:10, usePointStyle:true } },
                      tooltip:{ callbacks:{ label:function(c){ return c.dataset.label + ': ' + dinero(c.parsed.y); } } } },
            scales:{ y:{ grid:cuadricula, ticks:{ callback:function(v){ return dinero(v); } } }, x:{ grid:{ display:false } } } }
    });

    barras('gClientes',    tc, 'total',    '#6a3de8', true);
    barras('gProductos',   tp, 'unidades', '#14b8a6', false);
    barras('gProveedores', pr, 'total',    '#f59e0b', true);

    new Chart(document.getElementById('gPagos'), {
        type:'doughnut',
        data:{ labels:pg.map(function(x){ return x.nombre; }),
               datasets:[{ data:pg.map(function(x){ return +x.total; }), backgroundColor:paleta, borderWidth:2, borderColor:'#fff' }] },
        options:{ responsive:true, maintainAspectRatio:false, cutout:'62%',
            plugins:{ legend:{ position:'right', labels:{ boxWidth:10, usePointStyle:true } },
                      tooltip:{ callbacks:{ label:function(c){ return c.label + ': ' + dinero(c.parsed); } } } } }
    });
})();
</script>
