<?php
// Verificar sesión (integrar con tu sistema de autenticación)
session_start();
// if (!isset($_SESSION['user_id'])) {
//     header('Location: https://www.istvidanueva.edu.ec/library-istvn/login.php');
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Certificados - Instituto Vida Nueva</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (como tu biblioteca) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- CSS personalizado - TEMA BIBLIOTECA -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar estilo Biblioteca Digital -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-award-fill"></i>
                <span>Sistema de Certificados</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house-fill"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=participants">
                            <i class="bi bi-people-fill"></i> Participantes
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="container-fluid mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Biblioteca Digital</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sistema de Certificados</li>
            </ol>
        </nav>
    </div>

    <!-- Contenido principal -->
    <main class="container-fluid main-content">
        <?php 
        $viewPath = __DIR__ . '/' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo '<div class="alert alert-danger">Vista no encontrada</div>';
        }
        ?>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <small class="text-muted">
                        &copy; <?php echo date('Y'); ?> Instituto Vida Nueva - Sistema de Certificados
                    </small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small class="text-muted">
                        <a href="https://www.istvidanueva.edu.ec" target="_blank" class="text-decoration-none">
                            www.istvidanueva.edu.ec
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <!-- JS personalizado -->
    <script src="assets/js/app.js"></script>
</body>
</html>