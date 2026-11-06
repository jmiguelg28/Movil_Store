<?php
/**
 * Controlador frontal.  Uso:  index.php?c=<controlador>&a=<acción>&id=<id>
 */
// Zona horaria del negocio (cámbiala si estás en otro país)
date_default_timezone_set('America/Bogota');

session_start();
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/core/helpers.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/config/conexion.php';

$rutas = array(
    'dashboard'   => 'DashboardController',
    'productos'   => 'ProductoController',
    'ventas'      => 'VentaController',
    'compras'     => 'CompraController',
    'clientes'    => 'ClienteController',
    'proveedores' => 'ProveedorController',
    'catalogo'    => 'CatalogoController',
    'auth'        => 'AuthController',
    'tienda'      => 'TiendaController',
);

$c = isset($_GET['c']) ? $_GET['c'] : 'tienda';
$a = isset($_GET['a']) ? $_GET['a'] : 'index';

if(!isset($rutas[$c]) || !preg_match('/^[a-z]+$/', $a)){
    http_response_code(404);
    exit('Página no encontrada');
}

// Sin sesión iniciada solo se puede ver el login
if($c !== 'auth' && empty($_SESSION['usuario'])){
    header('Location: ' . url('auth', 'login'));
    exit;
}

$clase = $rutas[$c];
require_once BASE_PATH . '/controllers/' . $clase . '.php';
$controlador = new $clase();

if(!is_callable(array($controlador, $a))){
    http_response_code(404);
    exit('Acción no encontrada');
}

$controlador->$a();
