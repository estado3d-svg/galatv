<?php
session_start();
require_once __DIR__ . '/config.php';
if (file_exists(__DIR__ . '/config.local.php')) require_once __DIR__ . '/config.local.php';
require_once __DIR__ . '/db.php';

// Requiere login
if (empty($_SESSION['logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json; charset=UTF-8');

$pdo = db();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

function fetchAllBanners($pdo) {
    return $pdo->query('SELECT * FROM banners ORDER BY position ASC, id ASC')->fetchAll();
}

switch ($action) {

    // LISTAR banners
    case 'list':
        echo json_encode(['success' => true, 'banners' => fetchAllBanners($pdo)], JSON_UNESCAPED_UNICODE);
        break;

    // EDITAR link de un banner
    case 'update_link':
        $id = (int)($_POST['id'] ?? 0);
        $link = trim($_POST['link'] ?? '');
        $st = $pdo->prepare('UPDATE banners SET link = ? WHERE id = ?');
        $st->execute([$link, $id]);
        echo json_encode(['success' => true]);
        break;

    // EDITAR cuerpos (1-3) de un banner
    case 'update_bodies':
        $id = (int)($_POST['id'] ?? 0);
        $bodies = max(1, min(3, (int)($_POST['bodies'] ?? 1)));
        $st = $pdo->prepare('UPDATE banners SET bodies = ? WHERE id = ?');
        $st->execute([$bodies, $id]);
        echo json_encode(['success' => true]);
        break;

    // CAMBIAR imagen (subir reemplazo) de un banner existente
    case 'update_image':
        $id = (int)($_POST['id'] ?? 0);
        if (empty($_FILES['file']['name'])) { echo json_encode(['success' => false, 'error' => 'No se recibió archivo']); exit; }
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['gif','png','jpg','jpeg'])) { echo json_encode(['success' => false, 'error' => 'Formato no permitido. Usá GIF, PNG o JPG.']); exit; }
        $filename = 'banner-' . $id . '-' . time() . '.' . $ext;
        $dest = SITE_ROOT . '/' . $filename;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) { echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']); exit; }
        // borrar imagen anterior si es propia del panel
        $prev = $pdo->prepare('SELECT src FROM banners WHERE id = ?');
        $prev->execute([$id]);
        $oldSrc = $prev->fetchColumn();
        if ($oldSrc && strpos($oldSrc, 'banner-') === 0 && file_exists(SITE_ROOT . '/' . $oldSrc)) @unlink(SITE_ROOT . '/' . $oldSrc);
        $st = $pdo->prepare('UPDATE banners SET src = ? WHERE id = ?');
        $st->execute([$filename, $id]);
        echo json_encode(['success' => true]);
        break;

    // AGREGAR nuevo banner (subir GIF, opcional link, 1-3 cuerpos)
    case 'add':
        $link = trim($_POST['link'] ?? '');
        $alt = trim($_POST['alt'] ?? 'Banner');
        $bodies = max(1, min(3, (int)($_POST['bodies'] ?? 1)));
        if (empty($_FILES['file']['name'])) { echo json_encode(['success' => false, 'error' => 'Subí un archivo']); exit; }
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['gif','png','jpg','jpeg'])) { echo json_encode(['success' => false, 'error' => 'Formato no permitido. Usá GIF, PNG o JPG.']); exit; }
        $filename = 'banner-' . time() . '.' . $ext;
        $dest = SITE_ROOT . '/' . $filename;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) { echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']); exit; }
        $maxPos = (int)$pdo->query('SELECT COALESCE(MAX(position),0) FROM banners')->fetchColumn();
        $st = $pdo->prepare('INSERT INTO banners (src, link, bodies, alt, position) VALUES (?,?,?,?,?)');
        $st->execute([$filename, $link, $bodies, $alt, $maxPos + 1]);
        echo json_encode(['success' => true]);
        break;

    // BORRAR banner
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        $prev = $pdo->prepare('SELECT src FROM banners WHERE id = ?');
        $prev->execute([$id]);
        $oldSrc = $prev->fetchColumn();
        if ($oldSrc && strpos($oldSrc, 'banner-') === 0 && file_exists(SITE_ROOT . '/' . $oldSrc)) @unlink(SITE_ROOT . '/' . $oldSrc);
        $st = $pdo->prepare('DELETE FROM banners WHERE id = ?');
        $st->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    // ORDENAR banners
    case 'reorder':
        $order = json_decode($_POST['order'] ?? '[]', true);
        if (!is_array($order)) { echo json_encode(['success' => false, 'error' => 'Orden inválido']); exit; }
        $st = $pdo->prepare('UPDATE banners SET position = ? WHERE id = ?');
        $pos = 1;
        foreach ($order as $id) {
            $st->execute([$pos++, (int)$id]);
        }
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción desconocida']);
}
