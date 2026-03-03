<?php
/**
 * ARCHIVO DE CONEXIÓN A LA BASE DE DATOS
 * usando PDO (PHP Data Objects) para mayor seguridad
 */

// Configuración de la base de datos
// IMPORTANTE: Cambia estos valores según tu configuración local
define('DB_HOST', 'localhost');
define('DB_NAME', 'padres_patria');
define('DB_USER', 'root');           // Cambiar según tu usuario de MySQL
define('DB_PASS', 'root');               // Cambiar según tu contraseña de MySQL
define('DB_CHARSET', 'utf8mb4');

// Opciones de PDO para mejor seguridad y manejo de errores
$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Crear conexión PDO
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        $opciones
    );
    
    // Configurar la zona horaria
    $pdo->exec("SET time_zone = '-06:00'"); // Ajustar según tu zona horaria
    
} catch (PDOException $e) {
    // En producción, NO mostrar el error real al usuario
    die("Error de conexión: " . $e->getMessage());
}

/**
 * Función auxiliar para limpiar datos de entrada
 */
function limpiar_entrada($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

/**
 * Función para validar email
 */
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Función para validar contraseña
 * Mínimo 8 caracteres, al menos una letra, un número y un carácter especial
 */
function validar_password($password) {
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/', $password);
}
?>
