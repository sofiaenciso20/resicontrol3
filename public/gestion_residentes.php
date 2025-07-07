<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Controllers/ResidentesController.php';
require_once __DIR__ . '/../src/Config/permissions.php';

session_start();

if (!tienePermiso('gestion_residentes')) {
    header('Location: dashboard.php');
    exit;
}

$controller = new ResidentesController();

// Obtener parámetros
$filtro = $_GET['filter'] ?? null;
$busqueda = $_GET['busqueda'] ?? null;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$porPagina = 10;

// Obtener datos con paginación y búsqueda
$resultados = $controller->index($filtro, $busqueda, $pagina, $porPagina);
$visitas = $resultados['datos']; // Mantener compatibilidad
$totalPaginas = $resultados['totalPaginas'];

// Definir título
$titulo = 'Gestión de Residentes';
switch($filtro) {
    case 'activos':
        $titulo = 'Residentes Activos';
        break;
    case 'inactivos':
        $titulo = 'Residentes Inactivos';
        break;
    case 'residentes':
        $titulo = 'Todos los Residentes';
        break;
}

// Obtener estadísticas
$estadisticas = $controller->getEstadisticasResidentes();

// Variables para la vista
$pagina_actual = 'gestion_residentes';

// Iniciar buffer
ob_start();
require_once __DIR__ . '/../views/components/gestion_residentes.php';
$contenido = ob_get_clean();

// Incluir layout
require_once __DIR__ . '/../views/layout/main.php';