<?php
/**
 * GalaTV API - Programacion semanal v2
 * Usa el campo `dias` (codigos L,M,J,V) en lugar de `dia`
 */

require_once __DIR__ . '/config.php';

$diasMap = [
    'L' => 'Lunes',
    'M' => 'Martes',
    'M2' => 'Miercoles',
    'J' => 'Jueves',
    'V' => 'Viernes',
    'S' => 'Sabado',
    'D' => 'Domingo'
];

try {
    $db = getDB();

    $stmt = $db->query(
        'SELECT id, titulo, categoria, dia, TIME_FORMAT(hora, "%H:%i") as hora, dias, imagen, posicion
         FROM programas
         ORDER BY posicion ASC, hora ASC'
    );

    $programacion = $stmt->fetchAll();

    foreach ($programacion as &$prog) {
        if (!empty($prog['dias'])) {
            $codigos = explode(',', $prog['dias']);
            $nombresDias = [];
            foreach ($codigos as $codigo) {
                $codigo = trim($codigo);
                if (isset($diasMap[$codigo])) {
                    $nombresDias[] = $diasMap[$codigo];
                }
            }
            $prog['dias_texto'] = implode(', ', $nombresDias);
        } else {
            $prog['dias_texto'] = $prog['dia'] ?? '';
        }
        if (empty($prog['dia']) && !empty($prog['dias'])) {
            $primerCodigo = trim(explode(',', $prog['dias'])[0]);
            $prog['dia'] = $diasMap[$primerCodigo] ?? '';
        }
    }

    jsonResponder([
        'programacion' => $programacion
    ]);

} catch (Exception $e) {
    jsonResponder([
        'programacion' => [],
        'error' => $e->getMessage()
    ], 200);
}
