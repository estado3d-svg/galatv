<?php
// Configuración SMTP - archivo protegido por .htaccess
// Usamos la IP del servidor de correo para evitar el problema del CN del certificado wildcard de Ferozo

define('MAIL_SMTP_HOST', '200.58.111.88');
define('MAIL_SMTP_PORT', 587);
define('MAIL_SMTP_SECURE', 'tls');
define('MAIL_SMTP_USER', 'contacto@galatv.com.ar');
define('MAIL_SMTP_PASS', 'Vph4N*Di');
define('MAIL_FROM', 'contacto@galatv.com.ar');
define('MAIL_TO', 'bodorola@gmail.com');
