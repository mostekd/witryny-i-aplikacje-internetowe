<?php
require_once '../database/models/contact.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = new Contact();
    $ok = $contact->send_message($_POST['topic'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['message']);
    $msg = $ok ? "✅ Wiadomość wysłana pomyślnie." : "❌ Błąd podczas wysyłania.";
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Kontakt | Biblioteka Szkolna</title>
<link rel="stylesheet" href="../static/css/user.css">
</head>
<body>
<?php include 'includes/menu.php'; ?>
<main>
  <h2>Kontakt z biblioteką</h2>
  <?php if ($msg): ?><p><b><?= htmlspecialchars($msg) ?></b></p><?php endif; ?>

  <form method="post">
    <label>Temat:</label>
    <select name="topic">
      <option>inna sprawa</option>
      <option>zapytanie o dostępność książki</option>
      <option>prośba o rezerwację</option>
    </select>

    <label>Imię:</label>
    <input type="text" name="first_name">

    <label>Nazwisko:</label>
    <input type="text" name="last_name">

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Wiadomość:</label>
    <textarea name="message" rows="6" required></textarea>

    <input type="submit" value="Wyślij">
  </form>
</main>
</body>
</html>
