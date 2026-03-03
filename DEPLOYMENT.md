# 🚀 Guía de Deployment en InfinityFree

Esta guía te ayudará a subir el Sistema de Padres de la Patria a InfinityFree de manera gratuita.

## 📋 Requisitos Previos

- Cuenta en InfinityFree (https://infinityfree.net)
- Cliente FTP (FileZilla, Cyberduck, o WinSCP)
- Todos los archivos del proyecto

## 🔧 Paso 1: Crear Cuenta en InfinityFree

1. Ve a https://infinityfree.net
2. Haz clic en "Sign Up"
3. Completa el registro y verifica tu email

## 🌐 Paso 2: Crear un Sitio Web

1. En tu panel de InfinityFree, haz clic en "Create Account"
2. Elige un subdominio gratuito o usa tu propio dominio
3. Ingresa una etiqueta/nombre para tu sitio
4. Haz clic en "Create Account"
5. **Anota las credenciales FTP y MySQL** que aparecen

## 📁 Paso 3: Subir Archivos via FTP

### Opción A: FileZilla (Recomendado)

1. Descarga FileZilla desde https://filezilla-project.org
2. Abre FileZilla y conecta con:
   - **Host**: `ftpupload.net` o la URL que te proporcionó InfinityFree
   - **Usuario**: Tu usuario FTP (ejemplo: `epiz_12345678`)
   - **Contraseña**: Tu contraseña FTP
   - **Puerto**: 21

3. Una vez conectado:
   - En el panel derecho, navega a la carpeta `htdocs`
   - Sube TODOS los archivos del proyecto a esta carpeta
   - Asegúrate de mantener la estructura de carpetas:
     ```
     htdocs/
     ├── css/
     ├── includes/
     ├── js/
     ├── index.php
     ├── login.php
     └── ... (todos los demás archivos)
     ```

### Opción B: File Manager (desde el panel)

1. Ve al panel de control de InfinityFree
2. Haz clic en "File Manager"
3. Navega a `htdocs`
4. Sube los archivos (puede ser más lento)

## 🗄️ Paso 4: Crear y Configurar la Base de Datos

### Crear la Base de Datos

1. En el panel de InfinityFree, busca "MySQL Databases"
2. Haz clic en "MySQL Databases"
3. Crea una nueva base de datos con el nombre `padres_patria` (o el que prefieras)
4. **Anota**:
   - Nombre del servidor MySQL (ejemplo: `sql123.infinityfree.com`)
   - Nombre de usuario
   - Nombre de la base de datos
   - Contraseña

### Importar la Base de Datos

1. En el panel, haz clic en "phpMyAdmin"
2. Selecciona tu base de datos en el panel izquierdo
3. Haz clic en la pestaña "Importar"
4. Selecciona el archivo `database.sql`
5. Haz clic en "Continuar"

## ⚙️ Paso 5: Configurar el Archivo de Conexión

1. En tu servidor FTP, navega a `htdocs/includes/`
2. Edita el archivo `conexion.php` con los datos correctos:

```php
define('DB_HOST', 'sql123.infinityfree.com');     // Tu host MySQL
define('DB_NAME', 'epiz_12345678_padres_patria'); // Tu nombre de BD
define('DB_USER', 'epiz_12345678');               // Tu usuario MySQL
define('DB_PASS', 'tu_contraseña');               // Tu contraseña MySQL
```

**IMPORTANTE**: Puedes usar el archivo `conexion.example.php` como referencia.

## 🔐 Paso 6: Verificar Permisos

1. Asegúrate de que `.htaccess` se haya subido correctamente
2. Verifica que los archivos tengan permisos 644
3. Verifica que las carpetas tengan permisos 755

## ✅ Paso 7: Probar la Aplicación

1. Abre tu navegador
2. Ve a tu URL (ejemplo: `http://tu-sitio.infinityfreeapp.com`)
3. Deberías ver la página de inicio
4. Prueba:
   - Registro de estudiantes
   - Login
   - Inscripción a asignaturas

## 🐛 Solución de Problemas

### Error: "Can't connect to MySQL server"
- Verifica que los datos de conexión en `conexion.php` sean correctos
- Asegúrate de usar el host correcto (sqlXXX.infinityfree.com)

### Error 403 o 404
- Verifica que los archivos estén en la carpeta `htdocs`
- Revisa que `index.php` exista y tenga permisos correctos

### Página en blanco
- Activa la visualización de errores temporalmente
- Revisa los logs de error en el panel de InfinityFree

### Caracteres Especiales (Ñ, Acentos)
- Verifica que la base de datos use `utf8mb4`
- Asegúrate de que todos los archivos PHP tengan `charset=UTF-8`

## 📊 Limitaciones de InfinityFree

- 5 GB de almacenamiento
- 400 bases de datos MySQL
- Sin soporte para envío de emails nativamente (usa APIs externas)
- Los sitios se suspenden tras 90 días de inactividad

## 🔄 Actualizaciones Futuras

Para actualizar el sitio:
1. Haz los cambios localmente
2. Sube los archivos modificados via FTP
3. Si hay cambios en la BD, ejecuta las queries en phpMyAdmin

## 📞 Soporte

- Documentación InfinityFree: https://forum.infinityfree.net
- GitHub del proyecto: https://github.com/StuartGa/Sistema-padres-patriaEA

---

## ✨ Checklist Final

- [ ] Cuenta de InfinityFree creada
- [ ] Sitio web creado
- [ ] Archivos subidos a `htdocs/`
- [ ] Base de datos creada
- [ ] `database.sql` importado
- [ ] `conexion.php` configurado con credenciales correctas
- [ ] Sitio probado y funcionando
- [ ] Login y registro funcionan
- [ ] Inscripciones funcionan

¡Listo! Tu aplicación debería estar funcionando en InfinityFree. 🎉
