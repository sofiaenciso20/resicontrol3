<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Carga el autoloader de Composer para dependencias externas
require_once __DIR__ . '/../src/Controllers/HistorialPaquetesController.php'; // Incluye el controlador específico para historial de paquetes
require_once __DIR__ . '/../src/Config/permissions.php'; // Incluye las funciones de gestión de permisos

session_start(); // Inicia la sesión para acceder a variables de usuario

// Verifica si el usuario tiene permiso para ver el historial de paquetes
if (!tienePermiso('historial_paquetes')) {
    header('Location: dashboard.php'); // Si no tiene permiso, redirige al dashboard
    exit; // Detiene la ejecución del script
}

// Instanciar el controlador de historial de paquetes
$controller = new HistorialPaquetesController();

// Obtiene la lista de paquetes usando el método index() del controlador
$paquetes = $controller->index();

// Variables para el layout (título de la página y menú activo)
$titulo = 'Historial de Paquetes';
$pagina_actual = 'historial_paquetes';

// Inicia el buffer de salida para capturar el contenido de la vista
ob_start();

// Incluye el componente de la vista que muestra el historial de paquetes
require_once __DIR__ . '/../views/components/historial_paquetes.php';

// Guarda el contenido capturado en la variable $contenido
$contenido = ob_get_clean();

// Carga el layout principal de la aplicación, que usará $contenido para mostrar la página completa
require_once __DIR__ . '/../views/layout/main.php';
?>