<?php
// Inicio de sesión segura (cookie HttpOnly + Secure + SameSite=Lax)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'httponly' => true,
        'secure'   => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
