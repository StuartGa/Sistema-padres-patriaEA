# Integración de reCAPTCHA v3 - Guía Completa

## 📋 Descripción

Se ha integrado **Google reCAPTCHA v3** al formulario de registro de estudiantes para prevenir registros automatizados (bots). El sistema valida automáticamente cada envío del formulario sin interferir en la experiencia del usuario.

---

## 🔐 ¿Qué es reCAPTCHA v3?

- **Invisible**: No muestra captchas visuales al usuario
- **Basado en puntuación**: Asigna una puntuación entre 0.0 (probable bot) y 1.0 (probable humano)
- **Umbral ajustable**: Decimos al servidor qué máximo score aceptar
- **Sin fricción**: El usuario simplemente completa el formulario normalmente

---

## 🚀 Pasos de Configuración

### Paso 1: Obtener las Claves de Google

1. Ve a **https://www.google.com/recaptcha/admin**
2. Inicia sesión con tu cuenta de Google (crea una si no tienes)
3. Haz clic en **"+" (Create)**

### Paso 2: Configurar un nuevo sitio

Completa el formulario con:

```
Label (Etiqueta):           Instituto Padres Patria (o nombre de tu sitio)
reCAPTCHA type:             ★ reCAPTCHA v3 (muy importante, selecciona v3)
Domains (Dominios):         institutoea.fwh.is (tu dominio real)
```

### Paso 3: Aceptar términos

- Marca: "Accept the reCAPTCHA Terms of Service"
- Haz clic en **"Create"**

### Paso 4: Copiar las claves

En la siguiente pantalla verás:

```
Site Key (Clave del Sitio):     6Lc...xxxxx
Secret Key (Clave Secreta):     6Lc...xxxxx
```

**⚠️ IMPORTANTE:**
- **Site Key**: Se usa en el frontend (está visible, no hay problema)
- **Secret Key**: Se usa SOLO en el servidor (mantén en secreto, NO la compartas)

---

## 📝 Configuración en el Proyecto

### Paso 1: Crear el archivo de configuración

1. Ve a la carpeta `includes/`
2. Copia el archivo `recaptcha_config.example.php` y renómbralo a `recaptcha_config.php`
3. Abre `recaptcha_config.php` y reemplaza:

```php
define('RECAPTCHA_SITE_KEY', '6Lc...xxxxx');    // Tu Site Key aquí
define('RECAPTCHA_SECRET_KEY', '6Lc...xxxxx');  // Tu Secret Key aquí
```

### Paso 2: Verificar que está protegido

El archivo `recaptcha_config.php` está en `.gitignore`, así que **nunca se subirá a GitHub**.

---

## 🎯 Cómo Funciona en el Proyecto

### Frontend (registro-es.php)

```html
<!-- Script de Google carga automáticamente -->
<script src="https://www.google.com/recaptcha/api.js?render=TU_SITE_KEY"></script>

<!-- Token se guarda en este campo oculto -->
<input type="hidden" name="recaptcha_token" id="recaptcha_token">

<!-- JavaScript genera token cuando carga la página -->
<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('TU_SITE_KEY', {action: 'registro'}).then(function(token) {
            document.getElementById('recaptcha_token').value = token;
        });
    });
</script>
```

### Backend (registro-es.php - PHP)

```php
// 1. Verificar que llegó el token
if (!isset($_POST['recaptcha_token']) || empty($_POST['recaptcha_token'])) {
    echo "Error: No se recibió el token";
}

// 2. Enviar token a Google para validación
$verify_url = "https://www.google.com/recaptcha/api/siteverify";
$response = file_get_contents($verify_url, false, $context);
$responseData = json_decode($response, true);

// 3. Validar:
//    - success == true (token válido)
//    - score >= 0.5 (probablemente humano)
//    - action == "registro" (acción correcta)

if ($responseData["success"] && $responseData["score"] >= 0.5) {
    // ✅ Registro permitido
    INSERT INTO usuarios ...
} else {
    // ❌ Registro rechazado
    echo "No se pudo validar la solicitud";
}
```

---

## 🧪 Pruebas

### Registrar un usuario legítimo

1. Abre: `https://institutoea.fwh.is/Act3-SistemaPadresPatria/registro-es.php`
2. Completa el formulario normalmente:
   ```
   Nombre: Juan
   Apellido: Pérez
   Correo: juan@ejemplo.com
   Usuario: juanperez
   Contraseña: MyPassword123!
   ```
