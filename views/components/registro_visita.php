<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
          <h3 class="mb-0">Registro de Visita</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info d-flex align-items-center" role="alert">
              <i class="bi bi-info-circle-fill me-2"></i>
              <?= htmlspecialchars($mensaje) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="../registro_visita.php" id="formRegistroVisita" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nombre del Visitante</label>
                <input type="text" name="nombre" class="form-control" required
                  pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}">
                <div class="invalid-feedback">Nombre inválido. Solo letras, mínimo 2 caracteres.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Apellido del Visitante</label>
                <input type="text" name="apellido" class="form-control" required
                  pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ ]{2,50}">
                <div class="invalid-feedback">Apellido inválido. Solo letras, mínimo 2 caracteres.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Documento del Visitante</label>
                <input type="text" name="documento" class="form-control" required 
                      pattern="\d{6,10}" inputmode="numeric">
                <div class="invalid-feedback">Documento inválido. Ingrese entre 6 y 10 dígitos numéricos.</div>
              </div>

               <?php if (in_array($_SESSION['user']['role'], [1, 2])): ?>
              <div class="col-md-6 mb-3">
                <label class="form-label">Residente</label>
                <select name="id_usuarios" class="form-select" required>
                  <option value="">Seleccione un residente</option>
                  <?php foreach ($residentes as $r): ?>
                    <option value="<?= $r['documento'] ?>"><?= htmlspecialchars($r['nombre_completo']) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Debe seleccionar un residente.</div>
              </div>
              <?php else: ?>
                <input type="hidden" name="id_usuarios" value="<?= htmlspecialchars($_SESSION['user']['documento']) ?>">
              <?php endif; ?>
 

              <div class="col-md-6 mb-3">
                <label class="form-label">Motivo de la Visita</label>
                <select name="id_mot_visi" class="form-select" required>
                  <option value="">Seleccione un motivo</option>
                  <?php foreach ($motivos as $m): ?>
                    <option value="<?= $m['id_mot_visi'] ?>"><?= htmlspecialchars($m['motivo_visita']) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Debe seleccionar un motivo.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Fecha de la Visita</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" required>
                <div class="invalid-feedback">Ingrese una fecha válida (hoy o posterior).</div>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label">Hora Estimada de Llegada</label>
                <input type="time" name="hora_ingreso" class="form-control" required>
                <div class="invalid-feedback">Ingrese una hora válida.</div>
              </div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-success">Registrar Visita</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Script de validación -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formRegistroVisita');
    const fechaInput = document.getElementById('fecha_ingreso');

    // Establecer fecha mínima como hoy
    const hoy = new Date().toISOString().split('T')[0];
    fechaInput.setAttribute('min', hoy);

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
