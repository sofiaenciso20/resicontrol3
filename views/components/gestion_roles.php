<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Roles</title>
</head>
<body class="bg-light">
  <div class="container-fluid py-3">
    <div class="card shadow-lg">
      <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h3 class="mb-0">Gestión de Roles de Usuarios</h3>
        <?php if (in_array($_SESSION['user']['role'], [1, 2])): ?>
        <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#exportModal" title="Exportar a Excel">
          <img src="/assets/img/excel.png" alt="Exportar a Excel" width="50">
        </button>
        <?php endif; ?>
      </div>
      
      <div class="card-body">
        <!-- Mensaje de éxito -->
        <?php if (!empty($mensaje_exito)): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php $rol_usuario_logueado = $_SESSION['user']['role']; ?>

        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th class="text-center">Documento</th>
                <th class="text-center">Nombre</th>
                <th class="text-center">Correo</th>
                <th class="text-center">Rol actual</th>
                <th class="text-center actions-column">Cambiar Rol</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $user): ?>
                <tr>
                  <td class="text-center"><?= $user['documento'] ?></td>
                  <td class="text-center"><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></td>
                  <td class="text-center"><?= htmlspecialchars($user['correo']) ?></td>
                  <td class="text-center">
                    <?php 
                      switch($user['id_rol']) {
                        case 1: echo '<span class="badge bg-danger">Super Admin</span>'; break;
                        case 2: echo '<span class="badge bg-primary">Administrador</span>'; break;
                        case 3: echo '<span class="badge bg-success">Residente</span>'; break;
                        case 4: echo '<span class="badge bg-info">Vigilante</span>'; break;
                        default: echo '<span class="badge bg-secondary">Desconocido</span>';
                      }
                    ?>
                  </td>
                  <td class="text-center">
                    <?php if ($rol_usuario_logueado == 1 || $rol_usuario_logueado == 2): ?>
                      <form method="POST" action="/gestion_roles.php" class="d-flex justify-content-center gap-1">
                        <input type="hidden" name="documento" value="<?= $user['documento'] ?>">
                        <select name="id_rol" class="form-select form-select-sm w-auto" required>
                          <option value="">Seleccione</option>
                          <?php foreach ($roles as $rol): ?>
                            <?php
                            if ($rol_usuario_logueado == 2 && !in_array($rol['id_rol'], [3, 4])) continue;
                            ?>
                            <option value="<?= $rol['id_rol'] ?>" <?= $user['id_rol'] == $rol['id_rol'] ? 'selected' : '' ?>>
                              <?= htmlspecialchars($rol['rol']) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-success">
                          <i class="bi bi-check-circle-fill"></i> Cambiar
                        </button>
                      </form>
                    <?php else: ?>
                      <span class="text-muted">Sin permisos</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      
      <div class="card-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
          <small class="text-muted">
            Mostrando <strong><?= count($usuarios) ?></strong> registros
          </small>
          <?php if (count($usuarios) > 0): ?>
          <small class="text-muted">
            Última actualización: <?= date('d/m/Y H:i') ?>
          </small>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para exportar -->
  <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="exportModalLabel">Exportar Roles</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="exportar_roles_excel.php" method="POST" id="exportForm">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Formato de exportación:</label>
              <select class="form-select" name="formato">
                <option value="excel">Excel (.xlsx)</option>
                <option value="csv">CSV (.csv)</option>
                <option value="pdf">PDF (.pdf)</option>
              </select>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="incluirInactivos" name="incluir_inactivos">
              <label class="form-check-label" for="incluirInactivos">
                Incluir usuarios inactivos
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-download"></i> Exportar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>