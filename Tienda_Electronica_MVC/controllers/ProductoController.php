<?php
require_once BASE_PATH . '/models/Producto.php';

class ProductoController extends Controller {

    private $modelo;

    public function __construct(){
        $this->modelo = new Producto();
    }

    public function index(){
        $this->render('productos/index', array('productos' => $this->modelo->listar()), 'Productos');
    }

    /** Descarga inventario real en CSV, sin exponer información de conexión. */
    public function exportar(){
        $filas = $this->modelo->listar();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="inventario_movill_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-store, no-cache');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, array('Código', 'Producto', 'Marca', 'Categoría', 'Precio', 'Stock', 'Colores'), ';');
        foreach($filas as $f){
            $texto = function($v){ $v = (string)$v; return preg_match('/^[\s]*[=+\-@]/u', $v) ? "'" . $v : $v; };
            fputcsv($out, array($f['cod_producto'], $texto($f['nom_producto']), $texto($f['nom_marca']), $texto($f['nom_tipo_p']), $f['precio'], (int)$f['stock'], $texto($f['colores'])), ';');
        }
        fclose($out);
        exit;
    }

    public function crear(){
        $this->formulario(null, array('stock' => 0, 'colores' => array(), 'materiales' => array()));
    }

    public function editar(){
        $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
        $p  = $this->modelo->obtener($id);
        if(!$p){
            set_flash('error', 'El producto no existe.');
            $this->redirigir('productos');
        }
        $this->formulario($id, $p);
    }

    public function guardar(){
        $this->soloPost();
        $this->procesar(null);
    }

    public function actualizar(){
        $this->soloPost();
        $this->procesar((int)(isset($_POST['id']) ? $_POST['id'] : 0));
    }

