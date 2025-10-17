<?php
session_start();
require_once '../../database/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('Missing student ID');
}

$id = (int)$_GET['id'];
$sql = "select * from uczniowie where id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    http_response_code(404);
    exit('Student not found');
}

header('Content-Type: application/json');
echo json_encode($result->fetch_assoc());