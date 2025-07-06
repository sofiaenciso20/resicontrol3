<div class="container mt-5">
  <div class="card shadow-lg">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
      <h3 class="mb-0">Gestión de Reservas</h3>
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
              <th>Zona</th>
              <th>Fecha</th>
              <th>Horario</th>
              <th>Residente</th>
              <th>Estado</th>
              <?php if ($_SESSION['user']['role'] == 2): ?>
                <th>Fecha Aprobación</th>
                <th>Administrador</th>
              <?php endif; ?>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservas as $reserva): ?>
              <tr>
                <td><?= htmlspecialchars($reserva['nombre_zona']) ?></td>
                <td><?= date('d/m/Y', strtotime($reserva['fecha'])) ?></td>
                <td><?= htmlspecialchars($reserva['horario']) ?></td>
                <td><?= htmlspecialchars($reserva['nombre_residente']) ?></td>
                <td>
                  <?php
                    if ($reserva['estado'] === 'Pendiente') {
                        $badgeClass = 'bg-warning';
                    } elseif ($reserva['estado'] === 'Aprobada') {
                        $badgeClass = 'bg-success';
                    } elseif ($reserva['estado'] === 'Rechazada') {
                        $badgeClass = 'bg-danger';
                    } else {
                        $badgeClass = 'bg-secondary';
                    }
 
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= $reserva['estado'] ?></span>
                </td>
                <?php if ($_SESSION['user']['role'] == 2): ?>
                  <td><?= $reserva['fecha_apro'] ? date('d/m/Y', strtotime($reserva['fecha_apro'])) : '-' ?></td>
                  <td><?= $reserva['nombre_administrador'] ?? '-' ?></td>
                <?php endif; ?>
                <td>
                  <div class="d-flex justify-content-center gap-2">
                    <a href="detalle_reserva.php?id=<?= $reserva['id_reservas'] ?>"
                       class="btn btn-sm btn-outline-secondary"
                       title="Ver Detalles">
                      <i class="bi bi-eye"></i>
                    </a>
                   
                    <?php if ($_SESSION['user']['role'] == 2 && $reserva['estado'] === 'Pendiente'): ?>
                      <a href="aprobar_reserva.php?id=<?= $reserva['id_reservas'] ?>"
                         class="btn btn-sm btn-outline-success"
                         title="Aprobar"
                         onclick="return confirm('¿Estás seguro de que deseas aprobar esta reserva?');">
                        <i class="bi bi-check-lg"></i>
                      </a>
                     
                      <a href="rechazar_reserva.php?id=<?= $reserva['id_reservas'] ?>"
                         class="btn btn-sm btn-outline-danger"
                         title="Rechazar"
                         onclick="return prompt('Por favor, ingrese el motivo del rechazo:') !== null;">
                        <i class="bi bi-x-lg"></i>
                      </a>
                    <?php endif; ?>
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
        <h5 class="modal-title" id="exportModalLabel">Exportar Reservas del Mes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="exportar_reservas_excel.php" method="POST" id="exportForm">
        <div class="modal-body">
          <p>Se exportarán todas las reservas del mes actual.</p>
          <div class="mb-3">
            <label for="mes" class="form-label">Seleccionar Mes</label>
            <select class="form-select" id="mes" name="mes" required>
              <?php
              $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
              ];
              $mesActual = date('n');
              foreach ($meses as $num => $nombre) {
                $selected = $num == $mesActual ? 'selected' : '';
                echo "<option value=\"$num\" $selected>$nombre</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="anio" class="form-label">Año</label>
            <select class="form-select" id="anio" name="anio" required>
              <?php
              $anioActual = date('Y');
              for ($i = $anioActual - 1; $i <= $anioActual + 1; $i++) {
                $selected = $i == $anioActual ? 'selected' : '';
                echo "<option value=\"$i\" $selected>$i</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="incluirRechazadas" name="incluir_rechazadas">
            <label class="form-check-label" for="incluirRechazadas">
              Incluir reservas rechazadas
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
 
 
 
 