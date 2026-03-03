# Sistema de Gestión Académica - Instituto Padres de la Patria

Sistema web completo para la gestión de asignaturas, inscripciones y usuarios del Instituto de Educación Secundaria "Padres de la Patria".

## 🎓 Información del Proyecto

**Materia:** Programación Web 2 (DPW2)  
**Institución:** UNADM  
**Unidad:** 3 - Actividad 3  
**Tecnologías:** PHP 8.x, MySQL, Bootstrap 5, JavaScript

---

## 📋 Características Principales

### Funcionalidades Generales
- ✅ Sistema de autenticación (login/logout)
- ✅ Registro de usuarios estudiantes (ES)
- ✅ Gestión de perfiles
- ✅ Diseño responsive con Bootstrap 5
- ✅ Navbar fija en todas las páginas
- ✅ Tooltips informativos en formularios
- ✅ Validaciones del lado del servidor y cliente

### Para Estudiantes (ES)
- Consultar asignaturas inscritas
- Inscribirse a nuevas asignaturas
- Ver calificaciones y estatus
- Visualizar cupos disponibles
- Perfil de usuario

### Para Coordinadores (CE)
- Gestión completa de asignaturas (CRUD)
- Visualizar todas las inscripciones
- Reportes estadísticos
- Control de cupos
- Eliminación con confirmación modal

---

## 🛠️ Requisitos del Sistema

### Software Necesario
- **PHP:** 8.0 o superior
- **MySQL:** 5.7 o superior (o MariaDB 10.x)
- **Servidor Web:** Apache (con mod_rewrite) o Nginx
- **Navegador:** Moderno (Chrome, Firefox, Edge, Safari)

### Recomendación
Usa **XAMPP**, **WAMP**, **MAMP** o **Laragon** para desarrollo local.

---

## 📦 Instalación

### Paso 1: Descargar el Proyecto
```bash
# Si usas Git
git clone [URL-del-repositorio]
cd Act3-SistemaPadresPatria

# O descarga el ZIP y extrae en la carpeta htdocs (XAMPP) o www (WAMP)
```

### Paso 2: Configurar la Base de Datos

1. **Abrir phpMyAdmin** o tu cliente MySQL favorito

2. **Importar el script SQL:**
   - Abre el archivo `database.sql`
   - Ejecuta todo el contenido en tu servidor MySQL
   - Esto creará:
     - Base de datos `padres_patria`
     - Tablas: `usuarios`, `asignaturas`, `asignaturas_usuarios`
     - Vista: `vista_inscripciones`
     - Datos de prueba (usuarios y asignaturas)

3. **Usuarios de Prueba Creados:**

**Coordinador:**
- Usuario: `admin`
- Contraseña: `Admin123*`

**Estudiante:**
- Usuario: `estudiante1`
- Contraseña: `Alumno123*`

### Paso 3: Configurar Conexión a BD

Edita el archivo `includes/conexion.php` y ajusta las credenciales:

```php
define('DB_HOST', 'localhost');      // Host de MySQL
define('DB_NAME', 'padres_patria');  // Nombre de la BD
define('DB_USER', 'root');           // Tu usuario de MySQL
define('DB_PASS', '');               // Tu contraseña de MySQL
```

### Paso 4: Iniciar el Servidor

#### Con XAMPP/WAMP:
1. Inicia Apache y MySQL
2. Accede a: `http://localhost/Act3-SistemaPadresPatria/`

#### Con servidor PHP integrado:
```bash
cd Act3-SistemaPadresPatria
php -S localhost:8000
```
Luego accede a: `http://localhost:8000/`

---

## 📁 Estructura del Proyecto

```
Act3-SistemaPadresPatria/
│
├── css/
│   └── estilos.css              # Estilos personalizados
│
├── js/
│   └── main.js                  # JavaScript principal (tooltips, validaciones)
│
├── includes/
│   ├── conexion.php             # Conexión PDO a MySQL
│   └── navbar.php               # Barra de navegación reusable
│
├── database.sql                 # Script completo de la base de datos
│
├── index.php                    # Página de inicio
├── login.php                    # Iniciar sesión
├── logout.php                   # Cerrar sesión
├── registro-es.php              # Registro de estudiantes
│
├── consulta-es.php              # Panel de estudiante
├── consulta-ce.php              # Panel de coordinador
│
├── inscribirse.php              # Inscripción a asignaturas (ES)
├── perfil.php                   # Perfil de usuario
│
├── asignatura-alta.php          # Crear asignatura (CE)
├── asignatura-editar.php        # Editar asignatura (CE)
├── reporte-inscripciones.php    # Reportes (CE)
│
└── README.md                    # Este archivo
```

---

## 🚀 Uso del Sistema

### Flujo de Trabajo para Estudiantes

1. **Registro:**
   - Accede a `registro-es.php`
   - Completa el formulario (todos los campos con tooltips)
   - Layout responsive: 3 columnas (lg), 2 (md), 1 (sm)

2. **Iniciar Sesión:**
   - Accede a `login.php`
   - Ingresa usuario/correo y contraseña
   - Redirige automáticamente a `consulta-es.php`

3. **Inscribirse a Asignaturas:**
   - Desde tu panel, click en "Inscribirse"
   - Selecciona las materias disponibles
   - Visualiza cupos en tiempo real

4. **Consultar Asignaturas:**
   - Tabla con todas tus materias inscritas
   - Clases Bootstrap: `table-striped`, `table-hover`, `table-responsive`
   - Ver calificaciones y estatus

