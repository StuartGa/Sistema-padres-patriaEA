<?php
/**
 * CONFIGURACIÓN DE RECAPTCHA V3 - ARCHIVO DE EJEMPLO
 * Renombra este archivo a "recaptcha_config.php" y reemplaza TU_SITE_KEY y TU_SECRET_KEY
 * 
 * ¿Cómo obtener las claves?
 * 1. Ve a https://www.google.com/recaptcha/admin
 * 2. Inicia sesión con tu cuenta de Google
 * 3. Click en "+" para crear una clave nueva
 * 4. Configura:
 *    - Etiqueta: "Instituto Padres Patria" (o el nombre que prefieras)
 *    - reCAPTCHA tipo: Selecciona "reCAPTCHA v3"
 *    - Dominios: Agrega tu dominio (ej: institutoea.fwh.is)
 * 5. Acepta términos y crea la clave
 * 6. Copia la "Site Key" y "Secret Key"
 * 7. Pega las claves en recaptcha_config.php
 */

// ============================================================
// REEMPLAZA ESTAS CLAVES CON LAS TUYAS
// ============================================================
define('RECAPTCHA_SITE_KEY', 'TU_SITE_KEY');
define('RECAPTCHA_SECRET_KEY', 'TU_SECRET_KEY');

// Umbral de puntuación: 0.0 (bot) a 1.0 (humano)
// 0.5 es un buen equilibrio: rechaza la mayoría de bots pero acepta usuarios reales
define('RECAPTCHA_SCORE_THRESHOLD', 0.5);

// Acción para validar (debe coincidir con la del frontend)
define('RECAPTCHA_ACTION', 'registro');
?>
