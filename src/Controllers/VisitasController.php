<?php
// Ruta: src/Controllers/VisitasController.php

// Requiere la clase Database para conexión con la base de datos
require_once __DIR__ . '/../config/Database.php';
// Usa el namespace correcto para instanciar la base de datos
use App\Config\Database;

// Define la clase principal VisitasController
class VisitasController {
    // Atributo privado que almacenará la conexión a la base de datos
    private $conn;

    // Constructor: se ejecuta automáticamente al crear un objeto de esta clase
    public function __construct() {
        $db = new Database();                 // Crea una instancia de la clase Database
        $this->conn = $db->getConnection();   // Guarda la conexión PDO en $this->conn
    }

    /**
     * Método principal que obtiene todas las visitas registradas
     * Si el usuario es residente, solo ve sus propias visitas.
     * Admin, SuperAdmin y Vigilante pueden ver todas.
     */
    public function index() {
        // Obtiene el rol y documento del usuario actual desde la sesión
        $rol = $_SESSION['user']['role'] ?? null;
        $documento_usuario = $_SESSION['user']['documento'] ?? null;

        // Consulta general: obtiene todas las visitas con datos del residente y del motivo
        $query = "SELECT v.*,
                 v.nombre as visitante_nombre,
                 v.apellido as visitante_apellido,
                 u.nombre as residente_nombre,
                 u.apellido as residente_apellido,
                 u.direccion_casa,
                 mv.motivo_visita
                 FROM visitas v
                 INNER JOIN usuarios u ON CAST(v.id_usuarios AS CHAR) = u.documento
                 INNER JOIN motivo_visita mv ON v.id_mot_visi = mv.id_mot_visi
                 ORDER BY v.fecha_ingreso DESC, v.hora_ingreso DESC";

        // Si el usuario es residente (rol 3), solo puede ver sus propias visitas
        if ($rol == 3) {
            // Modifica la consulta para filtrar solo las visitas del residente actual
            $query = "SELECT v.*,
                    v.nombre as visitante_nombre,
                    v.apellido as visitante_apellido,
                    u.nombre as residente_nombre,
                    u.apellido as residente_apellido,
                    u.direccion_casa,
                    mv.motivo_visita
                    FROM visitas v
                    INNER JOIN usuarios u ON CAST(v.id_usuarios AS CHAR) = u.documento
                    INNER JOIN motivo_visita mv ON v.id_mot_visi = mv.id_mot_visi
                    WHERE CAST(v.id_usuarios AS CHAR) = :documento
                    ORDER BY v.fecha_ingreso DESC, v.hora_ingreso DESC";

            // Prepara la consulta y pasa el documento del residente como parámetro
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':documento', $documento_usuario);
        } else {
            // Si no es residente (admin, superadmin o vigilante), puede ver todas las visitas
            $stmt = $this->conn->prepare($query);
        }

        // Ejecuta la consulta
        $stmt->execute();

        // Devuelve todos los resultados como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el detalle completo de una visita específica.
     * @param int $id ID de la visita que se desea consultar
     * @return array|null Datos de la visita o null si no existe
     */
    public function obtenerDetalleVisita($id) {
        // Consulta para obtener todos los datos relevantes de una visita
        $query = "SELECT v.id_visita, v.nombre, v.apellido, v.documento,
                         u.nombre AS residente_nombre, u.apellido AS residente_apellido, u.direccion_casa,
                         mv.motivo_visita, v.fecha_ingreso, v.hora_ingreso
                  FROM visitas v
                  INNER JOIN usuarios u ON CAST(v.id_usuarios AS CHAR) = u.documento
                  INNER JOIN motivo_visita mv ON v.id_mot_visi = mv.id_mot_visi
                  WHERE v.id_visita = :id";

        // Prepara y ejecuta la consulta con el ID
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Retorna el primer resultado como un array asociativo
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos de una visita específica
     * @param int $id ID de la visita que se desea actualizar
     * @param array $datos Datos nuevos que se actualizarán en la base de datos
     */
    public function actualizarVisita($id, $datos) {
        // Consulta SQL para actualizar campos específicos de la visita
        $query = "UPDATE visitas
                  SET nombre = :nombre, apellido = :apellido, documento = :documento,
                      id_mot_visi = :id_mot_visi, fecha_ingreso = :fecha_ingreso, hora_ingreso = :hora_ingreso
                  WHERE id_visita = :id";

        // Prepara la consulta
        $stmt = $this->conn->prepare($query);

        // Enlaza los valores del array $datos a los parámetros SQL
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':apellido', $datos['apellido']);
        $stmt->bindParam(':documento', $datos['documento']);
        $stmt->bindParam(':id_mot_visi', $datos['id_mot_visi']);
        $stmt->bindParam(':fecha_ingreso', $datos['fecha_ingreso']);
        $stmt->bindParam(':hora_ingreso', $datos['hora_ingreso']);
        $stmt->bindParam(':id', $id);

        // Ejecuta la actualización
        $stmt->execute();
    }
}
