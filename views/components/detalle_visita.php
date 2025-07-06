<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Visita</title>
</head>
<body class="container mt-5">
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3>Detalle de la Visita</h3>
    </div>
    <div class="card-body">
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if (!$visita): ?>
            <div class="alert alert-danger">No se encontró la visita.</div>
        <?php elseif ($modo_edicion): ?>
            <form method="POST">
                <div class="mb-2"><label>Nombre:</label><input class="form-control" name="nombre" value="<?= htmlspecialchars($visita['nombre']) ?>" required></div>
                <div class="mb-2"><label>Apellido:</label><input class="form-control" name="apellido" value="<?= htmlspecialchars($visita['apellido']) ?>" required></div>
                <div class="mb-2"><label>Documento:</label><input class="form-control" name="documento" value="<?= htmlspecialchars($visita['documento']) ?>" required></div>
                <div class="mb-2">
                    <label>Motivo:</label>
                    <select class="form-select" name="id_mot_visi">
                        <option value="1" <?= $visita['motivo_visita'] == 'Familiar' ? 'selected' : '' ?>>Familiar</option>
                        <option value="2" <?= $visita['motivo_visita'] == 'Técnico' ? 'selected' : '' ?>>Técnico</option>
                        <option value="3" <?= $visita['motivo_visita'] == 'Amigo' ? 'selected' : '' ?>>Amigo</option>
                    </select>
                </div>
                <div class="mb-2"><label>Fecha:</label><input type="date" class="form-control" name="fecha_ingreso" value="<?= htmlspecialchars($visita['fecha_ingreso']) ?>" required></div>
                <div class="mb-2"><label>Hora:</label><input type="time" class="form-control" name="hora_ingreso" value="<?= htmlspecialchars($visita['hora_ingreso']) ?>" required></div>
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="detalle_visita.php?id=<?= $visita['id_visita'] ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        <?php else: ?>
            <ul class="list-group">
                <li class="list-group-item"><strong>Visitante:</strong> <?= htmlspecialchars($visita['nombre'] . ' ' . $visita['apellido']) ?></li>
                <li class="list-group-item"><strong>Documento:</strong> <?= htmlspecialchars($visita['documento']) ?></li>
                <li class="list-group-item"><strong>Motivo:</strong> <?= htmlspecialchars($visita['motivo_visita']) ?></li>
                <li class="list-group-item"><strong>Fecha:</strong> <?= htmlspecialchars($visita['fecha_ingreso']) ?></li>
                <li class="list-group-item"><strong>Hora:</strong> <?= htmlspecialchars($visita['hora_ingreso']) ?></li>
                <li class="list-group-item"><strong>Residente:</strong> <?= htmlspecialchars($visita['residente_nombre'] . ' ' . $visita['residente_apellido']) ?></li>
                <li class="list-group-item"><strong>Casa:</strong> <?= htmlspecialchars($visita['direccion_casa']) ?></li>
            </ul>
            <?php if (in_array($_SESSION['user']['role'], [1, 2])): ?>
                <a href="detalle_visita.php?id=<?= $visita['id_visita'] ?>&editar=1" class="btn btn-primary mt-3">Editar</a>
            <?php endif; ?>
            <a href="/historial_visitas.php" class="btn btn-outline-primary mt-3">Volver</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
