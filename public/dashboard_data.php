<?php
// Archivo para manejar las peticiones AJAX de datos filtrados del dashboard
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../src/Controllers/DashboardController.php';
use App\Controllers\DashboardController;

$user = $_SESSION['user'];
$dashboardController = new DashboardController();

// Obtener el tipo de filtro solicitado
$filter = $_GET['filter'] ?? '';

$data = [];

try {
    switch($filter) {
        case 'residentes_activos':
            if (in_array($user['role'], [1, 2])) {
                $data = $dashboardController->getResidentesActivos();
            }
            break;
            
        case 'visitas_dia':
            $data = $dashboardController->getVisitasDelDia();
            break;
            
        case 'reservas_pendientes':
            if (in_array($user['role'], [1, 2, 4])) {
                $data = $dashboardController->getReservasSoloPendientes();
            }
            break;
            
        case 'paquetes_pendientes':
            $data = $dashboardController->getPaquetesSinReclamar();
            break;
            
        case 'mis_visitas':
            if ($user['role'] == 3) {
                $data = $dashboardController->getMisVisitasDetalle($user['documento']);
            }
            break;
            
        case 'mis_paquetes':
            if ($user['role'] == 3) {
                $data = $dashboardController->getMisPaquetesDetalle($user['documento']);
            }
            break;
            
        case 'mis_reservas':
            if ($user['role'] == 3) {
                $data = $dashboardController->getMisReservasDetalle($user['documento']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Filtro no válido']);
            exit;
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $data,
        'count' => count($data)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?>
