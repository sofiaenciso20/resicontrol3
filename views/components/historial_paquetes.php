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
                                case 'pendientes': echo 'Solo paquetes sin reclamar'; break;
                                case 'entregados': echo 'Solo paquetes entregados'; break;
                                case 'hoy': echo 'Solo paquetes recibidos hoy'; break;
                                case 'semana': echo 'Paquetes de la última semana'; break;
                                case 'mes_actual': echo 'Paquetes del mes actual'; break;
                                case 'mis_pendientes': echo 'Solo mis paquetes pendientes'; break;
                                default: echo 'Filtro personalizado';
                            }
                        ?>
                        <a href="historial_paquetes.php" class="btn btn-sm btn-outline-light ms-2">
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
                            <small class="text-muted">Sin Reclamar</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success bg-opacity-10 border-success">
                        <div class="card-body text-center">
                            <h4 class="text-success"><?php echo $estadisticas['entregados']; ?></h4>
                            <small class="text-muted">Entregados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info bg-opacity-10 border-info">
                        <div class="card-body text-center">
                            <h4 class="text-info"><?php echo $estadisticas['hoy']; ?></h4>
                            <small class="text-muted">Recibidos Hoy</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary bg-opacity-10 border-primary">
                        <div class="card-body text-center">
                            <h4 class="text-primary"><?php echo $estadisticas['semana']; ?></h4>
                            <small class="text-muted">Esta Semana</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Filtros rápidos -->
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="historial_paquetes.php" class="btn btn-sm btn-outline-secondary <?php echo !isset($_GET['filter']) ? 'active' : ''; ?>">
                        <i class="bi bi-list"></i> Todos
                    </a>
                    <a href="historial_paquetes.php?filter=pendientes" class="btn btn-sm btn-outline-warning <?php echo ($_GET['filter'] ?? '') === 'pendientes' ? 'active' : ''; ?>">
                        <i class="bi bi-box"></i> Sin Reclamar
                    </a>
                    <a href="historial_paquetes.php?filter=entregados" class="btn btn-sm btn-outline-success <?php echo ($_GET['filter'] ?? '') === 'entregados' ? 'active' : ''; ?>">
                        <i class="bi bi-check-circle"></i> Entregados
                    </a>
                    <a href="historial_paquetes.php?filter=hoy" class="btn btn-sm btn-outline-info <?php echo ($_GET['filter'] ?? '') === 'hoy' ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-day"></i> Hoy
                    </a>
                    <a href="historial_paquetes.php?filter=semana" class="btn btn-sm btn-outline-primary <?php echo ($_GET['filter'] ?? '') === 'semana' ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-week"></i> Semana
                    </a>
                    <?php if ($_SESSION['user']['role'] == 3): ?>
                    <a href="historial_paquetes.php?filter=mis_pendientes" class="btn btn-sm btn-outline-danger <?php echo ($_GET['filter'] ?? '') === 'mis_pendientes' ? 'active' : ''; ?>">
                        <i class="bi bi-person-exclamation"></i> Mis Pendientes
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Residente</th>
                            <th class="text-center">Vigilante</th>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Recepción</th>
                            <th class="text-center">Entrega</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center actions-column">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($paquetes)): ?>
                            <?php foreach ($paquetes as $p): ?>
                                <tr>
                                    <td class="text-center"><?= $p['id_paquete'] ?></td>
                                    <td class="text-center"><?= htmlspecialchars($p['nombre_residente'] . ' ' . $p['apellido_residente']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($p['nombre_vigilante'] . ' ' . $p['apellido_vigilante']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($p['descripcion']) ?></td>
                                    <td class="text-center">
                                        <?= date('d/m/Y H:i', strtotime($p['fech_hor_recep'])) ?>
                                        <?php if (date('Y-m-d', strtotime($p['fech_hor_recep'])) === date('Y-m-d')): ?>
                                            <span class="badge bg-info ms-1">Hoy</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $p['fech_hor_entre'] ? date('d/m/Y H:i', strtotime($p['fech_hor_entre'])) : '<span class="badge bg-warning text-dark">Pendiente</span>' ?></td>
                                    <td class="text-center">
                                        <?php if ($p['estado'] == 'Entregado'): ?>
                                            <span class="badge bg-success"><?= htmlspecialchars($p['estado']) ?></span>
                                        <?php elseif ($p['estado'] == 'Pendiente'): ?>
                                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($p['estado']) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($p['estado']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="detalle_paquete.php?id=<?= $p['id_paquete'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye-fill"></i> Ver
                                            </a>
                                            <?php if (in_array($_SESSION['user']['role'], [1, 2, 4]) && $p['estado'] == 'Pendiente'): ?>
                                                <form method="POST" action="entregar_paquete.php" class="d-inline">
                                                    <input type="hidden" name="id_paquete" value="<?= $p['id_paquete'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Confirmar la entrega del paquete?')">
                                                        <i class="bi bi-check-circle-fill"></i> Entregar
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-exclamation-circle-fill"></i> 
                                    <?php 
                                        if (isset($_GET['filter'])) {
                                            switch($_GET['filter']) {
                                                case 'pendientes': echo 'No hay paquetes sin reclamar.'; break;
                                                case 'entregados': echo 'No hay paquetes entregados.'; break;
                                                case 'hoy': echo 'No se recibieron paquetes hoy.'; break;
                                                case 'semana': echo 'No hay paquetes de esta semana.'; break;
                                                case 'mis_pendientes': echo 'No tienes paquetes pendientes.'; break;
                                                default: echo 'No hay paquetes que coincidan con el filtro.';
                                            }
                                        } else {
                                            if ($_SESSION['user']['role'] == 3) {
                                                echo 'No tienes paquetes registrados.';
                                            } else {
                                                echo 'No hay paquetes registrados en el sistema.';
                                            }
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
                    Mostrando <strong><?= count($paquetes) ?></strong> registros
                </small>
                <?php if (count($paquetes) > 0): ?>
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
                <h5 class="modal-title" id="exportModalLabel">Exportar Paquetes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="exportar_paquetes_excel.php" method="POST" id="exportForm">
                <div class="modal-body">
                    <p>Se exportarán los paquetes según el filtro actual.</p>
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="incluirEntregados" name="incluir_entregados" <?php echo ($_GET['filter'] ?? '') === 'entregados' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="incluirEntregados">
                            Incluir paquetes entregados
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="incluirPendientes" name="incluir_pendientes" <?php echo ($_GET['filter'] ?? '') === 'pendientes' || ($_GET['filter'] ?? '') === 'mis_pendientes' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="incluirPendientes">
                            Incluir paquetes pendientes
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

    // Actualizar automáticamente los checkboxes según el filtro
    const filtroActual = '<?php echo $_GET['filter'] ?? ''; ?>';
    const incluirEntregadosCheckbox = document.getElementById('incluirEntregados');
    const incluirPendientesCheckbox = document.getElementById('incluirPendientes');
    
    if (filtroActual === 'entregados') {
        incluirEntregadosCheckbox.checked = true;
        incluirPendientesCheckbox.checked = false;
    } else if (filtroActual === 'pendientes' || filtroActual === 'mis_pendientes') {
        incluirPendientesCheckbox.checked = true;
        incluirEntregadosCheckbox.checked = false;
    }
});
</script>