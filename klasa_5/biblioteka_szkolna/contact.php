<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $topic = $_POST['topic'] ?? '';
  $first = $_POST['first'] ?? '';
  $last = $_POST['last'] ?? '';
  $email = $_POST['email'] ?? '';
  $message = $_POST['message'] ?? '';

  $to = 'biblioteka@wesolaszkola.pl';
  $subject = "Kontakt: " . $topic;
  $body = "Od: $first $last <$email>\n\n" . $message;
  $headers = "From: $email";
  // note: mail() may need server configured - this is a placeholder
  if (mail($to, $subject, $body, $headers)) {
    $sent = true;
  } else {
    $sent = 'error';
  }
}
?>

<h2>Kontakt</h2>
<?php if ($sent === true): ?><p>Wiadomość wysłana.</p><?php elseif ($sent === 'error'): ?><p>Wysyłanie nie powiodło się (sprawdź konfigurację serwera e-mail).</p><?php endif; ?>

<form method="post">
  <label>Temat
    <select name="topic">
      <option>Zapytanie o dostępność książki</option>
      <option>Prośba o rezerwację</option>
      <option>Inna sprawa</option>
    </select>
  </label>
  <label>Imię<br><input name="first"></label>
  <label>Nazwisko<br><input name="last"></label>
  <label>E-mail<br><input name="email" type="email"></label>
  <label>Treść<br><textarea name="message"></textarea></label>
  <button type="submit">Wyślij</button>
  <button type="reset">Resetuj</button>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
