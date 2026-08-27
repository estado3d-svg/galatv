<?php
// Configurar error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Configuración
$to = "bodorola@gmail.com";
$subject = "Nuevo mensaje desde GalaTV - Sitio Web";
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: Contacto GalaTV <no-reply@galatv.com>" . "\r\n";
$headers .= "Reply-To: {$_POST['email']}" . "\r\n";

// Validar y sanitizar datos
$nombre = isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8') : '';
$email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$telefono = isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8') : '';
$asunto = isset($_POST['asunto']) ? htmlspecialchars(trim($_POST['asunto']), ENT_QUOTES, 'UTF-8') : '';
$mensaje = isset($_POST['mensaje']) ? htmlspecialchars(trim($_POST['mensaje']), ENT_QUOTES, 'UTF-8') : '';

// Validaciones básicas
$errors = [];

if (empty($nombre)) {
    $errors[] = "El nombre es obligatorio";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Por favor ingresa un correo electrónico válido";
}

if (empty($mensaje)) {
    $errors[] = "El mensaje es obligatorio";
}

if (!empty($telefono) && strlen($telefono) < 10) {
    $errors[] = "El teléfono debe tener al menos 10 dígitos";
}

// Si no hay errores, enviar correo
if (empty($errors)) {
    $body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background: #050505; color: #fff; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #080808; padding: 30px; border-radius: 8px; border: 1px solid #2c2410; }
            h2 { color: #FFD700; margin-bottom: 20px; }
            .info { margin-bottom: 15px; padding: 10px; background: #111; border-left: 3px solid #FFD700; }
            .label { color: #FFD700; font-weight: bold; }
            .success { color: #00ff00; margin-top: 20px; padding: 15px; background: rgba(0, 255, 0, 0.1); border-radius: 4px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>📧 Nuevo Mensaje de Contacto</h2>
            <div class='info'>
                <span class='label'>Nombre:</span> {$nombre}<br>
                <span class='label'>Email:</span> {$email}<br>
                <span class='label'>Teléfono:</span> {$telefono}
            </div>
            <div class='info'>
                <span class='label'>Asunto:</span> {$asunto}
            </div>
            <div class='info'>
                <span class='label'>Mensaje:</span><br>
                {$mensaje}
            </div>
            <div class='success'>
                ✅ Mensaje enviado correctamente
            </div>
        </div>
    </body>
    </html>
    ";

    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $body, $headers)) {
        // Guardar en archivo log
        $logFile = __DIR__ . '/contact-log.txt';
        $logEntry = sprintf(
            "[%s] Nombre: %s | Email: %s | Mensaje: %s\n",
            date('Y-m-d H:i:s'),
            $nombre,
            $email,
            $mensaje
        );
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    } else {
        // Si falla el envío, mostrar error
        $errorBody = "
            <html>
            <body style='font-family:Arial;padding:20px;background:#050505;color:#fff;'>
                <h2 style='color:#FFD700'>Error al enviar el mensaje</h2>
                <p>Hubo un problema al procesar tu solicitud. Por favor intenta nuevamente o contáctanos por teléfono.</p>
            </body>
            </html>
        ";
        mail($to, "Error - Formulario de Contacto", $errorBody, $headers);
        die("<script>alert('Hubo un error al enviar el mensaje. Intenta nuevamente.'); window.history.back();</script>");
    }
} else {
    // Mostrar errores
    die("<script>alert('Error: " . implode(', ', $errors) . "'); window.history.back();</script>");
}
?>