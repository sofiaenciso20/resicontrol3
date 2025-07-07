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
    public function index($filtro = null) {
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
            e.estado,
            p.id_estado
        FROM paquetes p
        JOIN usuarios u ON CAST(p.id_usuarios AS CHAR) = u.documento
        JOIN usuarios v ON CAST(p.id_vigilante AS CHAR) = v.documento
        JOIN estado e ON p.id_estado = e.id_estado";

        // Array para almacenar las condiciones WHERE
        $condiciones = [];
        $parametros = [];

        // ------------------------------------------
        // Filtrado según el tipo de usuario
        // ------------------------------------------
        if ($rol == 3) {
            // Si el usuario es Residente, solo puede ver sus propios paquetes
            $condiciones[] = "CAST(p.id_usuarios AS CHAR) = :documento";
            $parametros[':documento'] = $documento_usuario;
        }

        // Aplicar filtros específicos
        switch($filtro) {
            case 'pendientes':
                // Solo paquetes sin reclamar (estado = 1)
                $condiciones[] = "p.id_estado = 1";
                break;
            case 'entregados':
                // Solo paquetes entregados (estado = 2)
                $condiciones[] = "p.id_estado = 2";
                break;
            case 'hoy':
                // Solo paquetes recibidos hoy
                $condiciones[] = "DATE(p.fech_hor_recep) = CURDATE()";
                break;
            case 'semana':
                // Paquetes de la última semana
                $condiciones[] = "p.fech_hor_recep >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'mes_actual':
                // Paquetes del mes actual
                $condiciones[] = "MONTH(p.fech_hor_recep) = MONTH(CURDATE()) AND YEAR(p.fech_hor_recep) = YEAR(CURDATE())";
                break;
            case 'mis_pendientes':
                // Para residentes: sus paquetes pendientes
                if ($rol == 3) {
                    $condiciones[] = "p.id_estado = 1";
                }
                break;
        }

        // Agregar condiciones WHERE si existen
        if (!empty($condiciones)) {
            $query .= " WHERE " . implode(" AND ", $condiciones);
        }

        // Agregar ORDER BY
        $query .= " ORDER BY p.fech_hor_recep DESC";

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
     * Obtiene solo los paquetes pendientes (sin reclamar)
     * @return array Paquetes pendientes
     */
    public function getPaquetesPendientes() {
        return $this->index('pendientes');
    }

    /**
     * Obtiene solo los paquetes entregados
     * @return array Paquetes entregados
     */
    public function getPaquetesEntregados() {
        return $this->index('entregados');
    }

    /**
     * Obtiene solo los paquetes recibidos hoy
     * @return array Paquetes del día
     */
    public function getPaquetesDelDia() {
        return $this->index('hoy');
    }

    /**
     * Obtiene los paquetes pendientes del residente actual
     * @return array Paquetes pendientes del residente
     */
    public function getMisPaquetesPendientes() {
        return $this->index('mis_pendientes');
    }

    /**
     * Obtiene estadísticas de paquetes
     * @return array Estadísticas de paquetes
     */
    public function getEstadisticasPaquetes() {
        $rol = $_SESSION['user']['role'] ?? null;
        $documento_usuario = $_SESSION['user']['documento'] ?? null;

        $query = "SELECT 
                    COUNT(*) as total_paquetes,
                    SUM(CASE WHEN id_estado = 1 THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN id_estado = 2 THEN 1 ELSE 0 END) as entregados,
                    SUM(CASE WHEN DATE(fech_hor_recep) = CURDATE() THEN 1 ELSE 0 END) as hoy,
                    SUM(CASE WHEN fech_hor_recep >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as semana
                  FROM paquetes";

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

    /**
     * Marca un paquete como entregado
     * @param int $id_paquete ID del paquete
     * @return bool true si se entregó correctamente
     */
    public function entregarPaquete($id_paquete) {
        try {
            $this->conn->beginTransaction();

            // Verifica que el paquete existe y está pendiente
            $stmt = $this->conn->prepare("
                SELECT id_estado
                FROM paquetes
                WHERE id_paquete = ?
                AND id_estado = 1");
            $stmt->execute([$id_paquete]);

            if (!$stmt->fetch()) {
                throw new Exception("El paquete no existe o ya fue entregado");
            }

            // Actualiza el estado a "Entregado" y registra fecha/hora de entrega
            $stmt = $this->conn->prepare("
                UPDATE paquetes
                SET id_estado = 2,
                    fech_hor_entre = NOW()
                WHERE id_paquete = ?");
            $stmt->execute([$id_paquete]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}
?>
