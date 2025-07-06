<?php

// ===============================================
// VER/EDITAR RESIDENTE - CONTROLADOR DE DETALLE
// ===============================================

// Incluye el controlador de Residentes
require_once __DIR__ . '/../src/Controllers/ResidentesController.php';

// Carga automática de clases con Composer
require_once __DIR__ . '/../vendor/autoload.php';

// ===============================================
// INICIO DE SESIÓN
// ===============================================

session_start(); // Inicia la sesión para acceder a los datos del usuario logueado

// ===============================================
// OBTENCIÓN DEL ID Y MODO EDICIÓN
// ===============================================

// Captura el ID del residente desde la URL (?id=...)
$id = $_GET['id'] ?? null;

// Verifica si está en modo edición (solo Admin y Super Admin pueden editar)
$modo_edicion = (
    isset($_GET['editar']) && $_GET['editar'] == 1 &&
    isset($_SESSION['user']) &&
    in_array($_SESSION['user']['role'], [1, 2])
);

// ===============================================
// INICIALIZACIÓN DE VARIABLES
// ===============================================

$residente = null; // Guardará los datos del residente
$mensaje = '';     // Mensaje para mostrar al usuario (ej. éxito al actualizar)

// ===============================================
// CONTROLADOR DE RESIDENTES
// ===============================================

$controller = new ResidentesController(); // Se instancia el controlador

// ===============================================
// PROCESAMIENTO DEL FORMULARIO (EDICIÓN)
// ===============================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $id &&
    isset($_SESSION['user']) &&
    in_array($_SESSION['user']['role'], [1, 2])
) {
    // Recoge los datos enviados desde el formulario
    $datos = [
        'nombre' => $_POST['nombre'] ?? '',
        'apellido' => $_POST['apellido'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'correo' => $_POST['correo'] ?? '',
        'direccion_casa' => $_POST['direccion_casa'] ?? '',
        'cantidad_personas' => $_POST['cantidad_personas'] ?? '',
        'tiene_animales' => isset($_POST['tiene_animales']) ? 1 : 0,
        'cantidad_animales' => $_POST['cantidad_animales'] ?? '',
        'direccion_residencia' => $_POST['direccion_residencia'] ?? ''
    ];

    // Actualiza los datos del residente en la base de datos
    $controller->actualizarResidente($id, $datos);

    // Redirige a la misma página sin modo edición y muestra mensaje de éxito
    header('Location: /ResiControl/public/detalle_persona.php?id=' . urlencode($id) . '&actualizado=1');
    exit;
}

// ===============================================
// OBTENER DATOS DEL RESIDENTE
// ===============================================

if ($id) {
    $residente = $controller->obtenerDetalleResidente($id);
}

// ===============================================
// MENSAJE DE ACTUALIZACIÓN EXITOSA
// ===============================================

if (isset($_GET['actualizado'])) {
    $mensaje = 'Datos actualizados correctamente.';
}

// ===============================================
// CONFIGURACIÓN PARA LA VISTA
// ===============================================

$titulo = 'Ver Residente';           // Título de la página
$pagina_actual = 'ver_residente';    // Página actual para marcar activa en el menú

// ===============================================
// RENDERIZADO DE LA VISTA
// ===============================================

ob_start(); // Inicia el almacenamiento del contenido de la vista
include __DIR__ . '/../views/components/detalle_persona.php'; // Vista que muestra o edita el residente
$contenido = ob_get_clean(); // Captura el contenido generado

// ===============================================
// CARGA DEL LAYOUT PRINCIPAL
// ===============================================

require_once __DIR__ . '/../views/layout/main.php'; // Inserta el contenido en el diseño general
