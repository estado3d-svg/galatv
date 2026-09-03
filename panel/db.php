<?php
// Conexión a la base de datos MySQL (DonWeb/Ferozo)
// Credenciales definidas en config.php / config.local.php

function db() {
    static $pdo = null;
    if ($pdo === null) {
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $name = defined('DB_NAME') ? DB_NAME : '';
        $user = defined('DB_USER') ? DB_USER : '';
        $pass = defined('DB_PASS') ? DB_PASS : '';
        $dsn = "mysql:host=$host;dbname=$name;charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, $user, $pass, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'DB connection error: ' . $e->getMessage()]);
            exit;
        }
    }
    return $pdo;
}
