<?php

// ==========================================
// CONTACTANOS.PHP - Controlador de vista
// ==========================================

// Cargar el autoload de Composer para incluir todas las clases automáticamente
require_once __DIR__ . '/../vendor/autoload.php';

// ==========================================
// Configuración de errores para desarrollo
// ==========================================

// Reporta todos los tipos de errores
error_reporting(E_ALL);

// Muestra los errores directamente en el navegador
ini_set('display_errors', '1');

// ==========================================
// Zona horaria por defecto
// ==========================================

// Define la zona horaria de Bogotá (Colombia) para todas las funciones de fecha y hora
date_default_timezone_set('America/Bogota');

// ==========================================
// Variables relacionadas con la página
// ==========================================

// Título de la página que puede ser utilizado en el <title> del HTML
$titulo = 'contactanos';

// Nombre interno de la página, útil para marcar el menú activo u otras validaciones
$pagina_actual = 'contactanos';

// ==========================================
// Contenido de la página
// ==========================================

// Inicia el almacenamiento en buffer de salida. Todo lo que se imprima ahora se guarda.
ob_start();

// Incluye la vista/componente correspondiente a esta página
require_once __DIR__ . '/../views/components/contactanos.php';

// Almacena el contenido del buffer en la variable $contenido y limpia el buffer
$contenido = ob_get_clean();

// ==========================================
// Cargar el layout principal
// ==========================================

// El layout principal se encarga de envolver $contenido en la estructura HTML general
require_once __DIR__ . '/../views/layout/main.php';
