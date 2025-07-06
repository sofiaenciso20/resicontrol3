<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Exception;
use App\Config\Database;
use App\Controllers\LicenciasController;

class PersonaController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = new Database();
            $conn = $db->getConnection();

            // Recoge los datos comunes
            $tipo_usuario = $_POST['tipo_usuario'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $apellido = $_POST['apellido'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $id_tipo_doc = $_POST['tipo_identificacion'] ?? '';
            $documento = $_POST['numero_identificacion'] ?? '';
            $correo = $_POST['correo'] ?? '';
            $contrasena = password_hash($_POST['contrasena'] ?? '', PASSWORD_DEFAULT);

            // Mapeo del rol
            switch ($tipo_usuario) {
                case 'vigilante':
                    $id_rol = 4;
                    break;
                case 'habitante':
                case 'residente':
                    $id_rol = 3;
                    break;
                case 'administrador':
                    $id_rol = 2;
                    break;
                default:
                    $id_rol = null;
            }

            // Comunes y opcionales
            $empresa = $_POST['empresa'] ?? null;
            $direccion_casa = $_POST['direccion_casa'] ?? null;
            $id_manzana = null; // si existe, ajustar
            $cantidad_personas = $_POST['cantidad_personas'] ?? null;
            $tiene_animales = $_POST['tiene_animales'] ?? null;
            $cantidad_animales = $_POST['cantidad_animales'] ?? null;
            $direccion_residencia = $_POST['direccion_residencia'] ?? null;
            $id_estado = 1;
            $nit = null;

            // Inicia transacción
            try {
                $conn->beginTransaction();

                 // Verificar si el documento ya existe
                $sql_verificar = "SELECT documento FROM usuarios WHERE documento = :documento";
                $stmt_verificar = $conn->prepare($sql_verificar);
                $stmt_verificar->bindParam(':documento', $documento);
                $stmt_verificar->execute();
 
                if ($stmt_verificar->fetch()) {
                    return "El número de documento " . htmlspecialchars($documento) . " ya está registrado en el sistema.";
                }

                // Insertar usuario
                $sql = "INSERT INTO usuarios
                    (documento, id_tipo_doc, nombre, apellido, telefono, correo, contrasena, id_rol, id_estado, id_manzana, nit, empresa, direccion_casa, cantidad_personas, tiene_animales, cantidad_animales, direccion_residencia)
                    VALUES
                    (:documento, :id_tipo_doc, :nombre, :apellido, :telefono, :correo, :contrasena, :id_rol, :id_estado, :id_manzana, :nit, :empresa, :direccion_casa, :cantidad_personas, :tiene_animales, :cantidad_animales, :direccion_residencia)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':documento', $documento);
                $stmt->bindParam(':id_tipo_doc', $id_tipo_doc);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido', $apellido);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':contrasena', $contrasena);
                $stmt->bindParam(':id_rol', $id_rol);
                $stmt->bindParam(':id_estado', $id_estado);
                $stmt->bindParam(':id_manzana', $id_manzana);
                $stmt->bindParam(':nit', $nit);
                $stmt->bindParam(':empresa', $empresa);
                $stmt->bindParam(':direccion_casa', $direccion_casa);
                $stmt->bindParam(':cantidad_personas', $cantidad_personas);
                $stmt->bindParam(':tiene_animales', $tiene_animales);
                $stmt->bindParam(':cantidad_animales', $cantidad_animales);
                $stmt->bindParam(':direccion_residencia', $direccion_residencia);
                //ejecuta la consulta
                if (!$stmt->execute()) {
                    //no deja cambiar cosas en la base de datos si hay un error
                    $conn->rollBack();
                    return "Error al registrar la persona.";
                }

                // Solo si es habitante, registra vehículo
                 // Solo si es habitante, registra vehículo
                if ($id_rol == 3) {
                    $id_tipo_vehi = $_POST['id_tipo_vehi'] ?? null;
                    $placa = $_POST['placa'] ?? null;
                    $id_marca = $_POST['id_marca'] ?? null;
 
                    if ($id_tipo_vehi && $placa && $id_marca) {
                        // Verificar que el tipo de vehículo existe
                        $stmt = $conn->prepare("SELECT id_tipo_vehi FROM tipo_vehiculos WHERE id_tipo_vehi = ?");
                        $stmt->execute([$id_tipo_vehi]);
                        if (!$stmt->fetch()) {
                            $conn->rollBack();
                            return "Error: El tipo de vehículo seleccionado no existe.";
                        }
 
                        // Verificar que la marca existe
                        $stmt = $conn->prepare("SELECT id_marca FROM marca WHERE id_marca = ?");
                        $stmt->execute([$id_marca]);
                        if (!$stmt->fetch()) {
                            $conn->rollBack();
                            return "Error: La marca seleccionada no existe.";
                        }
 
                        $sql_vehiculo = "INSERT INTO vehiculos (id_tipo_vehi, id_usuarios, placa, id_marca)
                                         VALUES (:id_tipo_vehi, :id_usuarios, :placa, :id_marca)";
                       
                        $stmtVehiculo = $conn->prepare($sql_vehiculo);
                        $stmtVehiculo->bindParam(':id_tipo_vehi', $id_tipo_vehi);
                        $stmtVehiculo->bindParam(':id_usuarios', $documento);
                        $stmtVehiculo->bindParam(':placa', $placa);
                        $stmtVehiculo->bindParam(':id_marca', $id_marca);
 
                        if (!$stmtVehiculo->execute()) {
                            $error = $stmtVehiculo->errorInfo();
                            $conn->rollBack();
                            return "Error al registrar el vehículo: " . $error[2];
                        }
                    }
                }
 

                $conn->commit();
                return "¡Persona registrada exitosamente!";
            } catch (Exception $e) {
                $conn->rollBack();
                return "Error en el proceso: " . $e->getMessage();
            }
        }
        return "";
    }

    public function crearPersona($datos) {
        try {
            // Validar límites de licencia antes de crear el usuario
            $licenciasController = new LicenciasController();
            $validacionLimites = $licenciasController->validarLimitesLicencia($datos['id_licencia'], $datos['id_rol']);
            
            if (!$validacionLimites['success']) {
                return [
                    'success' => false,
                    'mensaje' => $validacionLimites['mensaje']
                ];
            }

            // Continuar con la creación del usuario si pasa la validación
            $sql = "INSERT INTO usuarios (nombre, apellido, email, password, id_rol, id_licencia) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            // Hashear la contraseña
            $hashedPassword = password_hash($datos['password'], PASSWORD_DEFAULT);
            
            $resultado = $stmt->execute([
                $datos['nombre'],
                $datos['apellido'],
                $datos['email'],
                $hashedPassword,
                $datos['id_rol'],
                $datos['id_licencia']
            ]);

            if ($resultado) {
                return [
                    'success' => true,
                    'mensaje' => 'Usuario creado exitosamente',
                    'id' => $this->conn->lastInsertId()
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => 'Error al crear el usuario'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error en crearPersona: " . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error al crear el usuario'
            ];
        }
    }
}