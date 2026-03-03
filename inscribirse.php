<?php
/**
 * INSCRIBIRSE A ASIGNATURAS
 * Para estudiantes (ES)
 */
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'ES') {
    header("Location: login.php");
    exit;
}

require_once 'includes/conexion.php';

$mensaje = '';
$tipo_mensaje = '';

// Procesar inscripción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignatura_id'])) {
    $asignatura_id = intval($_POST['asignatura_id']);
    $usuario_id = $_SESSION['usuario_id'];
    
    try {
        // Verificar que no esté ya inscrito
        $stmt = $pdo->prepare("SELECT id FROM asignaturas_usuarios WHERE id_usuario = ? AND id_asignatura = ?");
        $stmt->execute([$usuario_id, $asignatura_id]);
        
        if ($stmt->rowCount() > 0) {
            $tipo_mensaje = 'warning';
            $mensaje = 'Ya estás inscrito en esta asignatura.';
        } else {
            // Verificar cupo
            $stmt = $pdo->prepare("
                SELECT a.cupo_maximo, COUNT(au.id) as inscritos
                FROM asignaturas a
                LEFT JOIN asignaturas_usuarios au ON a.id = au.id_asignatura
                WHERE a.id = ?
                GROUP BY a.id
            ");
            $stmt->execute([$asignatura_id]);
            $cupo = $stmt->fetch();
            
            if ($cupo && $cupo['inscritos'] >= $cupo['cupo_maximo']) {
                $tipo_mensaje = 'danger';
                $mensaje = 'La asignatura ha alcanzado su cupo máximo.';
            } else {
                // Inscribir
                $stmt = $pdo->prepare("INSERT INTO asignaturas_usuarios (id_usuario, id_asignatura) VALUES (?, ?)");
                if ($stmt->execute([$usuario_id, $asignatura_id])) {
                    $tipo_mensaje = 'success';
                    $mensaje = '¡Inscripción exitosa! <a href="consulta-es.php" class="alert-link">Ver mis asignaturas</a>';
                }
            }
        }
    } catch (PDOException $e) {
        $tipo_mensaje = 'danger';
        $mensaje = 'Error: ' . $e->getMessage();
    }
}

// Obtener asignaturas disponibles (no inscritas por el usuario)
$stmt = $pdo->prepare("
    SELECT a.*, COUNT(au.id) as inscritos
    FROM asignaturas a
    LEFT JOIN asignaturas_usuarios au ON a.id = au.id_asignatura
    WHERE a.activa = 1 
    AND a.id NOT IN (
        SELECT id_asignatura 
        FROM asignaturas_usuarios 
        WHERE id_usuario = ?
    )
    GROUP BY a.id
    ORDER BY a.nombre
");
$stmt->execute([$_SESSION['usuario_id']]);
$asignaturas_disponibles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscribirse - Instituto Padres de la Patria</title>
    
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
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>
                    <i class="fas fa-plus-circle text-primary"></i>
                    Inscribirse a Asignaturas
                </h2>
                <p class="text-muted">Selecciona las materias que deseas cursar</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="consulta-es.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Mis Asignaturas
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Asignaturas disponibles -->
        <?php if (count($asignaturas_disponibles) > 0): ?>
            <div class="row g-4">
                <?php foreach ($asignaturas_disponibles as $asignatura): ?>
                    <?php 
                    $disponible = $asignatura['inscritos'] < $asignatura['cupo_maximo'];
                    $porcentaje = ($asignatura['inscritos'] / $asignatura['cupo_maximo']) * 100;
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 <?php echo $disponible ? '' : 'border-danger'; ?>">
                            <div class="card-header <?php echo $disponible ? 'bg-primary' : 'bg-danger'; ?> text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-book me-2"></i>
                                    <?php echo htmlspecialchars($asignatura['nombre']); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong><i class="fas fa-users me-1"></i>Grupo:</strong> 
                                    <?php echo htmlspecialchars($asignatura['grupo']); ?>
                                </p>
                                <p class="card-text">
                                    <strong><i class="fas fa-chalkboard-teacher me-1"></i>Profesor:</strong> 
                                    <?php echo htmlspecialchars($asignatura['profesor']); ?>
                                </p>
                                <?php if ($asignatura['descripcion']): ?>
                                    <p class="card-text small text-muted">
                                        <?php echo htmlspecialchars($asignatura['descripcion']); ?>
                                    </p>
                                <?php endif; ?>
                                
                                <!-- Barra de cupo -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small><strong>Cupo:</strong></small>
                                        <small><?php echo $asignatura['inscritos']; ?> / <?php echo $asignatura['cupo_maximo']; ?></small>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar <?php echo $porcentaje >= 90 ? 'bg-danger' : ($porcentaje >= 70 ? 'bg-warning' : 'bg-success'); ?>" 
                                             role="progressbar" 
                                             style="width: <?php echo min($porcentaje, 100); ?>%">
                                        </div>
                                    </div>
                                </div>

                                <?php if ($disponible): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="asignatura_id" value="<?php echo $asignatura['id']; ?>">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-check me-1"></i>Inscribirse
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-danger w-100" disabled>
                                        <i class="fas fa-times me-1"></i>Cupo Lleno
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>No hay asignaturas disponibles para inscripción.</strong>
                Ya estás inscrito en todas las asignaturas disponibles o no hay materias activas en este momento.
            </div>
        <?php endif; ?>

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
