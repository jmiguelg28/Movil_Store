<div class="page-header">
    <h1>Mi cuenta</h1>
    <p>Sesión iniciada como <strong><?= e($_SESSION['usuario']['nombre']) ?></strong> (<?= e($_SESSION['usuario']['usuario']) ?>)</p>
</div>

<div class="form-card">
    <h3 class="subtitulo" style="margin-top:0">Cambiar contraseña</h3>
    <form method="POST" action="<?= url('auth', 'cambiar') ?>">
        <?= csrf_field() ?>
        <label for="actual">Contraseña actual</label>
        <input id="actual" type="password" name="actual" required autocomplete="current-password">
        <label for="nueva">Nueva contraseña</label>
        <input id="nueva" type="password" name="nueva" minlength="6" required autocomplete="new-password">
        <label for="repite">Repite la nueva contraseña</label>
        <input id="repite" type="password" name="repite" minlength="6" required autocomplete="new-password">
        <div class="botones">
            <button class="btn-primary">Guardar</button>
            <a class="btn-secundario" href="<?= url('dashboard') ?>">Volver</a>
        </div>
    </form>
</div>
