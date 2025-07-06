<?php
// ===========================================
// CONFIRMAR VISITA - CAMBIO DE ESTADO
// ===========================================

// Incluye el archivo de conexión a la base de datos
require_once __DIR__ . '/../src/Config/Database.php';

// Inicia la sesión para manejar variables de sesión si se necesitan
session_start();

// ===========================================
// CONEXIÓN A LA BASE DE DATOS
// ===========================================

// Crea una instancia de la clase Database (usando el namespace completo)
$db = new \App\Config\Database();

// Llama al método que devuelve la conexión activa a la base de datos
$conn = $db->getConnection();

// ===========================================
// LÓGICA PARA CONFIRMAR LA VISITA
// ===========================================

// Verifica que la solicitud sea de tipo POST y que se haya enviado el id_visita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_visita'])) {
    
    // Prepara la consulta SQL para actualizar el estado de la visita
    // Cambia el campo id_estado a 2 (estado confirmado)
    $stmt = $conn->prepare("UPDATE visitas SET id_estado = 2 WHERE id_visita = ?");
    
    // Ejecuta la consulta con el valor recibido desde el formulario
    $stmt->execute([$_POST['id_visita']]);
}

// ===========================================
// REDIRECCIÓN DESPUÉS DE CONFIRMAR
// ===========================================

// Redirige de vuelta al historial de visitas después de ejecutar la acción
header('Location: historial_visitas.php');
exit;
