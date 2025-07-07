<?php
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
            
            // Verificar que hay una sesión activa
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Verificar que el usuario está logueado
            if (!isset($_SESSION['user']) || !isset($_SESSION['user']['documento'])) {
                $_SESSION['mensaje_paquete'] = "❌ Error: Usuario no autenticado.";
                header("Location: /registro_paquete.php");
                exit();
            }

            // Crea una instancia de la base de datos
            $db = new Database();
            $conn = $db->getConnection(); // Se obtiene la conexión PDO

            // Se reciben los datos del formulario
            $id_usuarios = $_POST['id_usuarios'];               // Documento del residente
            $id_vigilante = $_SESSION['user']['documento'];    // Documento del vigilante logueado (automático)
            $descripcion = $_POST['descripcion'] ?? null;       // Descripción del paquete (puede ser nula)
            $fech_hor_recep = date('Y-m-d H:i:s');         // Fecha y hora actual del sistema
            $id_estado = 1;                                     // Estado inicial del paquete (1 = recibido, sin entregar)

            try {
                // Verificar que el residente existe y está activo
                $stmtVerificar = $conn->prepare("
                    SELECT documento, CONCAT(nombre, ' ', apellido) as nombre_completo 
                    FROM usuarios 
                    WHERE documento = :documento AND id_rol = 3 AND id_estado_usuario = 4
                ");
                $stmtVerificar->bindParam(':documento', $id_usuarios);
                $stmtVerificar->execute();
                $residente = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

                if (!$residente) {
                    $_SESSION['mensaje_paquete'] = "❌ Error: El residente seleccionado no existe o no está activo.";
                    header("Location: /registro_paquete.php");
                    exit();
                }

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
                    $_SESSION['mensaje_paquete'] = "✅ ¡Paquete registrado exitosamente para " . htmlspecialchars($residente['nombre_completo']) . "!";
                } else {
                    // Si falló la ejecución, se guarda un mensaje de error
                    $_SESSION['mensaje_paquete'] = "❌ Error al registrar el paquete en la base de datos.";
                }

            } catch (Exception $e) {
                // Manejo de errores de base de datos
                $_SESSION['mensaje_paquete'] = "❌ Error del sistema: " . $e->getMessage();
            }

            // Redirige al formulario de registro de paquete
            header("Location: /registro_paquete.php");
            exit(); // Detiene la ejecución del script
        }
    }

    /**
     * Método para obtener estadísticas de paquetes del vigilante actual
     * @return array Estadísticas de paquetes
     */
    public function getEstadisticasVigilante() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['documento'])) {
            return [];
        }

        $db = new Database();
        $conn = $db->getConnection();

        $query = "SELECT 
                    COUNT(*) as total_registrados,
                    SUM(CASE WHEN id_estado = 1 THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN DATE(fech_hor_recep) = CURDATE() THEN 1 ELSE 0 END) as hoy
                  FROM paquetes 
                  WHERE id_vigilante = :vigilante";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':vigilante', $_SESSION['user']['documento']);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
