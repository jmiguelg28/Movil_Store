<?php $f = get_flash(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · Movill Store</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<div class="login-grid">

    <section class="hero">
        <span class="blob b1"></span><span class="blob b2"></span><span class="blob b3"></span>

        <div class="hero-contenido">
            <div class="hero-marca"><span class="logo">M</span>Movill Store</div>

            <h1>Tu tienda de tecnología,<br>siempre al día.</h1>
            <p>Ventas, compras e inventario en un solo lugar.</p>

            <div class="grafico" aria-hidden="true">
                <span style="--h:38%;--d:0s"></span><span style="--h:56%;--d:.15s"></span>
                <span style="--h:44%;--d:.3s"></span><span style="--h:72%;--d:.45s"></span>
                <span style="--h:60%;--d:.6s"></span><span style="--h:88%;--d:.75s"></span>
                <span style="--h:76%;--d:.9s"></span>
            </div>

            <div class="chip c1"><i class="verde"><?= icono('subida', 18) ?></i><b>Ventas</b></div>
            <div class="chip c2"><i class="naranja"><?= icono('caja', 18) ?></i><b>Inventario</b></div>
            <div class="chip c3"><i class="rosa"><?= icono('usuarios', 18) ?></i><b>Clientes</b></div>
        </div>
    </section>

    <section class="panel-form">
        <form method="POST" action="<?= url('auth', 'entrar') ?>" class="caja" autocomplete="on">
            <?= csrf_field() ?>
            <h2>Bienvenido</h2>
            <p class="sub">Ingresa con tu usuario para continuar.</p>

            <?php if($f): ?>
                <div class="alerta <?= $f['tipo'] === 'error' ? 'error' : 'ok' ?>"><?= e($f['msg']) ?></div>
            <?php endif; ?>

            <label for="usuario">Usuario</label>
            <div class="campo">
                <?= icono('usuario', 18) ?>
                <input id="usuario" name="usuario" type="text" required autofocus autocomplete="username" placeholder="Tu usuario">
            </div>

            <label for="clave">Contraseña</label>
            <div class="campo">
                <?= icono('candado', 18) ?>
                <input id="clave" name="clave" type="password" required autocomplete="current-password" placeholder="Tu contraseña">
                <button type="button" class="ver" id="ver" aria-label="Mostrar contraseña"><?= icono('ojo', 18) ?></button>
            </div>

            <button class="entrar">Ingresar</button>
        </form>
    </section>

</div>
<script>
    document.getElementById('ver').addEventListener('click', function(){
        var i = document.getElementById('clave');
        i.type = i.type === 'password' ? 'text' : 'password';
        this.classList.toggle('activo');
    });
</script>
</body>
</html>
