<?php
/**
 * GalaTV API - Banners publicitarios
 * Devuelve los banners ordenados por posicion.
 */

require_once __DIR__ . '/config.php';

try {
    $db = getDB();

    $banners = $db->query(
        'SELECT id, src, link, bodies, alt, position
         FROM banners
         ORDER BY position ASC, id ASC'
    )->fetchAll();

    jsonResponder([
        'banners' => $banners
    ]);

} catch (Exception $e) {
    jsonResponder([
        'banners' => [],
        'error' => $e->getMessage()
    ], 200);
}
