<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Carga el autoloader de Composer para dependencias externas
require_once __DIR__ . '/../src/Controllers/VisitasController.php'; // Incluye el controlador de visitas
require_once __DIR__ . '/../src/Config/permissions.php'; // Incluye las funciones de gestión de permisos

session_start(); // Inicia la sesión para acceder a variables de usuario

// Verifica si el usuario tiene permiso para ver el historial de visitas
if (!tienePermiso('historial_visitas')) {
    header('Location: dashboard.php'); // Si no tiene permiso, redirige al dashboard
    exit; // Detiene la ejecución del script
}

// Instancia el controlador de visitas
$controller = new VisitasController();

// Obtener el filtro desde la URL si existe
$filtro = $_GET['filter'] ?? null;

// Obtiene la lista de visitas usando el método index() del controlador con filtro
$visitas = $controller->index($filtro);

// Variables para el layout (título de la página y menú activo)
$titulo = 'Historial de Visitas';
$pagina_actual = 'historial_visitas';

// Ajustar el título según el filtro aplicado
switch($filtro) {
    case 'hoy':
        $titulo = 'Visitas del Día';
        break;
    case 'pendientes':
        $titulo = 'Visitas Pendientes';
        break;
    case 'activas':
        $titulo = 'Mis Visitas Activas';
        break;
}

// Elimina visitas pendientes cuya hora_ingreso + 1h es menor que la hora actual y la fecha es hoy
$db = new \App\Config\Database(); // Instancia la clase de conexión a la base de datos
$conn = $db->getConnection(); // Obtiene la conexión PDO
$stmt = $conn->prepare("DELETE FROM visitas WHERE id_estado = 1 AND TIMESTAMPADD(HOUR, 1, hora_ingreso) < CURTIME() AND fecha_ingreso = CURDATE()");
$stmt->execute(); // Ejecuta la consulta para limpiar visitas pendientes "vencidas" del día

// Inicia el buffer de salida para capturar el contenido de la vista
ob_start();
require_once __DIR__ . '/../views/components/historial_visitas.php'; // Incluye el componente de la vista que muestra el historial de visitas
$contenido = ob_get_clean(); // Guarda el contenido capturado en la variable $contenido

require_once __DIR__ . '/../views/layout/main.php'; // Carga el layout principal de la aplicación, que usará $contenido para mostrar la página completa
