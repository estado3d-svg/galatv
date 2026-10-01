<?php
/**
 * GalaTV API - Estado del stream v3
 * Con header Referer para que la YouTube API acepte la peticion
 * desde el servidor con restriccion de sitios web
 */

require_once __DIR__ . '/config.php';

if (!defined('YOUTUBE_API_KEY')) {
    define('YOUTUBE_API_KEY', 'AIzaSyBACy36qlNgVTUuLuT0EAXs3pimckL3JMw');
}

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

    // Video de respaldo desde settings
    $videoRespaldo = YOUTUBE_FALLBACK_VIDEO_ID;
    try {
        $stmtSettings = $db->query('SELECT off_link FROM settings LIMIT 1');
        $settings = $stmtSettings->fetch();
        if ($settings && !empty($settings['off_link'])) {
            $videoRespaldo = $settings['off_link'];
        }
    } catch (Exception $e) {}

    if (strpos($videoRespaldo, 'youtube.com/watch') !== false) {
        parse_str(parse_url($videoRespaldo, PHP_URL_QUERY), $params);
        $videoRespaldo = $params['v'] ?? $videoRespaldo;
    } elseif (strpos($videoRespaldo, 'youtu.be/') !== false) {
        $videoRespaldo = substr($videoRespaldo, strrpos($videoRespaldo, '/') + 1);
    }

    // Verificar vivo con YouTube API - enviar Referer para pasar la restriccion de sitios web
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

    // Siguiente programa usando campo `dias`
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

        $stmtNext = $db->prepare(
            'SELECT titulo, TIME_FORMAT(hora, "%H:%i") as hora, imagen, categoria
             FROM programas
             WHERE FIND_IN_SET(?, REPLACE(dias, " ", "")) > 0
               AND hora > ?
             ORDER BY hora ASC LIMIT 1'
        );
        $stmtNext->execute([$codigoDiaActual, $horaActual]);
        $siguiente = $stmtNext->fetch();

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
    } catch (Exception $e) {}

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
