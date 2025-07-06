<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Carga las dependencias de Composer (autoloader)
require_once __DIR__ . '/../src/Config/Database.php'; // Incluye la clase de conexión a la base de datos
require_once __DIR__ . '/../src/Config/permissions.php'; // Incluye la gestión de permisos

session_start(); // Inicia la sesión para acceder a variables de usuario

// Verificar permisos: solo roles 1 (superadmin), 2 (admin) y 4 (vigilante) pueden entregar paquetes
if (!in_array($_SESSION['user']['role'], [1, 2, 4])) {
    $_SESSION['error'] = 'No tienes permiso para realizar esta acción';
    header('Location: historial_paquetes.php');
    exit;
}

// Verificar que se recibió el ID del paquete por POST
if (!isset($_POST['id_paquete'])) {
    $_SESSION['error'] = 'ID de paquete no proporcionado';
    header('Location: historial_paquetes.php');
    exit;
}

$id_paquete = (int)$_POST['id_paquete']; // Sanitiza el ID recibido

// Conectar a la base de datos usando la clase Database
$db = new \App\Config\Database();
$conn = $db->getConnection();

try {
    // Prepara la consulta para actualizar el estado del paquete a "entregado" (id_estado = 2)
    // Solo actualiza si el paquete está pendiente (id_estado = 1)
    $stmt = $conn->prepare("
        UPDATE paquetes
        SET id_estado = 2,
            fech_hor_entre = NOW()
        WHERE id_paquete = ?
        AND id_estado = 1
    ");
   
    // Ejecuta la consulta con el ID del paquete
    if ($stmt->execute([$id_paquete])) {
        // Si se actualizó alguna fila, el paquete fue entregado exitosamente
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'Paquete marcado como entregado exitosamente';
        } else {
            // Si no se actualizó ninguna fila, el paquete ya fue entregado o no existe
            $_SESSION['error'] = 'El paquete ya fue entregado o no existe';
        }
    } else {
        // Si la ejecución falla, muestra un error genérico
        $_SESSION['error'] = 'Error al actualizar el estado del paquete';
    }
} catch (PDOException $e) {
    // Si ocurre un error de base de datos, guarda un mensaje de error en la sesión
    $_SESSION['error'] = 'Error en la base de datos';
}

// Redirige de vuelta a la página anterior o a historial_paquetes.php si no hay referer
$referer = $_SERVER['HTTP_REFERER'] ?? 'historial_paquetes.php';
header("Location: $referer");
exit;
