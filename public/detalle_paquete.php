<?php

// ===========================================
// DETALLE DE PAQUETE - ACCESO Y VISTA
// ===========================================

// CARGA DE DEPENDENCIAS NECESARIAS

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload de Composer
require_once __DIR__ . '/../src/Controllers/HistorialPaquetesController.php'; // Controlador de paquetes
require_once __DIR__ . '/../src/Config/permissions.php'; // Función de permisos

// INICIAR SESIÓN Y VERIFICAR PERMISOS

session_start(); // Inicia la sesión

// Verifica si el usuario tiene permiso para acceder a 'historial_paquetes'
if (!tienePermiso('historial_paquetes')) {
    header('Location: dashboard.php');
    exit;
}

// VALIDACIÓN DEL ID DEL PAQUETE

// Captura el ID del paquete desde la URL (método GET)
$id = $_GET['id'] ?? null;

// Si no se proporciona ID, muestra error y redirige
if (!$id) {
    $_SESSION['error'] = 'ID de paquete no proporcionado';
    header('Location: historial_paquetes.php');
    exit;
}

// OBTENER DETALLE DEL PAQUETE DESDE EL CONTROLADOR

$controller = new HistorialPaquetesController(); // Instancia del controlador
$paquete = $controller->obtenerDetallePaquete($id); // Consulta del paquete

// Si el paquete no existe, muestra error y redirige
if (!$paquete) {
    $_SESSION['error'] = 'No se encontró información del paquete';
    header('Location: historial_paquetes.php');
    exit;
}

// VERIFICACIÓN DE PROPIEDAD DEL PAQUETE (SI ES RESIDENTE)

// Si el usuario es residente (rol 3) y el paquete no le pertenece
if ($_SESSION['user']['role'] == 3 && $paquete['id_usuarios'] != $_SESSION['user']['documento']) {
    $_SESSION['error'] = 'No tienes permiso para ver este paquete';
    header('Location: historial_paquetes.php');
    exit;
}

// CONFIGURACIÓN DE VARIABLES PARA LA VISTA

$titulo = 'Detalle de Paquete';
$pagina_actual = 'detalle_paquete';

// RENDERIZADO DE LA VISTA

ob_start(); // Inicia el buffer de salida
require_once __DIR__ . '/../views/components/detalle_paquete.php'; // Carga la vista del paquete
$contenido = ob_get_clean(); // Captura el contenido del buffer

// CARGA DEL LAYOUT PRINCIPAL

require_once __DIR__ . '/../views/layout/main.php'; // Carga el diseño principal
