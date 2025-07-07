<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
</head>
<body>
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
                                    case 'activos': echo 'Solo residentes activos'; break;
                                    case 'inactivos': echo 'Solo residentes inactivos'; break;
                                    case 'residentes': echo 'Todos los residentes'; break;
                                    default: echo 'Filtro personalizado';
                                }
                            ?>
                            <a href="gestion_residentes.php" class="btn btn-sm btn-outline-light ms-2">
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
                    <div class="col-md-4">
                        <div class="card bg-success bg-opacity-10 border-success">
                            <div class="card-body text-center">
                                <h4 class="text-success"><?php echo $estadisticas['activos']; ?></h4>
                                <small class="text-muted">Residentes Activos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning bg-opacity-10 border-warning">
                            <div class="card-body text-center">
                                <h4 class="text-warning"><?php echo $estadisticas['inactivos']; ?></h4>
                                <small class="text-muted">Residentes Inactivos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info bg-opacity-10 border-info">
                            <div class="card-body text-center">
                                <h4 class="text-info"><?php echo $estadisticas['total_residentes']; ?></h4>
                                <small class="text-muted">Total Residentes</small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Filtros y búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="gestion_residentes.php" class="btn btn-sm btn-outline-secondary <?php echo !isset($_GET['filter']) ? 'active' : ''; ?>">
                                <i class="bi bi-people-fill"></i> Todos
                            </a>
                            <a href="gestion_residentes.php?filter=residentes" class="btn btn-sm btn-outline-primary <?php echo ($_GET['filter'] ?? '') === 'residentes' ? 'active' : ''; ?>">
                                <i class="bi bi-house-door-fill"></i> Residentes
                            </a>
                            <a href="gestion_residentes.php?filter=activos" class="btn btn-sm btn-outline-success <?php echo ($_GET['filter'] ?? '') === 'activos' ? 'active' : ''; ?>">
                                <i class="bi bi-person-check-fill"></i> Activos
                            </a>
                            <a href="gestion_residentes.php?filter=inactivos" class="btn btn-sm btn-outline-warning <?php echo ($_GET['filter'] ?? '') === 'inactivos' ? 'active' : ''; ?>">
                                <i class="bi bi-person-x-fill"></i> Inactivos
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="d-flex">
                            <input type="hidden" name="filter" value="<?= $_GET['filter'] ?? '' ?>">
                            <input type="text" name="busqueda" class="form-control me-2" placeholder="Buscar residentes..." value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mensaje informativo para vigilantes -->
                <?php if ($_SESSION['user']['role'] == 4): ?>
                <div class="alert alert-info mb-3 d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>
                        <strong>Información:</strong> Como vigilante, puedes consultar la información de los residentes pero no puedes modificar su estado.
                    </div>
                </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th class="text-center">Rol</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center actions-column">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visitas as $index => $residente): ?>
                                <tr>
                                    <td class="text-center"><?= ($pagina - 1) * $porPagina + $index + 1 ?></td>
                                    <td><?= $residente['documento'] ?></td>
                                    <td><?= htmlspecialchars($residente['nombre']) ?></td>
                                    <td><?= $residente['telefono'] ?></td>
                                    <td><?= htmlspecialchars($residente['direccion_casa']) ?></td>
                                    <td class="text-center">
                                        <?php 
                                            switch($residente['id_rol']) {
                                                case 1: echo '<span class="badge bg-danger">Super Admin</span>'; break;
                                                case 2: echo '<span class="badge bg-primary">Administrador</span>'; break;
                                                case 3: echo '<span class="badge bg-success">Residente</span>'; break;
                                                case 4: echo '<span class="badge bg-info">Vigilante</span>'; break;
                                                default: echo '<span class="badge bg-secondary">Desconocido</span>';
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($residente['id_estado_usuario'] == 4): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="detalle_persona.php?id=<?= $residente['documento'] ?>" class="btn btn-sm btn-outline-primary" title="Ver">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <?php if ($residente['id_rol'] != 1 && in_array($_SESSION['user']['role'], [1, 2])): ?>
                                            <a href="inhabilitar_usuario.php?id=<?= $residente['documento'] ?>"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Inhabilitar"
                                                onclick="return confirm('¿Estás seguro de que deseas inhabilitar este usuario?');">
                                                <i class="bi bi-person-x-fill"></i>
                                            </a>
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
                                                    case 'activos': echo 'No hay residentes activos registrados.'; break;
                                                    case 'inactivos': echo 'No hay residentes inactivos.'; break;
                                                    case 'residentes': echo 'No hay residentes registrados.'; break;
                                                    default: echo 'No hay usuarios que coincidan con el filtro.';
                                                }
                                            } else {
                                                echo 'No hay usuarios registrados.';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-3">
                        <?php if ($pagina > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?filter=<?= $_GET['filter'] ?? '' ?>&busqueda=<?= $_GET['busqueda'] ?? '' ?>&pagina=1" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?filter=<?= $_GET['filter'] ?? '' ?>&busqueda=<?= $_GET['busqueda'] ?? '' ?>&pagina=<?= $pagina - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++): ?>
                            <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="?filter=<?= $_GET['filter'] ?? '' ?>&busqueda=<?= $_GET['busqueda'] ?? '' ?>&pagina=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($pagina < $totalPaginas): ?>
                            <li class="page-item">
                                <a class="page-link" href="?filter=<?= $_GET['filter'] ?? '' ?>&busqueda=<?= $_GET['busqueda'] ?? '' ?>&pagina=<?= $pagina + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="?filter=<?= $_GET['filter'] ?? '' ?>&busqueda=<?= $_GET['busqueda'] ?? '' ?>&pagina=<?= $totalPaginas ?>" aria-label="Last">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Mostrando <?= count($visitas) ?> de <?= $resultados['total'] ?> registros
                    </small>
                    <?php if (count($visitas) > 0): ?>
                    <small class="text-muted">
                        Página <?= $pagina ?> de <?= $totalPaginas ?>
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
                    <h5 class="modal-title" id="exportModalLabel">Exportar Residentes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="exportar_residentes_excel.php" method="POST" id="exportForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Formato de exportación:</label>
                            <select class="form-select" name="formato">
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="csv">CSV (.csv)</option>
                                <option value="pdf">PDF (.pdf)</option>
                            </select>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="incluirInactivos" name="incluir_inactivos">
                            <label class="form-check-label" for="incluirInactivos">
                                Incluir usuarios inactivos
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="soloResidentes" name="solo_residentes" <?php echo (($_GET['filter'] ?? '') === 'residentes' || ($_GET['filter'] ?? '') === 'activos') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="soloResidentes">
                                Solo residentes (excluir administradores y vigilantes)
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
        // Actualizar automáticamente el checkbox según el filtro
        const filtroActual = '<?php echo $_GET['filter'] ?? ''; ?>';
        const soloResidentesCheckbox = document.getElementById('soloResidentes');
        
        if (filtroActual === 'residentes' || filtroActual === 'activos') {
            soloResidentesCheckbox.checked = true;
        }
        
        // Opcional: Deshabilitar checkbox de inactivos si el filtro es solo activos
        const incluirInactivosCheckbox = document.getElementById('incluirInactivos');
        if (filtroActual === 'activos') {
            incluirInactivosCheckbox.checked = false;
            incluirInactivosCheckbox.disabled = true;
        }
    });
    </script>
</body>
</html>