<?php
// Config del panel
// Cargar credenciales desde archivo local NO versionado si existe
$localFile = __DIR__ . '/config.local.php';
if (file_exists($localFile)) {
    require_once $localFile;
}

// Rutas
define('SITE_ROOT', __DIR__ . '/../');
define('BANNERS_FILE', __DIR__ . '/../banners.json');
define('BANNERS_DIR', __DIR__ . '/gifs');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Usuarios permitidos
define('ALLOWED_USERS', array(
    'galatvstreaming@gmail.com',
    'bodorola@gmail.com'
));

// URI de redirección de Google (default; puede overriderse en config.local.php)
if (!defined('GOOGLE_REDIRECT_URI')) {
    define('GOOGLE_REDIRECT_URI', 'https://galatv.com.ar/panel/google-callback.php');
}

// Session segura
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
?>
