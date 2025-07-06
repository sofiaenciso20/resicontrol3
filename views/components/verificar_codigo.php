<div class="body-recuperar">
  <div class="login-wrapper">
    <div class="login-card">
      
      <!-- Imagen o logo superior -->
      <div class="login-image">
        <div class="logo-superior">
          <img src="assets/img/logo2.png" alt="Logo ResiControl" style="height: 50px;">
        </div>
      </div>

      <!-- Formulario de verificación y cambio de contraseña -->
      <div class="login-form">
        <div class="text-center">
          <h4>Verificar código y cambiar contraseña</h4>
        </div>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
             <!--Si hay un correo en la URL (?correo), lo muestra en el campo.
                  htmlspecialchars() previene ataques XSS escapando caracteres especiales.-->
            <input type="email" name="correo" class="form-control" required 
              value="<?php echo isset($_GET['correo']) ? htmlspecialchars($_GET['correo']) : ''; ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Código de recuperación</label>
            <input type="text" name="codigo" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Nueva contraseña</label>
            <input type="password" name="nueva_contra" class="form-control" required>
          </div>

          <button type="submit" class="btn btn-login">Cambiar contraseña</button>
        </form>
         <!-- Verifica si existe la variable $mensaje y si contiene algo.
              Si, muestra una alerta (tipo info) con el contenido.
              Se usa htmlspecialchars para evitar problemas si el mensaje incluye caracteres especiales.-->
        <?php if (isset($mensaje) && $mensaje): ?>
          <div class="alert alert-info mt-3">
            <?= htmlspecialchars($mensaje) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
