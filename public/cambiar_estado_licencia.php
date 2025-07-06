<?php
require_once __DIR__ . '/../bootstrap.php';
use App\Controllers\LicenciasController;

header('Content-Type: application/json');

try {
    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Verificar que el usuario sea superadmin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
        throw new Exception('Acceso no autorizado');
    }

    // Obtener y decodificar los datos JSON
    $jsonData = file_get_contents('php://input');
    $datos = json_decode($jsonData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }

    // Validar datos requeridos
    if (!isset($datos['id']) || !isset($datos['estado'])) {
        throw new Exception('ID de licencia y estado son requeridos');
    }

    // Validar estado
    if (!in_array($datos['estado'], ['activa', 'inactiva'])) {
        throw new Exception('Estado no válido');
    }

    // Actualizar el estado
    $licenciasController = new LicenciasController();
    $resultado = $licenciasController->actualizarLicencia($datos['id'], [
        'estado' => $datos['estado']
    ]);

    if (!$resultado['success']) {
        throw new Exception($resultado['mensaje']);
    }

    echo json_encode($resultado);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}