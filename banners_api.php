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

    $settingsRow = $pdo->query('SELECT off_link, off_loop, carousel_speed, carousel_auto, programacion_activa FROM settings WHERE id = 1')->fetch();
    if (!$settingsRow) $settingsRow = ['off_link' => '', 'off_loop' => 1, 'carousel_speed' => 3, 'carousel_auto' => 1, 'programacion_activa' => 1];

    $programas = $pdo->query('SELECT id, titulo, categoria, dias, hora, imagen, posicion FROM programas ORDER BY posicion ASC, id ASC')->fetchAll();

    echo json_encode([
        'success'   => true,
        'banners'   => $banners,
        'settings'  => $settingsRow,
        'programas' => $programas
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    // NO exponer detalles del error al público
    echo json_encode(['success' => false, 'banners' => [], 'settings' => [], 'programas' => [], 'error' => 'Error interno']);
}
