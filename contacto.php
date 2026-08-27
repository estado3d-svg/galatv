<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=UTF-8');

$response = ['success' => false, 'message' => ''];

try {
    $to = "bodorola@gmail.com";
    $subject = "Nuevo mensaje desde GalaTV - Sitio Web";

    $nombre   = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $asunto   = isset($_POST['asunto']) ? trim($_POST['asunto']) : '';
    $mensaje  = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

    if ($nombre === '') {
        throw new Exception('El nombre es obligatorio');
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Ingresa un correo electrónico válido');
    }
    if ($mensaje === '') {
        throw new Exception('El mensaje es obligatorio');
    }

    $nombre   = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
    $email    = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $telefono = htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8');
    $asunto   = htmlspecialchars($asunto, ENT_QUOTES, 'UTF-8');
    $mensaje  = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');

    $body = "
    <html><body style='font-family:Arial,sans-serif;background:#050505;color:#fff;padding:20px'>
      <div style='max-width:600px;margin:0 auto;background:#080808;padding:30px;border-radius:8px;border:1px solid #2c2410'>
        <h2 style='color:#FFD700'>Nuevo Mensaje de Contacto</h2>
        <p><strong style='color:#FFD700'>Nombre:</strong> {$nombre}</p>
        <p><strong style='color:#FFD700'>Email:</strong> {$email}</p>
        <p><strong style='color:#FFD700'>Teléfono:</strong> {$telefono}</p>
        <p><strong style='color:#FFD700'>Asunto:</strong> {$asunto}</p>
        <p><strong style='color:#FFD700'>Mensaje:</strong><br>{$mensaje}</p>
      </div>
    </body></html>
    ";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: GalaTV <no-reply@c2642305.ferozo.com>\r\n";
    $headers .= "Reply-To: {$email}\r\n";

    $sent = @mail($to, $subject, $body, $headers);

    // Guardar log del intento
    $logEntry = sprintf(
        "[%s] Enviado: %s | Nombre: %s | Email: %s | Mensaje: %s\n",
        date('Y-m-d H:i:s'),
        $sent ? 'SI' : 'NO',
        $nombre,
        $email,
        $mensaje
    );
    @file_put_contents(__DIR__ . '/contact-log.txt', $logEntry, FILE_APPEND);

    if ($sent) {
        $response['success'] = true;
        $response['message'] = '¡Gracias por tu mensaje! Te contactaremos pronto.';
    } else {
        throw new Exception('No se pudo enviar el correo. Intenta nuevamente.');
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
