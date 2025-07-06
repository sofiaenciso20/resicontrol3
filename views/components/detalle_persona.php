<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle del Residente</title>
</head>
<body class="bg-light py-5">
  <div class="container">
    <div class="card shadow-lg border-0">
      <div class="card-header bg-primary text-white d-flex align-items-center">
        <i class="bi bi-person-badge me-2" style="font-size: 1.5rem;"></i>
        <h3 class="mb-0">Detalle del Persona</h3>
      </div>
      <div class="card-body">
        <?php if (!empty($mensaje)): ?>
          <div class="alert alert-success text-center"> <?= htmlspecialchars($mensaje) ?> </div>
        <?php endif; ?>
        <?php if (empty($residente)): ?>
          <div class="alert alert-danger text-center">No se encontró información del residente.</div>
        <?php elseif (!empty($modo_edicion)): ?>

          <form method="POST">
            <ul class="list-group list-group-flush mb-4">
              <li class="list-group-item">
                <strong>Documento:</strong> <?= htmlspecialchars($residente['documento']) ?>
              </li>
              <li class="list-group-item">
                <strong>Nombre:</strong>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($residente['nombre']) ?>" required>
              </li>
              <li class="list-group-item">
                <strong>Apellido:</strong>
                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($residente['apellido']) ?>" required>
              </li>
              <li class="list-group-item">
                <strong>Teléfono:</strong>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($residente['telefono']) ?>">
              </li>
              <li class="list-group-item">
                <strong>Correo:</strong>
                <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($residente['correo']) ?>">
              </li>
              <li class="list-group-item">
                <strong>Dirección:</strong>
                <input type="text" name="direccion_casa" class="form-control" value="<?= htmlspecialchars($residente['direccion_casa']) ?>">
              </li>
              <li class="list-group-item">
                <strong>Cantidad de Personas:</strong>
                <input type="number" name="cantidad_personas" class="form-control" value="<?= htmlspecialchars($residente['cantidad_personas']) ?>">
              </li>
              <li class="list-group-item">
                <strong>Tiene Animales:</strong>
                <input type="checkbox" name="tiene_animales" value="1" <?= $residente['tiene_animales'] ? 'checked' : '' ?>>
              </li>
              <li class="list-group-item">
                <strong>Cantidad de Animales:</strong>
                <input type="number" name="cantidad_animales" class="form-control" value="<?= htmlspecialchars($residente['cantidad_animales']) ?>">
              </li>
              <li class="list-group-item">
                <strong>Dirección de Residencia:</strong>
                <input type="text" name="direccion_residencia" class="form-control" value="<?= htmlspecialchars($residente['direccion_residencia']) ?>">
              </li>
            </ul>
            <button type="submit" class="btn btn-success me-2"><i class="bi bi-save"></i> Guardar</button>
            <a href="detalle_persona.php?id=<?= urlencode($residente['documento']) ?>" class="btn btn-secondary">Cancelar</a>
          </form>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item">
              <strong>Documento:</strong> <?= htmlspecialchars($residente['documento']) ?>
            </li>
            <li class="list-group-item">
              <strong>Nombre:</strong> <?= htmlspecialchars($residente['nombre'] . ' ' . $residente['apellido']) ?>
            </li>
            <li class="list-group-item">
              <strong>Teléfono:</strong> <?= htmlspecialchars($residente['telefono']) ?>
            </li>
            <li class="list-group-item">
              <strong>Correo:</strong> <?= htmlspecialchars($residente['correo']) ?>
            </li>
            <li class="list-group-item">
              <strong>Dirección:</strong> <?= htmlspecialchars($residente['direccion_casa']) ?>
            </li>
            <li class="list-group-item">
              <strong>Cantidad de Personas:</strong> <?= htmlspecialchars($residente['cantidad_personas']) ?>
            </li>
            <li class="list-group-item">
              <strong>Tiene Animales:</strong> <?= $residente['tiene_animales'] ? 'Sí' : 'No' ?>
            </li>
            <li class="list-group-item">
              <strong>Cantidad de Animales:</strong> <?= htmlspecialchars($residente['cantidad_animales']) ?>
            </li>
            <li class="list-group-item">
              <strong>Dirección de Residencia:</strong> <?= htmlspecialchars($residente['direccion_residencia']) ?>
            </li>
          </ul>
          <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'], [1,2])): ?>
            <a href="detalle_persona.php?id=<?= urlencode($residente['documento']) ?>&editar=1" class="btn btn-primary me-2">
              <i class="bi bi-pencil"></i> Editar
            </a>
          <?php endif; ?>
          <a href="/gestion_residentes.php" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Volver a la Gestión
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
</body>
</html>