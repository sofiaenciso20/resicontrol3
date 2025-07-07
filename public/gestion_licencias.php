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
                                    //validacion ternaria anidado
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
<!-- Scripts específicos de la página -->
<script>
/*
 * ====================================================================
 * SCRIPT DE GESTIÓN DE LICENCIAS - SISTEMA RESIDENCIAL
 * ====================================================================
 * Este script maneja todas las operaciones CRUD de licencias:
 * - Crear nuevas licencias
 * - Editar licencias existentes
 * - Ver detalles de licencias
 * - Cambiar estado (activar/desactivar)
 * - Obtener estadísticas de uso
 * 
 * Todas las operaciones se realizan via AJAX para mejor UX
 * ====================================================================
 */

// ====================================================================
// FUNCIÓN 1: CREAR NUEVA LICENCIA
// ====================================================================
/**
 * Maneja la creación de nuevas licencias mediante AJAX
 * @param {Event} event - Evento del formulario de creación
 * @returns {boolean} - false para prevenir envío tradicional
 */
function crearLicencia(event) {
    // PASO 1: Interceptar el envío del formulario
    event.preventDefault(); // Cancela el comportamiento por defecto (recargar página)
    
    // PASO 2: Obtener el formulario y sus datos
    const form = event.target; // Obtiene el formulario que disparó el evento
    const formData = new FormData(form); // Crea objeto FormData con todos los campos
    
    // PASO 3: Estructurar los datos en formato JSON
    const datos = {
        // Obtener valores de campos de texto
        nombre_residencial: formData.get('nombre_residencial'), // Nombre del complejo residencial
        fecha_inicio: formData.get('fecha_inicio'),             // Fecha de inicio de vigencia
        fecha_fin: formData.get('fecha_fin'),                   // Fecha de fin de vigencia
        
        // Convertir strings a números enteros
        max_usuarios: parseInt(formData.get('max_usuarios')),     // Límite máximo de usuarios
        max_residentes: parseInt(formData.get('max_residentes')), // Límite máximo de residentes
        
        // Obtener array de características seleccionadas (checkboxes)
        caracteristicas: formData.getAll('caracteristicas[]')     // Array con todas las características marcadas
    };
    
    // PASO 4: Log para debugging en consola del navegador
    console.log('📋 Datos del formulario a enviar:', datos);

    // PASO 5: Enviar datos al servidor mediante AJAX
    fetch('crear_licencia.php', {
        method: 'POST',                                    // Método HTTP para enviar datos
        headers: {
            'Content-Type': 'application/json',           // Especifica que enviamos JSON
            'Accept': 'application/json'                  // Especifica que esperamos JSON como respuesta
        },
        body: JSON.stringify(datos)                       // Convierte objeto JavaScript a string JSON
    })
    // PASO 6: Procesar la respuesta del servidor
    .then(response => {
        // Log del código de estado HTTP (200, 404, 500, etc.)
        console.log('📡 Código de estado HTTP:', response.status);
        
        // Convertir respuesta a texto y luego a JSON
        return response.text().then(text => {
            try {
                // Intentar parsear el texto como JSON
                return JSON.parse(text);
            } catch (e) {
                // Si el JSON es inválido, mostrar error y lanzar excepción
                console.error('❌ Error al parsear JSON:', text);
                throw new Error('Respuesta inválida del servidor');
            }
        });
    })
    // PASO 7: Manejar el resultado de la operación
    .then(data => {
        console.log('✅ Respuesta del servidor:', data);
        
        // Verificar si la operación fue exitosa
        if (data.success) {
            // Operación exitosa: informar al usuario y recargar página
            alert('✅ Licencia creada exitosamente');
            window.location.reload(); // Recarga la página para mostrar la nueva licencia
        } else {
            // Operación fallida: mostrar mensaje de error
            alert('❌ Error al crear la licencia: ' + (data.mensaje || 'Error desconocido'));
        }
    })
    // PASO 8: Capturar y manejar errores
    .catch(error => {
        // Log del error para debugging
        console.error('💥 Error en la operación:', error);
        
        // Mostrar mensaje de error al usuario
        alert('❌ Error al procesar la solicitud: ' + error.message);
    });

    // PASO 9: Garantizar que el formulario no se envíe de forma tradicional
    return false;
}

// ====================================================================
// FUNCIÓN 2: EDITAR LICENCIA - CARGAR DATOS
// ====================================================================
/**
 * Obtiene los datos de una licencia específica y los carga en el modal de edición
 * @param {number} id - ID de la licencia a editar
 */
