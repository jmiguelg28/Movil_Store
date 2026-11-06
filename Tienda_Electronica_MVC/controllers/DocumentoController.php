<?php
require_once BASE_PATH . '/models/Producto.php';

/** Lógica común de Ventas y Compras. */
abstract class DocumentoController extends Controller {

    protected $modelo;
    protected $doc;   // configuración de textos / rutas

    abstract protected function configuracion();

    public function __construct(){
        $this->doc = $this->configuracion();
    }

    public function index(){
        $this->render('documentos/lista', array(
            'doc' => $this->doc, 'filas' => $this->modelo->listar()
        ), $this->doc['titulo']);
    }

    /** Exporta el registro actual de ventas o compras a un CSV compatible con Excel. */
    public function exportar(){
        $filas = $this->modelo->listar();
        $nombre = $this->doc['ruta'] . '_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Cache-Control: no-store, no-cache');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, array('Número', $this->doc['tercero'], 'Fecha', 'Forma de pago', 'Unidades', 'Total'), ';');
        foreach($filas as $f){
            // Evita que nombres manipulados se evalúen como fórmulas en hojas de cálculo.
            $texto = function($v){ $v = (string)$v; return preg_match('/^[\s]*[=+\-@]/u', $v) ? "'" . $v : $v; };
            fputcsv($out, array($f['id'], $texto($f['tercero']), $f['fecha'], $texto($f['forma_pago']), (int)$f['unidades'], $f['total']), ';');
        }
        fclose($out);
        exit;
    }

    public function ver(){
        $datos = $this->cargar();
        $this->render('documentos/ver', array(
            'doc' => $this->doc, 'cab' => $datos['cab'], 'items' => $datos['items']
        ), $this->doc['singular'] . ' #' . $datos['cab']['id']);
    }

    public function crear(){
        // Si llega ?producto=ID (botón "Vender" de Productos) se suma al borrador que ya tenga el navegador
        $pre = array();
        $id  = (int)(isset($_GET['producto']) ? $_GET['producto'] : 0);
        if($id > 0){
            $pre[] = array('cod_producto' => $id, 'cantidad' => 1, 'precio' => null);
        }
        $this->formulario(null, array('fecha' => date('Y-m-d')), array(), array(), $pre);
    }

    public function editar(){
        $datos = $this->cargar();
        $cab = $datos['cab'];
        $lineas = array();
        foreach($datos['items'] as $i){
            $lineas[] = array('cod_producto' => (int)$i['cod_producto'],
                              'cantidad' => (int)$i['cantidad'], 'precio' => (float)$i['precio']);
        }
        $this->formulario($cab['id'], array(
            'tercero_id' => $cab['tercero_id'], 'fecha' => $cab['fecha'], 'cod_f_pago' => $cab['cod_f_pago']
        ), $lineas);
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
        try{
            $this->modelo->eliminar((int)(isset($_POST['id']) ? $_POST['id'] : 0));
            set_flash('ok', $this->doc['singular'] . ' eliminada. El stock fue ajustado.');
        }catch(RuntimeException $e){
            set_flash('error', $e->getMessage());
        }catch(PDOException $e){
            set_flash('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
        $this->redirigir($this->doc['ruta']);
    }

    // ---------- internos ----------

    protected function cargar(){
        $id = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
        $datos = $this->modelo->obtener($id);
        if(!$datos){
            set_flash('error', 'El documento no existe.');
            $this->redirigir($this->doc['ruta']);
        }
        return $datos;
    }

    protected function formulario($id, $v, $lineas, $errores = array(), $pre = array()){
        $productos = (new Producto())->disponibles();
        $this->render('documentos/form', array(
            'doc' => $this->doc, 'id' => $id, 'v' => $v, 'lineas' => $lineas, 'errores' => $errores,
            'terceros' => $this->modelo->terceros(),
            'pagos'    => $this->modelo->formasPago(),
            'productos' => $productos, 'pre' => $pre,
        ), ($id ? 'Editar ' : 'Nueva ') . strtolower($this->doc['singular']));
    }

    protected function procesar($id){
        $errores = array();
        $tercero = (int)(isset($_POST['tercero_id']) ? $_POST['tercero_id'] : 0);
        $pago    = (int)(isset($_POST['cod_f_pago']) ? $_POST['cod_f_pago'] : 0);
        $fecha   = isset($_POST['fecha']) && is_string($_POST['fecha']) ? $_POST['fecha'] : '';

        if($tercero <= 0){ $errores[] = 'Selecciona ' . $this->doc['tercero_art'] . ' ' . strtolower($this->doc['tercero']) . '.'; }
        if($pago <= 0)   { $errores[] = 'Selecciona la forma de pago.'; }
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        if(!$d || $d->format('Y-m-d') !== $fecha){ $errores[] = 'La fecha no es válida.'; }

        // Unir líneas repetidas del mismo producto
        $prod = isset($_POST['producto']) ? (array)$_POST['producto'] : array();
        $cant = isset($_POST['cantidad']) ? (array)$_POST['cantidad'] : array();
        $prec = isset($_POST['precio'])   ? (array)$_POST['precio']   : array();
        $unidas = array();
        foreach($prod as $i => $p){
            $p = (int)$p;
            if($p <= 0){ continue; }
            $q = isset($cant[$i]) && is_scalar($cant[$i]) ? $cant[$i] : 0;
            $x = isset($prec[$i]) && is_scalar($prec[$i]) ? $prec[$i] : '';
            if(!ctype_digit((string)$q) || (int)$q <= 0 || (int)$q > 1000000){ $errores[] = 'Las cantidades deben ser enteros mayores a 0.'; break; }
            if(!is_numeric($x) || !is_finite((float)$x) || $x < 0 || $x > 9999999999){ $errores[] = 'Los precios deben ser números (0 o mayor).'; break; }
            $q = (int)$q;
            $sub = round($q * (float)$x, 2);
            if(isset($unidas[$p])){
                $unidas[$p]['cantidad'] += $q;
                $unidas[$p]['subtotal'] += $sub;
            }else{
                $unidas[$p] = array('cod_producto' => $p, 'cantidad' => $q, 'subtotal' => $sub);
            }
        }
        if(!$unidas && !$errores){ $errores[] = 'Agrega al menos un producto.'; }

        $lineas = array();
        foreach($unidas as $l){
            $l['precio'] = round($l['subtotal'] / $l['cantidad'], 2);
            $lineas[] = $l;
        }

        if($errores){
            $mostrar = array();
            foreach($lineas as $l){
                $mostrar[] = array('cod_producto' => $l['cod_producto'], 'cantidad' => $l['cantidad'], 'precio' => $l['precio']);
            }
            $this->formulario($id, array('tercero_id' => $tercero, 'fecha' => $fecha, 'cod_f_pago' => $pago), $mostrar, $errores);
            return;
        }

        try{
            $nro = $this->modelo->guardar($id, $tercero, $fecha, $pago, $lineas);
            set_flash('ok', $this->doc['singular'] . ' #' . $nro . ($id ? ' actualizada.' : ' registrada.') . ' El stock fue ajustado.');
            $this->redirigir($this->doc['ruta'], 'ver', array('id' => $nro, 'guardado' => 1));
        }catch(RuntimeException $e){
            $mostrar = array();
            foreach($lineas as $l){
                $mostrar[] = array('cod_producto' => $l['cod_producto'], 'cantidad' => $l['cantidad'], 'precio' => $l['precio']);
            }
            $this->formulario($id, array('tercero_id' => $tercero, 'fecha' => $fecha, 'cod_f_pago' => $pago),
                              $mostrar, array($e->getMessage()));
        }catch(PDOException $e){
            set_flash('error', 'No se pudo guardar: ' . $e->getMessage());
            $this->redirigir($this->doc['ruta']);
        }
    }
}
