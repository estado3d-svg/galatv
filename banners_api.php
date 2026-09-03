<?php
// Endpoint público: sirve banners, video de portada (settings) y programas desde la BD
// No requiere sesión.

require_once __DIR__ . '/panel/config.php';
if (file_exists(__DIR__ . '/panel/config.local.php')) require_once __DIR__ . '/panel/config.local.php';
require_once __DIR__ . '/panel/db.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    $pdo = db();

    $banners = $pdo->query('SELECT id, src, link, bodies, alt, position FROM banners ORDER BY position ASC, id ASC')->fetchAll();

    $settingsRow = $pdo->query('SELECT off_link, off_loop FROM settings WHERE id = 1')->fetch();
    if (!$settingsRow) $settingsRow = ['off_link' => '', 'off_loop' => 1];

    $programas = $pdo->query('SELECT id, titulo, categoria, dia, hora, posicion FROM programas ORDER BY posicion ASC, id ASC')->fetchAll();

    echo json_encode([
        'success'   => true,
        'banners'   => $banners,
        'settings'  => $settingsRow,
        'programas' => $programas
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'banners' => [], 'settings' => [], 'programas' => [], 'error' => $e->getMessage()]);
}
