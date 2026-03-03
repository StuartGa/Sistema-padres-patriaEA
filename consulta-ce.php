<?php
/**
 * CONSULTA DE COORDINADORES (CE)
 * Gestión de todas las asignaturas del instituto
 */
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'CE') {
    header("Location: login.php");
    exit;
}

require_once 'includes/conexion.php';

// Procesar eliminación de asignatura
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    try {
        $stmt = $pdo->prepare("DELETE FROM asignaturas WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: consulta-ce.php?msg=eliminado");
        exit;
    } catch (PDOException $e) {
        $error_msg = "Error al eliminar: " . $e->getMessage();
    }
}

// Obtener todas las asignaturas
$stmt = $pdo->query("
    SELECT a.*,
           COUNT(au.id) as total_inscritos
    FROM asignaturas a
    LEFT JOIN asignaturas_usuarios au ON a.id = au.id_asignatura
    WHERE a.activa = 1
    GROUP BY a.id
    ORDER BY a.nombre
");
$asignaturas = $stmt->fetchAll();

// Estadísticas
$total_asignaturas = count($asignaturas);
$stmt = $pdo->query("SELECT COUNT(*) FROM asignaturas_usuarios");
$total_inscripciones = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asignaturas - Instituto Padres de la Patria</title>
    
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
                    <i class="fas fa-tasks text-success"></i>
                    Gestión de Asignaturas
                </h2>
                <p class="text-muted">Panel de administración para coordinadores</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="asignatura-alta.php" class="btn btn-success">
                    <i class="fas fa-plus-square me-1"></i>Nueva Asignatura
                </a>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php
                $mensajes = [
                    'creado' => 'Asignatura creada exitosamente',
                    'actualizado' => 'Asignatura actualizada exitosamente',
                    'eliminado' => 'Asignatura eliminada exitosamente'
                ];
                echo $mensajes[$_GET['msg']] ?? 'Operación completada';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error_msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-book me-2"></i>Total Asignaturas
                        </h5>
                        <h2 class="mb-0"><?php echo $total_asignaturas; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-users me-2"></i>Total Inscripciones
                        </h5>
                        <h2 class="mb-0"><?php echo $total_inscripciones; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de asignaturas -->
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Listado de Asignaturas
                </h5>
            </div>
            <div class="card-body">
                <?php if ($total_asignaturas > 0): ?>
                    <!-- Tabla responsiva con Bootstrap 5 -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                    <th><i class="fas fa-book me-1"></i>Asignatura</th>
                                    <th><i class="fas fa-users me-1"></i>Grupo</th>
                                    <th><i class="fas fa-chalkboard-teacher me-1"></i>Profesor</th>
                                    <th class="text-center"><i class="fas fa-user-friends me-1"></i>Inscritos</th>
                                    <th class="text-center"><i class="fas fa-user-check me-1"></i>Cupo</th>
                                    <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asignaturas as $asignatura): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($asignatura['id']); ?></td>
                                        <td class="fw-bold">
                                            <?php echo htmlspecialchars($asignatura['nombre']); ?>
                                            <?php if ($asignatura['descripcion']): ?>
                                                <i class="fas fa-info-circle text-muted" 
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top"
                                                   title="<?php echo htmlspecialchars($asignatura['descripcion']); ?>"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($asignatura['grupo']); ?></td>
                                        <td><?php echo htmlspecialchars($asignatura['profesor']); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-info rounded-pill">
                                                <?php echo $asignatura['total_inscritos']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                            $porcentaje = ($asignatura['total_inscritos'] / $asignatura['cupo_maximo']) * 100;
                                            $color = $porcentaje >= 90 ? 'danger' : ($porcentaje >= 70 ? 'warning' : 'success');
                                            ?>
                                            <span class="badge bg-<?php echo $color; ?> rounded-pill">
                                                <?php echo $asignatura['cupo_maximo']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <!-- Botones de acción -->
                                            <div class="btn-group" role="group">
                                                <!-- Botón Editar -->
                                                <a href="asignatura-editar.php?id=<?php echo $asignatura['id']; ?>" 
                                                   class="btn btn-sm btn-warning"
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top"
                                                   title="Editar asignatura">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <!-- Botón Eliminar -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEliminar<?php echo $asignatura['id']; ?>"
                                                        title="Eliminar asignatura">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal de confirmación para eliminar -->
                                    <div class="modal fade" id="modalEliminar<?php echo $asignatura['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Confirmar Eliminación
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Estás seguro de eliminar la asignatura <strong><?php echo htmlspecialchars($asignatura['nombre']); ?></strong>?</p>
                                                    <p class="text-danger">
                                                        <i class="fas fa-exclamation-circle me-1"></i>
                                                        Esta acción no se puede deshacer y eliminará todas las inscripciones asociadas.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <a href="consulta-ce.php?eliminar=<?php echo $asignatura['id']; ?>" 
                                                       class="btn btn-danger">
                                                        <i class="fas fa-trash me-1"></i>Eliminar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>No hay asignaturas registradas.</strong>
                        <a href="asignatura-alta.php" class="alert-link">Crea la primera asignatura</a>.
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
    
    <!-- Inicializar tooltips -->
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
