<?php

// --------------------------------------
// Cargar dependencias y configuraciones
// --------------------------------------

// Carga automática de clases y librerías instaladas con Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Configuración de errores para mostrar todos los errores (solo para desarrollo, NO para producción)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Establece la zona horaria por defecto (importante para registros de fecha/hora en Colombia)
date_default_timezone_set('America/Bogota');

// --------------------------------------
// Variables de configuración de la página
// --------------------------------------

// Título de la pestaña o encabezado (puede usarse en el layout principal)
$titulo = 'sobre nosotros';

// Identificador de la página actual (útil para resaltar en el menú de navegación)
$pagina_actual = 'sobre_nosotros';

// --------------------------------------
// Renderizado del contenido de la vista
// --------------------------------------

// Inicia almacenamiento en búfer para capturar el contenido de la vista
ob_start();

// Incluye el archivo PHP que contiene la vista "sobre nosotros"
require_once __DIR__ . '/../views/components/sobre_nosotros.php';

// Guarda el contenido capturado en una variable
$contenido = ob_get_clean();

// --------------------------------------
// Carga del layout principal
// --------------------------------------

// Inserta el contenido dentro del diseño general del sitio web
require_once __DIR__ . '/../views/layout/main.php';
