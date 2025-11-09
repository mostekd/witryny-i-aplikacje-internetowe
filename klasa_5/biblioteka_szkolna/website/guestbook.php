<?php
require_once '../database/models/guestbook.php';
$guest = new Guestbook();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest->add_entry($_POST['nickname'], $_POST['email'], $_POST['message']);
}
$entries = $guest->get_approved_entries();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Księga gości | Biblioteka Szkolna</title>
<link rel="stylesheet" href="../static/css/user.css">
</head>
<body>
<?php include 'includes/menu.php'; ?>
<main>
  <h2>Dodaj wpis do księgi gości</h2>
  <form method="post">
    <label>Pseudonim:</label>
    <input type="text" name="nickname" required>

    <label>Email:</label>
    <input type="email" name="email">

    <label>Wiadomość:</label>
    <textarea name="message" rows="5" required></textarea>

    <input type="submit" value="Dodaj wpis">
  </form>

  <h2>Ostatnie wpisy</h2>
  <?php if (empty($entries)): ?>
    <p>Brak zatwierdzonych wpisów.</p>
  <?php else: ?>
    <?php foreach ($entries as $e): ?>
      <div class="guest-entry">
        <b><?= htmlspecialchars($e['nickname']) ?></b> napisał(a):<br>
        <?= nl2br(htmlspecialchars($e['message'])) ?><br>
        <small><?= htmlspecialchars($e['created_at']) ?></small>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</main>
</body>
</html>
