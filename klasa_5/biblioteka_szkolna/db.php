<?php
// db.php - MySQLi connection (edit credentials for your environment)
$dbHost = '127.0.0.1';
$dbName = 'biblioteka';
$dbUser = 'root';
$dbPass = '';

$mysqli = new mysqli();
$mysqli->real_connect($dbHost, $dbUser, $dbPass, $dbName);

if ($mysqli->connect_error) {
    die('DB connection failed: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Helper function to prepare and execute queries safely
function executeQuery($sql, $types = '', $params = []) {
    global $mysqli;
    $stmt = $mysqli->prepare($sql);
    
    if ($stmt === false) {
        die('Query preparation failed: ' . $mysqli->error);
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        die('Query execution failed: ' . $stmt->error);
    }
    
    return $stmt;
}

// Helper to fetch all rows
function fetchAll($stmt) {
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper to fetch single row
function fetchOne($stmt) {
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
