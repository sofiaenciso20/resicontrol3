<?php
// Inicia la sesión para acceder a las variables de sesión del usuario
session_start();

// Incluye el controlador de licencias y el autoload de Composer
require_once __DIR__ . '/../src/Controllers/LicenciasController.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Importa el namespace del controlador
use App\Controllers\LicenciasController;

// Verifica que la petición sea de tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método no permitido
    echo json_encode(['success' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

// Verifica que el usuario esté autenticado y sea superadmin (rol 1)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
    http_response_code(403); // Acceso prohibido
    echo json_encode(['success' => false, 'mensaje' => 'Acceso no autorizado']);
    exit;
}

// Obtiene y decodifica los datos JSON enviados en el cuerpo de la petición
$datos = json_decode(file_get_contents('php://input'), true);

// Valida que se hayan enviado los campos requeridos: id y estado
if (!isset($datos['id']) || !isset($datos['estado'])) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(['success' => false, 'mensaje' => 'ID y estado son requeridos']);
    exit;
}

// Valida que el estado enviado sea uno de los permitidos
if (!in_array($datos['estado'], ['activa', 'inactiva'])) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(['success' => false, 'mensaje' => 'Estado no válido']);
    exit;
}

// Instancia el controlador de licencias y actualiza el estado de la licencia
$licenciasController = new LicenciasController();
$resultado = $licenciasController->actualizarLicencia($datos['id'], [
    'estado' => $datos['estado']
]);

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($resultado);