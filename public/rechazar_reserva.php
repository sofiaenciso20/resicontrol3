<?php
// Incluimos el controlador necesario para manejar las reservas
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Controllers/ReservasController.php';
require_once __DIR__ . '/../src/Config/permissions.php';

// Iniciamos la sesión para acceder a las variables de sesión
session_start();

// Verificar permisos
if (!tienePermiso('gestion_reservas') || $_SESSION['user']['role'] != 2) {
    header('Location: dashboard.php'); // Si no tiene permiso o no es administrador, redirige al dashboard
    exit;
}

// Verificar que se recibió un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID de reserva inválido"; // Guarda mensaje de error en la sesión
    header('Location: gestion_reservas.php'); // Redirige a la gestión de reservas
    exit;
}

// Obtener observaciones del prompt
$observaciones = $_GET['observaciones'] ?? '';
if (empty($observaciones)) {
    $_SESSION['error'] = "Debe proporcionar un motivo para el rechazo"; // Mensaje si no se proporciona motivo
    header('Location: gestion_reservas.php');
    exit;
}

// Creamos una instancia del controlador de reservas
$controller = new ReservasController();

try {
    // Intentamos rechazar la reserva
    // El método rechazarReserva cambia el id_estado a 3 (rechazada) y guarda las observaciones
    if ($controller->rechazarReserva($_GET['id'], $observaciones)) {
        // Si la actualización fue exitosa, guardamos mensaje de éxito
        $_SESSION['success'] = 'Reserva rechazada exitosamente';
    } else {
        // Si hubo un error en la actualización, guardamos mensaje de error
        $_SESSION['error'] = 'No se pudo rechazar la reserva';
    }
} catch (Exception $e) {
    // Si ocurre una excepción (error de BD u otro), guardamos mensaje de error
    $_SESSION['error'] = $e->getMessage();
}

// Redirigimos al usuario a la página de gestión de reservas
header('Location: gestion_reservas.php');
exit;

