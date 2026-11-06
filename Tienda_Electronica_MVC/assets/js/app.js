(function(){
    'use strict';

    // Buscador de tablas: <input data-filtro="#tabla">
    document.querySelectorAll('[data-filtro]').forEach(function(input){
        var tabla = document.querySelector(input.getAttribute('data-filtro'));
        if(!tabla){ return; }
        input.addEventListener('input', function(){
            var q = input.value.toLowerCase();
            tabla.querySelectorAll('tbody tr').forEach(function(tr){
                tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
            });
        });
    });

    // Confirmación al eliminar: <form data-confirmar="¿Seguro?">
    document.querySelectorAll('form[data-confirmar]').forEach(function(f){
        f.addEventListener('submit', function(e){
            if(!confirm(f.getAttribute('data-confirmar'))){ e.preventDefault(); }
        });
    });

    // ---------- Carrito de venta (se comparte con el formulario de Ventas) ----------
    var CLAVE_CARRITO = 'borrador_ventas';

    function leerCarrito(){
        try{
            var c = JSON.parse(localStorage.getItem(CLAVE_CARRITO));
            if(c && Array.isArray(c.lineas)){ return c; }
        }catch(e){}
        return { tercero: '', pago: '', fecha: '', lineas: [] };
    }
    function guardarCarrito(c){
        try{ localStorage.setItem(CLAVE_CARRITO, JSON.stringify(c)); }catch(e){}
    }
    function actualizarBadge(){
        var badge = document.getElementById('badge-carrito');
        if(!badge){ return; }
        var n = 0;
        leerCarrito().lineas.forEach(function(l){ n += parseInt(l.cantidad, 10) || 0; });
        badge.textContent = n > 99 ? '99+' : n;
        badge.hidden = n <= 0;
    }
    function avisar(texto){
        var viejo = document.querySelector('.toast-carrito');
        if(viejo){ viejo.remove(); }
        var t = document.createElement('div');
        t.className = 'toast-carrito';
        var span = document.createElement('span'); span.textContent = texto;
        var a = document.createElement('a'); a.href = 'index.php?c=tienda&a=carrito'; a.textContent = 'Ver carrito';
        t.appendChild(span); t.appendChild(a);
        document.body.appendChild(t);
        setTimeout(function(){ t.remove(); }, 4000);
    }
    function agregarAlCarrito(id, cantidad, precio, stock, nombre){
        var carrito = leerCarrito();
        var linea = carrito.lineas.filter(function(l){ return String(l.cod_producto) === String(id); })[0];
        var actual = linea ? (parseInt(linea.cantidad, 10) || 0) : 0;
        var nueva = Math.min(actual + cantidad, stock);
        if(nueva <= actual){
            avisar('Ya tienes todo el stock disponible de ' + nombre + ' en el carrito.');
            return false;
        }
        if(linea){ linea.cantidad = nueva; }
        else{ carrito.lineas.push({ cod_producto: String(id), cantidad: nueva, precio: precio }); }
        guardarCarrito(carrito);
        actualizarBadge();
        avisar(nombre + ' agregado al carrito.');
        return true;
    }

    document.addEventListener('click', function(e){
        var b = e.target.closest ? e.target.closest('[data-add]') : null;
        if(!b){ return; }
        var cant = 1;
        var campo = b.getAttribute('data-cant') ? document.querySelector(b.getAttribute('data-cant')) : null;
        if(campo){ cant = Math.max(1, parseInt(campo.value, 10) || 1); }
        var ok = agregarAlCarrito(b.getAttribute('data-add'), cant, parseFloat(b.getAttribute('data-precio')) || 0,
                                  parseInt(b.getAttribute('data-stock'), 10) || 0, b.getAttribute('data-nombre') || 'Producto');
        if(ok && b.getAttribute('data-ir')){ location.href = 'index.php?c=ventas&a=crear'; }
    });
    actualizarBadge();

    // Selector de cantidad del detalle del producto
    var cantDet = document.getElementById('cant-detalle');
    if(cantDet){
        var tope = parseInt(cantDet.max, 10) || 1;
        var ajustar = function(d){ cantDet.value = Math.min(tope, Math.max(1, (parseInt(cantDet.value, 10) || 1) + d)); };
        var menos = document.querySelector('[data-menos]'); if(menos){ menos.addEventListener('click', function(){ ajustar(-1); }); }
        var mas = document.querySelector('[data-mas]'); if(mas){ mas.addEventListener('click', function(){ ajustar(1); }); }
        cantDet.addEventListener('change', function(){ ajustar(0); });
    }

    // Ordenar: envía el formulario al cambiar la opción
    document.querySelectorAll('[data-autosubmit]').forEach(function(sel){
        sel.addEventListener('change', function(){ sel.form.submit(); });
    });

    // Vista previa de la imagen al elegir un archivo
    var inputImg = document.getElementById('imagen');
    if(inputImg){
        inputImg.addEventListener('change', function(){
            var f = inputImg.files && inputImg.files[0];
            if(f && document.getElementById('vista-previa')){ document.getElementById('vista-previa').src = URL.createObjectURL(f); }
        });
    }

    // Botón del menú: en pantallas grandes alterna entre menú completo y solo íconos;
    // en pantallas pequeñas abre / cierra el menú. La elección se recuerda.
    var btnMenu = document.getElementById('btn-menu');
    function esMovil(){ return window.innerWidth <= 1000; }
    function recordarMenu(compacto){
        try{ localStorage.setItem('menuCompacto', compacto ? '1' : '0'); }catch(e){}
    }
    if(btnMenu){
        btnMenu.addEventListener('click', function(){
            if(esMovil()){
                document.body.classList.toggle('menu-abierto');
                btnMenu.setAttribute('aria-expanded', document.body.classList.contains('menu-abierto') ? 'true' : 'false');
            }else{
                recordarMenu(document.body.classList.toggle('menu-compacto'));
            }
        });
    }
    // Con el menú compacto, tocar "Catálogos" lo expande para mostrar la lista
    var resumenCat = document.querySelector('.submenu summary');
    if(resumenCat){
        resumenCat.addEventListener('click', function(){
            if(!esMovil() && document.body.classList.contains('menu-compacto')){
                document.body.classList.remove('menu-compacto');
                recordarMenu(false);
            }
        });
    }

    // Cierra el menú de usuario al hacer clic fuera
    document.addEventListener('click', function(e){
        document.querySelectorAll('details.usuario-menu[open]').forEach(function(d){
            if(!d.contains(e.target)){ d.removeAttribute('open'); }
        });
    });

    // Cancelar un documento nuevo descarta su borrador
    document.querySelectorAll('[data-limpiar]').forEach(function(a){
        a.addEventListener('click', function(){
            try{ localStorage.removeItem('borrador_' + a.getAttribute('data-limpiar')); }catch(e){}
        });
    });

    // Alternar tema visual sin afectar los datos ni el funcionamiento.
    var btnTema = document.getElementById('btn-tema');
    if(btnTema){
        btnTema.addEventListener('click', function(){
            var oscuro = document.body.classList.toggle('tema-oscuro');
            try{ localStorage.setItem('movill-tema', oscuro ? 'oscuro' : 'claro'); }catch(e){}
            btnTema.setAttribute('aria-pressed', oscuro ? 'true' : 'false');
        });
        btnTema.setAttribute('aria-pressed', document.body.classList.contains('tema-oscuro') ? 'true' : 'false');
    }
    var overlay = document.getElementById('cerrar-menu');
    if(overlay){overlay.addEventListener('click', function(){document.body.classList.remove('menu-abierto');});}
    document.querySelectorAll('.sidebar nav a').forEach(function(a){a.addEventListener('click', function(){document.body.classList.remove('menu-abierto');});});

    // Al confirmar correctamente la venta, eliminar solo el borrador de esa venta.
    var qs = new URLSearchParams(location.search);
    if((qs.get('c')==='ventas'||qs.get('c')==='compras') && qs.get('a')==='ver' && qs.get('guardado')==='1'){
        try{localStorage.removeItem('borrador_'+qs.get('c'))}catch(e){}
        actualizarBadge();
        if(history.replaceState){qs.delete('guardado');history.replaceState(null,'',location.pathname+'?'+qs.toString());}
    }

    // El carrito usa el inventario y precio ACTUALES del servidor (no el valor del navegador).
    var listaCart = document.getElementById('cart-list');
    if(listaCart && Array.isArray(window.CARRITO_PRODUCTOS)){
        var mapaCart = {};
        window.CARRITO_PRODUCTOS.forEach(function(p){mapaCart[p.id]=p;});
        var centavos = function(n){return '$'+Math.round(n).toLocaleString('es-CO');};
        function pintarCarrito(){
            var cart = leerCarrito();
            var limpio = [];
            cart.lineas.forEach(function(linea){
                var p = mapaCart[linea.cod_producto];
                if(!p || p.stock<=0) return;
                var q = Math.min(p.stock,Math.max(0,parseInt(linea.cantidad,10)||0));
                if(q>0)limpio.push({cod_producto:String(p.id),cantidad:q,precio:p.precio});
            });
            cart.lineas = limpio;
            guardarCarrito(cart);
            actualizarBadge();
            listaCart.replaceChildren();
            var unidades=0, total=0;
            limpio.forEach(function(linea){
                var p=mapaCart[linea.cod_producto];
                unidades+=linea.cantidad;total+=p.precio*linea.cantidad;
                var item=document.createElement('article');item.className='cart-item';
                var img=document.createElement('img');img.src=p.imagen;img.alt=p.nombre;img.onerror=function(){this.onerror=null;this.src='assets/img/sin-imagen.svg'};
                var info=document.createElement('div');info.className='cart-item-info';
                var title=document.createElement('a');title.href='index.php?c=tienda&a=ver&id='+p.id;title.textContent=p.nombre;title.className='cart-item-name';
                var sub=document.createElement('small');sub.textContent='Disponible: '+p.stock+' unidades';
                var rem=document.createElement('button');rem.type='button';rem.className='enlace cart-remove';rem.textContent='Eliminar';
                rem.addEventListener('click',function(){cart.lineas=cart.lineas.filter(function(l){return String(l.cod_producto)!==String(p.id)});guardarCarrito(cart);pintarCarrito();});
                info.append(title,sub,rem);
                var qty=document.createElement('div');qty.className='cart-quantity';
                var minus=document.createElement('button');minus.type='button';minus.textContent='−';minus.setAttribute('aria-label','Restar unidad');
                var count=document.createElement('span');count.textContent=linea.cantidad;
                var plus=document.createElement('button');plus.type='button';plus.textContent='+';plus.setAttribute('aria-label','Sumar unidad');
                function cambia(delta){var n=Math.max(1,Math.min(p.stock,linea.cantidad+delta));cart.lineas.forEach(function(l){if(String(l.cod_producto)===String(p.id))l.cantidad=n;});guardarCarrito(cart);pintarCarrito();}
                minus.addEventListener('click',function(){cambia(-1)});plus.addEventListener('click',function(){cambia(1)});
                qty.append(minus,count,plus);
                var precio=document.createElement('strong');precio.className='cart-item-price';precio.textContent=centavos(p.precio*linea.cantidad);
                item.append(img,info,qty,precio);listaCart.append(item);
            });
            document.getElementById('cart-empty').hidden=limpio.length>0;
            document.getElementById('cart-clear').hidden=limpio.length===0;
            document.getElementById('cart-conteo').textContent=unidades;
            document.getElementById('cart-units').textContent=unidades;
            document.getElementById('cart-subtotal').textContent=centavos(total);
            document.getElementById('cart-total').textContent=centavos(total);
            var check=document.getElementById('cart-checkout');
            check.classList.toggle('disabled', !limpio.length);
            check.setAttribute('aria-disabled', limpio.length?'false':'true');
            check.tabIndex=limpio.length?0:-1;
        }
        document.getElementById('cart-clear').addEventListener('click',function(){var cart=leerCarrito();cart.lineas=[];guardarCarrito(cart);pintarCarrito();});
        document.getElementById('cart-checkout').addEventListener('click',function(e){if(!leerCarrito().lineas.length)e.preventDefault();});
        pintarCarrito();
    }

    // ---------- Formulario de ventas / compras ----------
    var cfg = window.DOC;
    var tabla = document.getElementById('tabla-lineas');
    if(!cfg || !tabla){ return; }

    var cuerpo = tabla.querySelector('tbody');
    var totalEl = document.getElementById('total');
    var mapa = {};
    cfg.productos.forEach(function(p){ mapa[p.id] = p; });

    function dinero(n){ return '$' + Math.round(n).toLocaleString('es-CO'); }

    var formulario = document.getElementById('form-documento');
    var claveBorrador = 'borrador_' + cfg.ruta;

    function leerBorrador(){
        try{ return JSON.parse(localStorage.getItem(claveBorrador)); }catch(e){ return null; }
    }

    // El borrador solo aplica a documentos nuevos (no a los que se están editando)
    function guardarBorrador(){
        if(cfg.id){ return; }
        var lineas = [];
        cuerpo.querySelectorAll('tr').forEach(function(tr){
            var prod = tr.querySelector('select').value;
            if(!prod){ return; }
            lineas.push({
                cod_producto: prod,
                cantidad: tr.querySelector('.cantidad').value,
                precio: tr.querySelector('.precio').value
            });
        });
        try{
            localStorage.setItem(claveBorrador, JSON.stringify({
                tercero: document.getElementById('tercero_id').value,
                pago: document.getElementById('cod_f_pago').value,
                fecha: document.getElementById('fecha').value,
                lineas: lineas
            }));
        }catch(e){}
        actualizarBadge();
    }

    function recalcular(){
        var total = 0;
        cuerpo.querySelectorAll('tr').forEach(function(tr){
            var c = parseFloat(tr.querySelector('.cantidad').value) || 0;
            var p = parseFloat(tr.querySelector('.precio').value) || 0;
            var s = c * p;
            tr.querySelector('.sub').textContent = dinero(s);
            total += s;
        });
        totalEl.textContent = dinero(total);
        guardarBorrador();
    }

    function crearFila(linea){
        linea = linea || {};
        var tr = document.createElement('tr');

        // Producto
        var td1 = document.createElement('td');
        var sel = document.createElement('select');
        sel.name = 'producto[]';
        sel.required = true;
        var vacio = document.createElement('option');
        vacio.value = ''; vacio.textContent = '— Seleccione producto —';
        sel.appendChild(vacio);
        cfg.productos.forEach(function(p){
            var o = document.createElement('option');
            o.value = p.id;
            o.textContent = p.nombre;
            if(String(linea.cod_producto) === String(p.id)){ o.selected = true; }
            sel.appendChild(o);
        });
        var ayuda = document.createElement('small');
        td1.appendChild(sel); td1.appendChild(ayuda);

        // Cantidad
        var td2 = document.createElement('td');
        var cant = document.createElement('input');
        cant.type = 'number'; cant.name = 'cantidad[]'; cant.className = 'cantidad';
        cant.min = 1; cant.step = 1; cant.required = true;
        cant.value = linea.cantidad || 1;
        td2.appendChild(cant);

        // Precio
        var td3 = document.createElement('td');
        var prec = document.createElement('input');
        prec.type = 'number'; prec.name = 'precio[]'; prec.className = 'precio';
        prec.min = 0; prec.step = '0.01'; prec.required = true;
        td3.appendChild(prec);

        // Subtotal + quitar
        var td4 = document.createElement('td');
        td4.className = 'num sub'; td4.textContent = '$0';
        var td5 = document.createElement('td');
        var quitar = document.createElement('button');
        quitar.type = 'button'; quitar.className = 'quitar'; quitar.textContent = 'Quitar';
        quitar.title = 'Quitar línea';
        td5.appendChild(quitar);

        [td1, td2, td3, td4, td5].forEach(function(td){ tr.appendChild(td); });

        function actualizarAyuda(){
            var p = mapa[sel.value];
            if(cfg.muestraStock && p){
                ayuda.textContent = 'Stock disponible: ' + p.stock;
                cant.max = p.stock;
            }else{
                ayuda.textContent = '';
                cant.removeAttribute('max');
            }
        }

        sel.addEventListener('change', function(){
            var p = mapa[sel.value];
            if(p && cfg.precioAuto){ prec.value = p.precio; }
            actualizarAyuda();
            recalcular();
        });
        cant.addEventListener('input', recalcular);
        prec.addEventListener('input', recalcular);
        quitar.addEventListener('click', function(){
            if(cuerpo.querySelectorAll('tr').length > 1){ tr.remove(); recalcular(); }
        });

        // precio inicial: el guardado, o el del producto si es venta
        if(linea.precio !== undefined && linea.precio !== null){
            prec.value = linea.precio;
        }else if(cfg.precioAuto && mapa[linea.cod_producto]){
            prec.value = mapa[linea.cod_producto].precio;
        }else{
            prec.value = 0;
        }

        cuerpo.appendChild(tr);
        actualizarAyuda();
        return tr;
    }

    document.getElementById('agregar-linea').addEventListener('click', function(){
        crearFila({});
        recalcular();
    });

    // Qué líneas mostrar al abrir el formulario
    var inicial = [];
    var recuperado = false;
    if(cfg.id || (cfg.lineas && cfg.lineas.length)){
        // edición, o formulario devuelto por el servidor con errores
        inicial = cfg.lineas || [];
    }else{
        var borrador = leerBorrador();
        if(borrador){
            inicial = borrador.lineas || [];
            recuperado = inicial.length > 0;
            if(borrador.tercero){ document.getElementById('tercero_id').value = borrador.tercero; }
            if(borrador.pago){ document.getElementById('cod_f_pago').value = borrador.pago; }
            if(borrador.fecha){ document.getElementById('fecha').value = borrador.fecha; }
        }
        // producto que viene desde la lista de Productos: se suma a lo que ya había
        (cfg.pre || []).forEach(function(nuevo){
            var existente = inicial.filter(function(l){
                return String(l.cod_producto) === String(nuevo.cod_producto);
            })[0];
            if(existente){
                existente.cantidad = (parseInt(existente.cantidad, 10) || 0) + 1;
            }else{
                inicial.push(nuevo);
            }
        });
        // quita ?producto= de la URL para que al recargar no se sume otra vez
        if(cfg.pre && cfg.pre.length && window.history && history.replaceState){
            history.replaceState(null, '', location.pathname + '?c=' + encodeURIComponent(cfg.ruta) + '&a=crear');
        }
    }

    if(inicial.length){
        inicial.forEach(crearFila);
    }else{
        crearFila({});
    }
    recalcular();

    var aviso = document.getElementById('aviso-borrador');
    if(aviso && recuperado){ aviso.hidden = false; }

    // Cambios en la cabecera también se guardan
    ['tercero_id', 'cod_f_pago', 'fecha'].forEach(function(id){
        var el = document.getElementById(id);
        if(el){ el.addEventListener('change', guardarBorrador); }
    });

    // Empezar de nuevo
    var vaciar = document.getElementById('vaciar');
    if(vaciar){
        vaciar.addEventListener('click', function(){
            cuerpo.innerHTML = '';
            crearFila({});
            recalcular();
            if(aviso){ aviso.hidden = true; }
        });
    }

    // Conservar el borrador hasta que el servidor confirme la venta.
    formulario.addEventListener('submit', guardarBorrador);
})();
