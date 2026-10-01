<?php
/**
 * GalaTV API - Configuracion
 * Subir este archivo a: galatv.com.ar/api/config.php
 *
 * Cambiar las credenciales de la base de datos por las reales.
 */

// ====== CONFIGURACION BASE DE DATOS ======
define('DB_HOST', 'localhost');
define('DB_NAME', 'c2642305_1');
define('DB_USER', 'c2642305_1');
define('DB_PASS', 'dq0/ljt*ZUQl7wQ');
define('DB_CHARSET', 'utf8mb4');

// Mostrar errores PHP (quitar en produccion si se desea)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// ====== CONFIGURACION API ======
define('API_KEY', 'galatv-secret-key-2024'); // Cambiar por una clave segura

// ====== CANAL DE YOUTUBE ======
define('YOUTUBE_CHANNEL_ID', 'UCNbKWLI2_ivAZIAQQ5ot6Ng');
define('YOUTUBE_FALLBACK_VIDEO_ID', 'dQw4w9WgXcQ'); // Video de respaldo si settings.off_link esta vacio

// ====== CORS HEADERS ======
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Conexion PDO a MySQL
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error de conexion a BD']);
            exit;
        }
    }
    return $pdo;
}

/**
 * Verificar API key (opcional - descomentar para activar)
 */
function checkApiKey(): void {
    $key = $_SERVER['HTTP_AUTHORIZATION'] ?? $_GET['key'] ?? '';
    $key = str_replace('Bearer ', '', $key);
    if ($key !== API_KEY) {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }
}

/**
 * Respuesta JSON
 */
function jsonResponder(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
