<?php
/**
 * PÁGINA DE INICIO
 * Instituto Padres de la Patria
 */
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de gestión académica - Instituto Padres de la Patria">
    <title>Inicio - Instituto Padres de la Patria</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    
    <!-- Incluir navbar -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">
                        Bienvenido al Sistema Académico
                    </h1>
                    <p class="lead mb-4">
                        Plataforma integral para la gestión de asignaturas, inscripciones y servicios escolares del Instituto Padres de la Patria.
                    </p>
                    <?php if (!isset($_SESSION['usuario_id'])): ?>
                        <div class="d-flex gap-3">
                            <a href="registro-es.php" class="btn btn-light btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Registrarse
                            </a>
                            <a href="login.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $_SESSION['tipo_usuario'] === 'ES' ? 'consulta-es.php' : 'consulta-ce.php'; ?>" 
                           class="btn btn-light btn-lg">
                            <i class="fas fa-arrow-right me-2"></i>Ir a mi Panel
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6 text-center mt-4 mt-lg-0">
                    <i class="fas fa-school" style="font-size: 15rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Servicios Disponibles</h2>
            
            <div class="row g-4">
                <!-- Estudiantes -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-graduate fa-4x text-primary"></i>
                            </div>
                            <h5 class="card-title">Portal Estudiantes</h5>
                            <p class="card-text">
                                Consulta tus asignaturas inscritas, horarios, calificaciones e inscríbete a nuevas materias.
                            </p>
                            <a href="registro-es.php" class="btn btn-outline-primary">Registrarse</a>
                        </div>
                    </div>
                </div>

                <!-- Coordinadores -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-tie fa-4x text-success"></i>
                            </div>
                            <h5 class="card-title">Portal Coordinadores</h5>
                            <p class="card-text">
                                Gestiona asignaturas, inscripciones, actualiza información y genera reportes académicos.
                            </p>
                            <a href="login.php" class="btn btn-outline-success">Acceder</a>
                        </div>
                    </div>
                </div>

                <!-- Información -->
                <div class="col-md-12 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-info-circle fa-4x text-warning"></i>
                            </div>
                            <h5 class="card-title">Información</h5>
                            <p class="card-text">
                                Conoce más sobre el instituto, nuestros programas académicos y servicios educativos.
                            </p>
                            <a href="#informacion" class="btn btn-outline-warning">Ver más</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Información adicional -->
    <section id="informacion" class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Acerca del Sistema</h2>
            
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-success me-3"></i>
                        </div>
                        <div>
                            <h5>Fácil de usar</h5>
                            <p>Interfaz intuitiva diseñada para facilitar la gestión académica de estudiantes y coordinadores.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-shield-alt fa-2x text-primary me-3"></i>
                        </div>
                        <div>
                            <h5>Seguro</h5>
                            <p>Tu información está protegida con los más altos estándares de seguridad y encriptación.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-mobile-alt fa-2x text-info me-3"></i>
                        </div>
                        <div>
                            <h5>Responsive</h5>
                            <p>Accede desde cualquier dispositivo: computadora, tablet o teléfono móvil.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Instituto Padres de la Patria</h5>
                    <p class="small">Sistema de Gestión Académica</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small mb-0">
                        &copy; 2026 Instituto Padres de la Patria<br>
                        Todos los derechos reservados
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script personalizado -->
    <script src="js/main.js"></script>
</body>
</html>
