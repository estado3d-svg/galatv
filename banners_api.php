<?php
// Endpoint público: sirve los banners desde la BD para la página principal
// No requiere sesión. Solo expone los campos necesarios.

require_once __DIR__ . '/panel/config.php';
if (file_exists(__DIR__ . '/panel/config.local.php')) require_once __DIR__ . '/panel/config.local.php';
require_once __DIR__ . '/panel/db.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    $pdo = db();
    $rows = $pdo->query('SELECT id, src, link, bodies, alt, position FROM banners ORDER BY position ASC, id ASC')->fetchAll();
    echo json_encode(['success' => true, 'banners' => $rows], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'banners' => [], 'error' => $e->getMessage()]);
}
