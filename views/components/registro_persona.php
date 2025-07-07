<div class="container min-vh-100 d-flex flex-column">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center">
          <h3 class="mb-0">Registro de Persona</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($mensaje)): ?>
              <div class="alert <?= strpos($mensaje, 'exitosamente') !== false ? 'alert-success' : 'alert-danger' ?> d-flex align-items-center" role="alert">
                <i class="bi <?= strpos($mensaje, 'exitosamente') !== false ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
                <?= htmlspecialchars($mensaje); ?>
              </div>
            <?php endif; ?>

          <?php
          $rol_usuario = $_SESSION['user']['role'] ?? null;
          $es_admin = in_array($rol_usuario, [1, 2]);
          ?>

          <form method="POST" action="/registro_persona.php" id="formRegistroPersona" novalidate>
            <?php if ($es_admin): ?>
              <div class="mb-3">
                <label class="form-label">Tipo de Usuario</label>
                <select class="form-select" name="tipo_usuario" id="tipo_usuario" required>
                  <option value="">Selecciona</option>
                  <option value="vigilante">Vigilante</option>
                  <option value="habitante">Habitante</option>
                  <option value="administrador">Administrador</option>
                </select>
                <div class="invalid-feedback">Por favor selecciona un tipo de usuario.</div>
              </div>
            <?php else: ?>
              <input type="hidden" name="tipo_usuario" value="habitante">
            <?php endif; ?>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" minlength="2" maxlength="50">
                <div class="invalid-feedback">Nombre inválido (solo letras, 2-50 caracteres).</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Apellido</label>
                <input type="text" class="form-control" name="apellido" required pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" minlength="2" maxlength="50">
                <div class="invalid-feedback">Apellido inválido (solo letras, 2-50 caracteres).</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Teléfono</label>
                <input type="tel" class="form-control" name="telefono" required pattern="[0-9]{7,15}">
                <div class="invalid-feedback">Teléfono inválido (7-15 dígitos).</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Tipo de Identificación</label>
                <div class="input-group">
                  <select class="form-select" name="tipo_identificacion" required>
                    <option value="">Selecciona</option>
                    <option value="1">C.C</option>
                    <option value="2">T.I</option>
                    <option value="3">C.E</option>
                  </select>
                  <?php if ($es_admin): ?>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditarTipoIdentificacion">
                      <i class="bi bi-pencil"></i>
                    </button>
                  <?php endif; ?>
                </div>
                <div class="invalid-feedback">Selecciona un tipo de identificación.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Número de Identificación</label>
                <input type="text" class="form-control" name="numero_identificacion" required pattern="[0-9]{6,20}">
                <div class="invalid-feedback">Número inválido (6-20 dígitos).</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" name="correo" required>
                <div class="invalid-feedback">Correo electrónico inválido.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="contrasena" required 
                       pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$" 
                       id="contrasena">
                <div class="invalid-feedback">Mínimo 8 caracteres, con letras, números y un símbolo.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" name="confirmar_contrasena" required id="confirmar_contrasena">
                <div class="invalid-feedback">Las contraseñas no coinciden.</div>
              </div>
            </div>

            <?php if ($es_admin): ?>
              <div id="campos_vigilante" style="display: none;">
                <hr><h5>Datos Vigilante</h5>
                <div class="mb-3">
                  <label class="form-label">Nombre de la Empresa</label>
                  <input type="text" class="form-control" name="empresa" minlength="2" maxlength="100" id="empresa_vigilante">
                  <div class="invalid-feedback">Nombre inválido (2-100 caracteres).</div>
                </div>
              </div>

              <div id="campos_habitante" style="display: none;">
                <hr><h5>Datos Habitante</h5>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Dirección de la Casa</label>
                    <input type="text" class="form-control" name="direccion_casa" minlength="5" maxlength="200" id="direccion_casa">
                    <div class="invalid-feedback">Dirección inválida (5-200 caracteres).</div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Cantidad de Personas</label>
                    <input type="number" class="form-control" name="cantidad_personas" min="1" max="20" id="cantidad_personas">
                    <div class="invalid-feedback">Número entre 1 y 20 requerido.</div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">¿Tiene Animales?</label>
                    <select class="form-select" name="tiene_animales" id="tiene_animales">
                      <option value="no">No</option>
                      <option value="si">Sí</option>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3" id="cantidad_animales_div" style="display: none;">
                    <label class="form-label">Cantidad de Animales</label>
                    <input type="number" class="form-control" name="cantidad_animales" min="0" max="10" id="cantidad_animales">
                    <div class="invalid-feedback">Número entre 0 y 10 requerido.</div>
                  </div>
                </div>

                <h6 class="mt-3">Vehículo</h6>
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo de Vehículo</label>
                    <div class="input-group">
                      <select class="form-select" name="id_tipo_vehi" id="tipo_vehiculo">
                        <option value="">Seleccione</option>
                        <?php foreach ($tipos as $tipo): ?>
                          <option value="<?= $tipo['id_tipo_vehi'] ?>"><?= htmlspecialchars($tipo['tipo_vehiculos']) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditarTipoVehiculo">
                        <i class="bi bi-pencil"></i>
                      </button>
                    </div>
                    <div class="invalid-feedback">Selecciona un tipo de vehículo.</div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Placa</label>
                    <input type="text" class="form-control" name="placa" pattern="[A-Za-z0-9]{3,10}" id="placa">
                    <div class="invalid-feedback">Placa inválida (3-10 caracteres alfanuméricos).</div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Marca</label>
                    <div class="input-group">
                      <select class="form-select" name="id_marca" id="marca_vehiculo">
                        <option value="">Seleccione</option>
                        <?php foreach ($marcas as $marca): ?>
                          <option value="<?= $marca['id_marca'] ?>"><?= htmlspecialchars($marca['marca']) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditarMarca">
                        <i class="bi bi-pencil"></i>
                      </button>
                    </div>
                    <div class="invalid-feedback">Selecciona una marca.</div>
                  </div>
                </div>
              </div>

              <div id="campos_administrador" style="display: none;">
                <hr><h5>Datos Administrador</h5>
                <div class="mb-3">
                  <label class="form-label">Dirección de Residencia</label>
                  <input type="text" class="form-control" name="direccion_residencia" minlength="5" maxlength="200" id="direccion_residencia">
                  <div class="invalid-feedback">Dirección inválida (5-200 caracteres).</div>
                </div>
              </div>
            <?php endif; ?>

            <div class="text-end">
              <button type="submit" class="btn btn-success">Registrar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modales para editar opciones -->
