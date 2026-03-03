<?php
/**
 * REPORTE DE INSCRIPCIONES
 * Para coordinadores (CE)
 */
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'CE') {
    header("Location: login.php");
    exit;
}

require_once 'includes/conexion.php';

// Obtener todas las inscripciones usando la vista
$stmt = $pdo->query("
    SELECT * FROM vista_inscripciones
    ORDER BY asignatura, apellido, nombre
");
$inscripciones = $stmt->fetchAll();

// Estadísticas generales
$stmt = $pdo->query("
    SELECT 
        COUNT(DISTINCT a.id) as total_asignaturas,
        COUNT(DISTINCT u.id) as total_estudiantes,
        COUNT(au.id) as total_inscripciones,
        AVG(au.calificacion) as promedio_general
    FROM asignaturas a
    LEFT JOIN asignaturas_usuarios au ON a.id = au.id_asignatura
    LEFT JOIN usuarios u ON au.id_usuario = u.id AND u.tipo = 'ES'
    WHERE a.activa = 1
");
$stats = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inscripciones - Instituto Padres de la Patria</title>
    
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
                    <i class="fas fa-chart-bar text-info"></i>
                    Reporte de Inscripciones
                </h2>
                <p class="text-muted">Vista general de todas las inscripciones activas</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button onclick="window.print()" class="btn btn-info">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h3><?php echo $stats['total_asignaturas']; ?></h3>
                        <p class="mb-0">Asignaturas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h3><?php echo $stats['total_estudiantes']; ?></h3>
                        <p class="mb-0">Estudiantes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h3><?php echo $stats['total_inscripciones']; ?></h3>
                        <p class="mb-0">Inscripciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h3><?php echo $stats['promedio_general'] ? number_format($stats['promedio_general'], 1) : 'N/A'; ?></h3>
                        <p class="mb-0">Promedio General</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buscador -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           id="tableSearch" 
                           placeholder="Buscar en la tabla...">
                </div>
            </div>
        </div>

        <!-- Tabla de inscripciones -->
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Listado Completo de Inscripciones
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($inscripciones) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Correo</th>
                                    <th>Asignatura</th>
                                    <th>Grupo</th>
                                    <th>Profesor</th>
                                    <th class="text-center">Fecha Inscripción</th>
                                    <th class="text-center">Calificación</th>
                                    <th class="text-center">Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscripciones as $inscripcion): ?>
                                    <tr>
                                        <td class="fw-bold">
                                            <?php echo htmlspecialchars($inscripcion['nombre'] . ' ' . $inscripcion['apellido']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($inscripcion['correo']); ?></td>
                                        <td><?php echo htmlspecialchars($inscripcion['asignatura']); ?></td>
                                        <td><?php echo htmlspecialchars($inscripcion['grupo']); ?></td>
                                        <td><?php echo htmlspecialchars($inscripcion['profesor']); ?></td>
                                        <td class="text-center">
                                            <?php echo date('d/m/Y', strtotime($inscripcion['fecha_inscripcion'])); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($inscripcion['calificacion']): ?>
                                                <span class="badge bg-<?php echo $inscripcion['calificacion'] >= 7 ? 'success' : 'warning'; ?> rounded-pill">
                                                    <?php echo number_format($inscripcion['calificacion'], 1); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small">Sin calificar</span>
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
                                            $color = $badge_colors[$inscripcion['estatus']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $color; ?>">
                                                <?php echo ucfirst($inscripcion['estatus']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        No hay inscripciones registradas en el sistema.
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
