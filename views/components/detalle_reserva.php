<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de la Reserva</title>
</head>
<body class="container mt-5">
<div class="card shadow-lg">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">Detalle de Reserva</h3>
    </div>
    <div class="card-body">
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= $mensaje ?></div>
        <?php endif; ?>
 
        <?php if (!$reserva): ?>
            <div class="alert alert-danger">No se encontró la reserva.</div>
        <?php elseif ($modo_edicion): ?>
            <form method="POST">
                <div class="mb-2">
                    <label>Fecha:</label>
                    <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($reserva['fecha']) ?>" required>
                </div>
                <div class="mb-2">
                    <label>Horario:</label>
                    <select name="id_horario" class="form-select">
                        <option value="1" <?= $reserva['horario'] == '08:00 - 10:00' ? 'selected' : '' ?>>08:00 - 10:00</option>
                        <option value="2" <?= $reserva['horario'] == '10:00 - 12:00' ? 'selected' : '' ?>>10:00 - 12:00</option>
                        <option value="3" <?= $reserva['horario'] == '14:00 - 16:00' ? 'selected' : '' ?>>14:00 - 16:00</option>
                        <option value="4" <?= $reserva['horario'] == '16:00 - 18:00' ? 'selected' : '' ?>>16:00 - 18:00</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Motivo:</label>
                    <select name="id_mot_zonas" class="form-select">
                        <option value="1" <?= $reserva['motivo_zonas'] == 'Cumpleaños' ? 'selected' : '' ?>>Cumpleaños</option>
                        <option value="2" <?= $reserva['motivo_zonas'] == 'Reunión Familiar' ? 'selected' : '' ?>>Reunión Familiar</option>
                        <option value="3" <?= $reserva['motivo_zonas'] == 'Evento Comunitario' ? 'selected' : '' ?>>Evento Comunitario</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label>Observaciones:</label>
                    <textarea name="observaciones" class="form-control"><?= htmlspecialchars($reserva['observaciones']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="detalle_reserva.php?id=<?= urlencode($id) ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Zona:</strong> <?= htmlspecialchars($reserva['nombre_zona']) ?></p>
                    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($reserva['fecha'])) ?></p>
                    <p><strong>Horario:</strong> <?= htmlspecialchars($reserva['horario']) ?></p>
                    <p><strong>Motivo:</strong> <?= htmlspecialchars($reserva['motivo'] ?? 'No especificado') ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Residente:</strong> <?= htmlspecialchars($reserva['nombre_residente']) ?></p>
                    <p><strong>Documento:</strong> <?= htmlspecialchars($reserva['documento_residente']) ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($reserva['telefono_residente']) ?></p>
                    <p><strong>Estado:</strong>
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
                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($reserva['estado']) ?></span>
                    </p>
                </div>
            </div>
 
            <?php if ($reserva['estado'] !== 'Pendiente'): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <p><strong>Fecha de Procesamiento:</strong>
                        <?= $reserva['fecha_apro'] ? date('d/m/Y', strtotime($reserva['fecha_apro'])) : 'No procesada' ?>
                    </p>
                    <p><strong>Administrador:</strong>
                        <?= htmlspecialchars($reserva['nombre_administrador'] ?? 'No asignado') ?>
                    </p>
                    <?php if (!empty($reserva['observaciones'])): ?>
                        <p><strong>Observaciones:</strong></p>
                        <div class="alert alert-info">
                            <?= nl2br(htmlspecialchars($reserva['observaciones'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
 
            <div class="mt-4">
                <a href="gestion_reservas.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

