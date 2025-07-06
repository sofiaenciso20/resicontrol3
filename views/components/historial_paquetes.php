<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Paquetes</title>
 
</head>
<body class="bg-light py-5">
  <div class="container min-vh-100 d-flex flex-column">
    <div class="card shadow-lg flex-grow-1">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Historial de Paquetes</h3>
        <?php if (in_array($_SESSION['user']['role'], [1, 2])): ?>
      <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#exportModal" title="Exportar a Excel">
      <img src="/assets/img/excel.png" alt="Exportar a Excel" width="50">
      </button>
      <?php endif; ?>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Residente</th>
                <th>Vigilante</th>
                <th>Descripción</th>
                <th>Recepción</th>
                <th>Entrega</th>
                <th>Estado</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($paquetes)): ?>
                <?php foreach ($paquetes as $p): ?>
                  <tr>
                    <td><?= $p['id_paquete'] ?></td>
                    <td><?= htmlspecialchars($p['nombre_residente'] . ' ' . $p['apellido_residente']) ?></td>
                    <td><?= htmlspecialchars($p['nombre_vigilante'] . ' ' . $p['apellido_vigilante']) ?></td>
                    <td><?= htmlspecialchars($p['descripcion']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($p['fech_hor_recep'])) ?></td>
                    <td><?= $p['fech_hor_entre'] ? date('d/m/Y H:i', strtotime($p['fech_hor_entre'])) : '<span class="badge bg-warning text-dark">Pendiente</span>' ?></td>
                    <td>
                      <?php if ($p['estado'] == 'Entregado'): ?>
                        <span class="badge bg-success"><?= htmlspecialchars($p['estado']) ?></span>
                      <?php elseif ($p['estado'] == 'Pendiente'): ?>
                        <span class="badge bg-warning text-dark"><?= htmlspecialchars($p['estado']) ?></span>
                      <?php else: ?>
                        <span class="badge bg-secondary"><?= htmlspecialchars($p['estado']) ?></span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="d-flex justify-content-center gap-2">
                        <a href="detalle_paquete.php?id=<?= $p['id_paquete'] ?>" class="btn btn-sm btn-outline-info">
                          <i class="bi bi-eye"></i> Ver
                        </a>
                        <?php if (in_array($_SESSION['user']['role'], [1, 2, 4]) && $p['estado'] == 'Pendiente'): ?>
                          <form method="POST" action="entregar_paquete.php" class="d-inline">
                            <input type="hidden" name="id_paquete" value="<?= $p['id_paquete'] ?>">
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Confirmar la entrega del paquete?')">
                              <i class="bi bi-check-circle"></i> Entregar
                            </button>
                          </form>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-muted">
                    <?php if ($_SESSION['user']['role'] == 3): ?>
                      No tienes paquetes registrados.
                    <?php else: ?>
                      No hay paquetes registrados en el sistema.
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endif; ?>
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
        <h5 class="modal-title" id="exportModalLabel">Exportar Paquetes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="exportar_paquetes_excel.php" method="POST" id="exportForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
          </div>
          <div class="mb-3">
            <label for="fecha_fin" class="form-label">Fecha Fin</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="incluirEntregados" name="incluir_entregados" checked>
            <label class="form-check-label" for="incluirEntregados">
              Incluir paquetes entregados
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="incluirPendientes" name="incluir_pendientes" checked>
            <label class="form-check-label" for="incluirPendientes">
              Incluir paquetes pendientes
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
 
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Establecer fecha máxima como hoy
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('fecha_inicio').setAttribute('max', today);
    document.getElementById('fecha_fin').setAttribute('max', today);
   
    // Validar fechas
    document.getElementById('fecha_fin').addEventListener('change', function() {
        var fechaInicio = document.getElementById('fecha_inicio').value;
        var fechaFin = this.value;
       
        if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
            alert('La fecha final no puede ser menor que la fecha inicial');
            this.value = '';
        }
    });
 
    // Validar fecha inicio
    document.getElementById('fecha_inicio').addEventListener('change', function() {
        var fechaFin = document.getElementById('fecha_fin').value;
        if (fechaFin && this.value > fechaFin) {
            alert('La fecha inicial no puede ser mayor que la fecha final');
            this.value = '';
        }
    });
 
    // Validar que al menos un checkbox esté seleccionado
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        var incluirEntregados = document.getElementById('incluirEntregados').checked;
        var incluirPendientes = document.getElementById('incluirPendientes').checked;
       
        if (!incluirEntregados && !incluirPendientes) {
            e.preventDefault();
            alert('Debe seleccionar al menos un tipo de paquete para exportar');
            return false;
        }
    });
});
</script>
 
 
</body>
</html>
 
 