<?php
session_start();
require_once '../../database/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// Pobieranie parametrów filtrowania
$data = json_decode(file_get_contents('php://input'), true);

$adminId = isset($data['adminId']) ? (int)$data['adminId'] : null;
$dateFrom = $conn->real_escape_string($data['dateFrom']);
$dateTo = $conn->real_escape_string($data['dateTo']);
$search = $conn->real_escape_string($data['search']);

// Budowanie zapytania
$sql = "select l.*, a.login 
        from logi_admin l 
        join administrator a on l.admin_id = a.id 
        where 1=1";

if ($adminId) {
    $sql .= " and l.admin_id = $adminId";
}

if ($dateFrom) {
    $sql .= " and date(l.data_operacji) >= '$dateFrom'";
}

if ($dateTo) {
    $sql .= " and date(l.data_operacji) <= '$dateTo'";
}

if ($search) {
    $sql .= " and l.akcja like '%$search%'";
}

$sql .= " order by l.data_operacji desc limit 1000";

$result = $conn->query($sql);
$logs = [];

while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

header('Content-Type: application/json');
echo json_encode($logs);