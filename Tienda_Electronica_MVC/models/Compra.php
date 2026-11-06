<?php
require_once BASE_PATH . '/models/Documento.php';

/**
 * Nota: en el modelo original compras_producto tenía columnas varchar.
 * Aquí se usa CAST para que funcione tanto con varchar como con INT/DECIMAL.
 */
class Compra extends Documento {

    protected $tabla   = 'compras';
    protected $pk      = 'nro_f_compra';
    protected $detalle = 'compras_producto';
    protected $tercero = 'id_proveedor';
    protected $signo   = 1;

    public function listar(){
        $sql = "SELECT c.nro_f_compra AS id, c.fecha, c.total,
                       p.nom_proveedor AS tercero, f.nom_f_pago AS forma_pago,
                       (SELECT COALESCE(SUM(CAST(cp.cantidad AS UNSIGNED)), 0) FROM compras_producto cp
                         WHERE cp.nro_f_compra = c.nro_f_compra) AS unidades
                FROM compras c
                LEFT JOIN proveedores p ON p.id_proveedor = c.id_proveedor
                LEFT JOIN forma_pago f  ON f.cod_f_pago = c.cod_f_pago
                ORDER BY c.nro_f_compra DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtener($id){
        $st = $this->db->prepare(
            "SELECT c.nro_f_compra AS id, c.id_proveedor AS tercero_id, c.fecha, c.cod_f_pago, c.total,
                    p.nom_proveedor AS tercero, f.nom_f_pago AS forma_pago
             FROM compras c
             LEFT JOIN proveedores p ON p.id_proveedor = c.id_proveedor
             LEFT JOIN forma_pago f  ON f.cod_f_pago = c.cod_f_pago
             WHERE c.nro_f_compra = ?"
        );
        $st->execute(array($id));
        $cab = $st->fetch();
        if(!$cab){ return null; }

        $st = $this->db->prepare(
            "SELECT CAST(cp.cod_producto AS UNSIGNED) AS cod_producto, p.nom_producto,
                    CAST(cp.cantidad AS UNSIGNED) AS cantidad,
                    CAST(cp.valor AS DECIMAL(14,2)) AS precio,
                    CAST(cp.subtotal AS DECIMAL(14,2)) AS subtotal
             FROM compras_producto cp
             LEFT JOIN producto p ON p.cod_producto = CAST(cp.cod_producto AS UNSIGNED)
             WHERE cp.nro_f_compra = ?
             ORDER BY p.nom_producto"
        );
        $st->execute(array($id));
        return array('cab' => $cab, 'items' => $st->fetchAll());
    }

    protected function lineasActuales($id){
        $st = $this->db->prepare(
            "SELECT CAST(cod_producto AS UNSIGNED) AS cod_producto, CAST(cantidad AS UNSIGNED) AS cantidad
             FROM compras_producto WHERE nro_f_compra = ?"
        );
        $st->execute(array($id));
        return $st->fetchAll();
    }

    protected function insertarDetalle($id, $l){
        $this->db->prepare(
            "INSERT INTO compras_producto (nro_f_compra, cod_producto, cantidad, valor, subtotal)
             VALUES (?, ?, ?, ?, ?)"
        )->execute(array($id, $l['cod_producto'], $l['cantidad'], $l['precio'], $l['subtotal']));
    }

    public function terceros(){
        return $this->db->query("SELECT id_proveedor AS id, nom_proveedor AS texto FROM proveedores ORDER BY nom_proveedor")->fetchAll();
    }
}
