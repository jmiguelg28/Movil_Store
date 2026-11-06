<?php
require_once BASE_PATH . '/controllers/CrudController.php';

/**
 * Catálogos simples (ciudad, marca, tipo de producto, color, material, forma de pago).
 * Se elige con ?t=marca, ?t=color, etc.
 */
class CatalogoController extends CrudController {

    public static $catalogos = array(
        'ciudad' => array('Ciudades', 'Ciudad', 'ciudad', 'cod_ciudad', 'nom_ciudad',
            array(array('clientes','cod_ciudad','clientes'), array('proveedores','cod_ciudad','proveedores'))),
        'marca' => array('Marcas', 'Marca', 'marca', 'cod_marca', 'nom_marca',
            array(array('producto','cod_marca','productos'))),
        'tipo_producto' => array('Tipos de producto', 'Tipo de producto', 'tipo_producto', 'cod_tipo_p', 'nom_tipo_p',
            array(array('producto','cod_tipo_p','productos'))),
        'color' => array('Colores', 'Color', 'color', 'cod_color', 'nom_color',
            array(array('producto_color','cod_color','productos'))),
        'material' => array('Materiales', 'Material', 'material', 'cod_material', 'nom_material',
            array(array('producto_material','cod_material','productos'))),
        'forma_pago' => array('Formas de pago', 'Forma de pago', 'forma_pago', 'cod_f_pago', 'nom_f_pago',
            array(array('ventas','cod_f_pago','ventas'), array('compras','cod_f_pago','compras'))),
    );

    protected function configuracion(){
        $t = isset($_GET['t']) ? $_GET['t'] : (isset($_POST['t']) ? $_POST['t'] : 'marca');
        if(!isset(self::$catalogos[$t])){ $t = 'marca'; }
        $d = self::$catalogos[$t];
        return array(
            'titulo' => $d[0], 'singular' => $d[1],
            'tabla' => $d[2], 'pk' => $d[3],
            'ruta' => 'catalogo', 'extra' => array('t' => $t),
            'campos' => array(
                array('name'=>$d[4], 'label'=>'Nombre', 'type'=>'text', 'required'=>true, 'max'=>50)
            ),
            'columnas' => array($d[3] => 'Código', $d[4] => 'Nombre'),
            'deps' => $d[5],
        );
    }
}
