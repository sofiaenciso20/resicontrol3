<div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <div class="card shadow-lg border-0">
          <div class="card-header bg-primary text-white text-center">
            <h3 class="mb-0">Validar Código de Visita</h3>
          </div>
          <div class="card-body">
            <?php if (!empty($_SESSION['mensaje_visita'])): ?>
              <div class="alert <?= strpos($_SESSION['mensaje_visita'], '✅') !== false ? 'alert-success' : 'alert-danger' ?> d-flex align-items-center" role="alert">
                <?= htmlspecialchars($_SESSION['mensaje_visita']) ?>
              </div>
              <?php unset($_SESSION['mensaje_visita']); // Limpiar el mensaje después de mostrarlo ?>
            <?php endif; ?>
 
            <?php if (isset($_SESSION['codigo_visita_temp'])): ?>
              <div class="alert alert-info mb-4">
                <p class="mb-0">Se ha enviado un código de verificación a tu correo electrónico.</p>
                <p class="mb-0">Por favor, revisa tu bandeja de entrada e ingresa el código para confirmar la visita.</p>
                <small class="text-muted">El código expirará en 30 minutos.</small>
              </div>
            <?php endif; ?>
 
            <form method="POST" action="validar_visitas.php">
              <div class="mb-3">
                <label class="form-label">Código de Verificación</label>
                <input type="text" name="codigo" class="form-control" placeholder="Ingresa el código de 6 caracteres" required pattern="[A-Z0-9]{6}" maxlength="6" style="text-transform: uppercase;">
                <div class="form-text">Ingresa el código que recibiste en tu correo electrónico.</div>
              </div>
              <div class="d-flex justify-content-between">
                <a href="historial_visitas.php" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left"></i> Ver Historial
                </a>
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-check-circle"></i> Validar Código
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Convertir automáticamente a mayúsculas mientras se escribe
    const codigoInput = document.querySelector('input[name="codigo"]');
    codigoInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Enfocar automáticamente el campo de código
    codigoInput.focus();
});
</script>
