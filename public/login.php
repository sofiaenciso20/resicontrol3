<?php

// Carga el autoloader de Composer para dependencias externas y clases autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Configuración de errores para desarrollo: muestra todos los errores y advertencias
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Establece la zona horaria por defecto
date_default_timezone_set('America/Bogota');

// Inicia la sesión para acceder a variables de usuario
session_start();

// Inicializa variables para mensajes de error y éxito
$error_message = '';
$success_message = '';

// Si el formulario se ha enviado por el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Obtiene y sanitiza el email enviado por el formulario
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    // Obtiene la contraseña enviada por el formulario
    $password = $_POST['password'] ?? '';
   
    // Incluye el controlador de autenticación
    require_once __DIR__ . '/../src/Controllers/AuthController.php';
    // Crea una instancia del controlador de autenticación
    $auth = new AuthController();
    // Intenta autenticar al usuario con el email y la contraseña proporcionados
    $user = $auth->login($email, $password);

    if ($user) {
        // Si la autenticación es exitosa, guarda los datos del usuario en la sesión
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user'] = [
            'documento' => $user['documento'],
            'name' => $user['nombre'],
            'email' => $user['correo'],
            'role' => $user['rol']
        ];

        // Redirige al dashboard principal
        header('Location: dashboard.php');
        exit;
    } else {
        // Si la autenticación falla, muestra un mensaje de error
        $error_message = 'Credenciales incorrectas. Por favor, intenta de nuevo.';
    }
}

// Variables para la página (título y menú activo)
$titulo = 'Login - ResiControl';
$pagina_actual = 'login';

// Inicia el buffer de salida para capturar el contenido HTML generado por la vista
ob_start();
?>
<?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($error_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($success_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
// Importa el componente de la vista del formulario de login
require_once __DIR__ . '/../views/components/login.php';

// Captura el contenido generado en el buffer y lo guarda en la variable $contenido
$contenido = ob_get_clean();

// Carga el layout principal de la aplicación, que usará $contenido para mostrar la página completa
require_once __DIR__ . '/../views/layout/main.php';