<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Carga el autoloader de Composer para las dependencias externas
require_once __DIR__ . '/../src/Controllers/ReservasController.php'; // Incluye el controlador de reservas
require_once __DIR__ . '/../src/Config/permissions.php'; // Incluye las funciones de gestión de permisos

session_start(); // Inicia la sesión para acceder a variables de usuario

// Verifica si el usuario tiene permiso para gestionar reservas
if (!tienePermiso('gestion_reservas')) {
    header('Location: dashboard.php'); // Si no tiene permiso, redirige al dashboard
    exit; // Detiene la ejecución del script
}

// Instancia el controlador de reservas
$controller = new ReservasController();

// Obtener el filtro desde la URL si existe
$filtro = $_GET['filter'] ?? null;

// Obtiene la lista de reservas usando el método index() del controlador con filtro
$reservas = $controller->index($filtro);

// Define variables para el título de la página y el menú activo
$titulo = 'Gestión de Reservas';
$pagina_actual = 'gestion_reservas';

// Ajustar el título según el filtro aplicado
switch($filtro) {
    case 'pendientes':
        $titulo = 'Reservas Pendientes';
        break;
    case 'aprobadas':
        $titulo = 'Reservas Aprobadas';
        break;
    case 'rechazadas':
        $titulo = 'Reservas Rechazadas';
        break;
    case 'hoy':
        $titulo = 'Reservas del Día';
        break;
    case 'activas':
        $titulo = 'Mis Reservas Activas';
        break;
    case 'mes_actual':
        $titulo = 'Reservas del Mes Actual';
        break;
}

// Obtener estadísticas para mostrar en la vista
$estadisticas = $controller->getEstadisticasReservas();

// Inicia el buffer de salida para capturar el contenido de la vista
ob_start();

// Incluye el componente de la vista que muestra la gestión de reservas
require_once __DIR__ . '/../views/components/gestion_reservas.php';

// Guarda el contenido capturado en la variable $contenido
$contenido = ob_get_clean();

// Incluye el layout principal de la aplicación, que usará $contenido para mostrar la página completa
require_once __DIR__ . '/../views/layout/main.php';
