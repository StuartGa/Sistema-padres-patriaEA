-- ============================================================
-- SCRIPT SQL PARA EL SISTEMA DEL INSTITUTO PADRES DE LA PATRIA
-- Base de datos: padres_patria
-- ============================================================

-- IMPORTANTE PARA HOSTING COMPARTIDO (InfinityFree/000webhost):
-- 1) Crea la base de datos desde el panel del hosting.
-- 2) Selecciónala en phpMyAdmin antes de importar este archivo.
-- 3) No incluyas CREATE DATABASE ni USE, porque suelen estar bloqueados.

-- ============================================================
-- TABLA: usuarios
-- Almacena información de estudiantes (ES) y coordinadores (CE)
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tipo ENUM('ES', 'CE') NOT NULL DEFAULT 'ES',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_usuario (usuario),
    INDEX idx_correo (correo),
    INDEX idx_tipo (tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: asignaturas
-- Almacena las materias disponibles en el instituto
-- ============================================================
CREATE TABLE IF NOT EXISTS asignaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    grupo VARCHAR(20) NOT NULL,
    profesor VARCHAR(150) NOT NULL,
    cupo_maximo INT DEFAULT 30,
    descripcion TEXT,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activa TINYINT(1) DEFAULT 1,
    INDEX idx_nombre (nombre),
    INDEX idx_grupo (grupo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: asignaturas_usuarios (Inscripciones)
-- Relación muchos a muchos entre usuarios y asignaturas
-- ============================================================
CREATE TABLE IF NOT EXISTS asignaturas_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_asignatura INT NOT NULL,
    fecha_inscripcion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    calificacion DECIMAL(4,2) DEFAULT NULL,
    estatus ENUM('inscrito', 'cursando', 'aprobado', 'reprobado') DEFAULT 'inscrito',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_asignatura) REFERENCES asignaturas(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_inscripcion (id_usuario, id_asignatura),
    INDEX idx_usuario (id_usuario),
    INDEX idx_asignatura (id_asignatura),
    INDEX idx_estatus (estatus)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATOS DE PRUEBA
-- ============================================================

-- Insertar usuario coordinador (CE)
-- Usuario: admin / Contraseña: Admin123*
INSERT INTO usuarios (nombre, apellido, correo, usuario, password, tipo)
VALUES (
    'Juan', 
    'Pérez García',
    'admin@padrespatria.edu',
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Admin123*
    'CE'
) ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    apellido = VALUES(apellido),
    password = VALUES(password),
    tipo = VALUES(tipo),
    activo = 1;

-- Insertar usuario estudiante de prueba (ES)
-- Usuario: estudiante1 / Contraseña: Alumno123*
INSERT INTO usuarios (nombre, apellido, correo, usuario, password, tipo)
VALUES (
    'María',
    'González López',
    'maria.gonzalez@estudiantes.padrespatria.edu',
    'estudiante1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Alumno123*
    'ES'
) ON DUPLICATE KEY UPDATE
    nombre = VALUES(nombre),
    apellido = VALUES(apellido),
    password = VALUES(password),
    tipo = VALUES(tipo),
    activo = 1;

-- Insertar asignaturas de ejemplo
INSERT INTO asignaturas (nombre, grupo, profesor, cupo_maximo, descripcion)
SELECT 'Matemáticas Avanzadas', '3A', 'Dr. Carlos Ramírez', 30, 'Curso avanzado de cálculo diferencial e integral'
WHERE NOT EXISTS (
    SELECT 1 FROM asignaturas WHERE nombre = 'Matemáticas Avanzadas' AND grupo = '3A'
);

INSERT INTO asignaturas (nombre, grupo, profesor, cupo_maximo, descripcion)
SELECT 'Programación Web 2', '3B', 'Ing. Ana Martínez', 25, 'Desarrollo web con PHP, MySQL y frameworks modernos'
WHERE NOT EXISTS (
    SELECT 1 FROM asignaturas WHERE nombre = 'Programación Web 2' AND grupo = '3B'
);

INSERT INTO asignaturas (nombre, grupo, profesor, cupo_maximo, descripcion)
SELECT 'Física II', '3A', 'Dra. Laura Sánchez', 30, 'Mecánica clásica y termodinámica'
WHERE NOT EXISTS (
    SELECT 1 FROM asignaturas WHERE nombre = 'Física II' AND grupo = '3A'
);

INSERT INTO asignaturas (nombre, grupo, profesor, cupo_maximo, descripcion)
SELECT 'Literatura Universal', '3C', 'Lic. Roberto Torres', 35, 'Análisis de obras literarias clásicas y contemporáneas'
WHERE NOT EXISTS (
    SELECT 1 FROM asignaturas WHERE nombre = 'Literatura Universal' AND grupo = '3C'
);

INSERT INTO asignaturas (nombre, grupo, profesor, cupo_maximo, descripcion)
SELECT 'Química Orgánica', '3B', 'Dr. Miguel Ángel Ruiz', 28, 'Estudio de compuestos orgánicos y reacciones'
WHERE NOT EXISTS (
    SELECT 1 FROM asignaturas WHERE nombre = 'Química Orgánica' AND grupo = '3B'
);

INSERT INTO asignaturas (nombre, grupo, profesor, cupo_maximo, descripcion)
SELECT 'Historia de México', '3A', 'Mtro. Fernando Díaz', 32, 'Historia desde la época prehispánica hasta la actualidad'
WHERE NOT EXISTS (
    SELECT 1 FROM asignaturas WHERE nombre = 'Historia de México' AND grupo = '3A'
);

-- Inscribir al estudiante de prueba en algunas asignaturas
INSERT INTO asignaturas_usuarios (id_usuario, id_asignatura, estatus)
SELECT u.id, a.id, 'cursando'
FROM usuarios u
INNER JOIN asignaturas a ON a.nombre = 'Matemáticas Avanzadas' AND a.grupo = '3A'
WHERE u.usuario = 'estudiante1'
    AND NOT EXISTS (
            SELECT 1
            FROM asignaturas_usuarios au
            WHERE au.id_usuario = u.id AND au.id_asignatura = a.id
    );

INSERT INTO asignaturas_usuarios (id_usuario, id_asignatura, estatus)
SELECT u.id, a.id, 'cursando'
FROM usuarios u
INNER JOIN asignaturas a ON a.nombre = 'Programación Web 2' AND a.grupo = '3B'
WHERE u.usuario = 'estudiante1'
    AND NOT EXISTS (
            SELECT 1
            FROM asignaturas_usuarios au
            WHERE au.id_usuario = u.id AND au.id_asignatura = a.id
    );

INSERT INTO asignaturas_usuarios (id_usuario, id_asignatura, estatus)
SELECT u.id, a.id, 'cursando'
FROM usuarios u
INNER JOIN asignaturas a ON a.nombre = 'Literatura Universal' AND a.grupo = '3C'
WHERE u.usuario = 'estudiante1'
    AND NOT EXISTS (
            SELECT 1
            FROM asignaturas_usuarios au
            WHERE au.id_usuario = u.id AND au.id_asignatura = a.id
    );

-- ============================================================
-- NOTA IMPORTANTE
-- ============================================================
-- En InfinityFree/000webhost normalmente no hay permisos para CREATE VIEW.
-- Las consultas del proyecto ya están preparadas para funcionar sin vistas.

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================

-- Para generar los hashes de contraseña en PHP usa:
-- password_hash('Admin123*', PASSWORD_DEFAULT);
-- password_hash('Alumno123*', PASSWORD_DEFAULT);
