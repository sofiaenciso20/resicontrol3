<?php
// Incluye la clase Database para poder conectarnos a la base de datos
require_once __DIR__ . '/../config/Database.php';

// Importa la clase Database desde el namespace correspondiente
use App\Config\Database;

// Define la clase del controlador que se encargará de validar visitas
class ValidarVisitasController {
    
    // Variable privada que almacenará la conexión a la base de datos
    private $conn;

    // Constructor: se ejecuta automáticamente al crear un objeto de esta clase
    public function __construct() {
        // Crea una instancia de la base de datos
        $db = new Database();
        // Obtiene la conexión y la guarda en la propiedad $conn
        $this->conn = $db->getConnection();
    }

    /**
     * Método para validar el código de verificación de una visita
     * Este método se ejecuta cuando el usuario envía el formulario con el código
     */
    public function validar() {
        // Verifica si la petición se hizo mediante POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Captura el código enviado por el usuario desde el formulario
            $codigo = trim($_POST['codigo']);

            // Obtiene la fecha y hora actual del sistema en formato compatible con la base de datos
            $now = date('Y-m-d H:i:s');

            // Consulta para verificar si el código es válido, no ha expirado y aún no ha sido usado (estado pendiente)
            $stmt = $this->conn->prepare("
                SELECT id_visita, nombre, apellido
                FROM visitas
                WHERE codigo = :codigo
                AND codigo_expira > :now
                AND id_estado = 1
            ");

            // Asocia los parámetros a la consulta
            $stmt->bindParam(':codigo', $codigo);
            $stmt->bindParam(':now', $now);

            // Ejecuta la consulta
            $stmt->execute();

            // Obtiene el resultado de la consulta
            $visita = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si se encontró una visita válida
            if ($visita) {
                // Actualiza el estado de la visita a "verificada" (id_estado = 2)
                // y limpia el código y la fecha de expiración para que no pueda volver a usarse
                $stmtUpdate = $this->conn->prepare("
                    UPDATE visitas
                    SET id_estado = 2,
                        codigo = NULL,
                        codigo_expira = NULL
                    WHERE id_visita = :id_visita
                ");

                // Enlaza el ID de la visita encontrada
                $stmtUpdate->bindParam(':id_visita', $visita['id_visita']);

                // Ejecuta la actualización
                if ($stmtUpdate->execute()) {
                    // Guarda un mensaje de éxito en la sesión para mostrar en el historial
                    $_SESSION['mensaje_visita'] = "✅ ¡Visita de " . htmlspecialchars($visita['nombre'] . ' ' . $visita['apellido']) . " verificada exitosamente!";
                    
                    // Redirige al historial de visitas con filtro de visitas del día
                    header("Location: /historial_visitas.php?filter=hoy&validated=1");
                    exit();
                } else {
                    // Si falla la actualización, guarda un mensaje de error
                    $_SESSION['mensaje_visita'] = "❌ Error al verificar la visita.";
                    
                    // En caso de error, vuelve al formulario de validación
                    header("Location: /validar_visitas.php");
                    exit();
                }
            } else {
                // Si no se encontró la visita (código inválido o expirado)
                $_SESSION['mensaje_visita'] = "❌ Código inválido o expirado. Por favor, verifica e intenta nuevamente.";
                
                // En caso de código inválido, vuelve al formulario de validación
                header("Location: /validar_visitas.php");
                exit();
            }

        } else {
            // Si alguien intenta acceder a este método sin enviar un formulario, no se permite
            $_SESSION['mensaje_visita'] = "Acceso no permitido.";
            header("Location: /validar_visitas.php");
            exit();
        }
    }

    /**
     * Método que devuelve todas las visitas pendientes (aún no verificadas)
     * Esto puede usarse para mostrarlas en una lista o tabla en la interfaz
     */
    public function index() {
        // Consulta para obtener todas las visitas con estado pendiente (id_estado = 1)
        $sql = "SELECT * FROM visitas WHERE id_estado = 1";

        // Prepara y ejecuta la consulta
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        // Devuelve los resultados como un array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Método para obtener estadísticas de validación
     * @return array Estadísticas de códigos validados
     */
    public function getEstadisticasValidacion() {
        $query = "SELECT 
                    COUNT(*) as total_validadas_hoy,
                    SUM(CASE WHEN id_estado = 1 THEN 1 ELSE 0 END) as pendientes_validacion
                  FROM visitas 
                  WHERE DATE(fecha_ingreso) = CURDATE()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
