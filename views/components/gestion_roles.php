<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Roles</title>
  
</head>
<body class="bg-light py-5">
  <div class="container">
    <div class="card shadow-lg">
      <div class="card-header bg-primary text-white">
        <h3 class="mb-0">Gestión de Roles de Usuarios</h3>
      </div>
      <div class="card-body">
        <?php if (!empty($mensaje_exito)): ?>
          <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($mensaje_exito) ?>
          </div>
        <?php endif; ?>

        <?php $rol_usuario_logueado = $_SESSION['user']['role']; ?>

        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-dark">
              <tr>
                <th>Documento</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol actual</th>
                <th>Cambiar Rol</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $user): ?>
                <tr>
                  <td><?= $user['documento'] ?></td>
                  <td><?= htmlspecialchars($user['nombre'] . ' ' . $user['apellido']) ?></td>
                  <td><?= htmlspecialchars($user['correo']) ?></td>
                  <td><?= htmlspecialchars($user['rol']) ?></td>
                  <td>
                    <?php if ($rol_usuario_logueado == 1 || $rol_usuario_logueado == 2): ?>
                      <form method="POST" action="/gestion_roles.php" class="d-flex justify-content-center align-items-center gap-2">
                        <input type="hidden" name="documento" value="<?= $user['documento'] ?>">
                        <select name="id_rol" class="form-select form-select-sm w-auto" required>
                          <option value="">Seleccione</option>
                          <?php foreach ($roles as $rol): ?>
                            <?php
                            if ($rol_usuario_logueado == 2 && !in_array($rol['id_rol'], [3, 4])) continue;
                            ?>
                            <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['rol']) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-success">Cambiar</button>
                      </form>
                    <?php else: ?>
                      <span class="text-muted">Sin permisos</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div> <!-- table-responsive -->
      </div> <!-- card-body -->
    </div> <!-- card -->
  </div> <!-- container -->

</body>
</html>
