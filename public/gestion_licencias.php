<?php
session_start(); // Inicia la sesión para acceder a variables de usuario

// Carga dependencias y permisos
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload de Composer para cargar clases externas
require_once __DIR__ . '/../src/Config/permissions.php'; // Archivo de funciones de permisos

use App\Controllers\LicenciasController; // Importa el controlador de licencias

// 1. Verifica que el usuario esté autenticado
if (!isset($_SESSION['user'])) {
    header('Location: login.php'); // Si no hay usuario en sesión, redirige al login
    exit;
}

// 2. Verifica que el usuario tenga permiso y sea superadmin (rol 1)
if (!tienePermiso('gestion_licencias') || $_SESSION['user']['role'] != 1) {
    header('Location: dashboard.php'); // Si no tiene permiso o no es superadmin, redirige al dashboard
    exit;
}

// 3. Inicializa variables y obtiene las licencias desde el controlador
$pageTitle = "Gestión de Licencias"; // Título de la página
$pagina_actual = 'licencias'; // Variable para resaltar el menú activo

try {
    $licenciasController = new LicenciasController(); // Instancia el controlador
    $resultado = $licenciasController->obtenerLicencias(); // Obtiene todas las licencias
    $licencias = $resultado['success'] ? $resultado['data'] : []; // Si hay éxito, guarda las licencias, si no, un array vacío
} catch (Exception $e) {
    error_log("Error en gestion_licencias.php: " . $e->getMessage()); // Log de error si falla la consulta
    $licencias = [];
}

// 4. Inicia el buffer de salida para renderizar la vista
ob_start();
?>

