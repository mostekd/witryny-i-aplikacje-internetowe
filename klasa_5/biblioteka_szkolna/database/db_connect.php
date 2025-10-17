<?php
// Połączenie z bazą danych (mysqli)
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'biblioteka_szkolna';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die('Błąd połączenia z bazą danych: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>