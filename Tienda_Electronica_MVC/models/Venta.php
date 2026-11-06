<?php
require_once BASE_PATH . '/models/Documento.php';

class Venta extends Documento {

    protected $tabla   = 'ventas';
    protected $pk      = 'nro_f_venta';
    protected $detalle = 'ventas_producto';
    protected $tercero = 'id_cliente';
    protected $signo   = -1;

    public function listar(){
        $sql = "SELECT v.nro_f_venta AS id, v.fecha, v.total,
                       c.nom_cliente AS tercero, f.nom_f_pago AS forma_pago,
                       (SELECT COALESCE(SUM(vp.cantidad), 0) FROM ventas_producto vp
                         WHERE vp.nro_f_venta = v.nro_f_venta) AS unidades
                FROM ventas v
                LEFT JOIN clientes c   ON c.id_cliente = v.id_cliente
                LEFT JOIN forma_pago f ON f.cod_f_pago = v.cod_f_pago
                ORDER BY v.nro_f_venta DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtener($id){
        $st = $this->db->prepare(
            "SELECT v.nro_f_venta AS id, v.id_cliente AS tercero_id, v.fecha, v.cod_f_pago, v.total,
                    c.nom_cliente AS tercero, f.nom_f_pago AS forma_pago
             FROM ventas v
             LEFT JOIN clientes c   ON c.id_cliente = v.id_cliente
             LEFT JOIN forma_pago f ON f.cod_f_pago = v.cod_f_pago
             WHERE v.nro_f_venta = ?"
        );
        $st->execute(array($id));
        $cab = $st->fetch();
        if(!$cab){ return null; }

        $st = $this->db->prepare(
            "SELECT vp.cod_producto, p.nom_producto, vp.cantidad, vp.subtotal,
                    IF(vp.cantidad > 0, vp.subtotal / vp.cantidad, 0) AS precio
             FROM ventas_producto vp
             LEFT JOIN producto p ON p.cod_producto = vp.cod_producto
             WHERE vp.nro_f_venta = ?
             ORDER BY p.nom_producto"
        );
        $st->execute(array($id));
        return array('cab' => $cab, 'items' => $st->fetchAll());
    }

    protected function lineasActuales($id){
        $st = $this->db->prepare("SELECT cod_producto, cantidad FROM ventas_producto WHERE nro_f_venta = ?");
        $st->execute(array($id));
        return $st->fetchAll();
    }

    protected function insertarDetalle($id, $l){
        $this->db->prepare(
            "INSERT INTO ventas_producto (nro_f_venta, cod_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)"
        )->execute(array($id, $l['cod_producto'], $l['cantidad'], $l['subtotal']));
    }

    public function terceros(){
        return $this->db->query("SELECT id_cliente AS id, nom_cliente AS texto FROM clientes ORDER BY nom_cliente")->fetchAll();
    }
}
