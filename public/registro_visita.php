<?php

// Carga automática de clases y dependencias instaladas con Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Controlador encargado de la lógica relacionada con el registro de visitas
require_once __DIR__ . '/../src/Controllers/RegistroVisitasController.php';

// Archivo que gestiona los permisos del sistema
require_once __DIR__ . '/../src/Config/permissions.php';

// Inicia la sesión solo si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica si el usuario tiene el permiso para acceder al registro de visitas
// Si no tiene permiso, se redirige al dashboard y se detiene el script
if (!tienePermiso('registro_visita')) {
    header('Location: dashboard.php');
    exit;
}

// Conexión a la base de datos
require_once __DIR__ . '/../src/config/Database.php';
use App\Config\Database;

// Se instancia la clase Database y se obtiene la conexión PDO
$db = new Database();
$conn = $db->getConnection();

// --------------------
// Cargar datos necesarios para el formulario
// --------------------

// Consulta para obtener los residentes activos (rol 3 = residente, estado 1 = activo)
// Se selecciona el documento y se muestra el nombre completo concatenado
$sql = "SELECT documento, CONCAT(nombre, ' ', apellido) AS nombre_completo 
        FROM usuarios 
        WHERE id_rol = 3 AND id_estado = 1";
$residentes = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los motivos de visita disponibles en la base de datos
$sqlMotivos = "SELECT id_mot_visi, motivo_visita FROM motivo_visita";
$motivos = $conn->query($sqlMotivos)->fetchAll(PDO::FETCH_ASSOC);

// --------------------
// Mensajes del sistema
// --------------------

// Captura el mensaje de sesión, si existe (por ejemplo, un mensaje de éxito o error tras enviar un formulario)
$mensaje = $_SESSION['mensaje_visita'] ?? null;

// Borra el mensaje de la sesión para que no se vuelva a mostrar
unset($_SESSION['mensaje_visita']);

// --------------------
// Lógica del controlador
// --------------------

// Se crea una instancia del controlador de visitas
$controller = new RegistroVisitasController();

// Se obtiene el listado de visitas ya registradas (posiblemente para mostrar en una tabla o historial)
$visitas = $controller->index();

// --------------------
// Preparación de la vista
// --------------------

// Título de la página (puede mostrarse en el header o en el navegador)
$titulo = 'Registro de Visita';

// Nombre de la página actual, útil para resaltar el menú activo
$pagina_actual = 'registro_visita';

// Se inicia la captura del contenido generado por la vista
ob_start();

// Se incluye el archivo de la vista que contiene el formulario de registro de visitas
require_once __DIR__ . '/../views/components/registro_visita.php';

// Se guarda el contenido de la vista capturada
$contenido = ob_get_clean();

// Se carga el layout principal del sistema, al que se le inserta la variable $contenido
require_once __DIR__ . '/../views/layout/main.php';
