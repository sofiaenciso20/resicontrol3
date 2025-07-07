<?php
require_once __DIR__ . '/../src/Controllers/VisitasController.php';
require_once __DIR__ . '/../vendor/autoload.php';

session_start();
//obtiene el id de la url 
$id = $_GET['id'] ?? null;
// Determina si el usuario está en modo edición (solo Admin y SuperAdmin pueden editar)
$modo_edicion = (
    isset($_GET['editar']) && $_GET['editar'] == 1 &&
    isset($_SESSION['user']) && in_array($_SESSION['user']['role'], [1, 2])
);

$controller = new VisitasController();
$visita = null;
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id && $modo_edicion) {
    $datos = [
        'nombre' => $_POST['nombre'] ?? '',
        'apellido' => $_POST['apellido'] ?? '',
        'documento' => $_POST['documento'] ?? '',
        'id_mot_visi' => $_POST['id_mot_visi'] ?? '',
        'fecha_ingreso' => $_POST['fecha_ingreso'] ?? '',
        'hora_ingreso' => $_POST['hora_ingreso'] ?? ''
    ];
    $controller->actualizarVisita($id, $datos);
    header('Location:/detalle_visita.php?id=' . urlencode($id) . "&actualizado=1");
    exit;
}

if ($id) {
    $visita = $controller->obtenerDetalleVisita($id);
}
if (isset($_GET['actualizado'])) {
    $mensaje = 'Visita actualizada correctamente.';
}

$titulo = 'Ver Visita';
$pagina_actual = 'ver_visita';
 
// Inicia el output buffering para capturar el contenido de la vista
ob_start();
include __DIR__ . '/../views/components/detalle_visita.php';
$contenido = ob_get_clean();
 
// Carga el layout principal y muestra la página completa
require_once __DIR__ . '/../views/layout/main.php';