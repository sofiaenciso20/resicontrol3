<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Residente</title>
</head>
<body>
    <div class="container py-4">
        <div class="card residente-card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0">
                    <i class="bi bi-person-badge me-2"></i>
                    Detalle del Residente
                </h2>
            </div>
            
            <div class="card-body p-0">
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?= htmlspecialchars($mensaje) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($residente)): ?>
                    <div class="alert alert-danger d-flex align-items-center m-3" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Error:</strong> No se encontró información del residente.
                        </div>
                    </div>
                <?php elseif (!empty($modo_edicion)): ?>
                    <!-- MODO EDICIÓN -->
                    <form method="POST" class="needs-validation p-3" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-credit-card me-1"></i>
                                        Documento
                                    </label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($residente['documento']) ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-envelope me-1"></i>
                                        Correo Electrónico
                                    </label>
                                    <input type="email" name="correo" class="form-control" 
                                           value="<?= htmlspecialchars($residente['correo']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-person me-1"></i>
                                        Nombre
                                    </label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?= htmlspecialchars($residente['nombre']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-person me-1"></i>
                                        Apellido
                                    </label>
                                    <input type="text" name="apellido" class="form-control" 
                                           value="<?= htmlspecialchars($residente['apellido']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-telephone me-1"></i>
                                        Teléfono
                                    </label>
                                    <input type="tel" name="telefono" class="form-control" 
                                           value="<?= htmlspecialchars($residente['telefono']) ?>">
                                </div>
                            </div>
                            <?php if ($residente['id_rol'] == 3): // Solo mostrar para residentes ?>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-house me-1"></i>
                                            Dirección de Casa
                                        </label>
                                        <input type="text" name="direccion_casa" class="form-control" 
                                               value="<?= htmlspecialchars($residente['direccion_casa']) ?>">
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($residente['id_rol'] == 3): ?>
                            <!-- Campos específicos para residentes -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-people me-1"></i>
                                            Personas en Casa
                                        </label>
                                        <input type="number" name="cantidad_personas" class="form-control" min="1"
                                               value="<?= htmlspecialchars($residente['cantidad_personas']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-heart me-1"></i>
                                            ¿Tiene Mascotas?
                                        </label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="tiene_animales" value="1" 
                                                   <?= $residente['tiene_animales'] ? 'checked' : '' ?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="bi bi-heart-fill me-1"></i>
                                            Cantidad de Mascotas
                                        </label>
                                        <input type="number" name="cantidad_animales" class="form-control" min="0"
                                               value="<?= htmlspecialchars($residente['cantidad_animales']) ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="detalle_persona.php?id=<?= urlencode($residente['documento']) ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- MODO VISUALIZACIÓN -->
                    <!-- Información Personal -->
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="bi bi-person-lines-fill me-2"></i>
                            Información Personal
                        </h3>
                    </div>
                    
                    <div class="p-3">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="bi bi-credit-card"></i>
                                Documento:
                            </span>
                            <span class="info-value"><?= htmlspecialchars($residente['documento']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="bi bi-person"></i>
                                Nombre Completo:
                            </span>
                            <span class="info-value"><?= htmlspecialchars($residente['nombre'] . ' ' . $residente['apellido']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="bi bi-telephone"></i>
                                Teléfono:
                            </span>
                            <span class="info-value">
                                <?= $residente['telefono'] ? '<a href="tel:'.htmlspecialchars($residente['telefono']).'">'.htmlspecialchars($residente['telefono']).'</a>' : 'N/A' ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="bi bi-envelope"></i>
                                Correo Electrónico:
                            </span>
                            <span class="info-value">
                                <?= $residente['correo'] ? '<a href="mailto:'.htmlspecialchars($residente['correo']).'">'.htmlspecialchars($residente['correo']).'</a>' : 'N/A' ?>
                            </span>
                        </div>
                    </div>

                    <?php if ($residente['id_rol'] == 3): ?>
                        <!-- Información de Residencia (solo para residentes) -->
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="bi bi-house-door me-2"></i>
                                Información de Residencia
                            </h3>
                        </div>
                        
                        <div class="p-3">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="bi bi-geo-alt"></i>
                                    Dirección:
                                </span>
                                <span class="info-value"><?= $residente['direccion_casa'] ? htmlspecialchars($residente['direccion_casa']) : 'N/A' ?></span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="bi bi-people"></i>
                                    Personas en Casa:
                                </span>
                                <span class="info-value"><?= htmlspecialchars($residente['cantidad_personas'] ?? '0') ?></span>
                            </div>
                        </div>

                        <!-- Información de Mascotas (solo para residentes) -->
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="bi bi-heart me-2"></i>
                                Mascotas
                            </h3>
                        </div>
                        
                        <div class="p-3">
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="bi bi-heart-fill"></i>
                                    ¿Tiene Mascotas?:
                                </span>
                                <span class="info-value"><?= $residente['tiene_animales'] ? 'Sí' : 'No' ?></span>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">
                                    <i class="bi bi-heart"></i>
                                    Cantidad:
                                </span>
                                <span class="info-value"><?= htmlspecialchars($residente['cantidad_animales'] ?? '0') ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between">
                    <a href="gestion_residentes.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                    <?php if (isset($residente) && empty($modo_edicion) && in_array($_SESSION['user']['role'] ?? 0, [1, 2])): ?>
                        <a href="detalle_persona.php?id=<?= urlencode($residente['documento']) ?>&editar=1" class="btn btn-primary">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
</body>
</html>