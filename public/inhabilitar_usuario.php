<?php
require_once __DIR__ . '/../src/Config/Database.php'; // Incluye la clase de conexión a la base de datos
session_start(); // Inicia la sesión para acceder a variables de usuario
 
// Solo admin o superadmin pueden inhabilitar
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], [1,2])) {
    header('Location: gestion_residentes.php'); // Si no tiene permiso, redirige a la gestión de residentes
    exit;
}
 
$id = $_GET['id'] ?? null; // Obtiene el ID del usuario a inhabilitar desde la URL (GET)
if ($id) {
    $db = new \App\Config\Database(); // Crea una instancia de la clase Database
    $conn = $db->getConnection();      // Obtiene la conexión PDO a la base de datos
    // Cambia el estado a Inactivo (id_estado_usuario = 5)
    $stmt = $conn->prepare("UPDATE usuarios SET id_estado_usuario = 5 WHERE documento = ?");
    $stmt->execute([$id]); // Ejecuta la consulta con el ID recibido
}
 
header('Location: gestion_residentes.php'); // Redirige siempre a la gestión de residentes
exit;
