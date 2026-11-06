<?php
require_once BASE_PATH . '/controllers/DocumentoController.php';
require_once BASE_PATH . '/models/Venta.php';

class VentaController extends DocumentoController {

    public function __construct(){
        parent::__construct();
        $this->modelo = new Venta();
    }

    protected function configuracion(){
        return array(
            'titulo' => 'Ventas', 'singular' => 'Venta', 'ruta' => 'ventas',
            'tercero' => 'Cliente', 'tercero_art' => 'un',
            'precio_label' => 'Precio unitario', 'precio_auto' => true, 'muestra_stock' => true,
            'subtitulo' => 'Historial de ventas realizadas',
        );
    }
}
