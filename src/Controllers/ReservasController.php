<?php
// Controlador para manejar todas las operaciones relacionadas con reservas
// src/Controllers/ReservasController.php

// Importa la clase de conexión a la base de datos
require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

// Definición del controlador de reservas
class ReservasController {
    // Conexión a la base de datos
    private $conn;

    /**
     * Constructor de la clase
     * Crea la conexión a la base de datos cuando se instancia el controlador
     */
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Muestra todas las reservas registradas en el sistema.
     * Si el usuario es residente, filtra por su documento.
     * @return array Lista de reservas con datos de zonas, horarios, estado y usuarios relacionados
     */
    public function index() {
        // Obtiene el rol y documento del usuario autenticado
        $rol = $_SESSION['user']['role'] ?? null;
        $documento_usuario = $_SESSION['user']['documento'] ?? null;

        // Consulta SQL para traer datos de todas las reservas y sus relaciones
        $query = "SELECT
            r.id_reservas,
            r.fecha,
            r.fecha_apro,
            r.observaciones,
            zc.nombre_zona,
            h.horario,
            CONCAT(u.nombre, ' ', u.apellido) as nombre_residente,
            u.documento as documento_residente,
            e.estado,
            CONCAT(a.nombre, ' ', a.apellido) as nombre_administrador,
            mz.motivo_zonas as motivo
            FROM reservas r
            INNER JOIN zonas_comunes zc ON r.id_zonas_comu = zc.id_zonas_comu
            INNER JOIN horario h ON r.id_horario = h.id_horario
            INNER JOIN usuarios u ON CAST(r.id_usuarios AS CHAR) = u.documento
            INNER JOIN estado e ON r.id_estado = e.id_estado
            LEFT JOIN usuarios a ON CAST(r.id_administrador AS CHAR) = a.documento
            LEFT JOIN motivo_zonas mz ON r.id_mot_zonas = mz.id_mot_zonas";

        // Si el usuario es residente (rol 3), solo ve sus propias reservas
        if ($rol == 3) {
            $query .= " WHERE CAST(r.id_usuarios AS CHAR) = :documento";
            $stmt = $this->conn->prepare($query . " ORDER BY r.fecha DESC, h.horario ASC");
            $stmt->bindParam(':documento', $documento_usuario);
        } else {
            // Si es administrador, puede ver todas las reservas
            $stmt = $this->conn->prepare($query . " ORDER BY r.fecha DESC, h.horario ASC");
        }

        // Ejecuta la consulta y devuelve los resultados como arreglo asociativo
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el detalle completo de una reserva por su ID
     * @param int $id ID de la reserva
     * @return array|false Detalle completo o false si no se encuentra
     */
    public function obtenerDetalleReserva($id) {
        $query = "SELECT
            r.*,
            zc.nombre_zona,
            h.horario,
            CONCAT(u.nombre, ' ', u.apellido) as nombre_residente,
            u.documento as documento_residente,
            u.telefono as telefono_residente,
            e.estado,
            CONCAT(a.nombre, ' ', a.apellido) as nombre_administrador,
            mz.motivo_zonas as motivo
            FROM reservas r
            INNER JOIN zonas_comunes zc ON r.id_zonas_comu = zc.id_zonas_comu
            INNER JOIN horario h ON r.id_horario = h.id_horario
            INNER JOIN usuarios u ON CAST(r.id_usuarios AS CHAR) = u.documento
            INNER JOIN estado e ON r.id_estado = e.id_estado
            LEFT JOIN usuarios a ON CAST(r.id_administrador AS CHAR) = a.documento
            LEFT JOIN motivo_zonas mz ON r.id_mot_zonas = mz.id_mot_zonas
            WHERE r.id_reservas = :id";

        // Prepara y ejecuta la consulta con el ID de la reserva
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos de una reserva existente
     * @param int $id ID de la reserva
     * @param array $datos Arreglo con los nuevos valores (fecha, horario, observaciones, motivo)
     */
    public function actualizarReserva($id, $datos) {
        $query = "UPDATE reservas
                  SET fecha = :fecha,
                      id_horario = :id_horario,
                      observaciones = :observaciones,
                      id_mot_zonas = :id_mot_zonas
                  WHERE id_reservas = :id";

        // Prepara y ejecuta la actualización
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha', $datos['fecha']);
        $stmt->bindParam(':id_horario', $datos['id_horario']);
        $stmt->bindParam(':observaciones', $datos['observaciones']);
        $stmt->bindParam(':id_mot_zonas', $datos['id_mot_zonas']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Cambia el estado de una reserva a "Aprobada" (estado = 2)
     * @param int $id_reserva ID de la reserva
     * @return bool true si se aprobó correctamente
     */
    public function aprobarReserva($id_reserva) {
        try {
            $this->conn->beginTransaction(); // Inicia transacción

            // Verifica que la reserva existe y está pendiente
            $stmt = $this->conn->prepare("
                SELECT id_estado
                FROM reservas
                WHERE id_reservas = ?
                AND id_estado = 1");
            $stmt->execute([$id_reserva]);

            if (!$stmt->fetch()) {
                throw new Exception("La reserva no existe o ya fue procesada");
            }

            // Actualiza el estado a "Aprobada", asigna admin y fecha de aprobación
            $stmt = $this->conn->prepare("
                UPDATE reservas
                SET id_estado = 2,
                    id_administrador = ?,
                    fecha_apro = CURDATE()
                WHERE id_reservas = ?");
            $admin_documento = $_SESSION['user']['documento'];
            $stmt->execute([$admin_documento, $id_reserva]);

            $this->conn->commit(); // Guarda los cambios
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack(); // Revierte si hay error
            throw $e;
        }
    }

    /**
     * Cambia el estado de una reserva a "Rechazada" (estado = 3) y agrega observaciones
     * @param int $id_reserva ID de la reserva
     * @param string $observaciones Comentario del rechazo
     * @return bool true si se rechazó correctamente
     */
    public function rechazarReserva($id_reserva, $observaciones) {
        try {
            $this->conn->beginTransaction(); // Inicia transacción

            // Verifica que la reserva existe y está pendiente
            $stmt = $this->conn->prepare("
                SELECT id_estado
                FROM reservas
                WHERE id_reservas = ?
                AND id_estado = 1");
            $stmt->execute([$id_reserva]);

            if (!$stmt->fetch()) {
                throw new Exception("La reserva no existe o ya fue procesada");
            }

            // Actualiza el estado a "Rechazada", guarda admin, fecha y observación
            $stmt = $this->conn->prepare("
                UPDATE reservas
                SET id_estado = 3,
                    id_administrador = ?,
                    fecha_apro = CURDATE(),
                    observaciones = ?
                WHERE id_reservas = ?");
            $admin_documento = $_SESSION['user']['documento'];
            $stmt->execute([$admin_documento, $observaciones, $id_reserva]);

            $this->conn->commit(); // Guarda los cambios
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack(); // Revierte si hay error
            throw $e;
        }
    }
}
