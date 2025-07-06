<?php
 
// Verificar si el autoloader ya está cargado
if (!defined('AUTOLOADER_LOADED')) {
    // Cargar el autoloader de Composer
    require_once __DIR__ . '/vendor/autoload.php';
    define('AUTOLOADER_LOADED', true);
}
 
// Configurar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}