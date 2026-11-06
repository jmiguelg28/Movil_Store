<?php
require_once BASE_PATH . '/controllers/DocumentoController.php';
require_once BASE_PATH . '/models/Compra.php';

class CompraController extends DocumentoController {

    public function __construct(){
        parent::__construct();
        $this->modelo = new Compra();
    }

    protected function configuracion(){
        return array(
            'titulo' => 'Compras', 'singular' => 'Compra', 'ruta' => 'compras',
            'tercero' => 'Proveedor', 'tercero_art' => 'un',
            'precio_label' => 'Costo unitario', 'precio_auto' => false, 'muestra_stock' => false,
            'subtitulo' => 'Compras de mercancía a proveedores (aumentan el stock)',
        );
    }
}
