<?php
/**
 * PÁGINA DE REGISTRO PARA ESTUDIANTES (ES)
 * Con formulario usando grid de Bootstrap 5
 * Tooltips en los campos
 * Validaciones del lado del servidor
 */
session_start();
require_once 'includes/conexion.php';
require_once 'includes/recaptcha_config.php';

// Variables para mensajes y valores del formulario
$mensaje = '';
$tipo_mensaje = '';
$nombre = $apellido = $correo = $usuario = '';

// Procesar formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ============================================================
    // VERIFICACIÓN DE reCAPTCHA v3
    // ============================================================
    if (!isset($_POST['recaptcha_token']) || empty($_POST['recaptcha_token'])) {
        $tipo_mensaje = 'danger';
        $mensaje = 'Error: No se recibió el token de seguridad. Intenta nuevamente.';
    } else {
        $recaptcha_token = $_POST['recaptcha_token'];
        $recaptcha_secret = RECAPTCHA_SECRET_KEY;
        
        // Verificar token con Google
        $verify_url = "https://www.google.com/recaptcha/api/siteverify";
        $verify_data = http_build_query([
            'secret' => $recaptcha_secret,
            'response' => $recaptcha_token
        ]);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $verify_data
            ]
        ]);
        
        $verify_response = file_get_contents($verify_url, false, $context);
        $responseData = json_decode($verify_response, true);
        
        // Validar respuesta reCAPTCHA
        $recaptcha_valid = false;
        if ($responseData["success"] === true && 
            $responseData["score"] >= RECAPTCHA_SCORE_THRESHOLD && 
            $responseData["action"] === RECAPTCHA_ACTION) {
            $recaptcha_valid = true;
        }
        
        if (!$recaptcha_valid) {
            $tipo_mensaje = 'danger';
            $mensaje = 'No se pudo validar la solicitud de seguridad. Intenta nuevamente.';
        }
    }
    
    // ============================================================
    // Si reCAPTCHA es válido, proceder con validaciones de registro
    // ============================================================
    if (isset($recaptcha_valid) && $recaptcha_valid) {
        // Recibir y limpiar datos
        $nombre = limpiar_entrada($_POST['nombre'] ?? '');
        $apellido = limpiar_entrada($_POST['apellido'] ?? '');
        $correo = limpiar_entrada($_POST['correo'] ?? '');
        $usuario = limpiar_entrada($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        // Array de errores
        $errores = [];
        
        // Validaciones
        if (empty($nombre)) {
            $errores[] = 'El nombre es obligatorio';
        }
    
        if (empty($apellido)) {
            $errores[] = 'El apellido es obligatorio';
        }
        
        if (empty($correo) || !validar_email($correo)) {
            $errores[] = 'El correo electrónico no es válido';
        }
        
        if (empty($usuario) || strlen($usuario) < 4) {
            $errores[] = 'El usuario debe tener al menos 4 caracteres';
        }
        
        if (empty($password) || !validar_password($password)) {
            $errores[] = 'La contraseña debe tener mínimo 8 caracteres, incluir letras, números y al menos un caracter especial (#,*,?, etc.)';
        }
        
        if ($password !== $password_confirm) {
            $errores[] = 'Las contraseñas no coinciden';
        }
        
        // Si no hay errores, proceder al registro
        if (empty($errores)) {
            try {
                // Verificar si el usuario o correo ya existen
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = ? OR correo = ?");
                $stmt->execute([$usuario, $correo]);
                
                if ($stmt->rowCount() > 0) {
                    $errores[] = 'El usuario o correo electrónico ya están registrados';
                } else {
                    // Encriptar contraseña
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insertar nuevo usuario
                    $stmt = $pdo->prepare("
                        INSERT INTO usuarios (nombre, apellido, correo, usuario, password, tipo) 
                        VALUES (?, ?, ?, ?, ?, 'ES')
                    ");
                    
                    if ($stmt->execute([$nombre, $apellido, $correo, $usuario, $password_hash])) {
                        $tipo_mensaje = 'success';
                        $mensaje = '¡Registro exitoso! El usuario se ha almacenado correctamente. Ahora puedes iniciar sesión.';
                        // Limpiar variables
                        $nombre = $apellido = $correo = $usuario = '';
                        
                        // Redirigir después de 2 segundos
                        header("refresh:2;url=login.php");
                    } else {
                        $errores[] = 'Error al registrar el usuario';
                    }
                }
            } catch (PDOException $e) {
                $errores[] = 'Error en la base de datos: ' . $e->getMessage();
            }
        }
        
        // Si hay errores, mostrarlos
        if (!empty($errores)) {
            $tipo_mensaje = 'danger';
            $mensaje = '<ul class="mb-0">';
            foreach ($errores as $error) {
                $mensaje .= '<li>' . htmlspecialchars($error) . '</li>';
            }
            $mensaje .= '</ul>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Estudiante - Instituto Padres de la Patria</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/estilos.css">
    
    <!-- ============================================================ -->
    <!-- reCAPTCHA v3 - Script de Google -->
    <!-- ============================================================ -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo RECAPTCHA_SITE_KEY; ?>"></script>
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
                        <i class="fas fa-user-plus text-primary"></i>
                        Registro de Estudiante
                    </h2>
                    <p class="text-muted">Completa el formulario para crear tu cuenta</p>
                </div>

                <!-- Mensaje de éxito/error -->
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario de registro -->
                <div class="card shadow">
                    <div class="card-body p-4">
                        <form method="POST" action="" novalidate>
                            
                            <!-- Grid responsivo: 3 columnas en lg, 2 en md, 1 en sm -->
                            <div class="row g-3">
                                
                                <!-- Nombre -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="nombre" class="form-label">
                                        Nombre <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="<?php echo htmlspecialchars($nombre); ?>"
                                           required
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Ingresa tu nombre completo">
                                </div>

                                <!-- Apellido -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="apellido" class="form-label">
                                        Apellido <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="apellido" 
                                           name="apellido"
                                           value="<?php echo htmlspecialchars($apellido); ?>"
                                           required
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Ingresa tus apellidos completos">
                                </div>

                                <!-- Correo -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="correo" class="form-label">
                                        Correo Electrónico <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="correo" 
                                           name="correo"
                                           value="<?php echo htmlspecialchars($correo); ?>"
                                           required
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Usa tu correo institucional o personal válido">
                                </div>

                                <!-- Usuario -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="usuario" class="form-label">
                                        Nombre de Usuario <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="usuario" 
                                           name="usuario"
                                           value="<?php echo htmlspecialchars($usuario); ?>"
                                           required
                                           minlength="4"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Mínimo 4 caracteres, sin espacios. Será tu identificador de acceso">
                                </div>

                                <!-- Contraseña -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="password" class="form-label">
                                        Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password"
                                           required
                                           minlength="8"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Longitud mínima de 8 posiciones, con letras y números y al menos un carácter especial (#,*,?, etc.)">
                                </div>

                                <!-- Confirmar Contraseña -->
                                <div class="col-12 col-md-6 col-lg-4">
                                    <label for="password_confirm" class="form-label">
                                        Confirmar Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirm" 
                                           name="password_confirm"
                                           required
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top"
                                           title="Debe coincidir con la contraseña anterior">
                                </div>

                            </div>

                            <!-- ============================================================ -->
                            <!-- Campo oculto para token de reCAPTCHA v3 -->
                            <!-- ============================================================ -->
                            <input type="hidden" name="recaptcha_token" id="recaptcha_token">

                            <!-- Botones -->
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Registrarse
                                </button>
                            </div>

                            <hr class="my-4">

                            <!-- Enlace a login -->
                            <p class="text-center mb-0">
                                ¿Ya tienes cuenta? 
                                <a href="login.php">Inicia sesión aquí</a>
                            </p>

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
    
    <!-- ============================================================ -->
    <!-- reCAPTCHA v3 - Ejecutar validación de registro -->
    <!-- ============================================================ -->
    <script>
        // Ejecutar reCAPTCHA v3 cuando la página carga
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo RECAPTCHA_SITE_KEY; ?>', {action: '<?php echo RECAPTCHA_ACTION; ?>'}).then(function(token) {
                // Colocar el token en el campo oculto
                document.getElementById('recaptcha_token').value = token;
            });
        });
    </script>
    
    <!-- Inicializar tooltips -->
    <script>
        // Inicializar todos los tooltips de la página
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>
