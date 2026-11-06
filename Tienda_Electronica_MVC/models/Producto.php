<?php
class Producto {

    const POR_PAGINA = 12;

    private $db;

    public function __construct(){
        $this->db = (new Conexion())->conectar();
        // Las migraciones de base de datos se ejecutan manualmente, siempre tras un respaldo.
    }

    private function coloresSql(){
        return "(SELECT GROUP_CONCAT(c.nom_color ORDER BY c.nom_color SEPARATOR ', ')
                   FROM producto_color pc JOIN color c ON c.cod_color = pc.cod_color
                  WHERE pc.cod_producto = p.cod_producto) AS colores";
    }

    /** Listado de administración (tabla). */
    public function listar(){
        $sql = "SELECT p.cod_producto, p.nom_producto, p.precio, p.stock, p.imagen,
                       m.nom_marca, t.nom_tipo_p, " . $this->coloresSql() . "
                FROM producto p
                LEFT JOIN marca m ON m.cod_marca = p.cod_marca
                LEFT JOIN tipo_producto t ON t.cod_tipo_p = p.cod_tipo_p
                ORDER BY p.cod_producto DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /** Catálogo de la tienda con filtros, orden y paginación. */
    public function tienda($f){
        $w = array('1=1'); $p = array();

        if($f['q'] !== ''){
            $like = '%' . $f['q'] . '%';
            $w[] = "(p.nom_producto LIKE ? OR m.nom_marca LIKE ? OR t.nom_tipo_p LIKE ? OR p.descripcion LIKE ?)";
            array_push($p, $like, $like, $like, $like);
        }
        if($f['marca'] > 0){ $w[] = "p.cod_marca = ?";  $p[] = $f['marca']; }
        if($f['tipo']  > 0){ $w[] = "p.cod_tipo_p = ?"; $p[] = $f['tipo']; }
        if($f['minimo'] !== null){ $w[] = "p.precio >= ?"; $p[] = $f['minimo']; }
        if($f['maximo'] !== null){ $w[] = "p.precio <= ?"; $p[] = $f['maximo']; }
        if(!empty($f['disponibles'])){ $w[] = "p.stock > 0"; }

        $ordenes = array(
            'nuevos' => 'p.cod_producto DESC',
            'menor'  => 'p.precio ASC, p.nom_producto ASC',
            'mayor'  => 'p.precio DESC, p.nom_producto ASC',
            'nombre' => 'p.nom_producto ASC',
        );
        $orden = isset($ordenes[$f['orden']]) ? $ordenes[$f['orden']] : $ordenes['nuevos'];
        $desde = "FROM producto p
                  LEFT JOIN marca m ON m.cod_marca = p.cod_marca
                  LEFT JOIN tipo_producto t ON t.cod_tipo_p = p.cod_tipo_p
                  WHERE " . implode(' AND ', $w);

        $st = $this->db->prepare("SELECT COUNT(*) $desde");
        $st->execute($p);
        $total = (int)$st->fetchColumn();

        $paginas = max(1, (int)ceil($total / self::POR_PAGINA));
        $pagina  = min(max(1, (int)$f['pagina']), $paginas);
        $offset  = ($pagina - 1) * self::POR_PAGINA;

        $st = $this->db->prepare(
            "SELECT p.cod_producto, p.nom_producto, p.precio, p.stock, p.imagen, m.nom_marca, t.nom_tipo_p
             $desde ORDER BY $orden LIMIT " . self::POR_PAGINA . " OFFSET " . (int)$offset
        );
        $st->execute($p);
        return array('items' => $st->fetchAll(), 'total' => $total, 'pagina' => $pagina, 'paginas' => $paginas);
    }

    public function marcasConConteo(){
        return $this->db->query(
            "SELECT m.cod_marca AS id, m.nom_marca AS texto, COUNT(p.cod_producto) AS n
             FROM marca m JOIN producto p ON p.cod_marca = m.cod_marca
             GROUP BY m.cod_marca, m.nom_marca ORDER BY m.nom_marca"
        )->fetchAll();
    }

    public function tiposConConteo(){
        return $this->db->query(
            "SELECT t.cod_tipo_p AS id, t.nom_tipo_p AS texto, COUNT(p.cod_producto) AS n
             FROM tipo_producto t JOIN producto p ON p.cod_tipo_p = t.cod_tipo_p
             GROUP BY t.cod_tipo_p, t.nom_tipo_p ORDER BY t.nom_tipo_p"
        )->fetchAll();
    }

    /** Producto con marca, tipo, colores y materiales (página de detalle). */
    public function detalle($id){
        $st = $this->db->prepare(
            "SELECT p.*, m.nom_marca, t.nom_tipo_p
             FROM producto p
             LEFT JOIN marca m ON m.cod_marca = p.cod_marca
             LEFT JOIN tipo_producto t ON t.cod_tipo_p = p.cod_tipo_p
             WHERE p.cod_producto = ?"
        );
        $st->execute(array($id));
        $p = $st->fetch();
        if(!$p){ return null; }

        $st = $this->db->prepare("SELECT c.nom_color FROM producto_color pc JOIN color c ON c.cod_color = pc.cod_color WHERE pc.cod_producto = ? ORDER BY c.nom_color");
        $st->execute(array($id));
        $p['colores'] = $st->fetchAll(PDO::FETCH_COLUMN);

        $st = $this->db->prepare("SELECT ma.nom_material FROM producto_material pm JOIN material ma ON ma.cod_material = pm.cod_material WHERE pm.cod_producto = ? ORDER BY ma.nom_material");
        $st->execute(array($id));
        $p['materiales'] = $st->fetchAll(PDO::FETCH_COLUMN);
        return $p;
    }

    public function relacionados($tipo, $excluir){
        $st = $this->db->prepare(
            "SELECT p.cod_producto, p.nom_producto, p.precio, p.stock, p.imagen, m.nom_marca, t.nom_tipo_p
             FROM producto p
             LEFT JOIN marca m ON m.cod_marca = p.cod_marca
             LEFT JOIN tipo_producto t ON t.cod_tipo_p = p.cod_tipo_p
             WHERE p.cod_tipo_p = ? AND p.cod_producto <> ?
             ORDER BY (p.stock > 0) DESC, p.cod_producto DESC LIMIT 4"
        );
        $st->execute(array($tipo, $excluir));
        return $st->fetchAll();
    }

    /** Datos actuales de los productos para revisar el carrito antes de registrar una venta. */
    public function productosCarrito(){
        return $this->db->query("SELECT cod_producto, nom_producto, precio, stock, imagen FROM producto ORDER BY nom_producto")->fetchAll();
    }

    public function disponibles(){
        return $this->db->query(
            "SELECT cod_producto, nom_producto, precio, stock FROM producto ORDER BY nom_producto"
        )->fetchAll();
    }

    public function obtener($id){
        $st = $this->db->prepare("SELECT * FROM producto WHERE cod_producto = ?");
        $st->execute(array($id));
        $p = $st->fetch();
        if(!$p){ return null; }
        $p['colores']    = $this->ids('producto_color', 'cod_color', $id);
        $p['materiales'] = $this->ids('producto_material', 'cod_material', $id);
        return $p;
    }

    private function ids($tabla, $col, $id){
        $st = $this->db->prepare("SELECT `$col` FROM `$tabla` WHERE cod_producto = ?");
        $st->execute(array($id));
        return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    }

    public function catalogo($tabla, $id, $texto){
        return $this->db->query(
            "SELECT `$id` AS id, `$texto` AS texto FROM `$tabla` ORDER BY `$texto`"
        )->fetchAll();
    }

    public function crear($d, $colores, $materiales){
        $this->db->beginTransaction();
        try{
            $st = $this->db->prepare(
                "INSERT INTO producto (nom_producto, precio, stock, cod_marca, cod_tipo_p, imagen, descripcion)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $st->execute(array($d['nom_producto'], $d['precio'], $d['stock'], $d['cod_marca'], $d['cod_tipo_p'], $d['imagen'], $d['descripcion']));
            $id = $this->db->lastInsertId();
            $this->relaciones($id, $colores, $materiales);
            $this->db->commit();
            return $id;
        }catch(Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    public function actualizar($id, $d, $colores, $materiales){
        $this->db->beginTransaction();
        try{
            $st = $this->db->prepare(
                "UPDATE producto SET nom_producto = ?, precio = ?, stock = ?, cod_marca = ?, cod_tipo_p = ?,
                        imagen = ?, descripcion = ?
                 WHERE cod_producto = ?"
            );
            $st->execute(array($d['nom_producto'], $d['precio'], $d['stock'], $d['cod_marca'], $d['cod_tipo_p'], $d['imagen'], $d['descripcion'], $id));
            $this->db->prepare("DELETE FROM producto_color WHERE cod_producto = ?")->execute(array($id));
            $this->db->prepare("DELETE FROM producto_material WHERE cod_producto = ?")->execute(array($id));
            $this->relaciones($id, $colores, $materiales);
            $this->db->commit();
        }catch(Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    private function relaciones($id, $colores, $materiales){
        $c = $this->db->prepare("INSERT INTO producto_color (cod_producto, cod_color) VALUES (?, ?)");
        foreach($colores as $x){ $c->execute(array($id, $x)); }
        $m = $this->db->prepare("INSERT INTO producto_material (cod_producto, cod_material) VALUES (?, ?)");
        foreach($materiales as $x){ $m->execute(array($id, $x)); }
    }

    public function eliminar($id){
        $st = $this->db->prepare(
            "SELECT (SELECT COUNT(*) FROM ventas_producto WHERE cod_producto = ?)
                  + (SELECT COUNT(*) FROM compras_producto WHERE cod_producto = ?)"
        );
        $st->execute(array($id, $id));
        $n = (int)$st->fetchColumn();
        if($n > 0){
            throw new RuntimeException("No se puede eliminar: el producto aparece en $n línea(s) de ventas o compras.");
        }
        $this->db->beginTransaction();
        try{
            $this->db->prepare("DELETE FROM producto_color WHERE cod_producto = ?")->execute(array($id));
            $this->db->prepare("DELETE FROM producto_material WHERE cod_producto = ?")->execute(array($id));
            $this->db->prepare("DELETE FROM producto WHERE cod_producto = ?")->execute(array($id));
            $this->db->commit();
        }catch(Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }
}
