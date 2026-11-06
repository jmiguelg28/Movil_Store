<?php
require_once BASE_PATH . '/models/Producto.php';

/** Catálogo de productos estilo tienda en línea. */
class TiendaController extends Controller {

    public function index(){
        $m = new Producto();
        $num = function($k){
            return (isset($_GET[$k]) && is_scalar($_GET[$k]) && $_GET[$k] !== '' && is_numeric($_GET[$k]) && $_GET[$k] >= 0) ? (float)$_GET[$k] : null;
        };
        $f = array(
            'q'           => mb_substr(trim(isset($_GET['q']) && is_string($_GET['q']) ? $_GET['q'] : ''), 0, 120),
            'marca'       => (int)(isset($_GET['marca']) ? $_GET['marca'] : 0),
            'tipo'        => (int)(isset($_GET['tipo']) ? $_GET['tipo'] : 0),
            'minimo'      => $num('minimo'),
            'maximo'      => $num('maximo'),
            'disponibles' => !empty($_GET['disponibles']),
            'orden'       => isset($_GET['orden']) && is_string($_GET['orden']) ? $_GET['orden'] : 'nuevos',
            'pagina'      => (int)(isset($_GET['pagina']) ? $_GET['pagina'] : 1),
        );
        $this->render('tienda/index', array(
            'f' => $f, 'r' => $m->tienda($f),
            'marcas' => $m->marcasConConteo(),
            'tipos'  => $m->tiposConConteo(),
        ), 'Tienda');
    }

    public function carrito(){
        $m = new Producto();
        $this->render('tienda/carrito', array('productos' => $m->productosCarrito()), 'Mi carrito');
    }

    public function ver(){
        $m  = new Producto();
        $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
        $p  = $m->detalle($id);
        if(!$p){
            set_flash('error', 'El producto no existe.');
            $this->redirigir('tienda');
        }
        $this->render('tienda/ver', array(
            'p' => $p, 'relacionados' => $m->relacionados($p['cod_tipo_p'], $id)
        ), $p['nom_producto']);
    }
}
