<?php
// ==========================
// Mapeo de roles del sistema
// ==========================

// Se crea un arreglo que asigna un nombre descriptivo a cada rol por su ID
$roles = [
    1 => 'Super Admin',     // Rol 1: acceso total
    2 => 'Administrador',   // Rol 2: acceso administrativo
    3 => 'Residente',       // Rol 3: usuario común del sistema
    4 => 'Vigilante'        // Rol 4: personal de seguridad
];

// ==========================
// Definición de permisos por rol
// ==========================

// Cada rol tiene acceso solo a ciertas páginas o funcionalidades del sistema
$role_permissions = [
    1 => ['gestion_licencias' 
], // Super Admin: acceso completo a todas las páginas del sistema

    2 => [ // Administrador
        'registro_persona',     // Puede registrar personas
        'registro_terreno',     // Puede registrar terrenos o propiedades
        'historial_visitas',    // Puede ver el historial de visitas
        'gestion_roles',        // Puede gestionar los roles de usuarios
        'gestion_residentes',   // Puede gestionar datos de los residentes
        'historial_paquetes',   // Puede consultar los paquetes recibidos
        'gestion_reservas'      // Puede gestionar reservas de espacios
    ],

    3 => [ // Residente
        'validar_visitas',      // Puede validar visitas que ha solicitado
        'registro_reserva',     // Puede registrar reservas de zonas comunes
        'registro_visita',      // Puede registrar visitas nuevas
        'historial_visitas',    // Puede ver historial de sus visitas
        'historial_paquetes',   // Puede ver su historial de paquetes
        'gestion_reservas',     // Puede gestionar sus reservas
        'registro_persona'      // Puede registrar personas (por ejemplo, invitados)
    ],

    4 => [ // Vigilante
        'historial_visitas',    // Puede ver historial de visitas (control de entrada)
        'gestion_reservas',     // Puede ver o gestionar reservas para control de acceso
        'gestion_residentes',   // Puede consultar información de residentes
        'historial_paquetes',   // Puede consultar historial de paquetes
        'registro_paquete'      // Puede registrar paquetes que llegan
    ]
];

// =======================================
// Función para verificar permisos por página
// =======================================

// Recibe como parámetro la página o funcionalidad que se desea acceder
function tienePermiso($pagina) {
    // Si no hay sesión activa con usuario, no se permite el acceso
    if (!isset($_SESSION['user'])) return false;

    // Se obtiene el rol actual del usuario desde la variable de sesión
    $rol = $_SESSION['user']['role'];

    // Se accede a la variable global que contiene los permisos por rol
    global $role_permissions;

    // Si el rol tiene acceso total ('*'), se permite el acceso automáticamente
    if (in_array('*', $role_permissions[$rol])) {
        return true;
    }

    // Si no tiene acceso total, se revisa si la página actual está en sus permisos
    return in_array($pagina, $role_permissions[$rol]);
}
