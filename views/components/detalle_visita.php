<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                <i class="bi bi-person-badge me-2"></i>
                Detalle de Visita
            </h3>
            <?php if ($visita && in_array($_SESSION['user']['role'], [1, 2])): ?>
                <div class="btn-group">
                    <a href="detalle_visita.php?id=<?= urlencode($id) ?>&editar=1" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if ($mensaje): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!$visita): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Error:</strong> No se encontró la visita solicitada.
                        <br>
                        <small>Verifique que el ID de la visita sea correcto o que tenga permisos para verla.</small>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="historial_visitas.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Volver a Visitas
                    </a>
                </div>

            <?php elseif ($modo_edicion): ?>
                <!-- MODO EDICIÓN -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Modo Edición:</strong> Modifique los campos necesarios y guarde los cambios.
                </div>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-person me-1"></i>
                                    Nombre del Visitante
                                </label>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?= htmlspecialchars($visita['nombre']) ?>" required>
                                <div class="invalid-feedback">Ingrese el nombre del visitante.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-person me-1"></i>
                                    Apellido del Visitante
                                </label>
                                <input type="text" name="apellido" class="form-control" 
                                       value="<?= htmlspecialchars($visita['apellido']) ?>" required>
                                <div class="invalid-feedback">Ingrese el apellido del visitante.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-card-text me-1"></i>
                                    Documento de Identidad
                                </label>
                                <input type="text" name="documento" class="form-control" 
                                       value="<?= htmlspecialchars($visita['documento']) ?>" required>
                                <div class="invalid-feedback">Ingrese el documento del visitante.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-chat-text me-1"></i>
                                    Motivo de la Visita
                                </label>
                                <select name="id_mot_visi" class="form-select" required>
                                    <option value="">Seleccione un motivo</option>
                                    <option value="1" <?= $visita['motivo_visita'] == 'Familiar' ? 'selected' : '' ?>>Familiar</option>
                                    <option value="2" <?= $visita['motivo_visita'] == 'Técnico' ? 'selected' : '' ?>>Técnico</option>
                                    <option value="3" <?= $visita['motivo_visita'] == 'Amigo' ? 'selected' : '' ?>>Amigo</option>
                                    <option value="4" <?= $visita['motivo_visita'] == 'Trabajo' ? 'selected' : '' ?>>Trabajo</option>
                                    <option value="5" <?= $visita['motivo_visita'] == 'Delivery' ? 'selected' : '' ?>>Delivery</option>
                                </select>
                                <div class="invalid-feedback">Seleccione un motivo.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Fecha de Ingreso
                                </label>
                                <input type="date" name="fecha_ingreso" class="form-control" 
                                       value="<?= htmlspecialchars($visita['fecha_ingreso']) ?>" required>
                                <div class="invalid-feedback">Seleccione una fecha válida.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-clock me-1"></i>
                                    Hora de Ingreso
                                </label>
                                <input type="time" name="hora_ingreso" class="form-control" 
                                       value="<?= htmlspecialchars($visita['hora_ingreso']) ?>" required>
                                <div class="invalid-feedback">Seleccione una hora válida.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="detalle_visita.php?id=<?= urlencode($id) ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                    </div>
                </form>

            <?php else: ?>
                <!-- MODO VISUALIZACIÓN -->
                <div class="row">
                    <!-- Información del Visitante -->
                    <div class="col-md-6">
                        <div class="card border-primary mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-person-badge me-2"></i>
                                    Información del Visitante
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong><i class="bi bi-person-fill text-primary me-2"></i>Nombre Completo:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($visita['nombre'] . ' ' . $visita['apellido']) ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="bi bi-card-text text-primary me-2"></i>Documento:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($visita['documento']) ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="bi bi-chat-text text-primary me-2"></i>Motivo:</strong>
                                    <span class="badge bg-info ms-2"><?= htmlspecialchars($visita['motivo_visita']) ?></span>
                                </div>
                                <div class="mb-0">
                                    <strong><i class="bi bi-calendar-event text-primary me-2"></i>Fecha y Hora:</strong>
                                    <div class="ms-4 mt-2">
                                        <span class="badge bg-success me-2">
                                            <i class="bi bi-calendar3"></i>
                                            <?= date('d/m/Y', strtotime($visita['fecha_ingreso'])) ?>
                                        </span>
                                        <span class="badge bg-info">
                                            <i class="bi bi-clock"></i>
                                            <?= date('H:i', strtotime($visita['hora_ingreso'])) ?>
                                        </span>
                                        <?php if (date('Y-m-d', strtotime($visita['fecha_ingreso'])) === date('Y-m-d')): ?>
                                            <span class="badge bg-warning text-dark ms-1">Hoy</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Residente -->
                    <div class="col-md-6">
                        <div class="card border-success mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-house me-2"></i>
                                    Información del Residente
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong><i class="bi bi-person-fill text-success me-2"></i>Residente:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($visita['residente_nombre'] . ' ' . $visita['residente_apellido']) ?></span>
                                </div>
                                <div class="mb-0">
                                    <strong><i class="bi bi-geo-alt text-success me-2"></i>Dirección:</strong>
                                    <span class="ms-2">
                                        <?php if (!empty($visita['direccion_casa'])): ?>
                                            <span class="badge bg-success"><?= htmlspecialchars($visita['direccion_casa']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">No especificada</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="historial_visitas.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Visitas
                    </a>
                    
                    <div class="btn-group">
                        <?php if (in_array($_SESSION['user']['role'], [1, 2])): ?>
                            <a href="detalle_visita.php?id=<?= urlencode($id) ?>&editar=1" class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Script para validación del formulario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formularios Bootstrap
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>
