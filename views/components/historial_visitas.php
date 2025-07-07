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
                                case 'hoy': echo 'Solo visitas de hoy'; break;
                                case 'pendientes': echo 'Solo visitas pendientes'; break;
                                case 'activas': echo 'Solo mis visitas activas'; break;
                                default: echo 'Filtro personalizado';
                            }
                        ?>
                        <a href="historial_visitas.php" class="btn btn-sm btn-outline-light ms-2">
                            <i class="bi bi-x"></i> Quitar filtro
                        </a>
                    </small>
                <?php endif; ?>
                
                <!-- Mostrar mensaje de validación exitosa si viene desde validar_visitas -->
                <?php if (isset($_GET['validated']) && $_GET['validated'] == '1'): ?>
                    <div class="mt-2">
                        <small class="text-light">
                            <i class="bi bi-check-circle"></i> Código validado correctamente - Mostrando visitas del día
                        </small>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (in_array($_SESSION['user']['role'], [1, 2])): ?>
            <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#exportModal" title="Exportar a Excel">
            <img src="/assets/img/excel.png" alt="Exportar a Excel" width="50">
            </button>
            <?php endif; ?>
        </div>
        
        <div class="card-body">
            <!-- Mostrar mensaje de éxito si existe -->
            <?php if (!empty($_SESSION['mensaje_visita'])): ?>
                <div class="alert <?= strpos($_SESSION['mensaje_visita'], '✅') !== false ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['mensaje_visita']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['mensaje_visita']); ?>
            <?php endif; ?>

            <!-- Filtros rápidos -->
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="historial_visitas.php" class="btn btn-sm btn-outline-secondary <?php echo !isset($_GET['filter']) ? 'active' : ''; ?>">
                        <i class="bi bi-list"></i> Todas
                    </a>
                    <a href="historial_visitas.php?filter=hoy" class="btn btn-sm btn-outline-primary <?php echo ($_GET['filter'] ?? '') === 'hoy' ? 'active' : ''; ?>">
                        <i class="bi bi-calendar-day"></i> Hoy
                    </a>
                    <a href="historial_visitas.php?filter=pendientes" class="btn btn-sm btn-outline-warning <?php echo ($_GET['filter'] ?? '') === 'pendientes' ? 'active' : ''; ?>">
                        <i class="bi bi-clock"></i> Pendientes
                    </a>
                    <?php if ($_SESSION['user']['role'] == 3): ?>
                    <a href="historial_visitas.php?filter=activas" class="btn btn-sm btn-outline-success <?php echo ($_GET['filter'] ?? '') === 'activas' ? 'active' : ''; ?>">
                        <i class="bi bi-person-check"></i> Mis Activas
                    </a>
                    <?php endif; ?>
                    
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Visitante</th>
                            <th class="text-center">Residente</th>
                            <th class="text-center">Casa</th>
                            <th class="text-center">Motivo</th>
                            <th class="text-center">Hora</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center actions-column">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($visitas as $visita): ?>
                            <tr <?php echo (isset($_GET['validated']) && $visita['id_estado'] == 2) ? 'class="table-success"' : ''; ?>>
                                <td class="text-center"><?= htmlspecialchars(date("d/m/y", strtotime($visita['fecha_ingreso']))) ?></td>
                                <td class="text-center"><?= htmlspecialchars($visita['visitante_nombre'] . ' ' . $visita['visitante_apellido']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($visita['residente_nombre'] . ' ' . $visita['residente_apellido']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($visita['direccion_casa']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($visita['motivo_visita']) ?></td>
                                <td class="text-center"><?= date('g:i a', strtotime($visita['hora_ingreso'])) ?></td>
                                <td class="text-center">
                                    <?php if (isset($visita['id_estado'])): ?>
                                        <?php if ($visita['id_estado'] == 1): ?>
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        <?php elseif ($visita['id_estado'] == 2): ?>
                                            <span class="badge bg-success">
                                                Aprobada
                                                <?php if (isset($_GET['validated'])): ?>
                                                    <i class="bi bi-check-circle ms-1"></i>
                                                <?php endif; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Desconocido</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Desconocido</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="detalle_visita.php?id=<?= $visita['id_visita'] ?>" class="btn btn-sm btn-outline-primary" title="Ver"><i class="bi bi-eye-fill"></i></a>
                                        <?php if (isset($visita['id_estado']) && $visita['id_estado'] == 1 && in_array($_SESSION['user']['role'], [1, 2, 4])): ?>
                                            <form method="POST" action="confirmar_visitas.php" class="d-inline">
                                                <input type="hidden" name="id_visita" value="<?= $visita['id_visita'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success" title="Confirmar llegada">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($visitas)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-exclamation-circle-fill"></i> 
                                    <?php 
                                        if (isset($_GET['filter'])) {
                                            switch($_GET['filter']) {
                                                case 'hoy': echo 'No hay visitas registradas para hoy.'; break;
                                                case 'pendientes': echo 'No hay visitas pendientes.'; break;
                                                case 'activas': echo 'No tienes visitas activas.'; break;
                                                default: echo 'No hay visitas que coincidan con el filtro.';
                                            }
                                        } else {
                                            echo 'No hay visitas registradas.';
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
                    Mostrando <strong><?= count($visitas) ?></strong> registros
                </small>
                <?php if (count($visitas) > 0): ?>
                <small class="text-muted">
                    Última actualización: <?= date('d/m/Y H:i') ?>
                </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para selección de fechas -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="exportModalLabel">Exportar Visitas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="incluirPendientes" name="incluir_pendientes" checked>
                        <label class="form-check-label" for="incluirPendientes">
                            Incluir visitas pendientes
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

    // Si viene desde validación, hacer scroll suave hacia la tabla después de 1 segundo
    if (window.location.search.includes('validated=1')) {
        setTimeout(function() {
            document.querySelector('.table-responsive').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }, 1000);
    }
});
</script>