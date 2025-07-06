<?php
// Archivo: src/Controllers/ResidentesController.php
// Controlador encargado de gestionar acciones relacionadas con los residentes (habitantes del sistema)

// Se incluye la clase de conexión a la base de datos
require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

// Se define la clase del controlador
class ResidentesController {
    // Variable privada que almacenará la conexión a la base de datos
    private $conn;

    // Constructor de la clase: inicializa la conexión cuando se crea una instancia
    public function __construct() {
        $db = new Database();               // Se crea una instancia de la clase Database
        $this->conn = $db->getConnection(); // Se obtiene y guarda la conexión
    }

    /**
     * Obtiene la lista de residentes (u otros usuarios dependiendo del rol)
     * @return array Lista de usuarios que cumplen la condición del rol
     */
    public function index() {
        // Verifica si existe un rol en la sesión actual del usuario
        $rol = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;

        // Si es vigilante (rol 4), solo puede ver los residentes (rol 3)
        if ($rol == 4) {
            $query = "SELECT documento, CONCAT(nombre, ' ', apellido) AS nombre, telefono, direccion_casa, id_estado_usuario
                      FROM usuarios
                      WHERE id_rol = 3";
        }
        // Si es administrador (rol 2), puede ver todos excepto al superadmin (rol 1)
        elseif ($rol == 2) {
            $query = "SELECT documento, CONCAT(nombre, ' ', apellido) AS nombre, telefono, direccion_casa, id_estado_usuario
                      FROM usuarios
                      WHERE id_rol != 1";
        }
        // Si es superadmin u otro rol, puede ver todos los usuarios
        else {
            $query = "SELECT documento, CONCAT(nombre, ' ', apellido) AS nombre, telefono, direccion_casa, id_estado_usuario
                      FROM usuarios";
        }

        // Prepara y ejecuta la consulta
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        // Devuelve todos los resultados como array asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los detalles de un residente específico por su documento
     * @param string $id Documento del residente
     * @return array Datos del residente (nombre, correo, dirección, etc.)
     */
    public function obtenerDetalleResidente($id) {
        $query = "SELECT documento, nombre, apellido, telefono, correo, direccion_casa, cantidad_personas, tiene_animales, cantidad_animales, direccion_residencia
                  FROM usuarios
                  WHERE documento = :id ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR); // Asocia el parámetro ID
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve solo un resultado
    }

    /**
     * Actualiza la información de un residente.
     * Solo actualiza si el rol del usuario es residente (id_rol = 3)
     * @param string $id Documento del residente
     * @param array $datos Información nueva que se quiere actualizar
     */
    public function actualizarResidente($id, $datos) {
        // SQL para actualizar los campos del usuario con rol = 3
        $query = "UPDATE usuarios SET 
                    nombre = :nombre, 
                    apellido = :apellido, 
                    telefono = :telefono, 
                    correo = :correo, 
                    direccion_casa = :direccion_casa, 
                    cantidad_personas = :cantidad_personas, 
                    tiene_animales = :tiene_animales, 
                    cantidad_animales = :cantidad_animales, 
                    direccion_residencia = :direccion_residencia 
                  WHERE documento = :id AND id_rol = 3";

        $stmt = $this->conn->prepare($query);
        
        // Se vinculan los valores al SQL para evitar inyecciones y errores
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':apellido', $datos['apellido']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':correo', $datos['correo']);
        $stmt->bindParam(':direccion_casa', $datos['direccion_casa']);
        $stmt->bindParam(':cantidad_personas', $datos['cantidad_personas']);
        $stmt->bindParam(':tiene_animales', $datos['tiene_animales']);
        $stmt->bindParam(':cantidad_animales', $datos['cantidad_animales']);
        $stmt->bindParam(':direccion_residencia', $datos['direccion_residencia']);
        $stmt->bindParam(':id', $id);

        // Ejecuta la actualización
        $stmt->execute();
    }
}
