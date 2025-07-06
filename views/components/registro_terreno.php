<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
          <h3 class="mb-0">Registro de Terreno</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info text-center"><?= htmlspecialchars($mensaje) ?></div>
          <?php endif; ?>

          <form method="POST" action="/registro_terreno.php" id="formRegistroTerreno" novalidate>
            <div class="mb-3">
              <label class="form-label">Tipo de Terreno</label>
              <select class="form-select" name="tipo_terreno" id="tipo_terreno" required>
                <option value="">Selecciona</option>
                <option value="bloque">Bloque</option>
                <option value="manzana">Manzana</option>
              </select>
              <div class="invalid-feedback">Por favor selecciona un tipo de terreno.</div>
            </div>

            <div id="cantidad_apartamentos" class="mb-3" style="display: none;">
              <label class="form-label">Cantidad de Apartamentos</label>
              <input type="number" class="form-control" name="apartamentos" id="apartamentos"
                     min="1" max="500">
              <div class="invalid-feedback">Ingresa una cantidad válida entre 1 y 500 apartamentos.</div>
            </div>

            <div id="cantidad_casas" class="mb-3" style="display: none;">
              <label class="form-label">Cantidad de Casas</label>
              <input type="number" class="form-control" name="casas" id="casas"
                     min="1" max="500">
              <div class="invalid-feedback">Ingresa una cantidad válida entre 1 y 500 casas.</div>
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-success">Registrar Terreno</button>
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
    const tipoTerreno = document.getElementById('tipo_terreno');
    const apartamentosDiv = document.getElementById('cantidad_apartamentos');
    const casasDiv = document.getElementById('cantidad_casas');
    const form = document.getElementById('formRegistroTerreno');

    const apartamentosInput = document.getElementById('apartamentos');
    const casasInput = document.getElementById('casas');

    // Mostrar campos dinámicos según el tipo de terreno
    tipoTerreno.addEventListener('change', function () {
      const tipo = this.value;
      apartamentosDiv.style.display = tipo === 'bloque' ? 'block' : 'none';
      casasDiv.style.display = tipo === 'manzana' ? 'block' : 'none';

      // Limpiar valores y validaciones al cambiar
      apartamentosInput.value = '';
      casasInput.value = '';
      apartamentosInput.classList.remove('is-invalid', 'is-valid');
      casasInput.classList.remove('is-invalid', 'is-valid');
    });

    // Validación al enviar
    form.addEventListener('submit', function (e) {
      const tipo = tipoTerreno.value;
      let valid = true;

      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        valid = false;
      }

      // Validar campo dinámico según tipo de terreno
      if (tipo === 'bloque') {
        if (!apartamentosInput.value || apartamentosInput.value < 1 || apartamentosInput.value > 500) {
          apartamentosInput.classList.add('is-invalid');
          valid = false;
        } else {
          apartamentosInput.classList.remove('is-invalid');
        }
      }

      if (tipo === 'manzana') {
        if (!casasInput.value || casasInput.value < 1 || casasInput.value > 500) {
          casasInput.classList.add('is-invalid');
          valid = false;
        } else {
          casasInput.classList.remove('is-invalid');
        }
      }

      if (!valid) {
        e.preventDefault();
        e.stopPropagation();
      }

      form.classList.add('was-validated');
    });

    // Validación en tiempo real
    [apartamentosInput, casasInput].forEach(input => {
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
