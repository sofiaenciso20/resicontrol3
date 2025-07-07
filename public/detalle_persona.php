<?php
// Controlador para ver/editar residentes

require_once __DIR__ . '/../src/Controllers/ResidentesController.php';
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$id = $_GET['id'] ?? null;
$modo_edicion = (
    isset($_GET['editar']) && $_GET['editar'] == 1 &&
    isset($_SESSION['user']) &&
    in_array($_SESSION['user']['role'], [1, 2])
);

$residente = null;
$mensaje = '';

$controller = new ResidentesController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id && isset($_SESSION['user']) && in_array($_SESSION['user']['role'], [1, 2])) {
    $datos = [
        'nombre' => $_POST['nombre'] ?? '',
        'apellido' => $_POST['apellido'] ?? '',
        'telefono' => $_POST['telefono'] ?? '',
        'correo' => $_POST['correo'] ?? '',
        'direccion_casa' => $_POST['direccion_casa'] ?? '',
        'cantidad_personas' => $_POST['cantidad_personas'] ?? '',
        'tiene_animales' => isset($_POST['tiene_animales']) ? 1 : 0,
        'cantidad_animales' => $_POST['cantidad_animales'] ?? '',
        'direccion_residencia' => $_POST['direccion_residencia'] ?? ''
    ];

    $controller->actualizarResidente($id, $datos);
    header('Location: detalle_persona.php?id=' . urlencode($id) . '&actualizado=1');
    exit;
}

if ($id) {
    $residente = $controller->obtenerDetalleResidente($id);
}

if (isset($_GET['actualizado'])) {
    $mensaje = 'Datos actualizados correctamente.';
}

$titulo = 'Ver Residente';
$pagina_actual = 'ver_residente';

ob_start();
include __DIR__ . '/../views/components/detalle_persona.php';
$contenido = ob_get_clean();

require_once __DIR__ . '/../views/layout/main.php';