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
$sql = "select w.*, k.tytul 
        from wypozyczenia w 
        join ksiazki k on w.ksiazka_id = k.id 
        where w.uczen_id = $id 
        order by w.data_wypozyczenia desc";
$result = $conn->query($sql);

$loans = [];
while ($row = $result->fetch_assoc()) {
    $loans[] = $row;
}

header('Content-Type: application/json');
echo json_encode($loans);