async function editarLicencia(id) {
    try {
        // PASO 1: Solicitar datos de la licencia al servidor
        console.log('🔄 Solicitando datos de la licencia ID:', id);
        const response = await fetch(`obtener_licencia.php?id=${id}`);
        const data = await response.json();
        
        // PASO 2: Verificar si la solicitud fue exitosa
        if (data.success) {
            // Extraer los datos de la licencia
            const licencia = data.data;
            console.log('📋 Datos de la licencia obtenidos:', licencia);
            
            // PASO 3: Llenar el formulario de edición con los datos existentes
            document.getElementById('editar_licencia_id').value = licencia.id;                       // ID oculto
            document.getElementById('editar_nombre_residencial').value = licencia.nombre_residencial; // Nombre
            document.getElementById('editar_fecha_fin').value = licencia.fecha_fin;                   // Fecha fin
            document.getElementById('editar_max_usuarios').value = licencia.max_usuarios;             // Límite usuarios
            document.getElementById('editar_max_residentes').value = licencia.max_residentes;         // Límite residentes
            
            // PASO 4: Manejar las características (checkboxes)
            // Parsear el JSON de características o usar array vacío si es null
            const caracteristicas = JSON.parse(licencia.caracteristicas || '[]');
            
            // Marcar o desmarcar cada checkbox según las características existentes
            document.getElementById('editar_caract_visitas').checked = caracteristicas.includes('gestion_visitas');
            document.getElementById('editar_caract_paquetes').checked = caracteristicas.includes('gestion_paquetes');
            document.getElementById('editar_caract_reservas').checked = caracteristicas.includes('gestion_reservas');
            
            // PASO 5: Mostrar el modal de edición
            const modal = new bootstrap.Modal(document.getElementById('modalEditarLicencia'));
            modal.show();
            console.log('✅ Modal de edición mostrado');
        } else {
            // Error al obtener los datos
            alert('❌ Error al cargar la licencia: ' + data.mensaje);
        }
    } catch (error) {
        // Capturar errores de red o parsing
        console.error('💥 Error al cargar licencia:', error);
        alert('❌ Error al cargar la licencia');
    }
}

// ====================================================================
// FUNCIÓN 3: ACTUALIZAR LICENCIA - GUARDAR CAMBIOS
// ====================================================================
/**
 * Procesa los cambios del formulario de edición y los guarda en el servidor
 * @param {Event} event - Evento del formulario de edición
 * @returns {boolean} - false para prevenir envío tradicional
 */
async function actualizarLicencia(event) {
    // PASO 1: Interceptar el envío del formulario
    event.preventDefault(); // Cancela el comportamiento por defecto
    
    // PASO 2: Capturar y procesar los datos del formulario
    const formData = new FormData(event.target);                    // Obtener datos del formulario
    const datos = Object.fromEntries(formData.entries());           // Convertir FormData a objeto plano
    datos.caracteristicas = formData.getAll('caracteristicas[]');   // Manejar array de checkboxes por separado
    
    console.log('📋 Datos de actualización:', datos);
    
    try {
        // PASO 3: Enviar datos actualizados al servidor
        const response = await fetch('actualizar_licencia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datos)
        });
        
        // PASO 4: Procesar la respuesta del servidor
        const data = await response.json();
        
        if (data.success) {
            // Actualización exitosa
            alert('✅ Licencia actualizada exitosamente');
            window.location.reload(); // Recargar página para mostrar cambios
        } else {
            // Error en la actualización
            alert('❌ Error al actualizar la licencia: ' + data.mensaje);
        }
    } catch (error) {
        // Capturar errores de red o parsing
        console.error('💥 Error al actualizar licencia:', error);
        alert('❌ Error al actualizar la licencia');
    }
    
    // PASO 5: Garantizar que el formulario no se envíe de forma tradicional
    return false;
}

// ====================================================================
// FUNCIÓN 4: VER DETALLES DE LICENCIA
// ====================================================================
/**
 * Obtiene y muestra información completa de una licencia en modal de solo lectura
 * @param {number} id - ID de la licencia a mostrar
 */
