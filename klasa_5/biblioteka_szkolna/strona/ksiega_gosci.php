<?php
require_once '../database/db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tresc = $conn->real_escape_string($_POST['tresc']);
    $nick = $conn->real_escape_string($_POST['nick']);
    $email = $conn->real_escape_string($_POST['email']);
    if ($tresc && $nick && $email) {
        $conn->query("insert into ksiega_gosci (tresc, nick, email) values ('$tresc', '$nick', '$email')");
        echo '<p>Twój wpis został przesłany do akceptacji.</p>';
    }
}
?>
<h2>Księga gości</h2>
<form method="post">
  <textarea name="tresc" placeholder="Treść wpisu" required></textarea>
  <input type="text" name="nick" placeholder="Nick" required>
  <input type="email" name="email" placeholder="E-mail" required>
  <button type="submit">Wyślij</button>
  <button type="reset">Resetuj</button>
</form>
<h3>Ostatnie zatwierdzone wpisy:</h3>
<?php
$res = $conn->query("select nick, tresc, data_dodania from ksiega_gosci where zatwierdzony=1 and odrzucony=0 order by data_dodania desc limit 10");
while ($row = $res->fetch_assoc()) {
    echo '<div class="guestbook-entry">';
    echo '<div class="guestbook-nick">' . htmlspecialchars($row['nick']) . '</div>';
    echo '<div class="guestbook-date">' . htmlspecialchars($row['data_dodania']) . '</div>';
    echo '<div>' . nl2br(htmlspecialchars($row['tresc'])) . '</div>';
    echo '</div>';
}
?>