    public function eliminar(){
        $this->soloPost();
        $id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
        try{
            $p = $this->modelo->obtener($id);
            $this->modelo->eliminar($id);
            if($p){ $this->borrarArchivo($p['imagen']); }
            set_flash('ok', 'Producto eliminado correctamente.');
        }catch(RuntimeException $e){
            set_flash('error', $e->getMessage());
        }catch(PDOException $e){
            set_flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
        $this->redirigir('productos');
    }

    // ---------- internos ----------

    private function formulario($id, $v, $errores = array()){
        $this->render('productos/form', array(
            'id' => $id, 'v' => $v, 'errores' => $errores,
            'marcas'     => $this->modelo->catalogo('marca', 'cod_marca', 'nom_marca'),
            'tipos'      => $this->modelo->catalogo('tipo_producto', 'cod_tipo_p', 'nom_tipo_p'),
            'colores'    => $this->modelo->catalogo('color', 'cod_color', 'nom_color'),
            'materiales' => $this->modelo->catalogo('material', 'cod_material', 'nom_material'),
        ), $id ? 'Editar producto' : 'Agregar producto');
    }

    private function procesar($id){
        $v = $_POST;
        $v['colores']    = isset($_POST['colores'])    ? array_map('intval', (array)$_POST['colores'])    : array();
        $v['materiales'] = isset($_POST['materiales']) ? array_map('intval', (array)$_POST['materiales']) : array();

        $actual = $id ? $this->modelo->obtener($id) : null;
        if($id && !$actual){
            set_flash('error', 'El producto no existe.');
            $this->redirigir('productos');
        }
        $v['imagen'] = $actual ? $actual['imagen'] : null;

        $errores = array();
        $nombre = trim(isset($v['nom_producto']) && is_string($v['nom_producto']) ? $v['nom_producto'] : '');
        if($nombre === '')              { $errores['nom_producto'] = 'El nombre es obligatorio.'; }
        elseif(mb_strlen($nombre) > 100){ $errores['nom_producto'] = 'Máximo 100 caracteres.'; }

        if(!isset($v['precio']) || !is_numeric($v['precio']) || $v['precio'] < 0){
            $errores['precio'] = 'Ingresa un precio válido (0 o mayor).';
        }
        if(!isset($v['stock']) || !ctype_digit((string)$v['stock'])){
            $errores['stock'] = 'El stock debe ser un número entero (0 o mayor).';
        }
        if(empty($v['cod_marca']))  { $errores['cod_marca']  = 'Selecciona una marca.'; }
        if(empty($v['cod_tipo_p'])) { $errores['cod_tipo_p'] = 'Selecciona un tipo de producto.'; }

        $descripcion = trim(isset($v['descripcion']) && is_string($v['descripcion']) ? $v['descripcion'] : '');
        if(mb_strlen($descripcion) > 2000){ $errores['descripcion'] = 'La descripción admite máximo 2000 caracteres.'; }

        if($errores){
            $this->formulario($id, $v, $errores);
            return;
        }

        $imagen = $actual ? $actual['imagen'] : null;
        $errorImg = '';
        $nueva = $this->subirImagen($errorImg);
        if($errorImg !== ''){
            $this->formulario($id, $v, array('imagen' => $errorImg));
            return;
        }
        $aBorrar = null;
        if($nueva){
            $aBorrar = $imagen;
            $imagen  = $nueva;
        }elseif(!empty($_POST['quitar_imagen'])){
            $aBorrar = $imagen;
            $imagen  = null;
        }

        $datos = array(
            'nom_producto' => $nombre,
            'precio'       => (float)$v['precio'],
            'stock'        => (int)$v['stock'],
            'cod_marca'    => (int)$v['cod_marca'],
            'cod_tipo_p'   => (int)$v['cod_tipo_p'],
            'imagen'       => $imagen,
            'descripcion'  => $descripcion !== '' ? $descripcion : null,
        );
        try{
            if($id){
                $this->modelo->actualizar($id, $datos, $v['colores'], $v['materiales']);
                set_flash('ok', 'Producto actualizado correctamente.');
            }else{
                $this->modelo->crear($datos, $v['colores'], $v['materiales']);
                set_flash('ok', 'Producto creado correctamente.');
            }
            $this->borrarArchivo($aBorrar);
        }catch(PDOException $e){
            $this->borrarArchivo($nueva);
            set_flash('error', 'No se pudo guardar: ' . $e->getMessage());
        }
        $this->redirigir('productos');
    }

    /** Guarda la imagen subida en uploads/productos y devuelve su ruta relativa (o null). */
    private function subirImagen(&$error){
        if(empty($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE){ return null; }
        $f = $_FILES['imagen'];

        if($f['error'] !== UPLOAD_ERR_OK){
            $error = ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE)
                ? 'La imagen es demasiado grande para el servidor (máximo 4 MB).'
                : 'No se pudo subir la imagen. Inténtalo de nuevo.';
            return null;
        }
        if($f['size'] > 4 * 1024 * 1024){ $error = 'La imagen no puede pesar más de 4 MB.'; return null; }

        $info = @getimagesize($f['tmp_name']);
        $tipos = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif');
        if(defined('IMAGETYPE_WEBP')){ $tipos[IMAGETYPE_WEBP] = 'webp'; }
        if(!$info || !isset($tipos[$info[2]])){
            $error = 'El archivo debe ser una imagen JPG, PNG, WEBP o GIF.';
            return null;
        }

        $dir = BASE_PATH . '/uploads/productos';
        if(!is_dir($dir) && !@mkdir($dir, 0775, true)){
            $error = 'No se pudo crear la carpeta uploads/productos.';
            return null;
        }
        $nombre = bin2hex(random_bytes(10)) . '.' . $tipos[$info[2]];
        if(!move_uploaded_file($f['tmp_name'], $dir . '/' . $nombre)){
            $error = 'No se pudo guardar la imagen en el servidor.';
            return null;
        }
        return 'uploads/productos/' . $nombre;
    }

    /** Solo borra archivos subidos por el usuario, nunca las imágenes de demostración. */
    private function borrarArchivo($ruta){
        if($ruta && strpos($ruta, 'uploads/productos/') === 0){
            $archivo = BASE_PATH . '/uploads/productos/' . basename($ruta);
            if(is_file($archivo)){ @unlink($archivo); }
        }
    }
}