<!-- Vista principal -->
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestión de Licencias</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Gestión de Licencias</li>
    </ol>
    
    <!-- Cabecera con botón de nueva licencia -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Listado de Licencias</h4>
                        <!-- Botón para abrir el modal de creación de licencia -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearLicencia">
                            <i class="bi bi-plus-circle me-2"></i>Nueva Licencia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de licencias -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Residencial</th>
                                    <th>Estado</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Usuarios</th>
                                    <th>Residentes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($licencias)): ?>
                                <!-- Si no hay licencias, muestra mensaje -->
                                <tr>
                                    <td colspan="8" class="text-center">No hay licencias registradas</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($licencias as $licencia): 
                                    // Obtiene estadísticas de uso de la licencia
                                    $estadisticas = $licenciasController->obtenerEstadisticasUso($licencia['codigo_licencia']);
                                    // Determina la clase del badge según el estado
                                    $estado_clase = $licencia['estado'] === 'activa' ? 'success' : 
                                                 ($licencia['estado'] === 'inactiva' ? 'danger' : 'warning');
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($licencia['codigo_licencia']); ?></td>
                                    <td><?php echo htmlspecialchars($licencia['nombre_residencial']); ?></td>
                                    <td>
                                        <!-- Badge de estado -->
                                        <span class="badge bg-<?php echo $estado_clase; ?>">
                                            <?php echo ucfirst($licencia['estado']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($licencia['fecha_inicio'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($licencia['fecha_fin'])); ?></td>
                                    <td>
                                        <!-- Barra de progreso de usuarios -->
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar <?php echo $estadisticas['porcentaje_usuarios'] > 90 ? 'bg-danger' : 'bg-primary'; ?>" 
                                                     role="progressbar" 
                                                     style="width: <?php echo min(100, $estadisticas['porcentaje_usuarios']); ?>%"
                                                     aria-valuenow="<?php echo $estadisticas['porcentaje_usuarios']; ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="ms-2 small">
                                                <?php echo $estadisticas['total_usuarios']; ?>/<?php echo $estadisticas['max_usuarios']; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- Barra de progreso de residentes -->
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar <?php echo $estadisticas['porcentaje_residentes'] > 90 ? 'bg-danger' : 'bg-success'; ?>" 
                                                     role="progressbar" 
                                                     style="width: <?php echo min(100, $estadisticas['porcentaje_residentes']); ?>%"
                                                     aria-valuenow="<?php echo $estadisticas['porcentaje_residentes']; ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="ms-2 small">
                                                <?php echo $estadisticas['total_residentes']; ?>/<?php echo $estadisticas['max_residentes']; ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- Botones de acciones: editar, ver, activar/desactivar -->
                                        <div class="btn-group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    onclick="editarLicencia(<?php echo $licencia['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info" 
                                                    onclick="verDetalles(<?php echo $licencia['id']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <?php if ($licencia['estado'] === 'activa'): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="desactivarLicencia(<?php echo $licencia['id']; ?>)">
                                                <i class="bi bi-power"></i>
                                            </button>
                                            <?php else: ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success" 
                                                    onclick="activarLicencia(<?php echo $licencia['id']; ?>)">
                                                <i class="bi bi-power"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Licencia -->
<div class="modal fade" id="modalCrearLicencia" tabindex="-1" aria-labelledby="modalCrearLicenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearLicenciaLabel">Nueva Licencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Formulario de creación de licencia -->
            <form id="formCrearLicencia" onsubmit="return crearLicencia(event)">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="nombre_residencial" class="form-label">Nombre del Residencial</label>
                            <input type="text" class="form-control" id="nombre_residencial" name="nombre_residencial" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <div class="col-md-6">
                            <label for="max_usuarios" class="form-label">Máximo de Usuarios</label>
                            <input type="number" class="form-control" id="max_usuarios" name="max_usuarios" required min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="max_residentes" class="form-label">Máximo de Residentes</label>
                            <input type="number" class="form-control" id="max_residentes" name="max_residentes" required min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Características</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="caracteristicas[]" value="gestion_visitas" id="check_visitas">
                                <label class="form-check-label" for="check_visitas">
                                    Gestión de Visitas
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="caracteristicas[]" value="gestion_paquetes" id="check_paquetes">
                                <label class="form-check-label" for="check_paquetes">
                                    Gestión de Paquetes
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="caracteristicas[]" value="gestion_reservas" id="check_reservas">
                                <label class="form-check-label" for="check_reservas">
                                    Gestión de Reservas
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Licencia</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Licencia -->
<div class="modal fade" id="modalEditarLicencia" tabindex="-1" aria-labelledby="modalEditarLicenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLicenciaLabel">Editar Licencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Formulario de edición de licencia -->
            <form id="formEditarLicencia" onsubmit="return actualizarLicencia(event)">
                <input type="hidden" id="editar_licencia_id" name="licencia_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="editar_nombre_residencial" class="form-label">Nombre del Residencial</label>
                            <input type="text" class="form-control" id="editar_nombre_residencial" name="nombre_residencial" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editar_fecha_fin" class="form-label">Fecha de Fin</label>
                            <input type="date" class="form-control" id="editar_fecha_fin" name="fecha_fin" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editar_max_usuarios" class="form-label">Máximo de Usuarios</label>
                            <input type="number" class="form-control" id="editar_max_usuarios" name="max_usuarios" required min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="editar_max_residentes" class="form-label">Máximo de Residentes</label>
                            <input type="number" class="form-control" id="editar_max_residentes" name="max_residentes" required min="1">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Características</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="caracteristicas[]" value="gestion_visitas" id="editar_caract_visitas">
                                <label class="form-check-label" for="editar_caract_visitas">Gestión de Visitas</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="caracteristicas[]" value="gestion_paquetes" id="editar_caract_paquetes">
                                <label class="form-check-label" for="editar_caract_paquetes">Gestión de Paquetes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="caracteristicas[]" value="gestion_reservas" id="editar_caract_reservas">
                                <label class="form-check-label" for="editar_caract_reservas">Gestión de Reservas</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles -->
<div class="modal fade" id="modalDetallesLicencia" tabindex="-1" aria-labelledby="modalDetallesLicenciaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesLicenciaLabel">Detalles de la Licencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Código:</strong> <span id="detalle_codigo"></span></p>
                        <p><strong>Residencial:</strong> <span id="detalle_residencial"></span></p>
                        <p><strong>Estado:</strong> <span id="detalle_estado"></span></p>
                        <p><strong>Fecha Inicio:</strong> <span id="detalle_fecha_inicio"></span></p>
                        <p><strong>Fecha Fin:</strong> <span id="detalle_fecha_fin"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Usuarios:</strong> <span id="detalle_usuarios"></span></p>
                        <p><strong>Residentes:</strong> <span id="detalle_residentes"></span></p>
                        <p><strong>Características:</strong></p>
                        <ul id="detalle_caracteristicas"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos de la página -->
<script>
// Envía el formulario de creación de licencia por AJAX
function crearLicencia(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const datos = {
        nombre_residencial: formData.get('nombre_residencial'),
        fecha_inicio: formData.get('fecha_inicio'),
        fecha_fin: formData.get('fecha_fin'),
        max_usuarios: parseInt(formData.get('max_usuarios')),
        max_residentes: parseInt(formData.get('max_residentes')),
        caracteristicas: formData.getAll('caracteristicas[]')
    };
    // Log para debugging
    console.log('Datos del formulario:', datos);

    fetch('crear_licencia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(datos)
    })
    .then(response => {
        console.log('Status:', response.status);
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Error parsing JSON:', text);
                throw new Error('Respuesta inválida del servidor');
            }
        });
    })
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.success) {
            alert('Licencia creada exitosamente');
            window.location.reload();
        } else {
            alert('Error al crear la licencia: ' + (data.mensaje || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud: ' + error.message);
    });

    return false;
}

// Redirige a la página de edición de licencia
function editarLicencia(id) {
    window.location.href = `editar_licencia.php?id=${id}`;
}

// Redirige a la página de detalles de licencia
function verDetalles(id) {
    window.location.href = `detalle_licencia.php?id=${id}`;
}

// Confirma y desactiva una licencia
function desactivarLicencia(id) {
    if (confirm('¿Está seguro de que desea desactivar esta licencia?')) {
        actualizarEstadoLicencia(id, 'inactiva');
    }
}

// Confirma y activa una licencia
function activarLicencia(id) {
    if (confirm('¿Está seguro de que desea activar esta licencia?')) {
        actualizarEstadoLicencia(id, 'activa');
    }
}

