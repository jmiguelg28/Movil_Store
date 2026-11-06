<?php
/** Compatibilidad con instalaciones PHP sin extensión mbstring (preferir mbstring cuando exista). */
if(!function_exists('mb_strlen')){
    function mb_strlen($s){ return function_exists('iconv_strlen') ? iconv_strlen((string)$s, 'UTF-8') : strlen((string)$s); }
}
if(!function_exists('mb_substr')){
    function mb_substr($s, $inicio, $largo = null){
        if(function_exists('iconv_substr')){ return iconv_substr((string)$s, $inicio, $largo === null ? iconv_strlen((string)$s, 'UTF-8') : $largo, 'UTF-8'); }
        return substr((string)$s, $inicio, $largo === null ? strlen((string)$s) : $largo);
    }
}
if(!function_exists('mb_strtolower')){ function mb_strtolower($s){ return strtolower((string)$s); } }
if(!function_exists('mb_strtoupper')){ function mb_strtoupper($s){ return strtoupper((string)$s); } }

/** Escapa texto para HTML (evita XSS). */
function e($valor){
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

/** Formato de dinero: $1.250.000 */
function dinero($n){
    return '$' . number_format((float)$n, 0, ',', '.');
}

function fecha_corta($f){
    return $f ? date('d/m/Y', strtotime($f)) : '';
}

/** Construye una URL del sistema: url('clientes','editar',array('id'=>3)) */
function url($c = 'dashboard', $a = 'index', $params = array()){
    return 'index.php?' . http_build_query(array_merge(array('c' => $c, 'a' => $a), $params));
}

/** Mensajes temporales (ok / error) */
function set_flash($tipo, $mensaje){
    $_SESSION['flash'] = array('tipo' => $tipo, 'msg' => $mensaje);
}
function get_flash(){
    if(!isset($_SESSION['flash'])){ return null; }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

/** Token CSRF para los formularios POST */
function csrf_token(){
    if(empty($_SESSION['csrf'])){
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}
function csrf_field(){
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Íconos de línea (SVG en línea, sin dependencias externas). */
function icono($nombre, $tam = 18){
    $f = array(
        'inicio'   => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'caja'     => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/>',
        'carrito'  => '<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>',
        'camion'   => '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
        'usuarios' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'maletin'  => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
        'capas'    => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
        'menu'     => '<line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>',
        'subida'   => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>',
        'usuario'  => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'candado'  => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'ojo'      => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'tienda'   => '<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>',
        'lupa'     => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'calendario'=> '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
    );
    $d = isset($f[$nombre]) ? $f[$nombre] : '';
    return '<svg class="ico" width="' . $tam . '" height="' . $tam . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
}

function fecha_larga(){
    $dias  = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
    $meses = array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
    $txt = $dias[(int)date('w')] . ', ' . date('j') . ' de ' . $meses[(int)date('n') - 1];
    return mb_strtoupper(mb_substr($txt, 0, 1)) . mb_substr($txt, 1);
}

/** Ruta de la imagen del producto (o una imagen genérica si no tiene). */
function img_producto($ruta){
    return $ruta ? $ruta : 'assets/img/sin-imagen.svg';
}

/** URL de la tienda cambiando solo algunos filtros (reinicia la página). */
function url_tienda($cambios = array()){
    $q = $_GET;
    unset($q['c'], $q['a']);
    if(!array_key_exists('pagina', $cambios)){ unset($q['pagina']); }
    foreach($cambios as $k => $v){
        if($v === null || $v === '' || $v === false){ unset($q[$k]); } else { $q[$k] = $v; }
    }
    return url('tienda', 'index', $q);
}

/** Texto y clase según el stock. */
function estado_stock($stock){
    $stock = (int)$stock;
    if($stock <= 0){ return array('Agotado', 'rojo'); }
    if($stock <= 5){ return array('¡Últimas ' . $stock . ' unidades!', 'amarillo'); }
    return array('Stock disponible: ' . $stock, 'verde');
}
