<?php

// --------------------------------------
// Cargar dependencias del proyecto
// --------------------------------------

// Requiere el autoload de Composer para poder usar clases externas o propias con namespaces
require_once __DIR__ . '/../vendor/autoload.php';

// --------------------------------------
// Usar la clase Database desde el namespace
// --------------------------------------

// Se importa la clase Database ubicada en App\Config para poder instanciarla
use App\Config\Database;

// --------------------------------------
// Crear conexión a la base de datos
// --------------------------------------

// Se crea una nueva instancia de la clase Database
$db = new Database();

// Se obtiene la conexión (probablemente un objeto PDO)
$conn = $db->getConnection();

// --------------------------------------
// Verificar si la conexión fue exitosa
// --------------------------------------

// Si se logró conectar correctamente, se imprime un mensaje de éxito
if ($conn) {
    echo "¡Conexión exitosa a la base de datos!";
} else {
    // Si no se pudo conectar, se imprime un mensaje de error
    echo "Error de conexión.";
}
