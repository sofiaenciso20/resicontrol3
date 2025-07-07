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
     * @param string $filtro Filtro opcional para aplicar (ej: 'pendientes', 'aprobadas', 'hoy')
     * @return array Lista de reservas con datos de zonas, horarios, estado y usuarios relacionados
     */
    public function index($filtro = null) {
        // Obtiene el rol y documento del usuario autenticado
        $rol = $_SESSION['user']['role'] ?? null;
        $documento_usuario = $_SESSION['user']['documento'] ?? null;

        // Consulta SQL base para traer datos de todas las reservas y sus relaciones
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
            r.id_estado,
            CONCAT(a.nombre, ' ', a.apellido) as nombre_administrador,
            mz.motivo_zonas as motivo
            FROM reservas r
            INNER JOIN zonas_comunes zc ON r.id_zonas_comu = zc.id_zonas_comu
            INNER JOIN horario h ON r.id_horario = h.id_horario
            INNER JOIN usuarios u ON CAST(r.id_usuarios AS CHAR) = u.documento
            INNER JOIN estado e ON r.id_estado = e.id_estado
            LEFT JOIN usuarios a ON CAST(r.id_administrador AS CHAR) = a.documento
            LEFT JOIN motivo_zonas mz ON r.id_mot_zonas = mz.id_mot_zonas";

        // Array para almacenar las condiciones WHERE
        $condiciones = [];
        $parametros = [];

        // Si el usuario es residente (rol 3), solo ve sus propias reservas
        if ($rol == 3) {
            $condiciones[] = "CAST(r.id_usuarios AS CHAR) = :documento";
            $parametros[':documento'] = $documento_usuario;
        }

        // Aplicar filtros específicos
        switch($filtro) {
            case 'pendientes':
                $condiciones[] = "r.id_estado = 1";
                break;
            case 'aprobadas':
                $condiciones[] = "r.id_estado = 2";
                break;
            case 'rechazadas':
                $condiciones[] = "r.id_estado = 3";
                break;
            case 'hoy':
                $condiciones[] = "DATE(r.fecha) = CURDATE()";
                break;
            case 'activas':
                // Para residentes: sus reservas pendientes
                if ($rol == 3) {
                    $condiciones[] = "r.id_estado = 1";
                }
                break;
            case 'mes_actual':
                $condiciones[] = "MONTH(r.fecha) = MONTH(CURDATE()) AND YEAR(r.fecha) = YEAR(CURDATE())";
                break;
        }

        // Agregar condiciones WHERE si existen
        if (!empty($condiciones)) {
            $query .= " WHERE " . implode(" AND ", $condiciones);
        }

        // Agregar ORDER BY
        $query .= " ORDER BY r.fecha DESC, h.horario ASC";

        // Preparar y ejecutar la consulta
        $stmt = $this->conn->prepare($query);
        
        // Enlazar parámetros si existen
        foreach ($parametros as $param => $valor) {
            $stmt->bindParam($param, $valor);
        }

        // Ejecutar la consulta
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene solo las reservas pendientes
     * @return array Reservas pendientes
     */
    public function getReservasPendientes() {
        return $this->index('pendientes');
    }

    /**
     * Obtiene solo las reservas aprobadas
     * @return array Reservas aprobadas
     */
    public function getReservasAprobadas() {
        return $this->index('aprobadas');
    }

    /**
     * Obtiene solo las reservas del día actual
     * @return array Reservas del día
     */
    public function getReservasDelDia() {
        return $this->index('hoy');
    }

    /**
     * Obtiene las reservas activas del residente actual
     * @return array Reservas activas del residente
     */
    public function getMisReservasActivas() {
        return $this->index('activas');
    }

    /**
     * Obtiene estadísticas de reservas
     * @return array Estadísticas de reservas
     */
    public function getEstadisticasReservas() {
        $rol = $_SESSION['user']['role'] ?? null;
        $documento_usuario = $_SESSION['user']['documento'] ?? null;

        $query = "SELECT 
                    COUNT(*) as total_reservas,
                    SUM(CASE WHEN id_estado = 1 THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN id_estado = 2 THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN id_estado = 3 THEN 1 ELSE 0 END) as rechazadas,
                    SUM(CASE WHEN DATE(fecha) = CURDATE() THEN 1 ELSE 0 END) as hoy
                  FROM reservas";

        // Si es residente, solo sus estadísticas
        if ($rol == 3) {
            $query .= " WHERE CAST(id_usuarios AS CHAR) = :documento";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($rol == 3) {
            $stmt->bindParam(':documento', $documento_usuario);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
            u.direccion_casa as direccion_casa_residente,
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
