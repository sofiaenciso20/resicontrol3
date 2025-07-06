<?php

// Se cargan las dependencias instaladas con Composer, como autoloaders o librerías externas
require_once __DIR__ . '/../vendor/autoload.php';

// Se importa el controlador que maneja la lógica relacionada con terrenos
require_once __DIR__ . '/../src/Controllers/TerrenoController.php';

// Se cargan las funciones o configuraciones relacionadas con los permisos del sistema
require_once __DIR__ . '/../src/Config/permissions.php';

// Verifica si aún no se ha iniciado una sesión, y si no, la inicia.
// Esto es necesario para trabajar con variables de sesión como $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica si el usuario tiene el permiso 'registro_terreno'
// Si no tiene el permiso, lo redirige al dashboard y termina la ejecución
if (!tienePermiso('registro_terreno')) {
    header('Location: dashboard.php');
    exit;
}

// Recupera un mensaje de sesión, si existe, y luego lo elimina para evitar que se muestre varias veces
$mensaje = $_SESSION['mensaje'] ?? null;
unset($_SESSION['mensaje']);

// Se crea una instancia del controlador de terrenos
$controller = new terrenoController();

// Se llama al método registrar(), que probablemente maneja la lógica de guardado del terreno si es un POST
$controller->registrar();

// Se definen variables para el título de la página y el nombre de la página actual (puede usarse para resaltar un ítem del menú, por ejemplo)
$titulo = 'Registro de Terreno';
$pagina_actual = 'registro_terreno';

// Se inicia el almacenamiento en búfer de salida. Esto permite capturar el contenido generado por el archivo PHP en lugar de enviarlo directamente al navegador
ob_start();

// Se incluye la vista que contiene el formulario u otro contenido relacionado con el registro de terrenos
require_once __DIR__ . '/../views/components/registro_terreno.php';

// Se guarda el contenido capturado en la variable $contenido
$contenido = ob_get_clean();

// Finalmente, se carga el layout principal del sitio, al que se le inserta el contenido capturado anteriormente
require_once __DIR__ . '/../views/layout/main.php';
