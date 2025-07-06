<?php

// =============================================
// DASHBOARD - VISTA PRINCIPAL TRAS LOGIN
// =============================================

// Inicia la sesión para acceder a los datos del usuario autenticado
session_start();

// =============================================
// VERIFICACIÓN DE AUTENTICACIÓN
// =============================================

// Verifica si el usuario está logueado; si no, lo redirige al login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Obtiene el usuario desde la sesión
$user = $_SESSION['user'];

// =============================================
// CONSULTA DEL NOMBRE DEL ROL DESDE LA BASE DE DATOS
// =============================================

/*
---------------------------------------------------------------
FLUJO DE CONSULTA DEL NOMBRE DEL ROL EN EL DASHBOARD
---------------------------------------------------------------

1. Cuando el usuario inicia sesión, se guarda en la sesión el id del rol (id_rol) 
   como parte de $_SESSION['user']['role'].

2. Al cargar el dashboard, se recupera el usuario autenticado desde la sesión.

3. Antes de mostrar la vista, se realiza una consulta a la tabla 'roles' usando 
   el id_rol del usuario:
      SELECT rol FROM roles WHERE id_rol = ?

   Esto obtiene el nombre legible del rol (por ejemplo, "Administrador", 
   "Residente", etc.).

4. El nombre del rol obtenido ($rol_nombre) se pasa a la vista del dashboard.

5. En la vista, se muestra el nombre del rol en vez del id, haciendo la interfaz 
   más clara y amigable para el usuario.
*/

// Importa la clase Database y establece conexión
require_once __DIR__ . '/../src/Config/database.php';
$db = new \App\Config\Database();
$conn = $db->getConnection();

// Prepara y ejecuta la consulta para obtener el nombre del rol
$stmt = $conn->prepare("SELECT rol FROM roles WHERE id_rol = ?");
$stmt->execute([$user['role']]);

// Recupera el nombre del rol como string
$rol_nombre = $stmt->fetchColumn();

// =============================================
// CONFIGURACIÓN DE LA VISTA
// =============================================

// Título que aparecerá en el navegador o en la cabecera de la vista
$titulo = 'Dashboard';

// Nombre interno de la página (puede usarse para marcar navegación activa)
$pagina_actual = 'dashboard';

// =============================================
// RENDERIZADO DEL CONTENIDO
// =============================================

// Inicia el buffer de salida para capturar la vista
ob_start();

// Carga el componente visual del dashboard
require_once __DIR__ . '/../views/components/dashboard.php';

// Captura y guarda el contenido generado
$contenido = ob_get_clean();

// =============================================
// CARGA DEL LAYOUT PRINCIPAL
// =============================================

// Inserta el contenido del dashboard dentro de la estructura principal (layout)
require_once __DIR__ . '/../views/layout/main.php';
