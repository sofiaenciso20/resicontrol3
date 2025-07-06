<?php
require_once __DIR__ . '/../src/Config/Database.php'; // Incluye la clase de conexión a la base de datos

$db = new \App\Config\Database(); // Crea una instancia de la clase Database
$conn = $db->getConnection();      // Obtiene la conexión PDO a la base de datos

// Obtiene los parámetros enviados por POST: id de la zona y fecha seleccionada
$id_zona = $_POST['zona'] ?? null;
$fecha = $_POST['fecha'] ?? null;

$ocupados = []; // Inicializa el array que contendrá los horarios ocupados

// Si se recibieron ambos parámetros (zona y fecha)
if ($id_zona && $fecha) {
    // Prepara y ejecuta una consulta para obtener los id_horario ya reservados en esa zona y fecha
    $stmt = $conn->prepare("SELECT id_horario FROM reservas WHERE id_zonas_comu = ? AND fecha = ?");
    $stmt->execute([$id_zona, $fecha]);
    $ocupados = $stmt->fetchAll(PDO::FETCH_COLUMN); // Obtiene solo la columna id_horario como array
}

// Devuelve el array de horarios ocupados en formato JSON
echo json_encode($ocupados);