<?php
require_once '../database/db_connect.php';
$t = isset($_GET['tytul']) ? $conn->real_escape_string($_GET['tytul']) : '';
$a = isset($_GET['autor']) ? $conn->real_escape_string($_GET['autor']) : '';
$rok = isset($_GET['rok']) ? (int)$_GET['rok'] : '';
?>
<h2>Wyszukiwanie książek</h2>
<form method="get">
  <input type="text" name="tytul" placeholder="Tytuł" value="<?=htmlspecialchars($t)?>">
  <input type="text" name="autor" placeholder="Autor" value="<?=htmlspecialchars($a)?>">
  <input type="number" name="rok" placeholder="Rok wydania" value="<?=htmlspecialchars($rok)?>">
  <button type="submit">Szukaj</button>
</form>
<?php
if ($t || $a || $rok) {
    $q = "select tytul, autor, wydawnictwo, rok_wydania, isbn, uwagi from ksiazki where aktywna=1";
    if ($t) $q .= " and tytul like '%$t%'";
    if ($a) $q .= " and autor like '%$a%'";
    if ($rok) $q .= " and rok_wydania=$rok";
    $q .= " order by tytul";
    $res = $conn->query($q);
    if ($res && $res->num_rows > 0) {
        echo '<table class="table"><tr><th>Tytuł</th><th>Autor</th><th>Wydawnictwo</th><th>Rok</th><th>ISBN</th><th>Uwagi</th></tr>';
        while ($row = $res->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['tytul']) . '</td>';
            echo '<td>' . htmlspecialchars($row['autor']) . '</td>';
            echo '<td>' . htmlspecialchars($row['wydawnictwo']) . '</td>';
            echo '<td>' . htmlspecialchars($row['rok_wydania']) . '</td>';
            echo '<td>' . htmlspecialchars($row['isbn']) . '</td>';
            echo '<td>' . htmlspecialchars($row['uwagi']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Brak wyników.</p>';
    }
}
?>