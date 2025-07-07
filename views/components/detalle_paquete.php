<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                <i class="bi bi-box-seam me-2"></i>
                Detalle del Paquete
            </h3>
            <div class="btn-group">
                <?php if (stripos($paquete['estado'] ?? '', 'pendiente') !== false && in_array($_SESSION['user']['role'], [1, 2, 4])): ?>
                    <span class="badge bg-warning text-dark fs-6">
                        <i class="bi bi-clock me-1"></i>
                        Pendiente de Entrega
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($paquete)): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Error:</strong> No se encontró información del paquete.
                        <br>
                        <small>Verifique que el ID del paquete sea correcto o que tenga permisos para verlo.</small>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="historial_paquetes.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Volver a Paquetes
                    </a>
                </div>

            <?php else: ?>
                <div class="row">
                    <!-- Información del Paquete -->
                    <div class="col-md-6">
                        <div class="card border-primary mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-box me-2"></i>
                                    Información del Paquete
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong><i class="bi bi-hash text-primary me-2"></i>ID del Paquete:</strong>
                                    <span class="ms-2">
                                        <span class="badge bg-secondary">#<?= htmlspecialchars($paquete['id_paquete']) ?></span>
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="bi bi-file-text text-primary me-2"></i>Descripción:</strong>
                                    <div class="ms-4 mt-2">
                                        <div class="alert alert-light mb-0">
                                            <?= htmlspecialchars($paquete['descripcion']) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="bi bi-calendar-plus text-primary me-2"></i>Fecha de Recepción:</strong>
                                    <div class="ms-4 mt-2">
                                        <span class="badge bg-success">
                                            <i class="bi bi-calendar3"></i>
                                            <?= date('d/m/Y', strtotime($paquete['fech_hor_recep'])) ?>
                                        </span>
                                        <span class="badge bg-info ms-1">
                                            <i class="bi bi-clock"></i>
                                            <?= date('H:i', strtotime($paquete['fech_hor_recep'])) ?>
                                        </span>
                                        <?php if (date('Y-m-d', strtotime($paquete['fech_hor_recep'])) === date('Y-m-d')): ?>
                                            <span class="badge bg-warning text-dark ms-1">Hoy</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <strong><i class="bi bi-calendar-check text-primary me-2"></i>Fecha de Entrega:</strong>
                                    <div class="ms-4 mt-2">
                                        <?php if ($paquete['fech_hor_entre']): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-calendar3"></i>
                                                <?= date('d/m/Y', strtotime($paquete['fech_hor_entre'])) ?>
                                            </span>
                                            <span class="badge bg-info ms-1">
                                                <i class="bi bi-clock"></i>
                                                <?= date('H:i', strtotime($paquete['fech_hor_entre'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-clock me-1"></i>Pendiente de Entrega
                                            </span>
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
                                    <i class="bi bi-person me-2"></i>
                                    Residente Destinatario
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong><i class="bi bi-person-fill text-success me-2"></i>Nombre:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($paquete['nombre_residente'] . ' ' . $paquete['apellido_residente']) ?></span>
                                </div>
                                <div class="mb-0">
                                    <strong><i class="bi bi-house text-success me-2"></i>Información:</strong>
                                    <div class="ms-4 mt-2">
                                        <span class="badge bg-success">
                                            <i class="bi bi-person-check me-1"></i>Residente
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Vigilante -->
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-shield me-2"></i>
                                    Vigilante Responsable
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-0">
                                    <strong><i class="bi bi-person-badge text-info me-2"></i>Nombre:</strong>
                                    <span class="ms-2"><?= htmlspecialchars($paquete['nombre_vigilante'] . ' ' . $paquete['apellido_vigilante']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado y Acciones -->
                <div class="card border-warning mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-flag me-2"></i>
                            Estado y Acciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="bi bi-info-circle text-warning me-2"></i>Estado Actual:</strong>
                                <?php
                                $estado = htmlspecialchars($paquete['estado']);
                                $badge = 'bg-secondary';
                                $icon = 'bi-question-circle';
                                
                                if (stripos($estado, 'pendiente') !== false) {
                                    $badge = 'bg-warning text-dark';
                                    $icon = 'bi-clock';
                                }
                                if (stripos($estado, 'entregado') !== false) {
                                    $badge = 'bg-success';
                                    $icon = 'bi-check-circle';
                                }
                                if (stripos($estado, 'recibido') !== false) {
                                    $badge = 'bg-primary';
                                    $icon = 'bi-inbox';
                                }
                                ?>
                                <span class="badge <?= $badge ?> fs-6 ms-2">
                                    <i class="<?= $icon ?> me-1"></i>
                                    <?= $estado ?>
                                </span>
                            </div>

                            <!-- Botones de Acción para Cambiar Estado -->
                            <?php if (stripos($estado, 'pendiente') !== false && in_array($_SESSION['user']['role'], [1, 2, 4])): ?>
                                <div class="btn-group">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id_paquete" value="<?= htmlspecialchars($paquete['id_paquete']) ?>">
                                        <button type="submit" name="marcar_entregado" class="btn btn-success" 
                                                onclick="return confirm('¿Confirmar la entrega del paquete?')">
                                            <i class="bi bi-check-circle me-1"></i> Marcar como Entregado
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Botones de Navegación -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="historial_paquetes.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Historial
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Script para funcionalidades adicionales -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh cada 30 segundos si el paquete está pendiente
    <?php if (stripos($paquete['estado'] ?? '', 'pendiente') !== false): ?>
    setTimeout(function() {
        location.reload();
    }, 30000);
    <?php endif; ?>
});
</script>
