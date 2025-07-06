<?php
// Inicia la sesión para usar variables de sesión
session_start();

// Se importa el archivo de configuración de la base de datos
require_once __DIR__ . '/../config/Database.php';

// Se importa el espacio de nombres de la clase Database
use App\Config\Database;

// Se importan las clases necesarias de PHPMailer para enviar correos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Se define la clase que manejará el registro de visitas
class RegistroVisitasController {
    private $conn;

    // Constructor: crea la conexión a la base de datos al instanciar la clase
    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Método privado que se encarga de enviar el correo de verificación
    private function enviarCorreoVerificacion($codigo) {
        // Verifica que exista un email en la sesión, si no lanza excepción
        if (!isset($_SESSION['user']['email'])) {
            throw new Exception("No se encontró el correo del usuario en la sesión");
        }

        $correo_destino = $_SESSION['user']['email']; // correo del residente
        $nombre_usuario = $_SESSION['user']['name'];  // nombre del residente

        // Configuración del envío de correo con PHPMailer
        $mail = new PHPMailer(true); // Se crea instancia con manejo de excepciones

        try {
            $mail->isSMTP();                                // Se usará SMTP
            $mail->Host = 'smtp.gmail.com';                 // Servidor de correo
            $mail->SMTPAuth = true;                         // Se requiere autenticación
            $mail->Username = 'rresicontrol@gmail.com';     // Correo del remitente
            $mail->Password = 'oaiejctxxsymgzwz';           // Contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encriptación segura
            $mail->Port = 587;                              // Puerto SMTP
            $mail->CharSet = 'UTF-8';                       // Codificación

            $mail->setFrom('rresicontrol@gmail.com', 'ResiControl'); // Remitente
            $mail->addAddress($correo_destino, $nombre_usuario);     // Destinatario

            $mail->isHTML(true);                                      // Permitir HTML en el cuerpo
            $mail->Subject = 'Código de Verificación - Registro de Visita'; // Asunto
            $mail->Body = "
                <h2>Hola {$nombre_usuario}</h2>
                <p>Has solicitado registrar una nueva visita en ResiControl.</p>
                <p>Tu código de verificación es: <strong style='font-size: 24px; color: #007bff;'>{$codigo}</strong></p>
                <p>Este código expirará en 30 minutos.</p>
                <p>Si no solicitaste este código, por favor ignora este correo.</p>
                <br>
                <p>Saludos,<br>Equipo de ResiControl</p>
            ";

            $mail->send(); // Envía el correo
            return true;   // Éxito
        } catch (Exception $e) {
            error_log("Error al enviar correo: " . $mail->ErrorInfo); // Log del error
            throw new Exception("Error al enviar el correo de verificación: " . $e->getMessage());
        }
    }

    // Método público que registra una visita
    public function registrar() {
        // Verifica si la petición es POST (desde formulario)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Inicia una transacción para asegurar integridad de datos
                $this->conn->beginTransaction();

                // Captura y limpia los datos del formulario
                $nombre = $_POST['nombre'] ?? '';
                $apellido = $_POST['apellido'] ?? '';
                $documento = $_POST['documento'] ?? '';
                $id_usuarios = $_POST['id_usuarios'] ?? null;
                $id_mot_visi = $_POST['id_mot_visi'] ?? null;
                $fecha_ingreso = $_POST['fecha_ingreso'] ?? null;
                $hora_ingreso = $_POST['hora_ingreso'] ?? null;
                $fecha_soli = date('Y-m-d'); // Fecha actual
                $id_estado = 1; // 1 = Pendiente

                // Código de verificación único y fecha de expiración (30 min después)
                $codigo = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
                $codigo_expira = date('Y-m-d H:i:s', strtotime('+30 minutes'));

                // Consulta SQL para insertar la visita
                $sql = "INSERT INTO visitas (
                            nombre, apellido, documento, id_usuarios, id_mot_visi,
                            fecha_ingreso, hora_ingreso, fecha_soli, codigo, codigo_expira, id_estado
                        ) VALUES (
                            :nombre, :apellido, :documento, :id_usuarios, :id_mot_visi,
                            :fecha_ingreso, :hora_ingreso, :fecha_soli, :codigo, :codigo_expira, :id_estado
                        )";

                // Prepara la consulta y vincula los valores
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido', $apellido);
                $stmt->bindParam(':documento', $documento);
                $stmt->bindParam(':id_usuarios', $id_usuarios);
                $stmt->bindParam(':id_mot_visi', $id_mot_visi);
                $stmt->bindParam(':fecha_ingreso', $fecha_ingreso);
                $stmt->bindParam(':hora_ingreso', $hora_ingreso);
                $stmt->bindParam(':fecha_soli', $fecha_soli);
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':codigo_expira', $codigo_expira);
                $stmt->bindParam(':id_estado', $id_estado);

                // Si la inserción fue exitosa, se envía el correo
                if ($stmt->execute()) {
                    if ($this->enviarCorreoVerificacion($codigo)) {
                        $this->conn->commit(); // Guarda cambios en la base de datos

                        // Guarda datos temporales en sesión para mostrar en la siguiente vista
                        $_SESSION['codigo_visita_temp'] = $codigo;
                        $_SESSION['mensaje_visita'] = "Se ha enviado un código de verificación a tu correo ({$_SESSION['user']['email']}).";

                        // Redirecciona a la validación del código
                        header("Location: /validar_visitas.php");
                        exit();
                    } else {
                        throw new Exception("Error al enviar el correo de verificación");
                    }
                } else {
                    throw new Exception("Error al registrar la visita");
                }

            } catch (Exception $e) {
                // Si ocurre algún error, revierte la transacción
                $this->conn->rollBack();
                $_SESSION['mensaje_visita'] = "❌ Error: " . $e->getMessage();

                // Redirecciona nuevamente al formulario de registro
                header("Location: /registro_visita.php");
                exit();
            }
        }
    }

    // Método vacío, podría retornar listado de visitas más adelante
    public function index() {
        return [];
    }
}

// Ejecuta el controlador al cargar el archivo
$controller = new RegistroVisitasController();
$controller->registrar();
