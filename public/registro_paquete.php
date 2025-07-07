<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Carga el autoloader de Composer para dependencias externas
require_once __DIR__ . '/../src/Controllers/PaqueteController.php'; // Incluye el controlador de paquetes
require_once __DIR__ . '/../src/Config/permissions.php'; // Incluye las funciones de gestión de permisos

// Inicia la sesión si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica si el usuario tiene permiso para registrar paquetes
if (!tienePermiso('registro_paquete')) {
    header('Location: dashboard.php'); // Si no tiene permiso, redirige al dashboard
    exit;
}

// Conexión a la base de datos
require_once __DIR__ . '/../src/config/Database.php'; // Incluye la clase de conexión a la base de datos
use App\Config\Database;

$db = new Database(); // Crea una instancia de la clase Database
$conn = $db->getConnection(); // Obtiene la conexión PDO a la base de datos

// Cargar residentes activos (rol 3, estado activo)
$sqlResidentes = "SELECT documento, CONCAT(nombre, ' ', apellido) AS nombre_completo, direccion_casa
                  FROM usuarios 
                  WHERE id_rol = 3 AND id_estado_usuario = 4
                  ORDER BY nombre ASC";
$residentes = $conn->query($sqlResidentes)->fetchAll(PDO::FETCH_ASSOC); // Obtiene los residentes como array asociativo

// Obtener información del vigilante logueado
$vigilante_logueado = $_SESSION['user'] ?? null;

// Mostrar mensaje si hay (por ejemplo, éxito o error en el registro)
$mensaje = $_SESSION['mensaje_paquete'] ?? null; // Recupera el mensaje de la sesión si existe
unset($_SESSION['mensaje_paquete']); // Elimina el mensaje de la sesión para que no se muestre de nuevo

// Instancia el controlador de paquetes y ejecuta el método registrar() para procesar el formulario si se envió
$controller = new PaqueteController();
$controller->registrar();

// Variables para el layout (título de la página y menú activo)
$titulo = 'Registro de Paquete';
$pagina_actual = 'registro';

// Inicia el buffer de salida para capturar el contenido de la vista
ob_start();
require_once __DIR__ . '/../views/components/registro_paquete.php'; // Incluye el componente de la vista de registro de paquete
$contenido = ob_get_clean(); // Guarda el contenido generado en la variable $contenido

require_once __DIR__ . '/../views/layout/main.php'; // Carga el layout principal de la aplicación, que usará $contenido para mostrar la página completa
