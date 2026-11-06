<?php
require_once BASE_PATH . '/controllers/CrudController.php';

class ProveedorController extends CrudController {
    protected function configuracion(){
        return array(
            'titulo' => 'Proveedores', 'singular' => 'Proveedor',
            'tabla' => 'proveedores', 'pk' => 'id_proveedor',
            'ruta' => 'proveedores', 'extra' => array(),
            'campos' => array(
                array('name'=>'nom_proveedor',    'label'=>'Nombre',    'type'=>'text',  'required'=>true, 'max'=>50),
                array('name'=>'dir_proveedor',    'label'=>'Dirección', 'type'=>'text',  'max'=>100),
                array('name'=>'tel_proveedor',    'label'=>'Teléfono',  'type'=>'tel',   'max'=>20),
                array('name'=>'correo_proveedor', 'label'=>'Correo',    'type'=>'email', 'max'=>100),
                array('name'=>'cod_ciudad',       'label'=>'Ciudad',    'type'=>'select',
                      'opciones'=>array('tabla'=>'ciudad','id'=>'cod_ciudad','texto'=>'nom_ciudad')),
            ),
            'lista_sql' => "SELECT p.id_proveedor, p.nom_proveedor, p.dir_proveedor, p.tel_proveedor,
                                   p.correo_proveedor, ci.nom_ciudad
                            FROM proveedores p
                            LEFT JOIN ciudad ci ON ci.cod_ciudad = p.cod_ciudad
                            ORDER BY p.id_proveedor DESC",
            'columnas' => array(
                'id_proveedor'=>'#', 'nom_proveedor'=>'Nombre', 'tel_proveedor'=>'Teléfono',
                'correo_proveedor'=>'Correo', 'nom_ciudad'=>'Ciudad'
            ),
            'deps' => array(array('compras', 'id_proveedor', 'compras')),
        );
    }
}
