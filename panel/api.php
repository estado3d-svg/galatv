<?php
session_start();
require_once __DIR__ . '/config.php';

// Requiere login
if (empty($_SESSION['logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json; charset=UTF-8');

// Leer banners actuales
function loadBanners() {
    $file = BANNERS_FILE;
    if (!file_exists($file)) return ['banners' => []];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : ['banners' => []];
}

function saveBanners($banners) {
    $file = BANNERS_FILE;
    $json = json_encode(['banners' => $banners], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return file_put_contents($file, $json) !== false;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$banners = loadBanners()['banners'];
$lastId = 0;
foreach ($banners as $b) { if (isset($b['id']) && is_numeric($b['id']) && $b['id'] > $lastId) $lastId = (int)$b['id']; }

switch ($action) {

    // LISTAR banners
    case 'list':
        echo json_encode(['success' => true, 'banners' => $banners], JSON_UNESCAPED_UNICODE);
        break;

    // EDITAR link de un banner
    case 'update_link':
        $id = $_POST['id'] ?? '';
        $link = trim($_POST['link'] ?? '');
        foreach ($banners as &$b) {
            if ((string)$b['id'] === (string)$id) {
                $b['link'] = $link;
            }
        }
        unset($b);
        echo json_encode(['success' => saveBanners($banners)]);
        break;

    // CAMBIAR imagen (subir reemplazo) de un banner existente
    case 'update_image':
        $id = $_POST['id'] ?? '';
        if (empty($_FILES['file']['name'])) { echo json_encode(['success' => false, 'error' => 'No se recibió archivo']); exit; }
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['gif','png','jpg','jpeg'])) { echo json_encode(['success' => false, 'error' => 'Formato no permitido. Usá GIF, PNG o JPG.']); exit; }
        $targetDir = SITE_ROOT . '/';
        $filename = 'banner-' . $id . '-' . time() . '.' . $ext;
        $dest = $targetDir . $filename;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) { echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']); exit; }
        foreach ($banners as &$b) {
            if ((string)$b['id'] === (string)$id) { $b['src'] = $filename; $b['width'] = 333; $b['height'] = 150; }
        }
        unset($b);
        echo json_encode(['success' => saveBanners($banners)]);
        break;

    // AGREGAR nuevo banner (subir GIF, opcional link)
    case 'add':
        $link = trim($_POST['link'] ?? '');
        $alt = trim($_POST['alt'] ?? 'Banner');
        if (empty($_FILES['file']['name'])) { echo json_encode(['success' => false, 'error' => 'Subí un archivo']); exit; }
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['gif','png','jpg','jpeg'])) { echo json_encode(['success' => false, 'error' => 'Formato no permitido. Usá GIF, PNG o JPG.']); exit; }
        $filename = 'banner-' . ($lastId+1) . '-' . time() . '.' . $ext;
        $dest = SITE_ROOT . '/' . $filename;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) { echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo']); exit; }
        $newId = $lastId + 1;
        $banners[] = array(
            'id' => $newId,
            'src' => $filename,
            'link' => $link,
            'width' => 333,
            'height' => 150,
            'alt' => $alt
        );
        echo json_encode(['success' => saveBanners($banners)]);
        break;

    // BORRAR banner
    case 'delete':
        $id = $_POST['id'] ?? '';
        $newBanners = array();
        foreach ($banners as $b) {
            if ((string)$b['id'] === (string)$id) {
                // borrar archivo si es propio (no los gif-promo/tubarao originales)
                $f = SITE_ROOT . '/' . $b['src'];
                if (file_exists($f) && strpos($b['src'], 'banner-') === 0) @unlink($f);
            } else {
                $newBanners[] = $b;
            }
        }
        echo json_encode(['success' => saveBanners($newBanners)]);
        break;

    // ORDENAR banners (reordenar la fila 1)
    case 'reorder':
        $order = json_decode($_POST['order'] ?? '[]', true);
        if (!is_array($order)) { echo json_encode(['success' => false, 'error' => 'Orden inválido']); exit; }
        $byId = array();
        foreach ($banners as $b) $byId[(string)$b['id']] = $b;
        $newBanners = array();
        foreach ($order as $id) {
            if (isset($byId[(string)$id])) { $newBanners[] = $byId[(string)$id]; unset($byId[(string)$id]); }
        }
        foreach ($byId as $b) $newBanners[] = $b;
        echo json_encode(['success' => saveBanners($newBanners)]);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Acción desconocida']);
}
