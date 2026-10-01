<?php
/**
 * GalaTV API - Estado del stream
 * Endpoint: GET https://galatv.com.ar/api/estado.php
 */

require_once __DIR__ . '/config.php';

if (!defined('YOUTUBE_API_KEY')) {
    define('YOUTUBE_API_KEY', 'AIzaSyBACy36qlNgVTUuLuT0EAXs3pimckL3JMw');
}
if (!defined('YOUTUBE_CHANNEL_HANDLE')) {
    define('YOUTUBE_CHANNEL_HANDLE', '@GalaTvStreaming');
}

// Mapeo de codigos de dias (formato usado en el campo `dias` de la tabla programas)
$diasMap = [
    'L' => 'Lunes',
    'M' => 'Martes',
    'M2' => 'Miercoles',
    'J' => 'Jueves',
    'V' => 'Viernes',
    'S' => 'Sabado',
    'D' => 'Domingo'
];

// Mapeo inverso: nombre del dia -> codigo
$diasCodigo = [
    'Lunes' => 'L',
    'Martes' => 'M',
    'Miercoles' => 'M2',
    'Jueves' => 'J',
    'Viernes' => 'V',
    'Sabado' => 'S',
    'Domingo' => 'D'
];

try {
    $db = getDB();

    // ===== Obtener video de respaldo desde settings =====
    $videoRespaldo = YOUTUBE_FALLBACK_VIDEO_ID;
    try {
        $stmtSettings = $db->query('SELECT off_link FROM settings LIMIT 1');
        $settings = $stmtSettings->fetch();
        if ($settings && !empty($settings['off_link'])) {
            $videoRespaldo = $settings['off_link'];
        }
    } catch (Exception $e) {
        // Si la tabla settings no existe, usar el fallback
    }

    // Si off_link es una URL completa, extraer el ID del video
    if (strpos($videoRespaldo, 'youtube.com/watch') !== false) {
        parse_str(parse_url($videoRespaldo, PHP_URL_QUERY), $params);
        $videoRespaldo = $params['v'] ?? $videoRespaldo;
    } elseif (strpos($videoRespaldo, 'youtu.be/') !== false) {
        $videoRespaldo = substr($videoRespaldo, strrpos($videoRespaldo, '/') + 1);
    }

    // ===== Verificar si hay vivo con la API de YouTube =====
    $enVivo = false;
    $streamUrl = null;
    $tituloLive = 'Gala TV Streaming';

    if (function_exists('curl_init')) {
        $youtubeLiveUrl = sprintf(
            'https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=%s&eventType=live&type=video&key=%s',
            YOUTUBE_CHANNEL_ID,
            YOUTUBE_API_KEY
        );

        $ch = curl_init($youtubeLiveUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_REFERER => 'https://galatv.com.ar/',
            CURLOPT_HTTPHEADER => [
                'Referer: https://galatv.com.ar/',
                'Origin: https://galatv.com.ar',
            ],
        ]);
        $apiResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $apiResponse) {
            $data = json_decode($apiResponse, true);
            if (!empty($data['items'])) {
                $enVivo = true;
                $streamUrl = $data['items'][0]['id']['videoId'];
                $tituloLive = $data['items'][0]['snippet']['title'];
            }
        }
    }

    // ===== Calcular siguiente programa desde la tabla `programas` =====
    // El campo `dias` contiene codigos como L,M,J,V separados por coma
    // El campo `dia` puede estar vacio o tener texto como "MARTES"
    $siguiente = null;

    try {
        $diasEspanol = [
            'Sunday' => 'Domingo',
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miercoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sabado'
        ];
        $nombreDiaActual = $diasEspanol[date('l')] ?? date('l');
        $codigoDiaActual = $diasCodigo[$nombreDiaActual] ?? '';
        $horaActual = date('H:i:s');

        // Buscar programas que contengan el codigo del dia actual en el campo `dias`
        // y cuya hora sea mayor a la hora actual
        // Como dias es tipo "L,M,J", usamos FIND_IN_SET con separador coma
        $stmtNext = $db->prepare(
            'SELECT titulo, TIME_FORMAT(hora, "%H:%i") as hora, imagen, categoria
             FROM programas
             WHERE FIND_IN_SET(?, REPLACE(dias, " ", "")) > 0
               AND hora > ?
             ORDER BY hora ASC LIMIT 1'
        );
        $stmtNext->execute([$codigoDiaActual, $horaActual]);
        $siguiente = $stmtNext->fetch();

        // Si no hay mas programas hoy, buscar el primero de manana
        if (!$siguiente) {
            $mananaTimestamp = strtotime('tomorrow');
            $nombreManana = $diasEspanol[date('l', $mananaTimestamp)] ?? date('l', $mananaTimestamp);
            $codigoManana = $diasCodigo[$nombreManana] ?? 'L';

            $stmtNext2 = $db->prepare(
                'SELECT titulo, TIME_FORMAT(hora, "%H:%i") as hora, imagen, categoria
                 FROM programas
                 WHERE FIND_IN_SET(?, REPLACE(dias, " ", "")) > 0
                 ORDER BY hora ASC LIMIT 1'
            );
            $stmtNext2->execute([$codigoManana]);
            $siguiente = $stmtNext2->fetch();
        }
    } catch (Exception $e) {
        // Si hay error con la tabla, seguir sin siguiente programa
    }

    // ===== Construir respuesta =====
    jsonResponder([
        'en_vivo' => $enVivo,
        'stream_url' => $streamUrl,
        'video_youtube_id' => $videoRespaldo,
        'titulo' => $tituloLive,
        'descripcion' => 'Transmision en directo de Gala TV',
        'siguiente_programa' => $siguiente['titulo'] ?? null,
        'siguiente_hora' => $siguiente['hora'] ?? null,
        'siguiente_imagen' => $siguiente['imagen'] ?? null,
        'siguiente_categoria' => $siguiente['categoria'] ?? null
    ]);

} catch (Exception $e) {
    jsonResponder([
        'en_vivo' => false,
        'stream_url' => null,
        'video_youtube_id' => YOUTUBE_FALLBACK_VIDEO_ID,
        'titulo' => 'Gala TV Streaming',
        'descripcion' => 'Transmision en directo',
        'siguiente_programa' => null,
        'siguiente_hora' => null,
        'siguiente_imagen' => null,
        'siguiente_categoria' => null,
        'error' => $e->getMessage()
    ], 200);
}
