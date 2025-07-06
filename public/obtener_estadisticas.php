<?php
require_once __DIR__ . '/../bootstrap.php'; // Carga la configuración y dependencias principales de la aplicación
use App\Controllers\LicenciasController;    // Importa el controlador de licencias

header('Content-Type: application/json');   // Indica que la respuesta será en formato JSON

try {
    // Verificar que el usuario sea superadmin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
        throw new Exception('Acceso no autorizado'); // Si no es superadmin, lanza una excepción
    }

    // Verificar que se proporcionó un código
    if (!isset($_GET['codigo'])) {
        throw new Exception('Código de licencia no proporcionado'); // Si no se envió el código, lanza una excepción
    }

    $codigo = $_GET['codigo']; // Obtiene el código de licencia desde la URL (GET)
    $licenciasController = new LicenciasController(); // Instancia el controlador de licencias
    $estadisticas = $licenciasController->obtenerEstadisticasUso($codigo); // Obtiene las estadísticas de uso de la licencia

    // Devuelve la respuesta exitosa en formato JSON
    echo json_encode([
        'success' => true,
        'data' => $estadisticas
    ]);

} catch (Exception $e) {
    // Si ocurre cualquier error, responde con código 400 y un mensaje de error en JSON
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}