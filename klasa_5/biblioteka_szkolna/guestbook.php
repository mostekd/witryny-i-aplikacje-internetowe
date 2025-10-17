<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

// handle submission: store but not approve
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nick = $_POST['nick'] ?? '';
  $email = $_POST['email'] ?? '';
  $message = $_POST['message'] ?? '';
  $stmt = executeQuery('INSERT INTO guestbook_entries (nick,email,message) VALUES (?,?,?)', 'sss', [$nick,$email,$message]);
  $sent = true;
}

// show approved entries
$entries = $pdo->query('SELECT nick,message,submitted_at FROM guestbook_entries WHERE approved=1 ORDER BY submitted_at DESC')->fetchAll();
?>

<h2>Księga gości</h2>
<?php if (!empty($sent)): ?><p>Dziękujemy, wpis oczekuje na zatwierdzenie przez administratora.</p><?php endif; ?>
<form method="post">
  <label>Nick<br><input name="nick"></label><br>
  <label>E-mail<br><input name="email" type="email"></label><br>
  <label>Treść<br><textarea name="message"></textarea></label><br>
  <button type="submit">Wyślij</button>
  <button type="reset">Resetuj</button>
</form>

<h3>Wpisy</h3>
<?php foreach ($entries as $e): ?>
  <div class="guest-entry">
    <strong><?php echo h($e['nick']); ?></strong>
    <time><?php echo h($e['submitted_at']); ?></time>
    <p><?php echo nl2br(h($e['message'])); ?></p>
  </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
