<?php
// Panel API - Manejo de anuncios y programas
session_start();
require_once __DIR__ . '/config.php';
if (file_exists(__DIR__ . '/config.local.php')) require_once __DIR__ . '/config.local.php';
require_once __DIR__ . '/db.php';

if (empty($_SESSION['logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}
// Normalizar lista permitida (soporta constante o variable según versión)
$allowed = defined('ALLOWED_USERS') ? ALLOWED_USERS : (isset($ALLOWED_USERS) ? $ALLOWED_USERS : []);
if (!in_array($_SESSION['email'], $allowed)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json; charset=UTF-8');

$pdo = db();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$GIF_DIR = __DIR__ . '/gifs';
$GIF_URL = 'panel/gifs';
if (!is_dir($GIF_DIR)) { @mkdir($GIF_DIR, 0775, true); }

function bannerSrc($gifUrl, $filename) { return $gifUrl . '/' . $filename; }

switch ($action) {
    case 'list':
        echo json_encode(['success' => true, 'banners' => $pdo->query('SELECT * FROM banners ORDER BY position ASC, id ASC')->fetchAll()], JSON_UNESCAPED_UNICODE);
        break;

    case 'update_link':
        $id = (int)($_POST['id'] ?? 0);
        $link = trim($_POST['link'] ?? '');
        $stmt = $pdo->prepare('UPDATE banners SET link = ? WHERE id = ?');
        $stmt->execute([$link, $id]);
        echo json_encode(['success' => true]);
        break;

    case 'update_bodies':
        $id = (int)($_POST['id'] ?? 0);
        $bodies = max(1, min(3, (int)($_POST['bodies'] ?? 1)));
        $stmt = $pdo->prepare('UPDATE banners SET bodies = ? WHERE id = ?');
        $stmt->execute([$bodies, $id]);
        echo json_encode(['success' => true]);
        break;

    case 'update_image':
        $id = (int)($_POST['id'] ?? 0);
        if (empty($_FILES['file']['name'])) { echo json_encode(['success' => false, 'error' => 'No se recibió archivo']); exit; }
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['gif','png','jpg','jpeg'])) { echo json_encode(['success' => false, 'error' => 'Formato no permitido. Usá GIF, PNG o JPG.']); exit; }
        $filename = 'banner-' . $id . '-' . time() . '.' . $ext;
        $dest = $GIF_DIR . '/' . $filename;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) { echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']); exit; }
        $prev = $pdo->prepare('SELECT src FROM banners WHERE id = ?');
        $prev->execute([$id]);
        $oldSrc = $prev->fetchColumn();
        if ($oldSrc && strpos($oldSrc, 'panel/gifs/') === 0) {
            $oldFile = SITE_ROOT . '/' . $oldSrc;
            if (file_exists($oldFile)) @unlink($oldFile);
        }
        $stmt = $pdo->prepare('UPDATE banners SET src = ? WHERE id = ?');
        $stmt->execute([bannerSrc($GIF_URL, $filename), $id]);
        echo json_encode(['success' => true]);
        break;

    case 'add':
        $link = trim($_POST['link'] ?? '');
        $alt = trim($_POST['alt'] ?? 'Banner');
        $bodies = max(1, min(3, (int)($_POST['bodies'] ?? 1)));
        if (empty($_FILES['file']['name'])) { echo json_encode(['success' => false, 'error' => 'Subí un archivo']); exit; }
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['gif','png','jpg','jpeg'])) { echo json_encode(['success' => false, 'error' => 'Formato no permitido. Usá GIF, PNG o JPG.']); exit; }
        $filename = 'banner-' . time() . '.' . $ext;
        $dest = $GIF_DIR . '/' . $filename;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) { echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']); exit; }
        $maxPos = (int)$pdo->query('SELECT COALESCE(MAX(position),0) FROM banners')->fetchColumn();
        $stmt = $pdo->prepare('INSERT INTO banners (src, link, bodies, alt, position) VALUES (?,?,?,?,?)');
        $stmt->execute([bannerSrc($GIF_URL, $filename), $link, $bodies, $alt, $maxPos + 1]);
        echo json_encode(['success' => true]);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        $prev = $pdo->prepare('SELECT src FROM banners WHERE id = ?');
        $prev->execute([$id]);
        $oldSrc = $prev->fetchColumn();
        if ($oldSrc && strpos($oldSrc, 'panel/gifs/') === 0) {
            $oldFile = SITE_ROOT . '/' . $oldSrc;
            if (file_exists($oldFile)) @unlink($oldFile);
        }
        $stmt = $pdo->prepare('DELETE FROM banners WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'reorder':
        $order = json_decode($_POST['order'] ?? '[]', true);
        if (!is_array($order)) { echo json_encode(['success' => false, 'error' => 'Orden inválido']); exit; }
        $stmt = $pdo->prepare('UPDATE banners SET position = ? WHERE id = ?');
        $pos = 1;
        foreach ($order as $id) {
            $stmt->execute([$pos++, (int)$id]);
        }
        echo json_encode(['success' => true]);
        break;

    case 'settings_get':
        $row = $pdo->query('SELECT off_link, off_loop, carousel_speed, carousel_auto, programacion_activa FROM settings WHERE id = 1')->fetch();
        if (!$row) $row = ['off_link' => '', 'off_loop' => 1, 'carousel_speed' => 3, 'carousel_auto' => 1, 'programacion_activa' => 1];
        echo json_encode(['success' => true, 'settings' => $row], JSON_UNESCAPED_UNICODE);
        break;

    case 'settings_save':
        $offLink = trim($_POST['off_link'] ?? '');
        $offLoop = (isset($_POST['off_loop']) && $_POST['off_loop'] === '1') ? 1 : 0;
        $speed = max(1, min(10, (int)($_POST['carousel_speed'] ?? 3)));
        $auto = (isset($_POST['carousel_auto']) && $_POST['carousel_auto'] === '1') ? 1 : 0;
        $prog = (isset($_POST['programacion_activa']) && $_POST['programacion_activa'] === '1') ? 1 : 0;
        $stmt = $pdo->prepare('REPLACE INTO settings (id, off_link, off_loop, carousel_speed, carousel_auto, programacion_activa) VALUES (1, ?, ?, ?, ?, ?)');
        $stmt->execute([$offLink, $offLoop, $speed, $auto, $prog]);
        echo json_encode(['success' => true]);
        break;

    case 'programas_list':
        $rows = $pdo->query('SELECT * FROM programas ORDER BY posicion ASC, id ASC')->fetchAll();
        echo json_encode(['success' => true, 'programas' => $rows], JSON_UNESCAPED_UNICODE);
        break;

    case 'programas_save':
        $id = (int)($_POST['id'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $dias = trim($_POST['dias'] ?? '');   // letras separadas por coma, ej: "L,M,J"
        $hora = trim($_POST['hora'] ?? '');
        if ($titulo === '') { echo json_encode(['success' => false, 'error' => 'Falta el título']); exit; }
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE programas SET titulo=?, categoria=?, dias=?, hora=? WHERE id=?');
            $stmt->execute([$titulo, $categoria, $dias, $hora, $id]);
        } else {
            $maxPos = (int)$pdo->query('SELECT COALESCE(MAX(posicion),0) FROM programas')->fetchColumn();
            $stmt = $pdo->prepare('INSERT INTO programas (titulo, categoria, dias, hora, posicion) VALUES (?,?,?,?,?)');
            $stmt->execute([$titulo, $categoria, $dias, $hora, $maxPos + 1]);
        }
        echo json_encode(['success' => true]);
        break;

    case 'programas_delete':
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM programas WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    case 'programas_image':
        $id = (int)($_POST['id'] ?? 0);
        if (empty($_FILES['file']['name'])) { echo json_encode(['success' => false, 'error' => 'No se recibió archivo']); exit; }
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['gif','png','jpg','jpeg','webp'])) { echo json_encode(['success' => false, 'error' => 'Formato no permitido. Usá GIF, PNG, JPG o WEBP.']); exit; }

        $imgDir = __DIR__ . '/img_prog';
        $imgUrl = 'panel/img_prog';
        if (!is_dir($imgDir)) @mkdir($imgDir, 0775, true);

        // Redimensionar a 333x450 px
        $targetW = 333;
        $targetH = 450;

        $src = $_FILES['file']['tmp_name'];
        $srcImg = null;
        switch ($ext) {
            case 'jpg': case 'jpeg': $srcImg = @imagecreatefromjpeg($src); break;
            case 'png':  $srcImg = @imagecreatefrompng($src); break;
            case 'webp': $srcImg = @imagecreatefromwebp($src); break;
            case 'gif':  $srcImg = @imagecreatefromgif($src); break;
        }
        if (!$srcImg) { echo json_encode(['success' => false, 'error' => 'No se pudo leer la imagen']); exit; }

        $w = imagesx($srcImg);
        $h = imagesy($srcImg);
        $dst = imagecreatetruecolor($targetW, $targetH);

        // fondo gris/negro para transparentes
        imagefill($dst, 0, 0, imagecolorallocate($dst, 5, 5, 5));
        imagecopyresampled($dst, $srcImg, 0, 0, 0, 0, $targetW, $targetH, $w, $h);
        imagedestroy($srcImg);

        $filename = 'prog-' . $id . '-' . time() . '.jpg';
        $dest = $imgDir . '/' . $filename;
        if (!imagejpeg($dst, $dest, 85)) { echo json_encode(['success' => false, 'error' => 'No se pudo guardar la imagen']); exit; }
        imagedestroy($dst);

        // borrar imagen anterior si era propia
        $prev = $pdo->prepare('SELECT imagen FROM programas WHERE id = ?');
        $prev->execute([$id]);
        $oldImg = $prev->fetchColumn();
        if ($oldImg && strpos($oldImg, 'panel/img_prog/') === 0) {
            $oldFile = SITE_ROOT . '/' . $oldImg;
            if (file_exists($oldFile)) @unlink($oldFile);
        }
        $stmt = $pdo->prepare('UPDATE programas SET imagen = ? WHERE id = ?');
        $stmt->execute([$imgUrl . '/' . $filename, $id]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción desconocida']);
}