3. Haz clic en **"Registrarse"**
4. Deberías ver: **"¡Registro exitoso! El usuario se ha almacenado correctamente."**
5. Verifica en phpMyAdmin que el registro se insertó en la tabla `usuarios`

### Verificar la tabla de la BD

1. Ve a tu panel de InfinityFree → phpMyAdmin
2. Selecciona tu BD `if0_..._padres_patria`
3. Abre la tabla `usuarios`
4. Deberías ver tu nuevo registro

---

## ⚙️ Configuración Avanzada

### Cambiar el umbral de score

Si tienes muchos falsos positivos (usuarios legítimos rechazados):

```php
// En includes/recaptcha_config.php
define('RECAPTCHA_SCORE_THRESHOLD', 0.3);  // Más permisivo (0.0 - 1.0)
```

Recomendaciones:
- **0.3**: Muy permisivo, rechaza solo bots obvios
- **0.5**: Balance (por defecto)
- **0.7**: Estricto, mejor protección pero más falsos positivos

### Monitorear registros en Google

1. Ve a **https://www.google.com/recaptcha/admin**
2. Selecciona tu sitio
3. Haz clic en **"Analytics"**
4. Verás gráficos de:
   - Eventos procesados
   - Distribución de scores
   - Usuarios bloqueados/permitidos

---

## 🐛 Solución de Problemas

### Problema: "Error: No se recibió el token de seguridad"

**Solución:**
- Verifica que tengas internet (reCAPTCHA requiere conexión a Google)
- Abre la consola del navegador (F12 → Console) para ver errores
- Verifica que el Site Key sea válido en `registro-es.php`

### Problema: "No se pudo validar la solicitud de seguridad"

**Posibles causas:**
- Secret Key incorrecta en `recaptcha_config.php`
- reCAPTCHA v2 activado en vez de v3
- Dominio no registrado en Google
- Score muy bajo (demasiados falsos positivos)

**Solución:**
- Verifica las claves en Google Console
- Ajusta `RECAPTCHA_SCORE_THRESHOLD` a 0.3
- Verifica que el dominio esté en la lista de Google

### Problema: El registro se guarda pero sin validar reCAPTCHA

**Solución:**
- Verifica que el archivo `includes/recaptcha_config.php` exista
- Verifica que `require_once 'includes/recaptcha_config.php';` esté en `registro-es.php`
- Comprueba que las constantes RECAPTCHA_* no sean "TU_SITE_KEY" (valores por defecto)

---

## 📁 Archivos Modificados

```
registro-es.php                    ← Integración de reCAPTCHA v3, validación del token
includes/recaptcha_config.php      ← Almacena las claves (añadir a .gitignore)
includes/recaptcha_config.example.php ← Ejemplo, se sube a GitHub
.gitignore                         ← Actualizado para proteger recaptcha_config.php
```

---

## 🔒 Seguridad

### ✅ Buenas prácticas implementadas

- Secret Key protegida en servidor (no visible en HTML)
- Validación en backend (no confiar solo en frontend)
- Score threshold ajustable
- Action específica ("registro") para evitar reutilización de tokens

### ✅ Video flujo completo

1. **Usuario abre formulario** → Google asigna score silenciosamente
2. **Usuario completa y envía** → Token se incluye en POST
3. **PHP recibe token** → Valida con Google API
4. **Google devuelve resultado** → Score y validación
5. **Si válido** → Se guarda en BD
6. **Si inválido** → Se muestra error, no se guarda

---

## 📞 Documentación Oficial

- Guía de reCAPTCHA v3: https://developers.google.com/recaptcha/docs/v3
- Admin Console: https://www.google.com/recaptcha/admin
- Migrar de v2 a v3: https://developers.google.com/recaptcha/docs/v3/migrate

---

## ✨ Próximos Pasos (Opcional)

1. **Logging**: Guardar intentos fallidos de registro para auditoría
2. **Rate Limiting**: Limitar intentos por IP
3. **Email Verification**: Enviar correo de confirmación
4. **Dashboard**: Ver estadísticas de registros en tiempo real

---

**Fecha**: Marzo 2026  
**Proyecto**: Sistema de Gestión Académica - Instituto Padres de la Patria  
**Unidad**: 3 - Actividad 3 (reCAPTCHA v3 Integration)
