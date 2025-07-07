<?php
require_once __DIR__ . '/../../src/Controllers/DashboardController.php';
use App\Controllers\DashboardController;
 
$dashboardController = new DashboardController();
$metrics = $dashboardController->getMetrics($user['documento'], $user['role']);
?>
 
<div class="container-fluid py-4">
    <!-- Encabezado con información del usuario -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary p-3 me-4">
                        <i class="bi bi-person-circle text-white" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-center"><?php echo htmlspecialchars($user['name']); ?>!</h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-envelope-fill me-2"></i><?php echo htmlspecialchars($user['email']); ?> |
                            <i class="bi bi-shield-fill me-2"></i><?php echo htmlspecialchars($rol_nombre); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="row g-4">
        <?php if (in_array($user['role'], [2])): // Admin y Super Admin ?>
            <!-- Total Residentes -->
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100 dashboard-card" data-filter="residentes" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['total_residentes']; ?></h1>
                        <h5 class="card-title">Total Residentes</h5>
                        <i class="bi bi-people-fill fs-1"></i>
                    </div>
                </div>
            </div>
            <!-- Visitas del Día -->
            <div class="col-md-3">
                <div class="card bg-success text-white h-100 dashboard-card" data-filter="visitas_dia" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['visitas_dia']; ?></h1>
                        <h5 class="card-title">Visitas del Día</h5>
                        <i class="bi bi-calendar-check fs-1"></i>
                    </div>
                </div>
            </div>
            <!-- Reservas Pendientes -->
            <div class="col-md-3">
                <div class="card bg-warning text-dark h-100 dashboard-card" data-filter="reservas_pendientes" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['reservas_pendientes']; ?></h1>
                        <h5 class="card-title">Reservas Pendientes</h5>
                        <i class="bi bi-calendar2-week fs-1"></i>
                    </div>
                </div>
            </div>
            <!-- Paquetes Pendientes -->
            <div class="col-md-3">
                <div class="card bg-info text-white h-100 dashboard-card" data-filter="paquetes_pendientes" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['paquetes_pendientes']; ?></h1>
                        <h5 class="card-title">Paquetes sin Reclamar</h5>
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                </div>
            </div>
 
        <?php elseif ($user['role'] == 4): // Vigilante ?>
            <!-- Visitas del Día -->
            <div class="col-md-4">
                <div class="card bg-success text-white h-100 dashboard-card" data-filter="visitas_dia" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['visitas_dia']; ?></h1>
                        <h5 class="card-title">Visitas del Día</h5>
                        <i class="bi bi-calendar-check fs-1"></i>
                    </div>
                </div>
            </div>
            <!-- Paquetes Pendientes -->
            <div class="col-md-4">
                <div class="card bg-info text-white h-100 dashboard-card" data-filter="paquetes_pendientes" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['paquetes_pendientes']; ?></h1>
                        <h5 class="card-title">Paquetes sin Reclamar</h5>
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                </div>
            </div>
            <!-- Reservas del Día -->
            <div class="col-md-4">
                <div class="card bg-warning text-dark h-100 dashboard-card" data-filter="reservas_dia" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['reservas_dia']; ?></h1>
                        <h5 class="card-title">Reservas del Día</h5>
                        <i class="bi bi-calendar2-week fs-1"></i>
                    </div>
                </div>
            </div>
 
        <?php elseif ($user['role'] == 3): // Residente ?>
            <!-- Mis Visitas -->
            <div class="col-md-4">
                <div class="card bg-success text-white h-100 dashboard-card" data-filter="mis_visitas" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['mis_visitas']; ?></h1>
                        <h5 class="card-title">Mis Visitas Pendientes</h5>
                        <i class="bi bi-person-badge fs-1"></i>
                    </div>
                </div>
            </div>
            <!-- Mis Paquetes -->
            <div class="col-md-4">
                <div class="card bg-info text-white h-100 dashboard-card" data-filter="mis_paquetes" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['mis_paquetes']; ?></h1>
                        <h5 class="card-title">Mis Paquetes por Recoger</h5>
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                </div>
            </div>
            <!-- Mis Reservas -->
            <div class="col-md-4">
                <div class="card bg-warning text-dark h-100 dashboard-card" data-filter="mis_reservas" style="cursor: pointer;">
                    <div class="card-body text-center">
                        <h1 class="display-4"><?php echo $metrics['mis_reservas']; ?></h1>
                        <h5 class="card-title">Mis Reservas Pendientes</h5>
                        <i class="bi bi-calendar2-week fs-1"></i>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
 
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Añadir eventos de clic a las tarjetas del dashboard
    const dashboardCards = document.querySelectorAll('.dashboard-card');
    
    dashboardCards.forEach(card => {
        card.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Redirigir según el filtro específico
            switch(filter) {
                case 'residentes':
                    window.location.href = 'gestion_residentes.php?filter=activos';
                    break;
                case 'visitas_dia':
                    window.location.href = 'historial_visitas.php?filter=hoy';
                    break;
                case 'reservas_pendientes':
                    window.location.href = 'gestion_reservas.php?filter=pendientes';
                    break;
                case 'reservas_dia':
                    window.location.href = 'gestion_reservas.php?filter=hoy';
                    break;
                case 'paquetes_pendientes':
                    window.location.href = 'historial_paquetes.php?filter=pendientes';
                    break;
                case 'mis_visitas':
                    window.location.href = 'historial_visitas.php?filter=activas';
                    break;
                case 'mis_paquetes':
                    window.location.href = 'historial_paquetes.php?filter=pendientes';
                    break;
                case 'mis_reservas':
                    window.location.href = 'gestion_reservas.php?filter=activas';
                    break;
                default:
                    console.log('Filtro no reconocido:', filter);
            }
        });
        
        // Añadir efecto hover
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
