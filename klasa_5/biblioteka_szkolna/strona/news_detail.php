<?php
require_once '../database/db_connect.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "select tytul, data_publikacji, tresc, autor from news where id=$id limit 1";
$res = $conn->query($sql);
if ($row = $res->fetch_assoc()) {
    echo '<div class="news">';
    echo '<div class="news-title">' . htmlspecialchars($row['tytul']) . '</div>';
    echo '<div class="news-date">' . htmlspecialchars($row['data_publikacji']) . '</div>';
    echo '<div class="news-content">' . nl2br(htmlspecialchars($row['tresc'])) . '</div>';
    echo '<div class="news-author">' . htmlspecialchars($row['autor']) . '</div>';
    echo '<a class="news-more" href="index.php">Powrót</a>';
    echo '</div>';
} else {
    echo '<p>Nie znaleziono wpisu.</p>';
}
?>