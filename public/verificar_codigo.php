<?php

// --------------------------------------
// Dependencias y configuración inicial
// --------------------------------------

// Carga automática de clases con Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Habilita todos los errores para facilitar el desarrollo (no se recomienda en producción)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define la zona horaria por defecto (en este caso, Bogotá, Colombia)
date_default_timezone_set('America/Bogota');

// Inicializa una variable para mostrar mensajes (éxito o error) en la vista
$mensaje = '';

// --------------------------------------
// Procesamiento del formulario
// --------------------------------------

// Verifica si la solicitud es de tipo POST y si se enviaron los campos requeridos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correo'], $_POST['codigo'], $_POST['nueva_contra'])) {
    
    // Sanitiza el campo de correo para evitar entradas maliciosas
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);

    // Obtiene el código de verificación y la nueva contraseña directamente
    $codigo = $_POST['codigo'];
    $nueva_contra = $_POST['nueva_contra'];

    // --------------------------------------
    // Conexión a la base de datos
    // --------------------------------------

    require_once __DIR__ . '/../src/Config/database.php';
    $db = new \App\Config\Database();
    $conn = $db->getConnection();

    // Busca al usuario que tenga ese correo y código de recuperación
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ? AND codigo_recuperacion = ? LIMIT 1");
    $stmt->execute([$correo, $codigo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // --------------------------------------
    // Verificación del código y expiración
    // --------------------------------------

    if ($usuario) {
        // Verifica si el código aún está dentro del tiempo válido
        if (strtotime($usuario['codigo_expira']) >= time()) {

            // Hashea la nueva contraseña para guardarla de forma segura
            $hash = password_hash($nueva_contra, PASSWORD_DEFAULT);

            // Actualiza la contraseña y limpia el código de recuperación y su expiración
            $stmtUpdate = $conn->prepare("UPDATE usuarios SET contrasena = ?, codigo_recuperacion = NULL, codigo_expira = NULL WHERE correo = ?");
            $stmtUpdate->execute([$hash, $correo]);

            // --------------------------------------
            // Iniciar sesión automáticamente (opcional)
            // --------------------------------------

            session_start();
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user'] = [
                'documento' => $usuario['documento'],
                'name' => $usuario['nombre'] . ' ' . $usuario['apellido'],
                'email' => $usuario['correo'],
                'role' => $usuario['id_rol']
            ];

            // Redirige al dashboard del usuario después de cambiar la contraseña
            header('Location: dashboard.php');
            exit;
        } else {
            // Si el código ha expirado, muestra un mensaje de advertencia
            $mensaje = 'El código ha expirado. Solicita uno nuevo.';
        }
    } else {
        // Si no se encuentra usuario con ese correo y código, muestra mensaje de error
        $mensaje = 'El código o el correo no son correctos.';
    }
}

// --------------------------------------
// Preparación para mostrar la vista
// --------------------------------------

// Variables para el título y nombre de la página (útil para el layout y navegación)
$titulo = 'Verificar Código';
$pagina_actual = 'verificar_codigo';

// Inicia almacenamiento en búfer para capturar el contenido de la vista
ob_start();

// Se incluye la vista con el formulario para ingresar código y nueva contraseña
require_once __DIR__ . '/../views/components/verificar_codigo.php';

// Guarda el contenido capturado en la variable $contenido
$contenido = ob_get_clean();

// Carga el layout principal, que insertará el contenido en su estructura general (header, footer, etc.)
require_once __DIR__ . '/../views/layout/main.php';
