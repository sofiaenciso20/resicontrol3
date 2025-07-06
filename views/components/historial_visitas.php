<div class="container min-vh-100 d-flex flex-column">
  <div class="card shadow-lg flex-grow-1">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h3 class="mb-0">Historial de Visitas</h3>
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
              <th>Fecha</th>
              <th>Visitante</th>
              <th>Residente</th>
              <th>Casa</th>
              <th>Motivo</th>
              <th>Hora</th>
              <th>Estado</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($visitas as $visita): ?>
              <tr>
                <td><?= htmlspecialchars(date("d/m/y", strtotime($visita['fecha_ingreso']))) ?></td>
                <td><?= htmlspecialchars($visita['visitante_nombre'] . ' ' . $visita['visitante_apellido']) ?></td>
                <td><?= htmlspecialchars($visita['residente_nombre'] . ' ' . $visita['residente_apellido']) ?></td>
                <td><?= htmlspecialchars($visita['direccion_casa']) ?></td>
                <td><?= htmlspecialchars($visita['motivo_visita']) ?></td>
                <td><?= date('g:i a', strtotime($visita['hora_ingreso'])) ?></td>
                <td>
                  <?php if (isset($visita['id_estado'])): ?>
                    <?php if ($visita['id_estado'] == 1): ?>
                      <span class="badge bg-warning text-dark">Pendiente</span>
                    <?php elseif ($visita['id_estado'] == 2): ?>
                      <span class="badge bg-success">Aprobada</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Desconocido</span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="badge bg-secondary">Desconocido</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="d-flex justify-content-center gap-2">
                    <a href="detalle_visita.php?id=<?= $visita['id_visita'] ?>" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="bi bi-eye"></i></a>
                    <?php if (isset($visita['id_estado']) && $visita['id_estado'] == 1 && in_array($_SESSION['user']['role'], [1, 2, 4])): ?>
                      <form method="POST" action="confirmar_visitas.php" class="d-inline">
                        <input type="hidden" name="id_visita" value="<?= $visita['id_visita'] ?>">
                        <button type="submit" class="btn btn-sm btn-success" title="Confirmar llegada">
                          <i class="bi bi-check-circle"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($visitas)): ?>
              <tr><td colspan="8" class="text-muted">No hay visitas registradas.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Modal para selección de fechas -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportModalLabel">Exportar Visitas a Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="exportar_visitas_excel.php" method="POST" id="exportForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
          </div>
          <div class="mb-3">
            <label for="fecha_fin" class="form-label">Fecha Fin</label>
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
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
 
    // Validar formulario antes de enviar
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        var fechaInicio = document.getElementById('fecha_inicio').value;
        var fechaFin = document.getElementById('fecha_fin').value;
       
        if (!fechaInicio || !fechaFin) {
            e.preventDefault();
            alert('Por favor, seleccione ambas fechas');
            return false;
        }
       
        if (fechaFin < fechaInicio) {
            e.preventDefault();
            alert('El rango de fechas no es válido');
            return false;
        }
    });
});
</script>
 