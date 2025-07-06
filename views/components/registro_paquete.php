<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
          <h3 class="mb-0">Registro de Paquete</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info d-flex align-items-center" role="alert">
              <i class="bi bi-info-circle-fill me-2"></i>
              <?= htmlspecialchars($mensaje); ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="/registro_paquete.php" id="formRegistroPaquete" novalidate>
            <div class="mb-3">
              <label class="form-label">Residente</label>
              <select name="id_usuarios" class="form-select" required>
                <option value="">Seleccione un residente</option>
                <?php foreach ($residentes as $residente): ?>
                  <option value="<?= $residente['documento'] ?>"><?= htmlspecialchars($residente['nombre_completo']) ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Debe seleccionar un residente.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Vigilante</label>
              <select name="id_vigilante" class="form-select" required>
                <option value="">Seleccione un vigilante</option>
                <?php foreach ($vigilantes as $vigilante): ?>
                  <option value="<?= $vigilante['documento'] ?>"><?= htmlspecialchars($vigilante['nombre_completo']) ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Debe seleccionar un vigilante.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Descripción del Paquete</label>
              <input type="text" name="descripcion" class="form-control" required
                     pattern="[A-Za-z0-9ÁÉÍÓÚáéíóúñÑ,. ]{5,100}" maxlength="100"
                     placeholder="Ej: Caja mediana con documentos">
              <div class="invalid-feedback">Ingrese una descripción válida (5-100 caracteres, letras, números, comas o puntos).</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Fecha y Hora de Recepción</label>
              <input type="datetime-local" name="fech_hor_recep" id="fech_hor_recep" class="form-control" required>
              <div class="invalid-feedback">Ingrese una fecha y hora válida (presente o futura).</div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-success">Registrar Encomienda</button>
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
    const fechaInput = document.getElementById('fech_hor_recep');

    // Establecer fecha mínima como ahora
    const ahora = new Date();
    const zona = ahora.toISOString().slice(0, 16);
    fechaInput.min = zona;

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
  });
</script>