// Envía la solicitud para cambiar el estado de la licencia
function actualizarEstadoLicencia(id, estado) {
    fetch('actualizar_estado_licencia.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id, estado })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error al actualizar el estado: ' + data.mensaje);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}

// Función para editar licencia (AJAX, llena el modal de edición)
async function editarLicencia(id) {
    try {
        const response = await fetch(`obtener_licencia.php?id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const licencia = data.data;
            // Llenar el formulario de edición
            document.getElementById('editar_licencia_id').value = licencia.id;
            document.getElementById('editar_nombre_residencial').value = licencia.nombre_residencial;
            document.getElementById('editar_fecha_fin').value = licencia.fecha_fin;
            document.getElementById('editar_max_usuarios').value = licencia.max_usuarios;
            document.getElementById('editar_max_residentes').value = licencia.max_residentes;
            // Marcar características
            const caracteristicas = JSON.parse(licencia.caracteristicas || '[]');
            document.getElementById('editar_caract_visitas').checked = caracteristicas.includes('gestion_visitas');
            document.getElementById('editar_caract_paquetes').checked = caracteristicas.includes('gestion_paquetes');
            document.getElementById('editar_caract_reservas').checked = caracteristicas.includes('gestion_reservas');
            // Mostrar modal
            new bootstrap.Modal(document.getElementById('modalEditarLicencia')).show();
        } else {
            alert('Error al cargar la licencia: ' + data.mensaje);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar la licencia');
    }
}

// Envía el formulario de edición de licencia por AJAX
async function actualizarLicencia(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    const datos = Object.fromEntries(formData.entries());
    datos.caracteristicas = formData.getAll('caracteristicas[]');
    
    try {
        const response = await fetch('actualizar_licencia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datos)
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Licencia actualizada exitosamente');
            window.location.reload();
        } else {
            alert('Error al actualizar la licencia: ' + data.mensaje);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar la licencia');
    }
    
    return false;
}

// Muestra los detalles de la licencia en el modal
async function verDetalles(id) {
    try {
        const response = await fetch(`obtener_licencia.php?id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const licencia = data.data;
            // Llenar los detalles
            document.getElementById('detalle_codigo').textContent = licencia.codigo_licencia;
            document.getElementById('detalle_residencial').textContent = licencia.nombre_residencial;
            document.getElementById('detalle_estado').textContent = licencia.estado.toUpperCase();
            document.getElementById('detalle_fecha_inicio').textContent = new Date(licencia.fecha_inicio).toLocaleDateString();
            document.getElementById('detalle_fecha_fin').textContent = new Date(licencia.fecha_fin).toLocaleDateString();
            // Mostrar estadísticas
            const estadisticas = await obtenerEstadisticas(licencia.codigo_licencia);
            document.getElementById('detalle_usuarios').textContent = `${estadisticas.total_usuarios}/${licencia.max_usuarios}`;
            document.getElementById('detalle_residentes').textContent = `${estadisticas.total_residentes}/${licencia.max_residentes}`;
            // Mostrar características
            const caracteristicas = JSON.parse(licencia.caracteristicas || '[]');
            const ulCaracteristicas = document.getElementById('detalle_caracteristicas');
            ulCaracteristicas.innerHTML = '';
            caracteristicas.forEach(caract => {
                const li = document.createElement('li');
                li.textContent = caract.replace('gestion_', '').replace('_', ' ').toUpperCase();
                ulCaracteristicas.appendChild(li);
            });
            // Mostrar modal
            new bootstrap.Modal(document.getElementById('modalDetallesLicencia')).show();
        } else {
            alert('Error al cargar los detalles: ' + data.mensaje);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los detalles');
    }
}

// Obtiene estadísticas de uso de la licencia por AJAX
async function obtenerEstadisticas(codigo) {
    try {
        const response = await fetch(`obtener_estadisticas.php?codigo=${codigo}`);
        const data = await response.json();
        return data.success ? data.data : { total_usuarios: 0, total_residentes: 0 };
    } catch (error) {
        console.error('Error:', error);
        return { total_usuarios: 0, total_residentes: 0 };
    }
}

// Cambia el estado de la licencia (activa/inactiva) con confirmación
async function cambiarEstadoLicencia(id, estado) {
    if (!confirm(`¿Está seguro que desea ${estado === 'activa' ? 'activar' : 'desactivar'} esta licencia?`)) {
        return;
    }
    try {
        const response = await fetch('cambiar_estado_licencia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id, estado })
        });
        const data = await response.json();
        if (data.success) {
            alert('Estado de licencia actualizado exitosamente');
            window.location.reload();
        } else {
            alert('Error al actualizar el estado: ' + data.mensaje);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar el estado');
    }
}

// Funciones de activación/desactivación para usar desde los botones
function activarLicencia(id) {
    cambiarEstadoLicencia(id, 'activa');
}

function desactivarLicencia(id) {
    cambiarEstadoLicencia(id, 'inactiva');
}
</script>

<?php
// Finaliza el buffer de salida y lo asigna a $contenido para el layout principal
$contenido = ob_get_clean();
require_once __DIR__ . '/../views/layout/main.php'; // Incluye el layout principal de la aplicación
?>