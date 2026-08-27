<?php
// Configuración SMTP - NO subir credenciales en texto plano en archivos públicos
// Este archivo está protegido por .htaccess para que no sea accesible desde la web

define('MAIL_SMTP_HOST', 'mail.galatv.com.ar');
define('MAIL_SMTP_PORT', 587);
define('MAIL_SMTP_SECURE', 'tls');
define('MAIL_SMTP_USER', 'contacto@galatv.com.ar');
define('MAIL_SMTP_PASS', 'Vph4N*Di');
define('MAIL_FROM', 'contacto@galatv.com.ar');
define('MAIL_TO', 'bodorola@gmail.com');
