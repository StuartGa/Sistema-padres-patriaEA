<?php
/**
 * BARRA DE NAVEGACIÓN REUTILIZABLE
 * Compatible con Bootstrap 5
 * Navbar fija en la parte superior (fixed-top)
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
$usuario_autenticado = isset($_SESSION['usuario_id']);
$tipo_usuario = $_SESSION['tipo_usuario'] ?? null;
$nombre_usuario = $_SESSION['nombre_usuario'] ?? '';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <!-- Logo/Marca del Instituto -->
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-graduation-cap me-2"></i>
            Instituto Padres de la Patria
        </a>

        <!-- Botón hamburguesa para móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú de navegación -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                
                <!-- Enlace a Inicio -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                </li>

                <?php if (!$usuario_autenticado): ?>
                    <!-- Menú para usuarios NO autenticados -->
                    <li class="nav-item">
                        <a class="nav-link" href="registro-es.php">
                            <i class="fas fa-user-plus me-1"></i>Registro Estudiante
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Menú para usuarios AUTENTICADOS -->
                    
                    <?php if ($tipo_usuario === 'ES'): ?>
                        <!-- Menú específico para ESTUDIANTES -->
                        <li class="nav-item">
                            <a class="nav-link" href="consulta-es.php">
                                <i class="fas fa-book-reader me-1"></i>Mis Asignaturas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inscribirse.php">
                                <i class="fas fa-plus-circle me-1"></i>Inscribirse
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($tipo_usuario === 'CE'): ?>
                        <!-- Menú específico para COORDINADORES -->
                        <li class="nav-item">
                            <a class="nav-link" href="consulta-ce.php">
                                <i class="fas fa-list me-1"></i>Gestión Asignaturas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="asignatura-alta.php">
                                <i class="fas fa-plus-square me-1"></i>Nueva Asignatura
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reporte-inscripciones.php">
                                <i class="fas fa-chart-bar me-1"></i>Reportes
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Dropdown de usuario -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($nombre_usuario); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <span class="dropdown-item-text small">
                                    <i class="fas fa-id-badge me-1"></i>
                                    <?php echo $tipo_usuario === 'ES' ? 'Estudiante' : 'Coordinador'; ?>
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="perfil.php">
                                    <i class="fas fa-user-edit me-1"></i>Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                
            </ul>
        </div>
    </div>
</nav>

<!-- Espaciado para compensar la navbar fixed-top -->
<div style="margin-top: 70px;"></div>
