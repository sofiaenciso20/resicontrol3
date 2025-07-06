<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Carga el autoloader de Composer para dependencias externas
require_once __DIR__ . '/../src/Controllers/RegistroReservaController.php'; // Incluye el controlador de reservas
require_once __DIR__ . '/../src/Config/permissions.php'; // Incluye las funciones de gestión de permisos
require_once __DIR__ . '/../src/Config/Database.php'; // Incluye la clase de conexión a la base de datos
 
session_start(); // Inicia la sesión para acceder a variables de usuario

// Verifica si el usuario tiene permiso para registrar reservas
if (!tienePermiso('registro_reserva')) {
    header('Location: dashboard.php'); // Si no tiene permiso, redirige al dashboard
    exit;
}

// Instancia el controlador de reservas y obtiene datos iniciales (por ejemplo, para mostrar reservas existentes)
$controller = new RegistroReservaController();
$visitas = $controller->index(); // (El nombre $visitas puede ser confuso, pero depende del controlador)

$titulo = 'Registro de Reservas'; // Título de la página
$pagina_actual = 'registro_reservas'; // Identificador de la página actual para el menú

// Conexión a la base de datos
$db = new \App\Config\Database();
$conn = $db->getConnection();
 
// Obtener todas las zonas comunes disponibles
$zonas = $conn->query("SELECT id_zonas_comu, nombre_zona FROM zonas_comunes")->fetchAll(PDO::FETCH_ASSOC);
 
// Obtener todos los horarios posibles para reservas
$horariosPosibles = $conn->query("SELECT id_horario, horario FROM horario")->fetchAll(PDO::FETCH_ASSOC);
 
// Inicializar el array de horarios ocupados
$horariosOcupados = [];
if (isset($_POST['zona'], $_POST['fecha'])) {
    // Si se seleccionó zona y fecha, consulta los horarios ya reservados para esa combinación
    $id_zona = $_POST['zona'];
    $fecha = $_POST['fecha'];
    $stmt = $conn->prepare("SELECT id_horario FROM reservas WHERE id_zonas_comu = ? AND fecha = ?");
    $stmt->execute([$id_zona, $fecha]);
    $horariosOcupados = $stmt->fetchAll(PDO::FETCH_COLUMN); // Obtiene solo la columna id_horario como array
}

// Procesamiento del formulario de registro de reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_zona = $_POST['zona'];
    $fecha = $_POST['fecha'];
    $id_horario = $_POST['horario'];
    $residente = trim($_POST['residente']); // (No se usa directamente, pero podría usarse para mostrar en la vista)
    // Obtiene el documento del usuario en sesión (debería ser el residente que hace la reserva)
    $id_usuarios = isset($_SESSION['user']['documento']) ? $_SESSION['user']['documento'] : null;
    if (!$id_usuarios) {
        die('Error: No se encontró el usuario en sesión.');
    }
    $id_estado = 1; // Estado "Pendiente"
    $observaciones = '';
    $id_mot_zonas = 1; // Motivo de la reserva (puedes ajustar según tu formulario)
 
    // Inserta la nueva reserva en la base de datos
    $stmt = $conn->prepare("INSERT INTO reservas (id_zonas_comu, id_usuarios, fecha, id_horario, id_estado, observaciones, id_mot_zonas) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_zona, $id_usuarios, $fecha, $id_horario, $id_estado, $observaciones, $id_mot_zonas]);
    // Redirige a la gestión de reservas con mensaje de éxito
    header('Location: gestion_reservas.php?reserva=ok');
    exit;
}
 
// Renderizado de la vista
ob_start();
require_once __DIR__ . '/../views/components/registro_reserva.php'; // Incluye el componente de la vista de registro de reserva
$contenido = ob_get_clean();

require_once __DIR__ . '/../views/layout/main.php'; // Carga el layout principal de la aplicación, que usará $contenido para mostrar la página completa