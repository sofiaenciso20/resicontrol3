<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
          <h3 class="mb-0">Registro de Paquete</h3>
          <?php if (isset($vigilante_logueado)): ?>
          <small class="text-light">
            <i class="bi bi-person-badge"></i> 
            Vigilante: <?= htmlspecialchars($vigilante_logueado['name']) ?>
          </small>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <?php if (!empty($mensaje)): ?>
            <div class="alert <?= strpos($mensaje, '✅') !== false ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show d-flex align-items-center" role="alert">
              <i class="bi <?= strpos($mensaje, '✅') !== false ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
              <?= htmlspecialchars($mensaje); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <!-- Información del vigilante logueado -->
          <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
              <i class="bi bi-info-circle me-2"></i>
              <div>
                <strong>Información:</strong> El paquete se registrará automáticamente con la fecha y hora actual del sistema.
                <br>
                <small class="text-muted">
                  Vigilante responsable: <strong><?= htmlspecialchars($vigilante_logueado['name'] ?? 'Usuario actual') ?></strong>
                  <br>
                  Fecha/Hora de registro: <strong><span id="fechaActual"></span></strong>
                </small>
              </div>
            </div>
          </div>

          <form method="POST" action="/registro_paquete.php" id="formRegistroPaquete" novalidate>
            <div class="mb-3">
              <label class="form-label">
                <i class="bi bi-person-fill me-1"></i>
                Residente Destinatario
              </label>
              <select name="id_usuarios" class="form-select" required>
                <option value="">Seleccione un residente</option>
                <?php foreach ($residentes as $residente): ?>
                  <option value="<?= $residente['documento'] ?>">
                    <?= htmlspecialchars($residente['nombre_completo']) ?>
                    <?php if (!empty($residente['direccion_casa'])): ?>
                      - Casa: <?= htmlspecialchars($residente['direccion_casa']) ?>
                    <?php endif; ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Debe seleccionar un residente destinatario.</div>
              <div class="form-text">Seleccione el residente que recibirá el paquete.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">
                <i class="bi bi-box-seam me-1"></i>
                Descripción del Paquete
              </label>
              <input type="text" name="descripcion" class="form-control" required
                     pattern="[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ,.\- ]{5,100}" maxlength="100"
                     placeholder="Ej: Caja mediana con documentos, Sobre amarillo, Paquete de Amazon">
              <div class="invalid-feedback">Ingrese una descripción válida (5-100 caracteres).</div>
              <div class="form-text">Describa brevemente el contenido o características del paquete.</div>
            </div>

            <div class="d-flex justify-content-between">
              <a href="historial_paquetes.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Ver Historial
              </a>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-box-arrow-in-down"></i> Registrar Paquete
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ✅ VALIDACIÓN JS -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formRegistroPaquete');

    // Validación personalizada al enviar
    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add('was-validated');
    });

    // Validación en tiempo real
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
      input.addEventListener('input', function () {
        if (this.checkValidity()) {
          this.classList.remove('is-invalid');
          this.classList.add('is-valid');
        } else {
          this.classList.remove('is-valid');
          this.classList.add('is-invalid');
        }
      });
    });

    // Auto-completar búsqueda de residentes
    const selectResidente = document.querySelector('select[name="id_usuarios"]');
    selectResidente.addEventListener('change', function() {
      if (this.value) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
      }
    });

    // Mostrar fecha y hora actual en tiempo real
    function actualizarFechaActual() {
      const ahora = new Date();
      const opciones = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit', 
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit',
        hour12: false 
      };
      document.getElementById('fechaActual').textContent = ahora.toLocaleString('es-CO', opciones);
    }

    // Actualizar cada segundo
    actualizarFechaActual();
    setInterval(actualizarFechaActual, 1000);
  });
</script>
