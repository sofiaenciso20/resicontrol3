<?php

namespace App\Controllers;

use App\Config\Database;
use PDO;

class DashboardController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Método existente para obtener métricas
    public function getMetrics($userId, $userRole) {
        $metrics = [];

        if (in_array($userRole, [1, 2])) {
            $metrics['total_residentes'] = $this->getTotalResidentes();
            $metrics['visitas_dia'] = $this->getVisitasHoy();
            $metrics['reservas_pendientes'] = $this->getReservasPendientes();
            $metrics['paquetes_pendientes'] = $this->getPaquetesPendientes();
        }
        else if ($userRole == 4) {
            $metrics['visitas_dia'] = $this->getVisitasHoy();
            $metrics['paquetes_pendientes'] = $this->getPaquetesPendientes();
            $metrics['reservas_dia'] = $this->getReservasHoy();
        }
        else if ($userRole == 3) {
            $metrics['mis_visitas'] = $this->getMisVisitas($userId);
            $metrics['mis_paquetes'] = $this->getMisPaquetes($userId);
            $metrics['mis_reservas'] = $this->getMisReservas($userId);
        }

        return $metrics;
    }

    // NUEVOS MÉTODOS PARA OBTENER DATOS FILTRADOS

    // Obtener solo residentes activos
    public function getResidentesActivos() {
        $stmt = $this->conn->prepare("
            SELECT u.*, e.estado as nombre_estado
            FROM usuarios u
            LEFT JOIN estados_usuarios e ON u.id_estado_usuario = e.id_estado_usuario
            WHERE u.id_rol = 3
            AND u.id_estado_usuario = 4
            ORDER BY u.nombre ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener solo visitas del día actual
    public function getVisitasDelDia() {
        $stmt = $this->conn->prepare("
            SELECT v.*, u.nombre as nombre_residente, e.estado as nombre_estado
            FROM visitas v
            LEFT JOIN usuarios u ON v.documento = u.documento
            LEFT JOIN estados_visitas e ON v.id_estado = e.id_estado
            WHERE DATE(v.fecha_ingreso) = CURRENT_DATE
            ORDER BY v.fecha_ingreso DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener solo reservas pendientes
    public function getReservasSoloPendientes() {
        $stmt = $this->conn->prepare("
            SELECT r.*, u.nombre as nombre_usuario, a.nombre as nombre_area, e.estado as nombre_estado
            FROM reservas r
            LEFT JOIN usuarios u ON r.id_usuarios = u.documento
            LEFT JOIN areas_comunes a ON r.id_area_comun = a.id_area_comun
            LEFT JOIN estados_reservas e ON r.id_estado = e.id_estado
            WHERE r.id_estado = 1
            ORDER BY r.fecha ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener solo paquetes sin reclamar
    public function getPaquetesSinReclamar() {
        $stmt = $this->conn->prepare("
            SELECT p.*, u.nombre as nombre_usuario, e.estado as nombre_estado
            FROM paquetes p
            LEFT JOIN usuarios u ON p.id_usuarios = u.documento
            LEFT JOIN estados_paquetes e ON p.id_estado = e.id_estado
            WHERE p.id_estado = 1
            ORDER BY p.fecha_recepcion DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener visitas de un residente específico (solo activas)
    public function getMisVisitasDetalle($userId) {
        $stmt = $this->conn->prepare("
            SELECT v.*, e.estado as nombre_estado
            FROM visitas v
            LEFT JOIN estados_visitas e ON v.id_estado = e.id_estado
            WHERE v.documento = :userId
            AND v.id_estado = 1
            ORDER BY v.fecha_ingreso DESC
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener paquetes de un residente específico (solo activos)
    public function getMisPaquetesDetalle($userId) {
        $stmt = $this->conn->prepare("
            SELECT p.*, e.estado as nombre_estado
            FROM paquetes p
            LEFT JOIN estados_paquetes e ON p.id_estado = e.id_estado
            WHERE p.id_usuarios = :userId
            AND p.id_estado = 1
            ORDER BY p.fecha_recepcion DESC
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener reservas de un residente específico (solo activas)
    public function getMisReservasDetalle($userId) {
        $stmt = $this->conn->prepare("
            SELECT r.*, a.nombre as nombre_area, e.estado as nombre_estado
            FROM reservas r
            LEFT JOIN areas_comunes a ON r.id_area_comun = a.id_area_comun
            LEFT JOIN estados_reservas e ON r.id_estado = e.id_estado
            WHERE r.id_usuarios = :userId
            AND r.id_estado = 1
            ORDER BY r.fecha ASC
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Métodos privados existentes (sin cambios)
    private function getTotalResidentes() {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM usuarios
            WHERE id_rol = 3
            AND id_estado_usuario = 4
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function getVisitasHoy() {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM visitas
            WHERE DATE(fecha_ingreso) = CURRENT_DATE
            AND id_estado = 1
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function getReservasPendientes() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM reservas WHERE id_estado = 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function getPaquetesPendientes() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM paquetes WHERE id_estado = 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function getReservasHoy() {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM reservas
            WHERE DATE(fecha) = CURRENT_DATE
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function getMisVisitas($userId) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM visitas
            WHERE documento = :userId
            AND id_estado = 1
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function getMisPaquetes($userId) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM paquetes
            WHERE id_usuarios = :userId
            AND id_estado = 1
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function getMisReservas($userId) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM reservas
            WHERE id_usuarios = :userId
            AND id_estado = 1
        ");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>
