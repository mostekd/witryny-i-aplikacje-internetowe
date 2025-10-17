<?php
// Wyświetlanie newsów (blog)
require_once '../database/db_connect.php';
$sql = "select id, tytul, data_publikacji, wstep, autor from news order by data_publikacji desc limit 10";
$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        echo '<div class="news">';
        echo '<div class="news-title">' . htmlspecialchars($row['tytul']) . '</div>';
        echo '<div class="news-date">' . htmlspecialchars($row['data_publikacji']) . '</div>';
        echo '<div class="news-intro">' . nl2br(htmlspecialchars($row['wstep'])) . '</div>';
        echo '<a class="news-more" href="news_detail.php?id=' . (int)$row['id'] . '">więcej</a>';
        echo '<div class="news-author">' . htmlspecialchars($row['autor']) . '</div>';
        echo '</div>';
    }
} else {
    echo '<p>Brak wpisów.</p>';
}
?>