<?php

// ==============================================
// DETALLE DE RESERVA - VER Y EDITAR
// ==============================================

// Cargar dependencias necesarias
require_once __DIR__ . '/../src/Controllers/ReservasController.php'; // Controlador de reservas
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload de Composer

// ==============================================
// INICIAR SESIÓN Y VERIFICAR ROL
// ==============================================

session_start(); // Inicia la sesión para acceder a $_SESSION

// ==============================================
// CAPTURA DEL ID DE LA RESERVA Y MODO EDICIÓN
// ==============================================

// Obtiene el ID de la reserva desde la URL (?id=...)
$id = $_GET['id'] ?? null;

// Determina si el usuario está en modo edición (solo Admin y SuperAdmin pueden editar)
$modo_edicion = isset($_GET['editar']) && $_GET['editar'] == 1 &&
                isset($_SESSION['user']) &&
                in_array($_SESSION['user']['role'], [1, 2]);

// ==============================================
// INSTANCIAR CONTROLADOR Y VARIABLES
// ==============================================

$controller = new ReservasController(); // Crea una instancia del controlador
$reserva = null; // Aquí se almacenarán los datos de la reserva
$mensaje = '';   // Mensaje de confirmación si se actualiza la reserva

// ==============================================
// PROCESAMIENTO DEL FORMULARIO (ACTUALIZACIÓN)
// ==============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $modo_edicion && $id) {
    // Captura y organiza los datos enviados desde el formulario
    $datos = [
        'fecha' => $_POST['fecha'],
        'id_horario' => $_POST['id_horario'],
        'observaciones' => $_POST['observaciones'],
        'id_mot_zonas' => $_POST['id_mot_zonas']
    ];

    // Actualiza la reserva en la base de datos
    $controller->actualizarReserva($id, $datos);

    // Redirige a la misma página con mensaje de éxito (sin modo edición)
    header("Location: detalle_reserva.php?id=" . urlencode($id) . "&actualizado=1");
    exit;
}

// ==============================================
// OBTENER DETALLE DE LA RESERVA
// ==============================================

if ($id) {
    $reserva = $controller->obtenerDetalleReserva($id); // Consulta los datos
}

// Si se actualizó correctamente, mostrar mensaje
if (isset($_GET['actualizado'])) {
    $mensaje = 'Reserva actualizada correctamente.';
}

// ==============================================
// CONFIGURACIÓN DE LA VISTA
// ==============================================

$titulo = 'ver reservas';         // Título de la página
$pagina_actual = 'ver_reserva';   // Página activa para el menú

// ==============================================
// RENDERIZADO DE LA VISTA
// ==============================================

ob_start(); // Inicia el buffer de salida
include __DIR__ . '/../views/components/detalle_reserva.php'; // Carga el componente de la reserva
$contenido = ob_get_clean(); // Captura el contenido de la vista

// ==============================================
// CARGA DEL LAYOUT PRINCIPAL
// ==============================================

require_once __DIR__ . '/../views/layout/main.php'; // Muestra el contenido en el layout
