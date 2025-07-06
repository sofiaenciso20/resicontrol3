<?php

// Inicia la sesión (necesaria si se quiere usar datos del usuario logueado o mensajes de sesión)
session_start();

// Se carga la configuración y conexión a la base de datos
require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

// --------------------------------------
// Validación del método de envío
// --------------------------------------

// Verifica que la solicitud sea de tipo POST (viene de un formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Captura los datos enviados por el formulario
    $documento = $_POST['documento'];     // Documento del usuario al que se le cambiará el rol
    $nuevo_rol = $_POST['id_rol'];        // Nuevo rol seleccionado (2, 3 o 4)

    // --------------------------------------
    // Validación del nuevo rol
    // --------------------------------------

    // Solo permite cambiar a roles válidos (2 = Admin, 3 = Residente, 4 = Vigilante)
    if (in_array($nuevo_rol, ['2', '3', '4'])) {

        // Crear conexión a la base de datos
        $db = new Database();
        $conn = $db->getConnection();

        // Preparar la consulta SQL para actualizar el rol del usuario
        $sql = "UPDATE usuarios SET id_rol = :id_rol WHERE documento = :documento";
        $stmt = $conn->prepare($sql);

        // Enlazar los parámetros a la consulta
        $stmt->bindParam(':id_rol', $nuevo_rol);
        $stmt->bindParam(':documento', $documento);

        // Ejecutar la actualización en la base de datos
        $stmt->execute();
    }
}

// --------------------------------------
// Redirección después del proceso
// --------------------------------------

// Redirige nuevamente a la página principal de gestión de roles
header("Location: ../gestion_roles.php");
exit;
