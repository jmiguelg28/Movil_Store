<?php
/**
 * Modelo genérico para tablas simples.
 * $cfg: tabla, pk, campos[], lista_sql (opcional), deps[] (tablas que dependen de este registro)
 */
class Crud {

    private $db;
    private $cfg;

    public function __construct($cfg){
        $this->cfg = $cfg;
        $this->db  = (new Conexion())->conectar();
    }

    public function listar(){
        if(!empty($this->cfg['lista_sql'])){
            return $this->db->query($this->cfg['lista_sql'])->fetchAll();
        }
        $sql = "SELECT * FROM `{$this->cfg['tabla']}` ORDER BY `{$this->cfg['pk']}` DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtener($id){
        $sql = "SELECT * FROM `{$this->cfg['tabla']}` WHERE `{$this->cfg['pk']}` = ?";
        $st  = $this->db->prepare($sql);
        $st->execute(array($id));
        return $st->fetch();
    }

    public function crear($datos){
        $cols = array_keys($datos);
        $sql  = "INSERT INTO `{$this->cfg['tabla']}` (`" . implode('`,`', $cols) . "`) VALUES ("
              . implode(',', array_fill(0, count($cols), '?')) . ")";
        $this->db->prepare($sql)->execute(array_values($datos));
        return $this->db->lastInsertId();
    }

    public function actualizar($id, $datos){
        $set = array();
        foreach(array_keys($datos) as $col){ $set[] = "`$col` = ?"; }
        $sql = "UPDATE `{$this->cfg['tabla']}` SET " . implode(', ', $set)
             . " WHERE `{$this->cfg['pk']}` = ?";
        $valores = array_values($datos);
        $valores[] = $id;
        $this->db->prepare($sql)->execute($valores);
    }

    public function eliminar($id){
        // Evita dejar registros huérfanos aunque la BD no tenga llaves foráneas
        if(!empty($this->cfg['deps'])){
            foreach($this->cfg['deps'] as $dep){
                $st = $this->db->prepare("SELECT COUNT(*) FROM `{$dep[0]}` WHERE `{$dep[1]}` = ?");
                $st->execute(array($id));
                $n = (int)$st->fetchColumn();
                if($n > 0){
                    throw new RuntimeException(
                        "No se puede eliminar: está asociado a $n registro(s) de {$dep[2]}."
                    );
                }
            }
        }
        $st = $this->db->prepare("DELETE FROM `{$this->cfg['tabla']}` WHERE `{$this->cfg['pk']}` = ?");
        $st->execute(array($id));
    }

    /** Opciones para un <select>: array de array('id'=>..,'texto'=>..) */
    public function opciones($o){
        $sql = "SELECT `{$o['id']}` AS id, `{$o['texto']}` AS texto FROM `{$o['tabla']}` ORDER BY `{$o['texto']}`";
        return $this->db->query($sql)->fetchAll();
    }
}
