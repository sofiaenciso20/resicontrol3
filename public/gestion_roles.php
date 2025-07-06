<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Carga el autoloader de Composer para dependencias externas
require_once __DIR__ . '/../src/Config/permissions.php'; // Incluye las funciones de gestión de permisos

session_start(); // Inicia la sesión para acceder a variables de usuario

// Validar permiso antes de mostrar la vista
if (!tienePermiso('gestion_roles')) {
    header('Location: dashboard.php'); // Si no tiene permiso, redirige al dashboard
    exit;
}

// Procesamiento del formulario POST (cambio de rol)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['documento'], $_POST['id_rol'])) {
    // Obtiene los datos enviados: documento del usuario y nuevo rol
    $documento = $_POST['documento'];
    $id_rol = $_POST['id_rol'];

    // Se conecta a la base de datos usando la clase personalizada Database
    require_once __DIR__ . '/../src/Config/database.php';
    $db = new \App\Config\Database();
    $conn = $db->getConnection();

    // Ejecuta la consulta SQL para actualizar el rol del usuario correspondiente
    // Actualiza la columna id_rol en la tabla usuarios solo en el registro donde el documento coincida.
    $stmt = $conn->prepare("UPDATE usuarios SET id_rol = ? WHERE documento = ?");
    $stmt->execute([$id_rol, $documento]);

    // Guarda un mensaje de éxito en la sesión para mostrarlo después en la vista
    $_SESSION['mensaje_exito'] = 'Rol actualizado correctamente.';

    // Redirige para evitar el doble envío del formulario y recargar la tabla actualizada
    header('Location: gestion_roles.php');
    exit;
}

// Consulta para mostrar los usuarios y sus roles
require_once __DIR__ . '/../src/Config/database.php'; // Incluye la configuración y clase de conexión a la base de datos
$db = new \App\Config\Database();
$conn = $db->getConnection();

// Consulta la información de todos los usuarios y su rol actual, uniendo usuarios con roles
$query = "SELECT u.documento, u.nombre, u.apellido, u.correo, r.rol, u.id_rol
          FROM usuarios u
          JOIN roles r ON u.id_rol = r.id_rol";
$stmt = $conn->prepare($query);
$stmt->execute();
// Obtiene todos los resultados que devolvió la consulta.
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta todos los roles para mostrar en el <select>
$stmtRoles = $conn->query("SELECT id_rol, rol FROM roles");
$roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

// Mostrar mensaje de éxito si existe
$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}

// Variables de control para el layout
$titulo = 'Gestión de Roles';
$pagina_actual = 'gestion_roles';

// Cargar contenido dentro del layout
ob_start();
require_once __DIR__ . '/../views/components/gestion_roles.php'; // Incluye la vista que muestra la gestión de roles
$contenido = ob_get_clean();

require_once __DIR__ . '/../views/layout/main.php'; // Incluye el layout principal de la aplicación
