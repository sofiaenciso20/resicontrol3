<div class="container-fluid py-3">
    <div class="card shadow-lg">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <div>
                <h3 class="mb-0"><?php echo htmlspecialchars($titulo); ?></h3>
                <?php if (isset($_GET['filter'])): ?>
                    <small class="text-light">
                        <i class="bi bi-filter"></i> 
                        Filtro aplicado: <?php 
                            switch($_GET['filter']) {
                                case 'pendientes': echo 'Solo reservas pendientes'; break;
                                case 'aprobadas': echo 'Solo reservas aprobadas'; break;
                                case 'rechazadas': echo 'Solo reservas rechazadas'; break;
                                case 'hoy': echo 'Solo reservas de hoy'; break;
                                case 'activas': echo 'Solo mis reservas activas'; break;
                                case 'mes_actual': echo 'Solo reservas del mes actual'; break;
                                default: echo 'Filtro personalizado';
                            }
                        ?>
                        <a href="gestion_reservas.php" class="btn btn-sm btn-outline-light ms-2">
                            <i class="bi bi-x"></i> Quitar filtro
                        </a>
                    </small>
                <?php endif; ?>
            </div>
            <?php if (in_array($_SESSION['user']['role'], [1, 2])): ?>
            <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#exportModal" title="Exportar a Excel">
            <img src="/assets/img/excel.png" alt="Exportar a Excel" width="50">
            </button>
            <?php endif; ?>
        </div>
        
        <div class="card-body">
            <!-- Estadísticas rápidas -->
            <?php if (isset($estadisticas)): ?>
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="card bg-warning bg-opacity-10 border-warning">
                        <div class="card-body text-center">
                            <h4 class="text-warning"><?php echo $estadisticas['pendientes']; ?></h4>
                            <small class="text-muted">Pendientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success bg-opacity-10 border-success">
                        <div class="card-body text-center">
                            <h4 class="text-success"><?php echo $estadisticas['aprobadas']; ?></h4>
                            <small class="text-muted">Aprobadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger bg-opacity-10 border-danger">
                        <div class="card-body text-center">
                            <h4 class="text-danger"><?php echo $estadisticas['rechazadas']; ?></h4>
                            <small class="text-muted">Rechazadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info bg-opacity-10 border-info">
                        <div class="card-body text-center">
                            <h4 class="text-info"><?php echo $estadisticas['hoy']; ?></h4>
                            <small class="text-muted">Para Hoy</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Filtros rápidos -->
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="gestion_reservas.php" class="btn btn-sm btn-outline-secondary <?php echo !isset($_GET['filter']) ? 'active' : ''; ?>">
                        <i class="bi bi-list"></i> Todas
                    </a>
                    <a href="gestion_reservas.php?filter=pendientes" class="btn btn-sm btn-outline-warning <?php echo ($_GET['filter'] ?? '') === 'pendientes' ? 'active' : ''; ?>">
                        <i class="bi bi-clock"></i> Pendientes
                    </a>
                    <a href="gestion_reservas.php?filter=aprobadas" class="btn btn-sm btn-outline-success <?php echo ($_GET['filter'] ?? '') === 'aprobadas' ? 'active' : ''; ?>">
                        <i class="bi bi-check-circle"></i> Aprobadas
                    </a>
                    <a href="gestion_reservas.php?filter=rechazadas" class="btn btn-sm btn-outline-danger <?php echo ($_GET['filter'] ?? '') === 'rechazadas' ? 'active' : ''; ?>">
                        <i class="bi bi-x-circle"></i> Rechazadas
                    </a>
                    <a href="gestion_reservas.php?filter=hoy" class="btn btn-sm btn-outline-info <?php echo ($_GET['filter'] ?? '') === 'hoy' ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-day"></i> Hoy
                    </a>
                    <?php if ($_SESSION['user']['role'] == 3): ?>
                    <a href="gestion_reservas.php?filter=activas" class="btn btn-sm btn-outline-primary <?php echo ($_GET['filter'] ?? '') === 'activas' ? 'active' : ''; ?>">
                        <i class="bi bi-person-check"></i> Mis Activas
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Zona</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Horario</th>
                            <th class="text-center">Residente</th>
                            <th class="text-center">Estado</th>
                            <?php if ($_SESSION['user']['role'] == 2): ?>
                                <th class="text-center">Fecha Aprobación</th>
                                <th class="text-center">Administrador</th>
                            <?php endif; ?>
                            <th class="text-center actions-column">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $reserva): ?>
                            <tr>
                                <td class="text-center"><?= htmlspecialchars($reserva['nombre_zona']) ?></td>
                                <td class="text-center">
                                    <?= date('d/m/Y', strtotime($reserva['fecha'])) ?>
                                    <?php if (date('Y-m-d', strtotime($reserva['fecha'])) === date('Y-m-d')): ?>
                                        <span class="badge bg-info ms-1">Hoy</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= htmlspecialchars($reserva['horario']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($reserva['nombre_residente']) ?></td>
                                <td class="text-center">
                                    <?php
                                        if ($reserva['estado'] === 'Pendiente') {
                                            $badgeClass = 'bg-warning text-dark';
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
                                    <td class="text-center"><?= $reserva['fecha_apro'] ? date('d/m/Y', strtotime($reserva['fecha_apro'])) : '-' ?></td>
                                    <td class="text-center"><?= $reserva['nombre_administrador'] ?? '-' ?></td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="detalle_reserva.php?id=<?= $reserva['id_reservas'] ?>"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Ver Detalles">
                                            <i class="bi bi-eye-fill"></i>
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
                        <?php if (empty($reservas)): ?>
                            <tr>
                                <td colspan="<?php echo ($_SESSION['user']['role'] == 2) ? '8' : '6'; ?>" class="text-center text-muted py-4">
                                    <i class="bi bi-exclamation-circle-fill"></i> 
                                    <?php 
                                        if (isset($_GET['filter'])) {
                                            switch($_GET['filter']) {
                                                case 'pendientes': echo 'No hay reservas pendientes.'; break;
                                                case 'aprobadas': echo 'No hay reservas aprobadas.'; break;
                                                case 'rechazadas': echo 'No hay reservas rechazadas.'; break;
                                                case 'hoy': echo 'No hay reservas para hoy.'; break;
                                                case 'activas': echo 'No tienes reservas activas.'; break;
                                                default: echo 'No hay reservas que coincidan con el filtro.';
                                            }
                                        } else {
                                            echo 'No hay reservas registradas.';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando <strong><?= count($reservas) ?></strong> registros
                </small>
                <?php if (count($reservas) > 0): ?>
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
                <h5 class="modal-title" id="exportModalLabel">Exportar Reservas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="exportar_reservas_excel.php" method="POST" id="exportForm">
                <div class="modal-body">
                    <p>Se exportarán las reservas según el filtro actual.</p>
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
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="incluirRechazadas" name="incluir_rechazadas" <?php echo ($_GET['filter'] ?? '') === 'rechazadas' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="incluirRechazadas">
                            Incluir reservas rechazadas
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="soloPendientes" name="solo_pendientes" <?php echo ($_GET['filter'] ?? '') === 'pendientes' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="soloPendientes">
                            Solo reservas pendientes
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar automáticamente los checkboxes según el filtro
    const filtroActual = '<?php echo $_GET['filter'] ?? ''; ?>';
    const soloPendientesCheckbox = document.getElementById('soloPendientes');
    const incluirRechazadasCheckbox = document.getElementById('incluirRechazadas');
    
    if (filtroActual === 'pendientes') {
        soloPendientesCheckbox.checked = true;
    }
    
    if (filtroActual === 'rechazadas') {
        incluirRechazadasCheckbox.checked = true;
    }
});
</script>