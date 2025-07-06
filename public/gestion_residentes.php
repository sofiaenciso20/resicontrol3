<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Carga el autoloader de Composer para las dependencias externas
require_once __DIR__ . '/../src/Controllers/ResidentesController.php'; // Incluye el controlador de residentes
require_once __DIR__ . '/../src/Config/permissions.php'; // Incluye las funciones de gestión de permisos

session_start(); // Inicia la sesión para acceder a variables de usuario

// Verifica si el usuario tiene permiso para gestionar residentes
if (!tienePermiso('gestion_residentes')) {
    header('Location: dashboard.php'); // Si no tiene permiso, redirige al dashboard
    exit; // Detiene la ejecución del script
}

// Instancia el controlador de residentes
$controller = new ResidentesController();

// Obtiene la lista de residentes usando el método index() del controlador
$visitas = $controller->index(); // (Probablemente debería llamarse $residentes, pero depende del controlador)

// Define variables para el título de la página y el menú activo
$titulo = 'Gestión de Residentes';
$pagina_actual = 'gestion_residentes';

// Inicia el buffer de salida para capturar el contenido de la vista
ob_start();

// Incluye el componente de la vista que muestra la gestión de residentes
require_once __DIR__ . '/../views/components/gestion_residentes.php';

// Guarda el contenido capturado en la variable $contenido
$contenido = ob_get_clean();

// Incluye el layout principal de la aplicación, que usará $contenido para mostrar la página completa
require_once __DIR__ . '/../views/layout/main.php';
