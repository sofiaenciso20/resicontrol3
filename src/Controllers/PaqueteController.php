<?php
// Inicia la sesión para poder guardar mensajes (como notificaciones)
session_start();

// Se incluye el archivo de conexión a la base de datos
require_once __DIR__ . '/../config/Database.php';

// Se importa la clase Database desde el namespace App\Config
use App\Config\Database;

// Se declara la clase controladora de paquetes
class PaqueteController {

    // Método que se encarga de registrar un paquete
    public function registrar() {
        // Verifica que el formulario fue enviado mediante POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Crea una instancia de la base de datos
            $db = new Database();
            $conn = $db->getConnection(); // Se obtiene la conexión PDO

            // Se reciben los datos del formulario
            $id_usuarios = $_POST['id_usuarios'];               // Documento del residente
            $id_vigilante = $_POST['id_vigilante'];             // Documento del vigilante que recibe el paquete
            $descripcion = $_POST['descripcion'] ?? null;       // Descripción del paquete (puede ser nula)
            $fech_hor_recep = $_POST['fech_hor_recep'];         // Fecha y hora de recepción del paquete
            $id_estado = 1;                                     // Estado inicial del paquete (1 = recibido, sin entregar)

            // Consulta SQL para insertar el nuevo paquete
            $sql = "INSERT INTO paquetes 
                    (id_usuarios, id_vigilante, descripcion, fech_hor_recep, fech_hor_entre, id_estado)
                    VALUES 
                    (:id_usuarios, :id_vigilante, :descripcion, :fech_hor_recep, NULL, :id_estado)";
            // NOTA: El campo `fech_hor_entre` se deja en NULL porque aún no ha sido entregado

            // Preparamos la consulta para evitar inyecciones SQL
            $stmt = $conn->prepare($sql);

            // Enlazamos los valores a los marcadores
            $stmt->bindParam(':id_usuarios', $id_usuarios);
            $stmt->bindParam(':id_vigilante', $id_vigilante);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':fech_hor_recep', $fech_hor_recep);
            $stmt->bindParam(':id_estado', $id_estado);

            // Ejecutamos la consulta
            if ($stmt->execute()) {
                // Si la ejecución fue exitosa, se guarda un mensaje de éxito en la sesión
                $_SESSION['mensaje_paquete'] = "✅ ¡Paquete registrado exitosamente!";
            } else {
                // Si falló la ejecución, se guarda un mensaje de error
                $_SESSION['mensaje_paquete'] = "❌ Error al registrar el paquete.";
            }

            // Redirige al formulario de registro de paquete
            header("Location: /registro_paquete.php");
            exit(); // Detiene la ejecución del script
        }
    }
}

// Se crea una instancia del controlador y se ejecuta el método registrar()
$controller = new PaqueteController();
$controller->registrar();
