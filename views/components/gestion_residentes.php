<div class="container mt-5">
  <div class="card shadow-lg">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
      <h3 class="mb-0">Gestión de Personas</h3>
      <?php if (in_array($_SESSION['user']['role'], [1, 2])): ?>
      <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#exportModal" title="Exportar a Excel">
      <img src="/assets/img/excel.png" alt="Exportar a Excel" width="50">
      </button>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-bordered text-center align-middle">
          <thead class="table-dark">
            <tr>
              <th>Documento</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Dirección</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($visitas as $residente): ?>
              <tr>
                <td><?= $residente['documento'] ?></td>
                <td><?= htmlspecialchars($residente['nombre']) ?></td>
                <td><?= $residente['telefono'] ?></td>
                <td><?= htmlspecialchars($residente['direccion_casa']) ?></td>
                <td>
                  <?php if ($residente['id_estado_usuario'] == 4): ?>
                    <span class="badge bg-success">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="d-flex justify-content-center gap-2">
                    <a href="detalle_persona.php?id=<?= $residente['documento'] ?>" class="btn btn-sm btn-outline-secondary" title="Ver">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="inhabilitar_usuario.php?id=<?= $residente['documento'] ?>"
                        class="btn btn-sm btn-outline-warning"
                        title="Inhabilitar"
                        onclick="return confirm('¿Estás seguro de que deseas inhabilitar este usuario?');">
                        <i class="bi bi-person-x"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Modal para exportar -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportModalLabel">Exportar Residentes a Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="exportar_residentes_excel.php" method="POST" id="exportForm">
        <div class="modal-body">
          <p>Se exportarán todos los residentes registrados en el sistema.</p>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="incluirInactivos" name="incluir_inactivos">
            <label class="form-check-label" for="incluirInactivos">
              Incluir residentes inactivos
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Exportar</button>
        </div>
      </form>
    </div>
  </div>
</div>
 
 