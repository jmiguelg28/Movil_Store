<?php
/**
 * Conexión a MySQL (PDO).
 * Ajusta host / usuario / contraseña según tu servidor.
 * En XAMPP por defecto: usuario "root" y contraseña vacía ("").
 */
class Conexion {

    // Opcional: MOVILL_DB_HOST, MOVILL_DB_NAME, MOVILL_DB_USER, MOVILL_DB_PASS.
    // Para XAMPP se conservan los valores por defecto.
    private $host     = "127.0.0.1";
    private $db       = "movil_store_nueva";
    private $usuario  = "root";
    private $password = "";

    public function conectar(){
        try{
            $conexion = new PDO(
                "mysql:host=".(getenv('MOVILL_DB_HOST') ?: $this->host).";dbname=".(getenv('MOVILL_DB_NAME') ?: $this->db).";charset=utf8mb4",
                getenv('MOVILL_DB_USER') !== false ? getenv('MOVILL_DB_USER') : $this->usuario,
                getenv('MOVILL_DB_PASS') !== false ? getenv('MOVILL_DB_PASS') : $this->password,
                array(
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // misma hora de Colombia en MySQL (CURDATE, NOW...) que en PHP
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '-05:00'"
                )
            );
            return $conexion;
        }catch(PDOException $e){
            error_log("Movill Store: error de conexión a la base de datos: " . $e->getMessage());
            http_response_code(503);
            exit("No se pudo conectar con MySQL. Revisa XAMPP y config/conexion.php.");
        }
    }
}
