<?php

// Definición del namespace (ubicación del controlador dentro de la estructura del proyecto)
namespace App\Controllers;

// Importación de clases necesarias
use App\Config\Database;
use PDO;

// Declaración de la clase DashboardController
class DashboardController {
    private $db;    // Instancia de la clase Database
    private $conn;  // Conexión PDO a la base de datos

    // Constructor: se ejecuta automáticamente al instanciar la clase
    public function __construct() {
        $this->db = new Database();                 // Se crea la instancia de conexión
        $this->conn = $this->db->getConnection();   // Se obtiene la conexión PDO
    }

    // Método público para obtener métricas según el rol del usuario
    public function getMetrics($userId, $userRole) {
        $metrics = []; // Arreglo donde se guardarán las métricas personalizadas

        // Métricas para Administrador (rol 2) y Super Admin (rol 1)
        if (in_array($userRole, [1, 2])) {
            $metrics['total_residentes'] = $this->getTotalResidentes();
            $metrics['visitas_dia'] = $this->getVisitasHoy();
            $metrics['reservas_pendientes'] = $this->getReservasPendientes();
            $metrics['paquetes_pendientes'] = $this->getPaquetesPendientes();
        }

        // Métricas para Vigilante (rol 4)
        else if ($userRole == 4) {
            $metrics['visitas_dia'] = $this->getVisitasHoy();
            $metrics['paquetes_pendientes'] = $this->getPaquetesPendientes();
            $metrics['reservas_dia'] = $this->getReservasHoy();
        }

        // Métricas para Residente (rol 3)
        else if ($userRole == 3) {
            $metrics['mis_visitas'] = $this->getMisVisitas($userId);
            $metrics['mis_paquetes'] = $this->getMisPaquetes($userId);
            $metrics['mis_reservas'] = $this->getMisReservas($userId);
        }

        // Retorna el arreglo con todas las métricas correspondientes
        return $metrics;
    }

    // -------------------------
    // MÉTODOS PRIVADOS
    // -------------------------

    // Cuenta los residentes activos (rol 3 y estado 4)
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

    // Cuenta las visitas que ingresaron hoy (con estado 1)
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

    // Cuenta todas las reservas pendientes (estado = 1)
    private function getReservasPendientes() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM reservas WHERE id_estado = 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Cuenta todos los paquetes pendientes (estado = 1)
    private function getPaquetesPendientes() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM paquetes WHERE id_estado = 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Cuenta las reservas que ocurren hoy (para vigilantes)
    private function getReservasHoy() {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM reservas
            WHERE DATE(fecha) = CURRENT_DATE
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Cuenta las visitas de un residente específico (solo activas)
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

    // Cuenta los paquetes del residente actual (solo activos)
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

    // Cuenta las reservas del residente actual (solo activas)
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
