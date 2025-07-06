<?php
// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Asegura que la sesión esté activa para poder manipularla
}
 
// Limpiar todas las variables de sesión
$_SESSION = array(); // Vacía el array de variables de sesión
 
// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/'); // Elimina la cookie de sesión del navegador
}
 
// Destruir la sesión
session_destroy(); // Elimina completamente la sesión en el servidor
 
// Redirigir al usuario a la página de login
header('Location: /login.php'); // Envía al usuario al formulario de login
exit(); // Detiene la ejecución del script