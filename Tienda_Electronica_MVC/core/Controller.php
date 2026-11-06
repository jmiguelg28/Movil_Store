<?php
/** Controlador base: render de vistas, redirecciones y validación POST. */
abstract class Controller {

    protected function render($vista, $datos = array(), $titulo = ''){
        extract($datos);
        $tituloPagina = $titulo;
        $rutaActual   = isset($_GET['c']) ? $_GET['c'] : 'tienda';
        $catActual    = isset($_GET['t']) ? $_GET['t'] : '';
        require BASE_PATH . '/views/layout/header.php';
        require BASE_PATH . '/views/layout/sidebar.php';
        require BASE_PATH . '/views/' . $vista . '.php';
        require BASE_PATH . '/views/layout/footer.php';
    }

    /** Vista a pantalla completa (login), sin menú ni barra superior. */
    protected function renderSolo($vista, $datos = array()){
        extract($datos);
        require BASE_PATH . '/views/' . $vista . '.php';
    }

    protected function redirigir($c, $a = 'index', $params = array()){
        header('Location: ' . url($c, $a, $params));
        exit;
    }

    /** Solo acepta POST con token CSRF válido. */
    protected function soloPost(){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            exit('Método no permitido');
        }
        $token = isset($_POST['csrf']) ? $_POST['csrf'] : '';
        if(empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)){
            http_response_code(400);
            exit('Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
        }
    }
}