<?php if ($es_admin): ?>
  <!-- Modal para editar tipo de identificación -->
  <div class="modal fade" id="modalEditarTipoIdentificacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Tipos de Identificación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formTipoIdentificacion">
            <div class="mb-3">
              <label class="form-label">Nuevo Tipo</label>
              <input type="text" class="form-control" id="nuevoTipoIdentificacion" placeholder="Ej: Pasaporte">
            </div>
            <div class="mb-3">
              <label class="form-label">Tipos Existentes</label>
              <ul class="list-group" id="listaTiposIdentificacion">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  C.C
                  <button type="button" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  T.I
                  <button type="button" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  C.E
                  <button type="button" class="btn btn-sm btn-outline-danger">Eliminar</button>
                </li>
              </ul>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarTipoIdentificacion">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para editar tipos de vehículo -->
  <div class="modal fade" id="modalEditarTipoVehiculo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Tipos de Vehículo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formTipoVehiculo">
            <div class="mb-3">
              <label class="form-label">Nuevo Tipo</label>
              <input type="text" class="form-control" id="nuevoTipoVehiculo" placeholder="Ej: Motocicleta">
            </div>
            <div class="mb-3">
              <label class="form-label">Tipos Existentes</label>
              <ul class="list-group" id="listaTiposVehiculo">
                <?php foreach ($tipos as $tipo): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $tipo['id_tipo_vehi'] ?>">
                    <?= htmlspecialchars($tipo['tipo_vehiculos']) ?>
                    <button type="button" class="btn btn-sm btn-outline-danger btnEliminarTipoVehiculo">Eliminar</button>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarTipoVehiculo">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para editar marcas de vehículo -->
  <div class="modal fade" id="modalEditarMarca" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Marcas de Vehículo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formMarcaVehiculo">
            <div class="mb-3">
              <label class="form-label">Nueva Marca</label>
              <input type="text" class="form-control" id="nuevaMarcaVehiculo" placeholder="Ej: Tesla">
            </div>
            <div class="mb-3">
              <label class="form-label">Marcas Existentes</label>
              <ul class="list-group" id="listaMarcasVehiculo">
                <?php foreach ($marcas as $marca): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?= $marca['id_marca'] ?>">
                    <?= htmlspecialchars($marca['marca']) ?>
                    <button type="button" class="btn btn-sm btn-outline-danger btnEliminarMarca">Eliminar</button>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarMarca">Guardar</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- JS -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tipoUsuarioSelect = document.getElementById('tipo_usuario');
    const form = document.getElementById('formRegistroPersona');
    const contrasena = document.getElementById('contrasena');
    const confirmar = document.getElementById('confirmar_contrasena');

    if (tipoUsuarioSelect) {
      tipoUsuarioSelect.addEventListener('change', function () {
        const tipo = this.value;
        document.getElementById('campos_vigilante').style.display = tipo === 'vigilante' ? 'block' : 'none';
        document.getElementById('campos_habitante').style.display = tipo === 'habitante' ? 'block' : 'none';
        document.getElementById('campos_administrador').style.display = tipo === 'administrador' ? 'block' : 'none';
      });
    }

    document.getElementById('tiene_animales')?.addEventListener('change', function () {
      const mostrar = this.value === 'si';
      document.getElementById('cantidad_animales_div').style.display = mostrar ? 'block' : 'none';
      if (!mostrar) document.getElementById('cantidad_animales').value = '';
    });

    if (contrasena && confirmar) {
      contrasena.addEventListener('input', () => {
        const value = contrasena.value;
        const valid = /[A-Za-z]/.test(value) && /\d/.test(value) && /[@$!%*#?&]/.test(value) && value.length >= 8;
        contrasena.classList.toggle('is-valid', valid);
        contrasena.classList.toggle('is-invalid', !valid);
      });

      confirmar.addEventListener('input', () => {
        const match = contrasena.value === confirmar.value;
        confirmar.setCustomValidity(match ? '' : 'Las contraseñas no coinciden');
        confirmar.classList.toggle('is-invalid', !match);
      });
    }

    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        form.classList.add('was-validated');
      }
    });

    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
      input.addEventListener('input', () => {
        input.classList.toggle('is-valid', input.checkValidity());
        input.classList.toggle('is-invalid', !input.checkValidity());
      });
    });

    // Funcionalidad para los modales de edición
    <?php if ($es_admin): ?>
      // Manejar el modal de tipos de vehículo
      document.getElementById('btnGuardarTipoVehiculo')?.addEventListener('click', function() {
        const nuevoTipo = document.getElementById('nuevoTipoVehiculo').value.trim();
        if (nuevoTipo) {
          // Aquí iría la llamada AJAX para guardar en la base de datos
          fetch('guardar_tipo_vehiculo.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ tipo: nuevoTipo })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Agregar el nuevo tipo a la lista
              const lista = document.getElementById('listaTiposVehiculo');
              const nuevoItem = document.createElement('li');
              nuevoItem.className = 'list-group-item d-flex justify-content-between align-items-center';
              nuevoItem.dataset.id = data.id;
              nuevoItem.innerHTML = `
                ${nuevoTipo}
                <button type="button" class="btn btn-sm btn-outline-danger btnEliminarTipoVehiculo">Eliminar</button>
              `;
              lista.appendChild(nuevoItem);
              
              // Agregar al select en el formulario principal
              const select = document.querySelector('select[name="id_tipo_vehi"]');
              const nuevaOpcion = document.createElement('option');
              nuevaOpcion.value = data.id;
              nuevaOpcion.textContent = nuevoTipo;
              select.appendChild(nuevaOpcion);
              
              // Limpiar el input
              document.getElementById('nuevoTipoVehiculo').value = '';
              
              // Mostrar mensaje de éxito
              alert('Tipo de vehículo guardado exitosamente');
            } else {
              alert('Error al guardar: ' + data.mensaje);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar el tipo de vehículo');
          });
        }
      });

      // Manejar eliminación de tipos de vehículo
      document.getElementById('listaTiposVehiculo')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('btnEliminarTipoVehiculo')) {
          const item = e.target.closest('li');
          const id = item.dataset.id;
          const tipo = item.textContent.trim().replace('Eliminar', '').trim();
          
          if (confirm(`¿Está seguro que desea eliminar el tipo "${tipo}"?`)) {
            // Aquí iría la llamada AJAX para eliminar de la base de datos
            fetch('eliminar_tipo_vehiculo.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Eliminar de la lista
                item.remove();
                
                // Eliminar del select en el formulario principal
                const select = document.querySelector('select[name="id_tipo_vehi"]');
                const opcion = select.querySelector(`option[value="${id}"]`);
                if (opcion) opcion.remove();
                
                alert('Tipo de vehículo eliminado exitosamente');
              } else {
                alert('Error al eliminar: ' + data.mensaje);
              }
            })
            .catch(error => {
              console.error('Error:', error);
              alert('Error al eliminar el tipo de vehículo');
            });
          }
        }
      });

      // Manejar el modal de marcas de vehículo (similar al de tipos)
      document.getElementById('btnGuardarMarca')?.addEventListener('click', function() {
        const nuevaMarca = document.getElementById('nuevaMarcaVehiculo').value.trim();
        if (nuevaMarca) {
          // Llamada AJAX similar a la de tipos de vehículo
          // ...
        }
      });

      // Manejar eliminación de marcas (similar a tipos)
      document.getElementById('listaMarcasVehiculo')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('btnEliminarMarca')) {
          // Lógica similar a la de tipos de vehículo
          // ...
        }
      });

      // Manejar el modal de tipos de identificación (similar)
      document.getElementById('btnGuardarTipoIdentificacion')?.addEventListener('click', function() {
        // Lógica similar
        // ...
      });
    <?php endif; ?>
  });
</script>