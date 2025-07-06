<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        // Título de la página
        echo $titulo ?? 'Mi Proyecto PHP';

        ?>
    </title>

    <!-- CSS de Bootstrap 5 -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" rel="stylesheet">
   
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Navbar superior -->
    <?php require_once __DIR__ . '/../components/navbar.php'; ?>
 
    <div id="layoutSidenav">
        <!-- Sidebar lateral -->
        <?php require_once __DIR__ . '/../components/sidebar.php'; ?>
        <!-- Contenido principal -->
        <div id="layoutSidenav_content">
            <main class="container mt-4 flex-grow-1">
                <?php echo $contenido ?? ''; ?>
            </main>
            <!-- Footer -->
            <?php require_once __DIR__ . '/../components/footer.php'; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <!-- JavaScript de Bootstrap (con Popper.js incluido) -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="/js/script.js"></script>
</body>

</html>