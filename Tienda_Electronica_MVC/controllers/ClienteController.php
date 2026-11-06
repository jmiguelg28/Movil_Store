<?php
require_once BASE_PATH . '/controllers/CrudController.php';

class ClienteController extends CrudController {
    protected function configuracion(){
        return array(
            'titulo' => 'Clientes', 'singular' => 'Cliente',
            'tabla' => 'clientes', 'pk' => 'id_cliente',
            'ruta' => 'clientes', 'extra' => array(),
            'campos' => array(
                array('name'=>'nom_cliente',    'label'=>'Nombre',    'type'=>'text',  'required'=>true, 'max'=>50),
                array('name'=>'dir_cliente',    'label'=>'Dirección', 'type'=>'text',  'max'=>100),
                array('name'=>'tel_cliente',    'label'=>'Teléfono',  'type'=>'tel',   'max'=>20),
                array('name'=>'correo_cliente', 'label'=>'Correo',    'type'=>'email', 'max'=>100),
                array('name'=>'cod_ciudad',     'label'=>'Ciudad',    'type'=>'select',
                      'opciones'=>array('tabla'=>'ciudad','id'=>'cod_ciudad','texto'=>'nom_ciudad')),
            ),
            'lista_sql' => "SELECT c.id_cliente, c.nom_cliente, c.dir_cliente, c.tel_cliente,
                                   c.correo_cliente, ci.nom_ciudad
                            FROM clientes c
                            LEFT JOIN ciudad ci ON ci.cod_ciudad = c.cod_ciudad
                            ORDER BY c.id_cliente DESC",
            'columnas' => array(
                'id_cliente'=>'#', 'nom_cliente'=>'Nombre', 'tel_cliente'=>'Teléfono',
                'correo_cliente'=>'Correo', 'nom_ciudad'=>'Ciudad'
            ),
            'deps' => array(array('ventas', 'id_cliente', 'ventas')),
        );
    }
}
