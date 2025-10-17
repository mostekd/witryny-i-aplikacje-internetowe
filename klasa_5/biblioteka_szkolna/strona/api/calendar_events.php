<?php
require_once '../../database/db_connect.php';

header('Content-Type: application/json');

// Pobieranie wydarzeń
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
    if ($userId) {
        $sql = "select id, tytul, opis, data_rozpoczecia, data_zakonczenia, typ, kolor 
                from kalendarz_wydarzenia 
                where uzytkownik_id = $userId 
                order by data_rozpoczecia";
        $result = $conn->query($sql);
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        echo json_encode($events);
    }
}

// Dodawanie wydarzenia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['userId'], $data['title'], $data['startDate'])) {
        $userId = (int)$data['userId'];
        $title = $conn->real_escape_string($data['title']);
        $desc = $conn->real_escape_string($data['description'] ?? '');
        $start = $conn->real_escape_string($data['startDate']);
        $end = $conn->real_escape_string($data['endDate'] ?? $data['startDate']);
        $color = $conn->real_escape_string($data['color'] ?? '#3949ab');
        
        $sql = "insert into kalendarz_wydarzenia (uzytkownik_id, tytul, opis, data_rozpoczecia, data_zakonczenia, kolor) 
                values ($userId, '$title', '$desc', '$start', '$end', '$color')";
        
        if ($conn->query($sql)) {
            http_response_code(201);
            echo json_encode(['message' => 'Wydarzenie dodane']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Błąd podczas dodawania wydarzenia']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Brak wymaganych danych']);
    }
}
?>