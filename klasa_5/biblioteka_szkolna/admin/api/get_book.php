<?php
session_start();
require_once '../../database/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "select * from ksiazki where id=$id limit 1";
    $result = $conn->query($sql);
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Book not found']);
    }
}
?>