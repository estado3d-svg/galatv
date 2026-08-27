<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$response = ['success' => false, 'message' => ''];

try {
    $nombre   = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $asunto   = isset($_POST['asunto']) ? trim($_POST['asunto']) : '';
    $mensaje  = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

    if ($nombre === '') throw new Exception('El nombre es obligatorio');
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Ingresa un correo electrónico válido');
    if ($mensaje === '') throw new Exception('El mensaje es obligatorio');

    $mail = new PHPMailer(true);

    // SMTP
    // Cargar configuración SMTP desde archivo seguro (protegido por .htaccess)
    require_once __DIR__ . '/mail-config.php';

    $mail->isSMTP();
    $mail->Host       = MAIL_SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_SMTP_USER;
    $mail->Password   = MAIL_SMTP_PASS;
    $mail->SMTPSecure = MAIL_SMTP_SECURE === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    // Desactivar verificación de certificado (el CN del wildcard de Ferozo no coincide con la IP/host)
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
            'peer_name'         => 'mail.galatv.com.ar'
        )
    );
    $mail->Host = MAIL_SMTP_HOST;

    // Remitente y destinatario
    $mail->setFrom(MAIL_FROM, 'GalaTV');
    $mail->addReplyTo($email, $nombre);
    $mail->addAddress(MAIL_TO, 'GalaTV Contacto');

    $mail->Subject = $asunto !== '' ? "GalaTV: $asunto" : "Nuevo mensaje desde GalaTV";

    $body = "
    <div style='font-family:Arial,sans-serif;background:#050505;color:#fff;padding:20px'>
      <div style='max-width:600px;margin:0 auto;background:#080808;padding:30px;border-radius:8px;border:1px solid #2c2410'>
        <h2 style='color:#FFD700'>Nuevo Mensaje de Contacto</h2>
        <p><strong style='color:#FFD700'>Nombre:</strong> " . $nombre . "</p>
        <p><strong style='color:#FFD700'>Email:</strong> " . $email . "</p>
        <p><strong style='color:#FFD700'>Teléfono:</strong> " . $telefono . "</p>
        <p><strong style='color:#FFD700'>Mensaje:</strong><br>" . $mensaje . "</p>
      </div>
    </div>
    ";

    $mail->isHTML(true);
    $mail->Body    = $body;
    $mail->AltBody = "Nuevo mensaje de contacto\nNombre: $nombre\nEmail: $email\nTeléfono: $telefono\nMensaje: $mensaje";

    $mail->send();

    $response['success'] = true;
    $response['message'] = '¡Gracias por tu mensaje! Te contactaremos pronto.';
} catch (\Throwable $e) {
    $response['success'] = false;
    $response['message'] = 'No se pudo enviar el correo. Intenta nuevamente.';
    $err = $e->getMessage();
    @file_put_contents(__DIR__ . '/error-log.txt', "[" . date('Y-m-d H:i:s') . "] " . $err . "\n", FILE_APPEND);
}

echo json_encode($response);
