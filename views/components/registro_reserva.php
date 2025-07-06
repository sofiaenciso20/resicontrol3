<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
          <h3 class="mb-0">Registro de Reserva</h3>
        </div>
        <div class="card-body">
          <form method="POST" action="/registro_reserva.php" id="formReserva" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="zona" class="form-label">Zona</label>
                <select name="zona" id="zona" class="form-select" required>
                  <option value="">Seleccione una zona</option>
                  <?php foreach ($zonas as $z): ?>
                    <option value="<?= $z['id_zonas_comu'] ?>" <?= (isset($_POST['zona']) && $_POST['zona'] == $z['id_zonas_comu']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($z['nombre_zona']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Debe seleccionar una zona común.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" class="form-control"
                       min="<?= date('Y-m-d') ?>" required
                       value="<?= isset($_POST['fecha']) ? htmlspecialchars($_POST['fecha']) : '' ?>">
                <div class="invalid-feedback">Ingrese una fecha válida (hoy o posterior).</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="horario" class="form-label">Horario</label>
                <select name="horario" id="horario" class="form-select" required>
                  <option value="">Seleccione un horario</option>
                  <?php foreach ($horariosPosibles as $h):
                    $disabled = in_array($h['id_horario'], $horariosOcupados) ? 'disabled style="background:#f8d7da;"' : ''; ?>
                    <option value="<?= $h['id_horario'] ?>" <?= $disabled ?>>
                      <?= htmlspecialchars($h['horario']) ?><?= $disabled ? ' (Ocupado)' : '' ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Debe seleccionar un horario disponible.</div>
              </div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-success">Reservar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Script de Validación + AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const zona = document.getElementById('zona');
  const fecha = document.getElementById('fecha');
  const horario = document.getElementById('horario');
  const form = document.getElementById('formReserva');

  // Establece la fecha mínima como hoy
  const hoy = new Date().toISOString().split('T')[0];
  fecha.setAttribute('min', hoy);

  function actualizarHorarios() {
    if (!zona.value || !fecha.value) return;

    const formData = new FormData();
    formData.append('zona', zona.value);
    formData.append('fecha', fecha.value);

    fetch('horarios_disponibles.php', {
      method: 'POST',
      body: formData
    })
      .then(resp => resp.json())
      .then(ocupados => {
        ocupados = ocupados.map(String); // Asegura que todos sean string
        for (let opt of horario.options) {
          if (!opt.value) continue;
          if (ocupados.includes(opt.value)) {
            opt.disabled = true;
            opt.style.background = '#f8d7da';
            if (!opt.textContent.includes(' (Ocupado)')) {
              opt.textContent += ' (Ocupado)';
            }
          } else {
            opt.disabled = false;
            opt.style.background = '';
            opt.textContent = opt.textContent.replace(' (Ocupado)', '');
          }
        }
        horario.selectedIndex = 0;
      });
  }

  zona.addEventListener('change', actualizarHorarios);
  fecha.addEventListener('change', actualizarHorarios);

  // Validación al enviar
  form.addEventListener('submit', function (e) {
    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
    }
    form.classList.add('was-validated');
  });

  // Validación en tiempo real
  form.querySelectorAll('input, select').forEach(input => {
    input.addEventListener('input', function () {
      if (this.checkValidity()) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
      } else {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
      }
    });
  });
});
</script>
