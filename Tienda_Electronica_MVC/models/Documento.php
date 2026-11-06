<?php
/**
 * Base para ventas y compras (cabecera + líneas de detalle + movimiento de stock).
 * Todo se guarda dentro de una transacción: si algo falla, no se modifica nada.
 */
abstract class Documento {

    protected $db;
    protected $tabla;     // cabecera: ventas / compras
    protected $pk;        // nro_f_venta / nro_f_compra
    protected $detalle;   // ventas_producto / compras_producto
    protected $tercero;   // id_cliente / id_proveedor
    protected $signo;     // -1 = la venta descuenta stock, +1 = la compra suma stock

    public function __construct(){
        $this->db = (new Conexion())->conectar();
    }

    abstract public function listar();
    abstract public function obtener($id);
    abstract protected function lineasActuales($id);
    abstract protected function insertarDetalle($id, $l);

    /**
     * $lineas: array de array(cod_producto, cantidad, precio, subtotal)
     * Devuelve el número del documento.
     */
    public function guardar($id, $tercero, $fecha, $pago, $lineas){
        $this->db->beginTransaction();
        try{
            $afectados = array();

            if($id){
                $ex = $this->db->prepare("SELECT COUNT(*) FROM `{$this->tabla}` WHERE `{$this->pk}` = ?");
                $ex->execute(array($id));
                if((int)$ex->fetchColumn() === 0){
                    throw new RuntimeException('El documento que intentas editar no existe.');
                }
                // deshacer el efecto de la versión anterior
                foreach($this->lineasActuales($id) as $old){
                    $this->moverStock($old['cod_producto'], -$this->signo * (int)$old['cantidad']);
                    $afectados[] = (int)$old['cod_producto'];
                }
                $this->db->prepare("DELETE FROM `{$this->detalle}` WHERE `{$this->pk}` = ?")->execute(array($id));
                $this->db->prepare(
                    "UPDATE `{$this->tabla}` SET `{$this->tercero}` = ?, fecha = ?, cod_f_pago = ? WHERE `{$this->pk}` = ?"
                )->execute(array($tercero, $fecha, $pago, $id));
            }else{
                $this->db->prepare(
                    "INSERT INTO `{$this->tabla}` (`{$this->tercero}`, fecha, cod_f_pago, total) VALUES (?, ?, ?, 0)"
                )->execute(array($tercero, $fecha, $pago));
                $id = $this->db->lastInsertId();
            }

            $total = 0;
            foreach($lineas as $l){
                $this->insertarDetalle($id, $l);
                $this->moverStock($l['cod_producto'], $this->signo * (int)$l['cantidad']);
                $afectados[] = (int)$l['cod_producto'];
                $total += $l['subtotal'];
            }

            $this->db->prepare("UPDATE `{$this->tabla}` SET total = ? WHERE `{$this->pk}` = ?")
                     ->execute(array(round($total, 2), $id));

            $this->verificarStock($afectados);
            $this->db->commit();
            return $id;
        }catch(Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    public function eliminar($id){
        $this->db->beginTransaction();
        try{
            $afectados = array();
            foreach($this->lineasActuales($id) as $old){
                $this->moverStock($old['cod_producto'], -$this->signo * (int)$old['cantidad']);
                $afectados[] = (int)$old['cod_producto'];
            }
            $this->db->prepare("DELETE FROM `{$this->detalle}` WHERE `{$this->pk}` = ?")->execute(array($id));
            $this->db->prepare("DELETE FROM `{$this->tabla}` WHERE `{$this->pk}` = ?")->execute(array($id));
            $this->verificarStock($afectados);
            $this->db->commit();
        }catch(Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    protected function moverStock($producto, $delta){
        $this->db->prepare("UPDATE producto SET stock = stock + ? WHERE cod_producto = ?")
                 ->execute(array($delta, $producto));
    }

    /** Si algún producto quedó con stock negativo se cancela toda la operación. */
    protected function verificarStock($productos){
        $productos = array_values(array_unique(array_map('intval', $productos)));
        if(!$productos){ return; }
        $in = implode(',', array_fill(0, count($productos), '?'));
        $ex = $this->db->prepare("SELECT COUNT(*) FROM producto WHERE cod_producto IN ($in)");
        $ex->execute($productos);
        if((int)$ex->fetchColumn() !== count($productos)){
            throw new RuntimeException('Alguno de los productos seleccionados ya no existe.');
        }
        $st = $this->db->prepare("SELECT nom_producto, stock FROM producto WHERE cod_producto IN ($in) AND stock < 0");
        $st->execute($productos);
        $malos = $st->fetchAll();
        if($malos){
            $txt = array();
            foreach($malos as $m){
                $txt[] = $m['nom_producto'] . ' (quedaría en ' . $m['stock'] . ')';
            }
            throw new RuntimeException('Stock insuficiente para: ' . implode(', ', $txt) . '.');
        }
    }

    public function terceros(){
        return array();
    }

    public function formasPago(){
        return $this->db->query("SELECT cod_f_pago AS id, nom_f_pago AS texto FROM forma_pago ORDER BY nom_f_pago")->fetchAll();
    }
}
