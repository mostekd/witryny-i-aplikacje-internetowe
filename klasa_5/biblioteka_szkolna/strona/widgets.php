<div class="widget" id="calendar-widget">
  <div id="calendar"></div>
</div>
<div class="widget" id="weather-widget">
  <div id="weather"></div>
</div>
<div class="widget" id="random-photo">
<?php
require_once '../database/db_connect.php';
$sql = "select sciezka, opis from zdjecia order by rand() limit 1";
$res = $conn->query($sql);
if ($row = $res->fetch_assoc()) {
    echo '<img src="' . htmlspecialchars($row['sciezka']) . '" alt="' . htmlspecialchars($row['opis']) . '" style="width:100%;border-radius:12px;">';
}
?>
</div>