async function verDetalles(id) {
    try {
        // PASO 1: Obtener datos básicos de la licencia
        console.log('🔍 Obteniendo detalles de la licencia ID:', id);
        const response = await fetch(`obtener_licencia.php?id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const licencia = data.data;
            
            // PASO 2: Llenar campos básicos del modal
            document.getElementById('detalle_codigo').textContent = licencia.codigo_licencia;        // Código único
            document.getElementById('detalle_residencial').textContent = licencia.nombre_residencial; // Nombre residencial
            document.getElementById('detalle_estado').textContent = licencia.estado.toUpperCase();    // Estado en mayúsculas
            
            // PASO 3: Formatear y mostrar fechas
            // Convertir strings de fecha a objetos Date y formatear según configuración regional
            document.getElementById('detalle_fecha_inicio').textContent = new Date(licencia.fecha_inicio).toLocaleDateString();
            document.getElementById('detalle_fecha_fin').textContent = new Date(licencia.fecha_fin).toLocaleDateString();
            
            // PASO 4: Obtener y mostrar estadísticas de uso
            const estadisticas = await obtenerEstadisticas(licencia.codigo_licencia);
            
            // Mostrar estadísticas en formato "actual/máximo"
            document.getElementById('detalle_usuarios').textContent = `${estadisticas.total_usuarios}/${licencia.max_usuarios}`;
            document.getElementById('detalle_residentes').textContent = `${estadisticas.total_residentes}/${licencia.max_residentes}`;
            
            // PASO 5: Procesar y mostrar características
            const caracteristicas = JSON.parse(licencia.caracteristicas || '[]');
            const ulCaracteristicas = document.getElementById('detalle_caracteristicas');
            
            // Limpiar lista anterior
            ulCaracteristicas.innerHTML = '';
            
            // Crear elemento de lista para cada característica
            caracteristicas.forEach(caract => {
                const li = document.createElement('li');
                
                // Formatear nombre: "gestion_visitas" -> "VISITAS"
                li.textContent = caract.replace('gestion_', '').replace('_', ' ').toUpperCase();
                
                // Agregar elemento a la lista
                ulCaracteristicas.appendChild(li);
            });
            
            // PASO 6: Mostrar modal de detalles
            const modal = new bootstrap.Modal(document.getElementById('modalDetallesLicencia'));
            modal.show();
            console.log('✅ Modal de detalles mostrado');
        } else {
            alert('❌ Error al cargar los detalles: ' + data.mensaje);
        }
    } catch (error) {
        console.error('💥 Error al cargar detalles:', error);
        alert('❌ Error al cargar los detalles');
    }
}

// ====================================================================
// FUNCIÓN 5: OBTENER ESTADÍSTICAS DE USO
// ====================================================================
/**
 * Función auxiliar que obtiene estadísticas de uso de una licencia específica
 * @param {string} codigo - Código de la licencia
 * @returns {Object} - Objeto con estadísticas o valores por defecto
 */
async function obtenerEstadisticas(codigo) {
    try {
        // PASO 1: Solicitar estadísticas al servidor
        console.log('📊 Obteniendo estadísticas para licencia:', codigo);
        const response = await fetch(`obtener_estadisticas.php?codigo=${codigo}`);
        const data = await response.json();
        
        // PASO 2: Retornar estadísticas o valores por defecto
        return data.success ? data.data : { total_usuarios: 0, total_residentes: 0 };
    } catch (error) {
        // En caso de error, retornar valores por defecto
        console.error('💥 Error al obtener estadísticas:', error);
        return { total_usuarios: 0, total_residentes: 0 };
    }
}

// ====================================================================
// FUNCIÓN 6: CAMBIAR ESTADO DE LICENCIA
// ====================================================================
/**
 * Cambia el estado de una licencia (activa/inactiva) con confirmación del usuario
 * @param {number} id - ID de la licencia
 * @param {string} estado - Nuevo estado ('activa' o 'inactiva')
 */
async function cambiarEstadoLicencia(id, estado) {
    // PASO 1: Solicitar confirmación al usuario
    const accion = estado === 'activa' ? 'activar' : 'desactivar';
    const confirmacion = confirm(`¿Está seguro que desea ${accion} esta licencia?`);
    
    if (!confirmacion) {
        console.log('❌ Usuario canceló la operación');
        return; // Salir si el usuario cancela
    }
    
    try {
        // PASO 2: Enviar solicitud de cambio de estado al servidor
        console.log(`🔄 Cambiando estado de licencia ${id} a: ${estado}`);
        const response = await fetch('cambiar_estado_licencia.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id, estado }) // Shorthand para { id: id, estado: estado }
        });
        
        // PASO 3: Procesar la respuesta del servidor
        const data = await response.json();
        
        if (data.success) {
            // Cambio exitoso
            alert('✅ Estado de licencia actualizado exitosamente');
            window.location.reload(); // Recargar página para reflejar cambios
        } else {
            // Error en el cambio
            alert('❌ Error al actualizar el estado: ' + data.mensaje);
        }
    } catch (error) {
        // Capturar errores de red o parsing
        console.error('💥 Error al cambiar estado:', error);
        alert('❌ Error al actualizar el estado');
    }
}

// ====================================================================
// FUNCIONES 7 y 8: WRAPPERS PARA ACTIVAR/DESACTIVAR
// ====================================================================
/**
 * Función wrapper para activar una licencia
 * @param {number} id - ID de la licencia a activar
 */
function activarLicencia(id) {
    console.log('🟢 Solicitando activación de licencia:', id);
    cambiarEstadoLicencia(id, 'activa');
}

/**
 * Función wrapper para desactivar una licencia
 * @param {number} id - ID de la licencia a desactivar
 */
function desactivarLicencia(id) {
    console.log('🔴 Solicitando desactivación de licencia:', id);
    cambiarEstadoLicencia(id, 'inactiva');
}

/*
 * ====================================================================
 * NOTAS IMPORTANTES:
 * ====================================================================
 * 1. Todas las funciones usan AJAX para evitar recargas de página
 * 2. Se incluye manejo robusto de errores con try-catch
 * 3. Se proporciona feedback visual al usuario con alerts
 * 4. Los logs facilitan el debugging en desarrollo
 * 5. Las funciones async/await mejoran la legibilidad del código
 * 6. Se validan las respuestas del servidor antes de procesarlas
 * 7. Se usan confirmaciones para operaciones críticas
 * ====================================================================
 */
</script>

<?php
// Finaliza el buffer de salida y lo asigna a $contenido para el layout principal
$contenido = ob_get_clean();
require_once __DIR__ . '/../views/layout/main.php'; // Incluye el layout principal de la aplicación
?>