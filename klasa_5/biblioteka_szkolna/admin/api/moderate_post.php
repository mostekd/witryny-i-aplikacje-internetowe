<?php
session_start();
require_once '../../database/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)$data['id'];
    $action = $data['action'];
    $admin_id = $_SESSION['admin_id'];
    
    if ($action === 'approve') {
        $sql = "update ksiega_gosci 
                set zatwierdzony=1, 
                    admin_id=$admin_id, 
                    data_akceptacji=current_timestamp 
                where id=$id";
    } elseif ($action === 'reject') {
        $sql = "update ksiega_gosci 
                set odrzucony=1,
                    admin_id=$admin_id,
                    data_akceptacji=current_timestamp 
                where id=$id";
    }
    
    if ($conn->query($sql)) {
        // Dodaj log
        $akcja = $action === 'approve' ? 'Zatwierdzono wpis' : 'Odrzucono wpis';
        $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, '$akcja w księdze gości #$id')");
        
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}
?>