<?php
class Dashboard {

    private $db;

    public function __construct(){
        $this->db = (new Conexion())->conectar();
    }

    private function valor($sql){
        return $this->db->query($sql)->fetchColumn();
    }

    public function indicadores(){
        return array(
            'productos'    => (int)$this->valor("SELECT COUNT(*) FROM producto"),
            'clientes'     => (int)$this->valor("SELECT COUNT(*) FROM clientes"),
            'proveedores'  => (int)$this->valor("SELECT COUNT(*) FROM proveedores"),
            'ventas'       => (int)$this->valor("SELECT COUNT(*) FROM ventas"),
            'compras'      => (int)$this->valor("SELECT COUNT(*) FROM compras"),
            'total_ventas' => (float)$this->valor("SELECT COALESCE(SUM(total),0) FROM ventas"),
            'total_compras'=> (float)$this->valor("SELECT COALESCE(SUM(total),0) FROM compras"),
            'ventas_mes'   => (float)$this->valor(
                "SELECT COALESCE(SUM(total),0) FROM ventas
                 WHERE YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())"),
            'valor_inventario' => (float)$this->valor("SELECT COALESCE(SUM(precio * stock),0) FROM producto"),
        );
    }

    /** Ventas y compras de los últimos 12 meses (rellena con 0 los meses sin movimiento). */
    public function seriesMensuales(){
        $nombres = array('Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
        $desde = new DateTime('first day of this month');
        $desde->modify('-11 months');

        $v = $this->mapaMensual('ventas', $desde->format('Y-m-d'));
        $c = $this->mapaMensual('compras', $desde->format('Y-m-d'));

        $labels = array(); $ventas = array(); $compras = array();
        $m = clone $desde;
        for($i = 0; $i < 12; $i++){
            $k = $m->format('Y-m');
            $labels[]  = $nombres[(int)$m->format('n') - 1] . ' ' . $m->format('y');
            $ventas[]  = isset($v[$k]) ? $v[$k] : 0;
            $compras[] = isset($c[$k]) ? $c[$k] : 0;
            $m->modify('+1 month');
        }
        return array('labels' => $labels, 'ventas' => $ventas, 'compras' => $compras);
    }

    private function mapaMensual($tabla, $desde){
        $st = $this->db->prepare(
            "SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(total) AS total
             FROM `$tabla` WHERE fecha >= ?
             GROUP BY DATE_FORMAT(fecha, '%Y-%m')"
        );
        $st->execute(array($desde));
        $out = array();
        foreach($st->fetchAll() as $r){ $out[$r['mes']] = (float)$r['total']; }
        return $out;
    }

    public function topClientes(){
        return $this->db->query(
            "SELECT c.nom_cliente AS nombre, SUM(v.total) AS total, COUNT(*) AS n
             FROM ventas v JOIN clientes c ON c.id_cliente = v.id_cliente
             GROUP BY c.id_cliente, c.nom_cliente
             ORDER BY total DESC LIMIT 5"
        )->fetchAll();
    }

    public function topProductos(){
        return $this->db->query(
            "SELECT p.nom_producto AS nombre, SUM(vp.cantidad) AS unidades
             FROM ventas_producto vp JOIN producto p ON p.cod_producto = vp.cod_producto
             GROUP BY p.cod_producto, p.nom_producto
             ORDER BY unidades DESC LIMIT 5"
        )->fetchAll();
    }

    public function topProveedores(){
        return $this->db->query(
            "SELECT pr.nom_proveedor AS nombre, SUM(c.total) AS total, COUNT(*) AS n
             FROM compras c JOIN proveedores pr ON pr.id_proveedor = c.id_proveedor
             GROUP BY pr.id_proveedor, pr.nom_proveedor
             ORDER BY total DESC LIMIT 5"
        )->fetchAll();
    }

    public function ventasPorPago(){
        return $this->db->query(
            "SELECT f.nom_f_pago AS nombre, SUM(v.total) AS total
             FROM ventas v JOIN forma_pago f ON f.cod_f_pago = v.cod_f_pago
             GROUP BY f.cod_f_pago, f.nom_f_pago
             ORDER BY total DESC"
        )->fetchAll();
    }

    public function stockBajo(){
        return $this->db->query(
            "SELECT nom_producto, stock FROM producto WHERE stock <= 5 ORDER BY stock ASC, nom_producto LIMIT 8"
        )->fetchAll();
    }

    public function ultimasVentas(){
        return $this->db->query(
            "SELECT v.nro_f_venta, v.fecha, v.total, c.nom_cliente
             FROM ventas v LEFT JOIN clientes c ON c.id_cliente = v.id_cliente
             ORDER BY v.fecha DESC, v.nro_f_venta DESC LIMIT 6"
        )->fetchAll();
    }
}
