<?php
require_once BASE_PATH . '/models/Usuario.php';

class AuthController extends Controller {

    public function login(){
        if(!empty($_SESSION['usuario'])){
            $this->redirigir('tienda');
        }
        (new Usuario())->preparar();
        $this->renderSolo('auth/login', array());
    }

    public function entrar(){
        $this->soloPost();

        // Freno sencillo contra intentos repetidos
        if(!empty($_SESSION['bloqueo_hasta']) && time() < $_SESSION['bloqueo_hasta']){
            set_flash('error', 'Demasiados intentos. Espera ' . ($_SESSION['bloqueo_hasta'] - time()) . ' segundos.');
            $this->redirigir('auth', 'login');
        }

        $modelo  = new Usuario();
        $usuario = trim(isset($_POST['usuario']) && is_string($_POST['usuario']) ? $_POST['usuario'] : '');
        $clave   = isset($_POST['clave']) && is_string($_POST['clave']) ? $_POST['clave'] : '';
        $fila    = $usuario !== '' ? $modelo->buscar($usuario) : false;

        if($fila && password_verify($clave, $fila['clave'])){
            session_regenerate_id(true);
            unset($_SESSION['intentos'], $_SESSION['bloqueo_hasta']);
            $_SESSION['usuario'] = array(
                'id' => (int)$fila['id_usuario'], 'nombre' => $fila['nombre'], 'usuario' => $fila['usuario']
            );
            $this->redirigir('tienda');
        }

        $_SESSION['intentos'] = (isset($_SESSION['intentos']) ? $_SESSION['intentos'] : 0) + 1;
        if($_SESSION['intentos'] >= 5){
            $_SESSION['bloqueo_hasta'] = time() + 30;
            $_SESSION['intentos'] = 0;
        }
        set_flash('error', 'Usuario o contraseña incorrectos.');
        $this->redirigir('auth', 'login');
    }

    public function salir(){
        $this->soloPost();
        $_SESSION = array();
        session_destroy();
        header('Location: ' . url('auth', 'login'));
        exit;
    }

    public function cuenta(){
        $this->exigirSesion();
        $this->render('auth/cuenta', array(), 'Mi cuenta');
    }

    public function cambiar(){
        $this->exigirSesion();
        $this->soloPost();
        $modelo = new Usuario();
        $u = $modelo->obtener($_SESSION['usuario']['id']);

        $actual = isset($_POST['actual']) ? $_POST['actual'] : '';
        $nueva  = isset($_POST['nueva']) ? $_POST['nueva'] : '';
        $repite = isset($_POST['repite']) ? $_POST['repite'] : '';

        if(!$u || !password_verify($actual, $u['clave'])){
            set_flash('error', 'La contraseña actual no es correcta.');
        }elseif(strlen($nueva) < 6){
            set_flash('error', 'La nueva contraseña debe tener al menos 6 caracteres.');
        }elseif($nueva !== $repite){
            set_flash('error', 'La confirmación no coincide con la nueva contraseña.');
        }else{
            $modelo->cambiarClave($u['id_usuario'], $nueva);
            set_flash('ok', 'Contraseña actualizada.');
        }
        $this->redirigir('auth', 'cuenta');
    }

    private function exigirSesion(){
        if(empty($_SESSION['usuario'])){
            $this->redirigir('auth', 'login');
        }
    }
}
