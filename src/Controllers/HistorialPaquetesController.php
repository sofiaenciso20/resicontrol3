<?php

// Carga el archivo de configuración de base de datos
require_once __DIR__ . '/../config/Database.php';

// Usa la clase Database del espacio de nombres App\Config
use App\Config\Database;

// Declaración de la clase controladora HistorialPaquetesController
class HistorialPaquetesController {

    // Propiedad privada para la conexión PDO
    private $conn;

    // Constructor: se ejecuta cuando se crea una instancia de esta clase
    public function __construct() {
        $db = new Database();                  // Crea una nueva instancia de la clase Database
        $this->conn = $db->getConnection();    // Obtiene la conexión a la base de datos y la guarda
    }

    // ------------------------------------------
    // Método principal que obtiene los paquetes
    // ------------------------------------------
    public function index() {
        // Obtiene el rol del usuario desde la sesión (role = 1, 2, 3, 4)
        $rol = $_SESSION['user']['role'] ?? null;

        // Obtiene el documento del usuario en sesión (necesario para filtrar si es residente)
        $documento_usuario = $_SESSION['user']['documento'] ?? null;

        // Consulta principal: obtiene información completa de los paquetes
        $query = "SELECT
            p.id_paquete,
            u.nombre AS nombre_residente,
            u.apellido AS apellido_residente,
            v.nombre AS nombre_vigilante,
            v.apellido AS apellido_vigilante,
            p.descripcion,
            p.fech_hor_recep,
            p.fech_hor_entre,
            e.estado
        FROM paquetes p
        JOIN usuarios u ON CAST(p.id_usuarios AS CHAR) = u.documento
        JOIN usuarios v ON CAST(p.id_vigilante AS CHAR) = v.documento
        JOIN estado e ON p.id_estado = e.id_estado";

        // ------------------------------------------
        // Filtrado según el tipo de usuario
        // ------------------------------------------

        if ($rol == 3) {
            // Si el usuario es Residente, solo puede ver sus propios paquetes
            $query .= " WHERE CAST(p.id_usuarios AS CHAR) = :documento";
            
            // Prepara la consulta ordenada por fecha de recepción descendente
            $stmt = $this->conn->prepare($query . " ORDER BY p.fech_hor_recep DESC");

            // Enlaza el parámetro para filtrar por documento
            $stmt->bindParam(':documento', $documento_usuario);
        } else {
            // Si es Admin, SuperAdmin o Vigilante, ve todos los paquetes
            $stmt = $this->conn->prepare($query . " ORDER BY p.fech_hor_recep DESC");
        }

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve todos los resultados como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------
    // Método para obtener el detalle de un paquete específico por su ID
    // ------------------------------------------
    public function obtenerDetallePaquete($id) {
        // Consulta para traer todos los datos del paquete y sus relaciones
        $query = "SELECT
            p.*,
            u.nombre AS nombre_residente,
            u.apellido AS apellido_residente,
            v.nombre AS nombre_vigilante,
            v.apellido AS apellido_vigilante,
            e.estado
        FROM paquetes p
        JOIN usuarios u ON CAST(p.id_usuarios AS CHAR) = u.documento
        JOIN usuarios v ON CAST(p.id_vigilante AS CHAR) = v.documento
        JOIN estado e ON p.id_estado = e.id_estado
        WHERE p.id_paquete = :id";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Enlaza el ID del paquete
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve un solo resultado (un paquete)
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
