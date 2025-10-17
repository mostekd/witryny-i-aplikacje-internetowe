<?php
// Animowany baner ze zdjęciami z bazy (do_banera=1)
require_once '../database/db_connect.php';
$images = [];
$sql = "select sciezka, opis from zdjecia where do_banera=1 order by data_dodania desc limit 3";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $images[] = $row;
}
if (count($images) === 0) {
    $images = [
        ['sciezka' => '../images/baner1.jpg', 'opis' => 'Biblioteka'],
        ['sciezka' => '../images/baner2.jpg', 'opis' => 'Czytelnia'],
        ['sciezka' => '../images/baner3.jpg', 'opis' => 'Książki']
    ];
}
foreach ($images as $img) {
    echo '<img src="' . htmlspecialchars($img['sciezka']) . '" alt="' . htmlspecialchars($img['opis']) . '">';
}
?>