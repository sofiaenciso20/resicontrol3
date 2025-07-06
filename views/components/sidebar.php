<?php
// Verifica si la sesión está iniciada, si no no va tener acceso
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../src/Config/permissions.php';

// Variable para saber si hay usuario logueado
$usuario_logueado = !empty($_SESSION['is_logged_in']) && !empty($_SESSION['user']);
?>
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading"></div>
                <?php if (!$usuario_logueado): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'inicio' ? 'active' : ''; ?>" href="index.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Inicio
                    </a>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'Sobre Nosotros' ? 'active' : ''; ?>"
                        href="sobre_nosotros.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Sobre Nosotros
                    </a>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'contactanos' ? 'active' : ''; ?>"
                        href="contactanos.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        contactanos
                    </a>
                <?php endif; ?>
                <a class="nav-link <?php echo ($pagina_actual ?? '') === 'dashboard' ? 'active' : ''; ?>"
                    href="/dashboard.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
                    Dashboard
                </a>
                <?php if (tienePermiso('registro_persona')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/registro_persona.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Registro de Persona
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('registro_terreno')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/registro_terreno.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Registro de Terreno
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('historial_visitas')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/historial_visitas.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Historial de Visitas
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('gestion_reservas')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/gestion_reservas.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Gestion de Reservas
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('gestion_residentes')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/gestion_residentes.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Gestion de Personas
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('registro_paquete')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/registro_paquete.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Registro de Paquete
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('registro_visita')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/registro_visita.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Registro de Visitas
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('registro_reserva')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/registro_reserva.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Registro de Reservas
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('gestion_roles')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/gestion_roles.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Gestion de Roles
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('historial_paquetes')): ?>
                 <a class="nav-link <?php echo ($pagina_actual ?? '') === 'registro' ? 'active' : ''; ?>"
                        href="/historial_paquetes.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                        Historial de Paquetes
                    </a>
                <?php endif; ?>
                <?php if (tienePermiso('gestion_licencias')): ?>
                    <a class="nav-link <?php echo ($pagina_actual ?? '') === 'licencias' ? 'active' : ''; ?>"
                        href="/gestion_licencias.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                        Gestión de Licencias
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Estado:</div>
            <?php echo $usuario_logueado ? htmlspecialchars($_SESSION['user']['name']) : 'No hay sesión activa'; ?>
        </div>
    </nav>
</div>