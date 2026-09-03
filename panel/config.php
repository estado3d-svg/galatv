<?php
// Config del panel

// Cargar credenciales desde archivo local NO versionado si existe
$localFile = __DIR__ . '/config.local.php';
if (file_exists($localFile)) {
    require_once $localFile;
}

// Si no están definidas, usar valores de entorno
if (!defined('GOOGLE_CLIENT_ID'))     define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
if (!defined('GOOGLE_REDIRECT_URI'))  define('GOOGLE_REDIRECT_URI', 'https://galatv.com.ar/panel/google-callback.php');

// Usuarios permitidos (variable + constante para compatibilidad)
$ALLOWED_USERS = array(
    'galatvstreaming@gmail.com',
    'bodorola@gmail.com'
);
if (!defined('ALLOWED_USERS')) {
    define('ALLOWED_USERS', $ALLOWED_USERS);
}

// Rutas
if (!defined('BANNERS_FILE')) define('BANNERS_FILE', __DIR__ . '/../banners.json');
if (!defined('SITE_ROOT'))    define('SITE_ROOT', dirname(__DIR__));
