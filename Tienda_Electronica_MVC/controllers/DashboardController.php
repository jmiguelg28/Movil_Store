<?php
require_once BASE_PATH . '/models/Dashboard.php';

class DashboardController extends Controller {

    public function index(){
        $m = new Dashboard();
        $this->render('dashboard', array(
            'kpi'            => $m->indicadores(),
            'series'         => $m->seriesMensuales(),
            'topClientes'    => $m->topClientes(),
            'topProductos'   => $m->topProductos(),
            'topProveedores' => $m->topProveedores(),
            'pagos'          => $m->ventasPorPago(),
            'stockBajo'      => $m->stockBajo(),
            'ultimas'        => $m->ultimasVentas(),
        ), 'Inicio');
    }
}
