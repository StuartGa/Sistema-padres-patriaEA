<?php
/**
 * CONSULTA DE ESTUDIANTES (ES)
 * Muestra las asignaturas inscritas del estudiante autenticado
 */
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'ES') {
    header("Location: login.php");
    exit;
}

require_once 'includes/conexion.php';

// Obtener asignaturas del estudiante usando JOIN directo (compatible con hosting sin VIEW)
$stmt = $pdo->prepare("
    SELECT 
        a.id AS asignatura_id,
        a.nombre AS asignatura,
        a.grupo,
        a.profesor,
        au.fecha_inscripcion,
        au.calificacion,
        au.estatus
    FROM asignaturas_usuarios au
    INNER JOIN usuarios u ON au.id_usuario = u.id
    INNER JOIN asignaturas a ON au.id_asignatura = a.id
    WHERE au.id_usuario = ?
      AND u.activo = 1
      AND a.activa = 1
    ORDER BY a.nombre
");
$stmt->execute([$_SESSION['usuario_id']]);
$asignaturas = $stmt->fetchAll();

// Contar total de asignaturas
$total_asignaturas = count($asignaturas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Asignaturas - Instituto Padres de la Patria</title>
    
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
                    <i class="fas fa-book-reader text-primary"></i>
                    Mis Asignaturas
                </h2>
                <p class="text-muted">Consulta las materias en las que estás inscrito</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="inscribirse.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i>Inscribirse a Nueva Asignatura
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-book me-2"></i>Total Asignaturas
                        </h5>
                        <h2 class="mb-0"><?php echo $total_asignaturas; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-check-circle me-2"></i>Cursando
                        </h5>
                        <h2 class="mb-0">
                            <?php 
                            $cursando = array_filter($asignaturas, fn($a) => $a['estatus'] === 'cursando');
                            echo count($cursando); 
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-star me-2"></i>Promedio
                        </h5>
                        <h2 class="mb-0">
                            <?php 
                            $calificaciones = array_filter(array_column($asignaturas, 'calificacion'));
                            $promedio = !empty($calificaciones) ? array_sum($calificaciones) / count($calificaciones) : 0;
                            echo number_format($promedio, 1); 
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de asignaturas -->
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Asignaturas Inscritas
                </h5>
            </div>
            <div class="card-body">
                <?php if ($total_asignaturas > 0): ?>
                    <!-- Tabla responsiva con clases de Bootstrap 5 -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                    <th><i class="fas fa-book me-1"></i>Asignatura</th>
                                    <th><i class="fas fa-users me-1"></i>Grupo</th>
                                    <th><i class="fas fa-chalkboard-teacher me-1"></i>Profesor</th>
                                    <th><i class="fas fa-calendar me-1"></i>Fecha Inscripción</th>
                                    <th class="text-center"><i class="fas fa-chart-line me-1"></i>Calificación</th>
                                    <th class="text-center"><i class="fas fa-info-circle me-1"></i>Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asignaturas as $asignatura): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($asignatura['asignatura_id']); ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($asignatura['asignatura']); ?></td>
                                        <td><?php echo htmlspecialchars($asignatura['grupo']); ?></td>
                                        <td><?php echo htmlspecialchars($asignatura['profesor']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($asignatura['fecha_inscripcion'])); ?></td>
                                        <td class="text-center">
                                            <?php if ($asignatura['calificacion']): ?>
                                                <span class="badge bg-<?php echo $asignatura['calificacion'] >= 7 ? 'success' : 'warning'; ?> rounded-pill">
                                                    <?php echo number_format($asignatura['calificacion'], 1); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Sin calificar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $badge_colors = [
                                                'inscrito' => 'secondary',
                                                'cursando' => 'primary',
                                                'aprobado' => 'success',
                                                'reprobado' => 'danger'
                                            ];
                                            $color = $badge_colors[$asignatura['estatus']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $color; ?>">
                                                <?php echo ucfirst($asignatura['estatus']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- Mensaje cuando no hay asignaturas -->
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>No tienes asignaturas inscritas.</strong>
                        <a href="inscribirse.php" class="alert-link">Haz clic aquí para inscribirte</a>.
                    </div>
                <?php endif; ?>
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
