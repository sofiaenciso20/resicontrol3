<?php
// Archivo: src/Controllers/ResidentesController.php
// Controlador encargado de gestionar acciones relacionadas con los residentes

require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

class ResidentesController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function index($filtro = null, $busqueda = null, $pagina = 1, $porPagina = 10) {
        $rol = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;

        $query = "SELECT SQL_CALC_FOUND_ROWS documento, CONCAT(nombre, ' ', apellido) AS nombre, telefono, direccion_casa, 
                         id_estado_usuario, id_rol, nombre as nombre_individual, apellido, correo
                  FROM usuarios";

        $condiciones = [];
        $parametros = [];

        switch($filtro) {
            case 'activos':
                $condiciones[] = "id_rol = 3";
                $condiciones[] = "id_estado_usuario = 4";
                break;
            case 'inactivos':
                $condiciones[] = "id_rol = 3";
                $condiciones[] = "id_estado_usuario != 4";
                break;
            case 'residentes':
                $condiciones[] = "id_rol = 3";
                break;
            default:
                if ($rol == 4) {
                    $condiciones[] = "id_rol = 3";
                }
                elseif ($rol == 2) {
                    $condiciones[] = "id_rol != 1";
                }
                break;
        }

        // Agregar búsqueda si existe
        if (!empty($busqueda)) {
            $condiciones[] = "(documento LIKE :busqueda OR 
                             nombre LIKE :busqueda OR 
                             apellido LIKE :busqueda OR 
                             CONCAT(nombre, ' ', apellido) LIKE :busqueda OR
                             telefono LIKE :busqueda OR
                             direccion_casa LIKE :busqueda)";
            $parametros[':busqueda'] = "%$busqueda%";
        }

        if (!empty($condiciones)) {
            $query .= " WHERE " . implode(" AND ", $condiciones);
        }

        $query .= " ORDER BY id_rol ASC, nombre ASC";

        // Agregar paginación
        $offset = ($pagina - 1) * $porPagina;
        $query .= " LIMIT $offset, $porPagina";

        $stmt = $this->conn->prepare($query);
        
        foreach ($parametros as $param => $valor) {
            $stmt->bindValue($param, $valor);
        }

        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener el total de registros
        $total = $this->conn->query("SELECT FOUND_ROWS()")->fetchColumn();

        return [
            'datos' => $resultados,
            'total' => $total,
            'pagina' => $pagina,
            'porPagina' => $porPagina,
            'totalPaginas' => ceil($total / $porPagina)
        ];
    }

    public function getResidentesActivos($busqueda = null, $pagina = 1, $porPagina = 10) {
        return $this->index('activos', $busqueda, $pagina, $porPagina);
    }

    public function getResidentesInactivos($busqueda = null, $pagina = 1, $porPagina = 10) {
        return $this->index('inactivos', $busqueda, $pagina, $porPagina);
    }

    public function getTodosLosResidentes($busqueda = null, $pagina = 1, $porPagina = 10) {
        return $this->index('residentes', $busqueda, $pagina, $porPagina);
    }

    public function obtenerDetalleResidente($id) {
        $query = "SELECT documento, nombre, apellido, telefono, correo, direccion_casa, 
                         cantidad_personas, tiene_animales, cantidad_animales, direccion_residencia, id_rol
                  FROM usuarios
                  WHERE documento = :id AND id_rol != 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarResidente($id, $datos) {
        // Verificar si el usuario es residente
        $query_verificar = "SELECT id_rol FROM usuarios WHERE documento = :id";
        $stmt_verificar = $this->conn->prepare($query_verificar);
        $stmt_verificar->bindParam(':id', $id);
        $stmt_verificar->execute();
        $usuario = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return false;
        }

        // Construir la consulta según el rol
        if ($usuario['id_rol'] == 3) {
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
                      WHERE documento = :id";
        } else {
            $query = "UPDATE usuarios SET 
                        nombre = :nombre, 
                        apellido = :apellido, 
                        telefono = :telefono, 
                        correo = :correo, 
                        direccion_casa = :direccion_casa
                      WHERE documento = :id";
        }

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':apellido', $datos['apellido']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':correo', $datos['correo']);
        $stmt->bindParam(':direccion_casa', $datos['direccion_casa']);
        $stmt->bindParam(':id', $id);

        if ($usuario['id_rol'] == 3) {
            $stmt->bindParam(':cantidad_personas', $datos['cantidad_personas']);
            $stmt->bindParam(':tiene_animales', $datos['tiene_animales']);
            $stmt->bindParam(':cantidad_animales', $datos['cantidad_animales']);
            $stmt->bindParam(':direccion_residencia', $datos['direccion_residencia']);
        }

        return $stmt->execute();
    }

    public function getEstadisticasResidentes() {
        $query = "SELECT 
                    COUNT(*) as total_residentes,
                    SUM(CASE WHEN id_estado_usuario = 4 THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN id_estado_usuario != 4 THEN 1 ELSE 0 END) as inactivos
                  FROM usuarios 
                  WHERE id_rol = 3";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}