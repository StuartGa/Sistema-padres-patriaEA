<?php
/**
 * ARCHIVO DE CONEXIÓN A LA BASE DE DATOS - EJEMPLO
 * 
 * INSTRUCCIONES PARA INFINITYFREE:
 * 1. Renombra este archivo a 'conexion.php' después de subirlo
 * 2. Modifica las constantes DB_HOST, DB_NAME, DB_USER y DB_PASS
 *    con los datos que te proporciona InfinityFree en el panel de control
 * 
 * NOTA: En InfinityFree el host suele ser algo como: sqlXXX.infinityfree.com
 *       El nombre de usuario y base de datos tienen un prefijo único
 */

// Configuración de la base de datos para InfinityFree
// CAMBIA ESTOS VALORES por los que aparecen en tu panel de InfinityFree
define('DB_HOST', 'sql000.infinityfree.com');     // Ejemplo: sql123.infinityfree.com
define('DB_NAME', 'epiz_12345678_padres_patria'); // Ejemplo: epiz_12345678_tunombre
define('DB_USER', 'epiz_12345678');               // Usuario de la base de datos
define('DB_PASS', 'tu_password_aqui');            // Contraseña de la base de datos
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
    // Puedes comentar esta línea una vez que la conexión funcione
    die("Error de conexión: " . $e->getMessage());
    
    // En producción, usa esto en su lugar:
    // die("Error de conexión a la base de datos. Por favor, intenta más tarde.");
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
 * Función para generar tokens seguros
 */
function generar_token($longitud = 32) {
    return bin2hex(random_bytes($longitud));
}
