<?php
/**
 * PERFIL DE USUARIO
 * Para estudiantes y coordinadores
 */
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/conexion.php';

// Obtener datos del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Instituto Padres de la Patria</title>
    
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

    <div class="container my-5">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="mb-3">
                <i class="fas fa-user-circle fa-5x text-primary"></i>
            </div>
            <h2>Mi Perfil</h2>
            <p class="text-muted">Información de tu cuenta</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <!-- Información del usuario -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-id-card me-2"></i>
                            Datos Personales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nombre Completo</label>
                                <p class="h5">
                                    <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tipo de Usuario</label>
                                <p class="h5">
                                    <span class="badge bg-<?php echo $usuario['tipo'] === 'ES' ? 'info' : 'success'; ?> fs-6">
                                        <?php echo $usuario['tipo'] === 'ES' ? 'Estudiante' : 'Coordinador'; ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Usuario</label>
                                <p class="h5"><?php echo htmlspecialchars($usuario['usuario']); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Correo Electrónico</label>
                                <p class="h5"><?php echo htmlspecialchars($usuario['correo']); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Fecha de Registro</label>
                                <p class="h5"><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Estado</label>
                                <p class="h5">
                                    <span class="badge bg-<?php echo $usuario['activo'] ? 'success' : 'danger'; ?> fs-6">
                                        <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas si es estudiante -->
                <?php if ($usuario['tipo'] === 'ES'): ?>
                    <?php
                    $stmt = $pdo->prepare("
                        SELECT COUNT(*) as total,
                               SUM(CASE WHEN estatus = 'cursando' THEN 1 ELSE 0 END) as cursando
                        FROM asignaturas_usuarios
                        WHERE id_usuario = ?
                    ");
                    $stmt->execute([$usuario['id']]);
                    $stats = $stmt->fetch();
                    ?>
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                Estadísticas Académicas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h2 class="text-primary"><?php echo $stats['total']; ?></h2>
                                    <p class="text-muted">Total Asignaturas</p>
                                </div>
                                <div class="col-6">
                                    <h2 class="text-success"><?php echo $stats['cursando']; ?></h2>
                                    <p class="text-muted">Cursando Actualmente</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Botones de acción -->
                <div class="mt-4 text-center">
                    <a href="<?php echo $usuario['tipo'] === 'ES' ? 'consulta-es.php' : 'consulta-ce.php'; ?>" 
                       class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>Volver al Panel
                    </a>
                </div>

            </div>
        </div>

    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0 small">&copy; 2026 Instituto Padres de la Patria</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script personalizado -->
    <script src="js/main.js"></script>
</body>
</html>
