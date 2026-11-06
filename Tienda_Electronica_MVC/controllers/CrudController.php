<?php
require_once BASE_PATH . '/models/Crud.php';

/**
 * CRUD genérico (listar, crear, editar, eliminar) para tablas simples.
 * Cada controlador hijo solo define configuracion().
 */
abstract class CrudController extends Controller {

    protected $cfg;
    protected $modelo;

    abstract protected function configuracion();

    public function __construct(){
        $this->cfg    = $this->configuracion();
        $this->modelo = new Crud($this->cfg);
    }

    public function index(){
        $this->render('crud/lista', array(
            'cfg'   => $this->cfg,
            'filas' => $this->modelo->listar()
        ), $this->cfg['titulo']);
    }

    public function crear(){
        $this->formulario(null, array());
    }

    public function editar(){
        $id   = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
        $fila = $this->modelo->obtener($id);
        if(!$fila){
            set_flash('error', 'El registro no existe.');
            $this->volver();
        }
        $this->formulario($id, $fila);
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
            $this->modelo->eliminar($id);
            set_flash('ok', $this->cfg['singular'] . ' eliminado correctamente.');
        }catch(RuntimeException $e){
            set_flash('error', $e->getMessage());
        }catch(PDOException $e){
            set_flash('error', 'No se pudo eliminar: el registro está siendo usado en otras tablas.');
        }
        $this->volver();
    }

    // ---------- internos ----------

    protected function volver(){
        $this->redirigir($this->cfg['ruta'], 'index', $this->cfg['extra']);
    }

    protected function formulario($id, $valores, $errores = array()){
        $opciones = array();
        foreach($this->cfg['campos'] as $campo){
            if(isset($campo['type']) && $campo['type'] === 'select'){
                $opciones[$campo['name']] = $this->modelo->opciones($campo['opciones']);
            }
        }
        $this->render('crud/form', array(
            'cfg' => $this->cfg, 'id' => $id, 'valores' => $valores,
            'errores' => $errores, 'opciones' => $opciones
        ), ($id ? 'Editar ' : 'Agregar ') . $this->cfg['singular']);
    }

    protected function procesar($id){
        list($datos, $errores) = $this->validar($_POST);
        if($errores){
            $this->formulario($id, $_POST, $errores);
            return;
        }
        try{
            if($id){
                $this->modelo->actualizar($id, $datos);
                set_flash('ok', $this->cfg['singular'] . ' actualizado correctamente.');
            }else{
                $this->modelo->crear($datos);
                set_flash('ok', $this->cfg['singular'] . ' creado correctamente.');
            }
        }catch(PDOException $e){
            set_flash('error', 'No se pudo guardar: ' . $e->getMessage());
        }
        $this->volver();
    }

    protected function validar($entrada){
        $datos = array();
        $errores = array();
        foreach($this->cfg['campos'] as $campo){
            $n    = $campo['name'];
            $tipo = isset($campo['type']) ? $campo['type'] : 'text';
            $v    = isset($entrada[$n]) && is_scalar($entrada[$n]) ? trim((string)$entrada[$n]) : '';

            if($v === ''){
                if(!empty($campo['required'])){
                    $errores[$n] = $campo['label'] . ' es obligatorio.';
                }
                $datos[$n] = null;
                continue;
            }
            if(!empty($campo['max']) && mb_strlen($v) > $campo['max']){
                $errores[$n] = $campo['label'] . ' admite máximo ' . $campo['max'] . ' caracteres.';
            }
            if($tipo === 'email' && !filter_var($v, FILTER_VALIDATE_EMAIL)){
                $errores[$n] = 'El correo no es válido.';
            }
            if($tipo === 'select'){ $v = (int)$v; }
            $datos[$n] = $v;
        }
        return array($datos, $errores);
    }
}
