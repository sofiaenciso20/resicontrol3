<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                <i class="bi bi-calendar-event me-2"></i>
                Detalle de Reserva
            </h3>
            <?php if ($reserva && in_array($_SESSION['user']['role'], [1, 2]) && $reserva['estado'] === 'Pendiente'): ?>
                <div class="btn-group">
                    <a href="detalle_reserva.php?id=<?= urlencode($id) ?>&editar=1" class="btn btn-outline-light btn-sm">
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

            <?php if (!$reserva): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Error:</strong> No se encontró la reserva solicitada.
                        <br>
                        <small>Verifique que el ID de la reserva sea correcto o que tenga permisos para verla.</small>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="gestion_reservas.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Volver a Reservas
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
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Fecha de la Reserva
                                </label>
                                <input type="date" name="fecha" class="form-control" 
                                       value="<?= htmlspecialchars($reserva['fecha']) ?>" required>
                                <div class="invalid-feedback">Seleccione una fecha válida.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-clock me-1"></i>
                                    Horario
                                </label>
                                <select name="id_horario" class="form-select" required>
                                    <option value="">Seleccione un horario</option>
                                    <option value="1" <?= $reserva['horario'] == '08:00 - 10:00' ? 'selected' : '' ?>>08:00 - 10:00</option>
                                    <option value="2" <?= $reserva['horario'] == '10:00 - 12:00' ? 'selected' : '' ?>>10:00 - 12:00</option>
                                    <option value="3" <?= $reserva['horario'] == '14:00 - 16:00' ? 'selected' : '' ?>>14:00 - 16:00</option>
                                    <option value="4" <?= $reserva['horario'] == '16:00 - 18:00' ? 'selected' : '' ?>>16:00 - 18:00</option>
                                </select>
                                <div class="invalid-feedback">Seleccione un horario.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-chat-text me-1"></i>
                            Motivo de la Reserva
                        </label>
                        <select name="id_mot_zonas" class="form-select" required>
                            <option value="">Seleccione un motivo</option>
                            <option value="1" <?= ($reserva['motivo'] ?? '') == 'Cumpleaños' ? 'selected' : '' ?>>Cumpleaños</option>
                            <option value="2" <?= ($reserva['motivo'] ?? '') == 'Reunión Familiar' ? 'selected' : '' ?>>Reunión Familiar</option>
                            <option value="3" <?= ($reserva['motivo'] ?? '') == 'Evento Comunitario' ? 'selected' : '' ?>>Evento Comunitario</option>
                            <option value="4" <?= ($reserva['motivo'] ?? '') == 'Reunión de Trabajo' ? 'selected' : '' ?>>Reunión de Trabajo</option>
                            <option value="5" <?= ($reserva['motivo'] ?? '') == 'Celebración' ? 'selected' : '' ?>>Celebración</option>
                        </select>
                        <div class="invalid-feedback">Seleccione un motivo.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-file-text me-1"></i>
                            Observaciones
                        </label>
                        <textarea name="observaciones" class="form-control" rows="4" 
                                  placeholder="Agregue observaciones adicionales..."><?= htmlspecialchars($reserva['observaciones'] ?? '') ?></textarea>
                        <div class="form-text">Información adicional sobre la reserva.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="detalle_reserva.php?id=<?= urlencode($id) ?>" class="btn btn-secondary">
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
                    <!-- Información de la Reserva -->
                    <div class="col-md-6">
                        <div class="card border-primary mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    Información de la Reserva
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong><i class="bi bi-geo-alt text-primary me-2"></i>Zona Común:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($reserva['nombre_zona']) ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="bi bi-calendar3 text-primary me-2"></i>Fecha:</strong>
                                    <span class="ms-2"><?= date('d/m/Y', strtotime($reserva['fecha'])) ?></span>
                                    <?php if (date('Y-m-d', strtotime($reserva['fecha'])) === date('Y-m-d')): ?>
                                        <span class="badge bg-info ms-2">Hoy</span>
                                    <?php elseif (date('Y-m-d', strtotime($reserva['fecha'])) < date('Y-m-d')): ?>
                                        <span class="badge bg-secondary ms-2">Pasada</span>
                                    <?php else: ?>
                                        <span class="badge bg-success ms-2">Próxima</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="bi bi-clock text-primary me-2"></i>Horario:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($reserva['horario']) ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="bi bi-chat-text text-primary me-2"></i>Motivo:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($reserva['motivo'] ?? 'No especificado') ?></span>
                                </div>
                                <div class="mb-0">
                                    <strong><i class="bi bi-flag text-primary me-2"></i>Estado:</strong>
                                    <?php
                                    if ($reserva['estado'] === 'Pendiente') {
                                        $badgeClass = 'bg-warning text-dark';
                                        $icon = 'bi-clock';
                                    } elseif ($reserva['estado'] === 'Aprobada') {
                                        $badgeClass = 'bg-success';
                                        $icon = 'bi-check-circle';
                                    } elseif ($reserva['estado'] === 'Rechazada') {
                                        $badgeClass = 'bg-danger';
                                        $icon = 'bi-x-circle';
                                    } else {
                                        $badgeClass = 'bg-secondary';
                                        $icon = 'bi-question-circle';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?> ms-2">
                                        <i class="<?= $icon ?> me-1"></i>
                                        <?= htmlspecialchars($reserva['estado']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Residente -->
                    <div class="col-md-6">
                        <div class="card border-info mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-person me-2"></i>
                                    Información del Residente
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong><i class="bi bi-person-fill text-info me-2"></i>Nombre:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($reserva['nombre_residente']) ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="bi bi-card-text text-info me-2"></i>Documento:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($reserva['documento_residente']) ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="bi bi-telephone text-info me-2"></i>Teléfono:</strong>
                                    <span class="ms-2">
                                        <a href="tel:<?= htmlspecialchars($reserva['telefono_residente']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($reserva['telefono_residente']) ?>
                                        </a>
                                    </span>
                                </div>
                                <div class="mb-0">
                                    <strong><i class="bi bi-house text-info me-2"></i>Dirección:</strong>
                                    <span class="ms-2">
                                        <?php if (!empty($reserva['direccion_casa_residente'])): ?>
                                            <?= htmlspecialchars($reserva['direccion_casa_residente']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">No especificada</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Procesamiento (si aplica) -->
                <?php if ($reserva['estado'] !== 'Pendiente'): ?>
                <div class="card border-secondary mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-gear me-2"></i>
                            Información de Procesamiento
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong><i class="bi bi-calendar-check text-secondary me-2"></i>Fecha de Procesamiento:</strong>
                                    <span class="ms-2">
                                        <?= $reserva['fecha_apro'] ? date('d/m/Y', strtotime($reserva['fecha_apro'])) : 'No procesada' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong><i class="bi bi-person-badge text-secondary me-2"></i>Administrador:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($reserva['nombre_administrador'] ?? 'No asignado') ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($reserva['observaciones'])): ?>
                            <div class="mb-0">
                                <strong><i class="bi bi-file-text text-secondary me-2"></i>Observaciones:</strong>
                                <div class="alert alert-light mt-2 mb-0">
                                    <?= nl2br(htmlspecialchars($reserva['observaciones'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Botones de Acción -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="gestion_reservas.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Reservas
                    </a>
                    
                    <div class="btn-group">
                        <?php if (in_array($_SESSION['user']['role'], [1, 2]) && $reserva['estado'] === 'Pendiente'): ?>
                            <a href="detalle_reserva.php?id=<?= urlencode($id) ?>&editar=1" class="btn btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            <a href="aprobar_reserva.php?id=<?= urlencode($id) ?>" 
                               class="btn btn-success"
                               onclick="return confirm('¿Está seguro de que desea aprobar esta reserva?');">
                                <i class="bi bi-check-lg"></i> Aprobar
                            </a>
                            <a href="rechazar_reserva.php?id=<?= urlencode($id) ?>" 
                               class="btn btn-danger"
                               onclick="return prompt('Ingrese el motivo del rechazo:') !== null;">
                                <i class="bi bi-x-lg"></i> Rechazar
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

    // Validación de fecha mínima (no permitir fechas pasadas para nuevas reservas)
    const fechaInput = document.querySelector('input[name="fecha"]');
    if (fechaInput) {
        const hoy = new Date().toISOString().split('T')[0];
        fechaInput.setAttribute('min', hoy);
    }
});
</script>
