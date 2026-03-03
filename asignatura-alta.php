<?php
/**
 * ALTA DE ASIGNATURA
 * Para coordinadores (CE)
 */
session_start();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'CE') {
    header("Location: login.php");
    exit;
}

require_once 'includes/conexion.php';

// Variables
$mensaje = '';
$tipo_mensaje = '';
$nombre = $grupo = $profesor = $descripcion = '';
$cupo_maximo = 30;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiar_entrada($_POST['nombre'] ?? '');
    $grupo = limpiar_entrada($_POST['grupo'] ?? '');
    $profesor = limpiar_entrada($_POST['profesor'] ?? '');
    $cupo_maximo = intval($_POST['cupo_maximo'] ?? 30);
    $descripcion = limpiar_entrada($_POST['descripcion'] ?? '');
    
    $errores = [];
    
    // Validaciones
    if (empty($nombre)) {
        $errores[] = 'El nombre de la asignatura es obligatorio';
    }
    
    if (empty($grupo)) {
        $errores[] = 'El grupo es obligatorio';
    }
    
    if (empty($profesor)) {
        $errores[] = 'El nombre del profesor es obligatorio';
    }
    
    if ($cupo_maximo < 1 || $cupo_maximo > 100) {
        $errores[] = 'El cupo debe estar entre 1 y 100';
    }
    
    if (empty($errores)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO asignaturas (nombre, grupo, profesor, cupo_maximo, descripcion) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$nombre, $grupo, $profesor, $cupo_maximo, $descripcion])) {
                header("Location: consulta-ce.php?msg=creado");
                exit;
            } else {
                $errores[] = 'Error al crear la asignatura';
            }
        } catch (PDOException $e) {
            $errores[] = 'Error en la base de datos: ' . $e->getMessage();
        }
    }
    
    if (!empty($errores)) {
        $tipo_mensaje = 'danger';
        $mensaje = '<ul class="mb-0">';
        foreach ($errores as $error) {
            $mensaje .= '<li>' . htmlspecialchars($error) . '</li>';
        }
        $mensaje .= '</ul>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Asignatura - Instituto Padres de la Patria</title>
    
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <!-- Título -->
                <div class="text-center mb-4">
                    <h2>
                        <i class="fas fa-plus-square text-success"></i>
                        Nueva Asignatura
                    </h2>
                    <p class="text-muted">Registra una nueva materia en el sistema</p>
                </div>

                <!-- Mensajes -->
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario -->
                <div class="card shadow">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            
                            <div class="row g-3">
                                
                                <!-- Nombre de la asignatura -->
                                <div class="col-12 col-md-6">
                                    <label for="nombre" class="form-label">
                                        Nombre de la Asignatura <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre" 
                                           name="nombre"
                                           value="<?php echo htmlspecialchars($nombre); ?>"
                                           required
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Nombre completo de la asignatura o materia">
                                </div>

                                <!-- Grupo -->
                                <div class="col-12 col-md-6">
                                    <label for="grupo" class="form-label">
                                        Grupo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="grupo" 
                                           name="grupo"
                                           value="<?php echo htmlspecialchars($grupo); ?>"
                                           required
                                           placeholder="Ej: 3A, 2B"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Identificador del grupo (ej: 3A, 2B)">
                                </div>

                                <!-- Profesor -->
                                <div class="col-12 col-md-6">
                                    <label for="profesor" class="form-label">
                                        Profesor <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="profesor" 
                                           name="profesor"
                                           value="<?php echo htmlspecialchars($profesor); ?>"
                                           required
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Nombre completo del profesor titular">
                                </div>

                                <!-- Cupo máximo -->
                                <div class="col-12 col-md-6">
                                    <label for="cupo_maximo" class="form-label">
                                        Cupo Máximo <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="cupo_maximo" 
                                           name="cupo_maximo"
                                           value="<?php echo $cupo_maximo; ?>"
                                           min="1" 
                                           max="100"
                                           required
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Número máximo de estudiantes permitidos (1-100)">
                                </div>

                                <!-- Descripción -->
                                <div class="col-12">
                                    <label for="descripcion" class="form-label">
                                        Descripción <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <textarea class="form-control" 
                                              id="descripcion" 
                                              name="descripcion" 
                                              rows="3"
                                              data-bs-toggle="tooltip" 
                                              data-bs-placement="top"
                                              title="Breve descripción del contenido de la asignatura"><?php echo htmlspecialchars($descripcion); ?></textarea>
                                </div>

                            </div>

                            <!-- Botones -->
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="consulta-ce.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Volver
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Guardar Asignatura
                                </button>
                            </div>

                        </form>
                    </div>
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
    
    <!-- Inicializar tooltips -->
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
