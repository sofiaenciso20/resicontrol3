<?php
require_once __DIR__ . '/../bootstrap.php'; // Carga la configuración y dependencias principales de la aplicación
use App\Controllers\LicenciasController;    // Importa el controlador de licencias

header('Content-Type: application/json');   // Indica que la respuesta será en formato JSON

try {
    // Verificar que el usuario sea superadmin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
        throw new Exception('Acceso no autorizado'); // Si no es superadmin, lanza una excepción
    }

    // Verificar que se proporcionó un ID
    if (!isset($_GET['id'])) {
        throw new Exception('ID de licencia no proporcionado'); // Si no se envió el ID, lanza una excepción
    }

    $id = (int)$_GET['id']; // Obtiene el ID de la licencia desde la URL (GET) y lo convierte a entero
    $licenciasController = new LicenciasController(); // Instancia el controlador de licencias
    $resultado = $licenciasController->obtenerLicencia($id); // Llama al método para obtener los datos de la licencia

    if (!$resultado['success']) {
        throw new Exception($resultado['mensaje']); // Si la consulta falla, lanza una excepción con el mensaje recibido
    }

    echo json_encode($resultado); // Devuelve la respuesta exitosa en formato JSON

} catch (Exception $e) {
    http_response_code(400); // Si ocurre cualquier error, responde con código 400
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage()
    ]);
}