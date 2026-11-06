<?php
class Usuario {

    private $db;

    public function __construct(){
        $this->db = (new Conexion())->conectar();
    }

    /**
     * Crea la tabla de usuarios si no existe y, si está vacía, el usuario inicial:
     *   usuario: admin    contraseña: admin123
     */
    public function preparar(){
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS usuarios (
                id_usuario INT NOT NULL AUTO_INCREMENT,
                nombre VARCHAR(60) NOT NULL,
                usuario VARCHAR(30) NOT NULL,
                clave VARCHAR(255) NOT NULL,
                creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id_usuario),
                UNIQUE KEY uq_usuario (usuario)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        if((int)$this->db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn() === 0){
            $this->db->prepare("INSERT INTO usuarios (nombre, usuario, clave) VALUES (?, ?, ?)")
                     ->execute(array('Administrador', 'admin', password_hash('admin123', PASSWORD_DEFAULT)));
        }
    }

    public function buscar($usuario){
        $st = $this->db->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $st->execute(array($usuario));
        return $st->fetch();
    }

    public function obtener($id){
        $st = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
        $st->execute(array($id));
        return $st->fetch();
    }

    public function cambiarClave($id, $nueva){
        $this->db->prepare("UPDATE usuarios SET clave = ? WHERE id_usuario = ?")
                 ->execute(array(password_hash($nueva, PASSWORD_DEFAULT), $id));
    }
}
