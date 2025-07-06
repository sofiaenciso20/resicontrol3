
<div class="body-recuperar">
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-image">
            <div class="logo-superior">
                <img src="assets/img/logo2.png" alt="Logo ResiControl" style="height: 50px;">
            </div>
        </div>
        <div class="login-form">
            <div class="text-center">
                <h4>Recuperar contraseña</h4>
                <p class="text-muted">Ingresa tu correo para recibir el enlace de recuperación</p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="correo" class="form-control" placeholder="tu@correo.com" required>
                </div>
                
                <button type="submit" class="btn btn-login">Enviar enlace</button>
            </form>
            <!-- Verifica si existe la variable $mensaje y si contiene algo.
              Si, muestra una alerta (tipo info) con el contenido.
              Se usa htmlspecialchars para evitar problemas si el mensaje incluye caracteres especiales.-->
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-info mt-3">
                    <?= $mensaje ?>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-3">
                <a href="login.php">← Volver al login</a>
            </div>
        </div>
    </div>
</div>
</div>
