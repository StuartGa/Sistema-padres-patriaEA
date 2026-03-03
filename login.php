<?php
/**
 * PÁGINA DE LOGIN
 * Para estudiantes y coordinadores
 */
session_start();

// Si ya está autenticado, redirigir
if (isset($_SESSION['usuario_id'])) {
    $redireccion = $_SESSION['tipo_usuario'] === 'ES' ? 'consulta-es.php' : 'consulta-ce.php';
    header("Location: $redireccion");
    exit;
}

require_once 'includes/conexion.php';

// Variables
$mensaje = '';
$tipo_mensaje = '';
$usuario = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = limpiar_entrada($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errores = [];
    
    // Validaciones básicas
    if (empty($usuario)) {
        $errores[] = 'El usuario o correo es obligatorio';
    }
    
    if (empty($password)) {
        $errores[] = 'La contraseña es obligatoria';
    }
    
    if (empty($errores)) {
        try {
            // Buscar usuario por nombre de usuario o correo
            $stmt = $pdo->prepare("
                SELECT id, nombre, apellido, usuario, password, tipo, activo 
                FROM usuarios 
                WHERE (usuario = ? OR correo = ?) AND activo = 1
            ");
            $stmt->execute([$usuario, $usuario]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Credenciales correctas, crear sesión
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre_usuario'] = $user['nombre'] . ' ' . $user['apellido'];
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['tipo_usuario'] = $user['tipo'];
                
                // Redirigir según tipo de usuario
                $redireccion = $user['tipo'] === 'ES' ? 'consulta-es.php' : 'consulta-ce.php';
                header("Location: $redireccion");
                exit;
            } else {
                $errores[] = 'Usuario o contraseña incorrectos';
            }
        } catch (PDOException $e) {
            $errores[] = 'Error en la base de datos: ' . $e->getMessage();
        }
    }
    
    // Mostrar errores
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
    <title>Iniciar Sesión - Instituto Padres de la Patria</title>
    
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
            <div class="col-lg-5 col-md-7">
                
                <!-- Título -->
                <div class="text-center mb-4">
                    <i class="fas fa-sign-in-alt fa-3x text-primary mb-3"></i>
                    <h2>Iniciar Sesión</h2>
                    <p class="text-muted">Accede a tu cuenta institucional</p>
                </div>

                <!-- Mensaje de error -->
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario de login -->
                <div class="card shadow">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            
                            <!-- Usuario -->
                            <div class="mb-3">
                                <label for="usuario" class="form-label">
                                    <i class="fas fa-user me-1"></i>Usuario o Correo
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="usuario" 
                                       name="usuario"
                                       value="<?php echo htmlspecialchars($usuario); ?>"
                                       required
                                       autofocus
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top"
                                       title="Ingresa tu nombre de usuario o correo electrónico">
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password"
                                       required
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top"
                                       title="Ingresa tu contraseña">
                            </div>

                            <!-- Recordar sesión (opcional) -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="recordar" name="recordar">
                                <label class="form-check-label" for="recordar">
                                    Recordar mi sesión
                                </label>
                            </div>

                            <!-- Botón de login -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-1"></i>Ingresar
                                </button>
                            </div>

                            <hr class="my-4">

                            <!-- Enlaces adicionales -->
                            <div class="text-center">
                                <p class="mb-2">
                                    <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                                </p>
                                <p class="mb-0">
                                    ¿No tienes cuenta? 
                                    <a href="registro-es.php" class="fw-bold">Regístrate aquí</a>
                                </p>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Credenciales de prueba -->
                <div class="alert alert-info mt-4" role="alert">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-1"></i>Credenciales de prueba
                    </h6>
                    <hr>
                    <p class="mb-1"><strong>Coordinador:</strong></p>
                    <p class="small mb-2">Usuario: <code>admin</code> / Contraseña: <code>Admin123*</code></p>
                    <p class="mb-1"><strong>Estudiante:</strong></p>
                    <p class="small mb-0">Usuario: <code>estudiante1</code> / Contraseña: <code>Alumno123*</code></p>
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
