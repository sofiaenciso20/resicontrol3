<?php
 
// Cargar el autoload de Composer para usar PHPMailer y otras dependencias
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
// Configuración de errores para desarrollo (muestra todos los errores)
error_reporting(E_ALL);
ini_set('display_errors', '1');
 
// Zona horaria por defecto
date_default_timezone_set('America/Bogota');
 
// Variable para mensajes de éxito o error que se mostrarán en la vista
$mensaje = '';
 
// Si el formulario fue enviado (método POST) y se recibió el campo 'correo'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correo'])) {
    // Sanitiza el correo recibido del formulario
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
 
    // Conexión a la base de datos
    require_once __DIR__ . '/../src/Config/database.php';
    $db = new \App\Config\Database();
    $conn = $db->getConnection();
 
    // Buscar usuario por correo en la base de datos
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ? LIMIT 1");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
 
    // Si el usuario existe
    if ($usuario) {
        // Generar un código aleatorio de 6 dígitos y una fecha de expiración (10 minutos desde ahora)
        $codigo = random_int(100000, 999999);
        $expira = date('Y-m-d H:i:s', strtotime('+10 minutes'));
 
        // Guardar el código y la expiración en la base de datos para ese usuario
        $stmtUpdate = $conn->prepare("UPDATE usuarios SET codigo_recuperacion = ?, codigo_expira = ? WHERE correo = ?");
        $stmtUpdate->execute([$codigo, $expira, $correo]);
 
        // Crear una instancia de PHPMailer para enviar el correo
        $mail = new PHPMailer(true);
        try {
            // Configuración SMTP para Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'rresicontrol@gmail.com'; // Correo remitente
            $mail->Password = 'oaiejctxxsymgzwz'; // Contraseña de aplicación de Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
           
            // Configuración del remitente y destinatario
            $mail->setFrom('rresicontrol@gmail.com', 'ResiControl');
            $mail->addAddress($correo, $usuario['nombre'] . ' ' . $usuario['apellido']);
            $mail->isHTML(true);
            $mail->Subject = 'Código de recuperación de contraseña';
            $mail->Body = '<p>Hola,</p><p>Tu código de recuperación es: <b>' . $codigo . '</b></p><p>Este código expirará en 10 minutos.</p>';
 
            // Envía el mensaje
            $mail->send();
 
            // Redirigir automáticamente al formulario de verificación de código, pasando el correo por la URL
            header('Location: verificar_codigo.php?correo=' . urlencode($correo));
            exit;
        } catch (Exception $e) {
            // Si ocurre un error al enviar el correo, mostrar el mensaje de error
            $mensaje = 'No se pudo enviar el correo. Error: ' . $mail->ErrorInfo;
        }
    } else {
        // Si no se encuentra el usuario, mostrar mensaje de error
        $mensaje = 'No se encontró una cuenta con ese correo.';
    }
}
 
// Variables para la página (título y página actual)
$titulo = 'Recuperar Contraseña';
$pagina_actual = 'recuperar_contra';
 
// Inicia el output buffering para capturar el contenido de la vista
ob_start();
 
// Importa el componente de la vista (formulario de recuperación)
require_once __DIR__ . '/../views/components/recuperar_contra.php';
 
// Captura el contenido generado por la vista
$contenido = ob_get_clean();
 
// Carga el layout principal y muestra la página completa
require_once __DIR__ . '/../views/layout/main.php';