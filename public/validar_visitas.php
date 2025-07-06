<?php

// --------------------------------------
// Cargar dependencias del sistema
// --------------------------------------

// Carga automática de clases y librerías usando Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Se incluye el controlador que maneja la lógica para validar visitas
require_once __DIR__ . '/../src/Controllers/ValidarVisitasController.php';

// Se incluye el archivo que contiene las funciones de permisos del sistema
require_once __DIR__ . '/../src/Config/permissions.php';

// --------------------------------------
// Verificación de sesión y permisos
// --------------------------------------

// Inicia la sesión (necesario para verificar permisos y manejar variables de sesión)
session_start();

// Verifica si el usuario tiene permiso para acceder a la validación de visitas
// Si no tiene, lo redirige al dashboard y detiene la ejecución del script
if (!tienePermiso('validar_visitas')) {
    header('Location: dashboard.php');
    exit;
}

// --------------------------------------
// Lógica del controlador
// --------------------------------------

// Se crea una instancia del controlador de validación de visitas
$controller = new ValidarVisitasController();

// Si la solicitud al servidor es de tipo POST (es decir, se envió un formulario),
// entonces se ejecuta el método 'validar' del controlador
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->validar();
}

// --------------------------------------
// Preparación para la vista
// --------------------------------------

// Se definen variables que pueden usarse en el layout o encabezado de la página
$titulo = 'Validar Visitas';
$pagina_actual = 'validar_visitas';

// Inicia el almacenamiento en búfer para capturar el contenido de la vista
ob_start();

// Se incluye la vista que contiene la interfaz para validar las visitas
require_once __DIR__ . '/../views/components/validar_visitas.php';

// Guarda el contenido generado por la vista
$contenido = ob_get_clean();

// Se carga el layout principal del sitio y se le inserta el contenido capturado
require_once __DIR__ . '/../views/layout/main.php';
