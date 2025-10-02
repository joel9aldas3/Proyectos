<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Certificados</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- CSS personalizado -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-mortarboard-fill"></i> Library Certificates
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Inicio</a>
                <a class="nav-link" href="index.php?action=participants">Participantes</a>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <?php 
        $viewPath = __DIR__ . '/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo '<div class="alert alert-danger">Vista no encontrada</div>';
        }
        ?>
    </main>

    <footer class="bg-light mt-5 py-3">
        <div class="container text-center">
            <small>&copy; 2025 Sistema de Certificados - Tu Institución</small>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.min.js"></script>
    <!-- JS personalizado -->
    <script src="assets/js/app.js"></script>
</body>
</html>
