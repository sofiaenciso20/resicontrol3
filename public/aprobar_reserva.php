<?php
// ===========================================
// APROBAR RESERVA - ACCIÓN DEL ADMINISTRADOR
// ===========================================

// Carga de archivos necesarios para el funcionamiento del script

// Cargamos las dependencias (si usas Composer para autoload)
require_once __DIR__ . '/../vendor/autoload.php';

// Cargamos el controlador de reservas que contiene la lógica para aprobar
require_once __DIR__ . '/../src/Controllers/ReservasController.php';

// Cargamos el archivo de permisos que define quién puede hacer qué
require_once __DIR__ . '/../src/Config/permissions.php';

// Iniciamos la sesión para poder acceder a variables como el usuario y sus permisos
session_start();

// ===========================================
// VERIFICACIÓN DE PERMISOS
// ===========================================

// Solo el Administrador (rol 2) con permiso para "gestionar reservas" puede ejecutar esta acción
if (!tienePermiso('gestion_reservas') || $_SESSION['user']['role'] != 2) {
    // Si no tiene permisos o no es administrador, se redirige al dashboard
    header('Location: dashboard.php');
    exit;
}

// ===========================================
// VALIDACIÓN DEL ID DE LA RESERVA
// ===========================================

// Verificamos que se haya recibido un parámetro "id" por GET y que sea numérico
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Si no se recibe un ID válido, se guarda un mensaje de error
    $_SESSION['error'] = "ID de reserva inválido";

    // Redirigimos de vuelta a la página de gestión de reservas
    header('Location: gestion_reservas.php');
    exit;
}

// Obtenemos el ID de la reserva desde el parámetro GET
$id_reserva = $_GET['id'];

// ===========================================
// ACCIÓN: APROBAR LA RESERVA
// ===========================================

// Creamos una instancia del controlador de reservas
$controller = new ReservasController();

try {
    // Intentamos aprobar la reserva usando el método del controlador
    // Este método actualiza el campo "id_estado" de la reserva a 2 (aprobada)
    if ($controller->aprobarReserva($id_reserva)) {
        // Si todo sale bien, se guarda un mensaje de éxito
        $_SESSION['success'] = 'Reserva aprobada exitosamente';
    } else {
        // Si algo falla (por ejemplo, no se encuentra la reserva), se guarda un error
        $_SESSION['error'] = 'No se pudo aprobar la reserva';
    }
} catch (Exception $e) {
    // Si ocurre una excepción, se captura el mensaje del error y se guarda
    $_SESSION['error'] = $e->getMessage();
}

// ===========================================
// REDIRECCIÓN FINAL
// ===========================================

// Redirigimos nuevamente a la gestión de reservas, ya sea con éxito o error
header('Location: gestion_reservas.php');
exit;