### Flujo de Trabajo para Coordinadores

1. **Iniciar Sesión:**
   - Usuario: `admin` / Contraseña: `Admin123*`
   - Redirige a `consulta-ce.php`

2. **Gestionar Asignaturas:**
   - **Crear:** Click en "Nueva Asignatura" → completar formulario
   - **Editar:** Click en botón amarillo (warning) con icono de lápiz
   - **Eliminar:** Click en botón rojo (danger) → confirmación con modal

3. **Ver Reportes:**
   - Click en "Reportes" en la navbar
   - Tabla completa de inscripciones
   - Estadísticas generales
   - Función de búsqueda en tiempo real

---

## 🎨 Especificaciones de Diseño

### Bootstrap 5
- **CDN:** `https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css`
- **Navbar:** `navbar`, `navbar-expand-lg`, `navbar-dark`, `bg-dark`, `fixed-top`
- **Grid Responsivo:**
  - Desktop (lg): 3 columnas
  - Tablet (md): 2 columnas
  - Móvil (sm): 1 columna

### Tooltips
- En todos los campos de formularios
- Inicializados con JavaScript:
```javascript
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
```

### Tablas
- `table`, `table-striped`, `table-hover`, `table-responsive`
- Botones: `btn-sm`, `btn-warning` (editar), `btn-danger` (eliminar)

---

## 🔒 Seguridad

### Implementadas
- ✅ **PDO con prepared statements:** Protección contra SQL Injection
- ✅ **password_hash():** Encriptación bcrypt de contraseñas
- ✅ **password_verify():** Verificación segura de credenciales
- ✅ **htmlspecialchars():** Prevención de XSS
- ✅ **Validaciones del servidor:** En todos los formularios
- ✅ **Sesiones seguras:** Control de acceso por roles
- ✅ **Validación de email y contraseñas:** Expresiones regulares

### Validación de Contraseña
- Mínimo 8 caracteres
- Al menos una letra
- Al menos un número
- Al menos un carácter especial (#, *, ?, etc.)

---

## 🗃️ Base de Datos

### Tablas

#### usuarios
```sql
id, nombre, apellido, correo (UNIQUE), usuario (UNIQUE), 
password (hash), tipo (ENUM 'ES'/'CE'), fecha_registro, activo
```

#### asignaturas
```sql
id, nombre, grupo, profesor, cupo_maximo, descripcion, 
fecha_creacion, activa
```

#### asignaturas_usuarios (relación N:M)
```sql
id, id_usuario (FK), id_asignatura (FK), fecha_inscripcion, 
calificacion, estatus (ENUM)
```

### Vista
**vista_inscripciones:** JOIN completo entre usuarios, asignaturas y relación

---

## 📝 Notas Importantes

1. **PHP 8.0+:** Usa características modernas (match, tipos de unión, etc.)
2. **UTF-8:** Todos los archivos en UTF-8 para caracteres especiales
3. **Responsive:** Probado en móvil, tablet y desktop
4. **Modales:** Confirmación antes de eliminar
5. **Feedback visual:** Alerts con colores según acción

---

## 🐛 Solución de Problemas

### Error de Conexión a BD
- Verifica que MySQL esté corriendo
- Confirma credenciales en `includes/conexion.php`
- Verifica que la BD `padres_patria` exista

### Tooltips no Funcionan
- Verifica que `main.js` esté cargado
- Abre la consola del navegador (F12) para ver errores
- Confirma que Bootstrap JS esté cargado

### Sesión no Persiste
- Verifica que `session_start()` esté al inicio de cada archivo PHP
- Revisa permisos de carpeta de sesiones del servidor

### Estilos no se Aplican
- Verifica rutas de archivos CSS
- Limpia caché del navegador (Ctrl+Shift+R)
- Confirma que `estilos.css` exista en la carpeta `css/`

---

## ✅ Checklist de Requisitos Cumplidos

- [x] Base de datos MySQL con 3 tablas y relaciones
- [x] Script SQL completo funcional
- [x] PHP 8.x con PDO
- [x] Bootstrap 5 vía CDN
- [x] Navbar fija y responsiva en todas las páginas
- [x] Registro de usuarios ES con formulario
- [x] Grid responsivo: 3 cols (lg), 2 (md), 1 (sm)
- [x] Tooltips en todos los controles de formularios
- [x] Login con autenticación y sesiones
- [x] Redirección según tipo de usuario (ES/CE)
- [x] Tablas con clases Bootstrap (striped, hover, responsive)
- [x] Botones de acción (editar/borrar) en lugar de enlaces
- [x] CRUD completo de asignaturas para CE
- [x] Consulta de asignaturas inscritas para ES
- [x] Validaciones del lado del servidor
- [x] Código comentado y limpio
- [x] Archivo includes/conexion.php
- [x] Archivo includes/navbar.php reutilizable

---

## 👨‍💻 Desarrollo

**Desarrollado por:** Stuart García  
**Fecha:** Marzo 2026  
**Versión:** 1.0.0

---

## 📄 Licencia

Este proyecto es un trabajo académico para la UNADM.

---

## 🆘 Soporte

Para dudas o problemas:
1. Revisa la sección "Solución de Problemas"
2. Verifica la consola del navegador (F12)
3. Revisa logs de PHP (en XAMPP: xampp/logs/php_error_log.txt)

---

**¡Listo para usar!** 🚀

Recuerda cambiar las contraseñas de prueba en un entorno de producción.
