<?php

// Carga el autoloader de Composer para dependencias externas y clases autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Configuración de errores para desarrollo:
// - Muestra todos los errores y advertencias en pantalla
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Establece la zona horaria por defecto para todas las funciones de fecha/hora
date_default_timezone_set('America/Bogota');

// Variables para la página:
// - $titulo: Título de la página (usado en el layout o en la vista)
// - $pagina_actual: Identificador de la página actual (útil para resaltar el menú activo)
$titulo = 'home';
$pagina_actual = 'home';

// Inicia el buffer de salida para capturar el contenido HTML generado por la vista
ob_start();

// Importa el componente de la vista principal (home)
require_once __DIR__ . '/../views/components/home.php';

// Captura el contenido generado en el buffer y lo guarda en la variable $contenido
$contenido = ob_get_clean();

// Carga el layout principal de la aplicación, que usará $contenido para mostrar la página completa
require_once __DIR__ . '/../views/layout/main.php';



