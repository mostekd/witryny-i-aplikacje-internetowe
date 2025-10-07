<?php
// db.php - simple PDO connection (edit credentials for your environment)
$dbHost = '127.0.0.1';
$dbName = 'biblioteka';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('DB connection failed: ' . $e->getMessage());
}

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
