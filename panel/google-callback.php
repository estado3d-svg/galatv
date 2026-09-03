<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/config.php';

// Verificar estado CSRF
if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth_state']) {
    die('Error de estado de seguridad. Volvé a intentar.');
}

if (isset($_GET['error'])) {
    die('Error de autenticación: ' . htmlspecialchars($_GET['error']));
}

if (!isset($_GET['code'])) {
    die('No se recibió el código de autorización.');
}

$code = $_GET['code'];

// Intercambiar código por token
$tokenUrl = 'https://oauth2.googleapis.com/token';
$postData = array(
    'code'          => $code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code'
);

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die('Error al obtener el token de Google.');
}

$tokenData = json_decode($response, true);
$idToken = $tokenData['id_token'] ?? null;
if (!$idToken) {
    die('No se recibió el id_token.');
}

// VERIFICAR el id_token contra la API de Google (valida firma y audiencia).
// NO decodificar manualmente: así evitamos tokens forjados.
$verifyUrl = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
$vch = curl_init($verifyUrl);
curl_setopt($vch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($vch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($vch, CURLOPT_TIMEOUT, 20);
$verifyRes = curl_exec($vch);
curl_close($vch);
$info = json_decode($verifyRes, true);

// Validar que la audiencia sea nuestro client_id
if (empty($info['aud']) || $info['aud'] !== GOOGLE_CLIENT_ID) {
    die('Token de Google inválido (audiencia no coincide).');
}

$googleEmail = $info['email'] ?? '';
$googleName  = $info['name'] ?? '';
$googleSub   = $info['sub'] ?? '';

// Validar que el email esté permitido
if (!in_array(strtolower($googleEmail), array_map('strtolower', $ALLOWED_USERS))) {
    die('Acceso denegado. Tu cuenta de Google (' . htmlspecialchars($googleEmail) . ') no está autorizada.');
}

// Login exitoso
session_regenerate_id(true);   // evita session fixation
$_SESSION['logged_in']  = true;
$_SESSION['email']      = $googleEmail;
$_SESSION['name']       = $googleName;
$_SESSION['google_sub'] = $googleSub;

header('Location: index.php');
exit;
