<?php
session_start();
require_once __DIR__ . '/config.php';

// Generar un estado aleatorio para CSRF
$_SESSION['oauth_state'] = bin2hex(random_bytes(16));

$params = array(
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $_SESSION['oauth_state'],
    'prompt'        => 'select_account'
);

$url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
header('Location: ' . $url);
exit;
