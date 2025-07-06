
<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="index.php">
        <img src="/assets/img/logo.png" alt="Logo ResiControl" class="navbar-logo">
    </a>

    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
            <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
        </div>
    </form>
    
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <?php if (!empty($_SESSION['is_logged_in']) && !empty($_SESSION['user'])): ?>
            <!-- Usuario logueado - Mostrar dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                    <span class="ms-2"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li>
                        <div class="dropdown-item-text">
                            <small class="text-muted">Sesión iniciada como:</small><br>
                            <strong><?php echo htmlspecialchars($_SESSION['user']['name']); ?></strong>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider" /></li>
                    <li>
                        <a class="dropdown-item" href="/logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </li>
        <?php else: ?>
            <!-- Usuario no logueado - Mostrar botón de login -->
            <li class="nav-item">
                <a class="nav-link" href="/login.php">
                    <i class="fas fa-sign-in-alt fa-fw me-2"></i>Iniciar Sesión
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>


</div>