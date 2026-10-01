<?php
/**
 * GalaTV API - Programacion semanal
 * Endpoint: GET https://galatv.com.ar/api/programacion.php
 *
 * Tabla: `programas`
 * Campos: id, titulo, categoria, dia, hora, dias, imagen, posicion
 *
 * El campo `dias` usa codigos: L=Lu, M=Ma, M2=Mi, J=Ju, V=Vi, S=Sa, D=Do
 */

require_once __DIR__ . '/config.php';

// Mapeo de codigos de dias
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

    // Traer toda la programacion
    $stmt = $db->query(
        'SELECT id, titulo, categoria, dia, TIME_FORMAT(hora, "%H:%i") as hora, dias, imagen, posicion
         FROM programas
         ORDER BY posicion ASC, hora ASC'
    );

    $programacion = $stmt->fetchAll();

    // Convertir el campo dias (L,M,J) a texto legible
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
        // Si el campo dia esta vacio, usar el primer dia de dias
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
