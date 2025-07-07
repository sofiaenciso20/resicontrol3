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
     * @param string $filtro Filtro opcional para aplicar (ej: 'hoy', 'pendientes')
     */
    public function index($filtro = null) {
        // Obtiene el rol y documento del usuario actual desde la sesión
        $rol = $_SESSION['user']['role'] ?? null;
        $documento_usuario = $_SESSION['user']['documento'] ?? null;

        // Consulta base para obtener todas las visitas con datos del residente y del motivo
        $query = "SELECT v.*,
                 v.nombre as visitante_nombre,
                 v.apellido as visitante_apellido,
                 u.nombre as residente_nombre,
                 u.apellido as residente_apellido,
                 u.direccion_casa,
                 mv.motivo_visita
                 FROM visitas v
                 INNER JOIN usuarios u ON CAST(v.id_usuarios AS CHAR) = u.documento
                 INNER JOIN motivo_visita mv ON v.id_mot_visi = mv.id_mot_visi";

        // Array para almacenar las condiciones WHERE
        $condiciones = [];
        $parametros = [];

        // Si el usuario es residente (rol 3), solo puede ver sus propias visitas
        if ($rol == 3) {
            $condiciones[] = "CAST(v.id_usuarios AS CHAR) = :documento";
            $parametros[':documento'] = $documento_usuario;
        }

        // Aplicar filtros específicos
        switch($filtro) {
            case 'hoy':
                $condiciones[] = "DATE(v.fecha_ingreso) = CURDATE()";
                break;
            case 'pendientes':
                $condiciones[] = "v.id_estado = 1";
                break;
            case 'aprobadas':
                $condiciones[] = "v.id_estado = 2";
                break;
            case 'activas':
                // Para residentes: sus visitas pendientes
                if ($rol == 3) {
                    $condiciones[] = "v.id_estado = 1";
                }
                break;
        }

        // Agregar condiciones WHERE si existen
        if (!empty($condiciones)) {
            $query .= " WHERE " . implode(" AND ", $condiciones);
        }

        // Agregar ORDER BY
        $query .= " ORDER BY v.fecha_ingreso DESC, v.hora_ingreso DESC";

        // Preparar y ejecutar la consulta
        $stmt = $this->conn->prepare($query);
        
        // Enlazar parámetros si existen
        foreach ($parametros as $param => $valor) {
            $stmt->bindParam($param, $valor);
        }

        // Ejecutar la consulta
        $stmt->execute();

        // Devolver todos los resultados como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene solo las visitas del día actual
     * @return array Visitas del día
     */
    public function getVisitasDelDia() {
        return $this->index('hoy');
    }

    /**
     * Obtiene solo las visitas pendientes
     * @return array Visitas pendientes
     */
    public function getVisitasPendientes() {
        return $this->index('pendientes');
    }

    /**
     * Obtiene las visitas activas del residente actual
     * @return array Visitas activas del residente
     */
    public function getMisVisitasActivas() {
        return $this->index('activas');
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
