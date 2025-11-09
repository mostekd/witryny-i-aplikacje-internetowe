<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../database/models/loan.php';
$loan = new Loan();
$loans = $loan->get_loans_by_student($_SESSION['student_id']);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Profil użytkownika</title>
<link rel="stylesheet" href="../static/css/user.css">
</head>
<body>
<?php include 'includes/menu.php'; ?>
<main>
  <h2>Witaj, <?= htmlspecialchars($_SESSION['student_name']) ?>!</h2>
  <p>Oto Twoje wypożyczenia:</p>

  <?php if (empty($loans)): ?>
    <p>Nie masz obecnie wypożyczonych książek.</p>
  <?php else: ?>
    <table class="book-table">
      <tr><th>Tytuł</th><th>Data wypożyczenia</th><th>Termin zwrotu</th><th>Status</th></tr>
      <?php foreach ($loans as $l): ?>
      <tr>
        <td><?= htmlspecialchars($l['title']) ?></td>
        <td><?= htmlspecialchars($l['date_borrowed']) ?></td>
        <td><?= htmlspecialchars($l['date_due']) ?></td>
        <td><?= $l['returned'] ? "✅ Zwrócona" : "📖 Wypożyczona" ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</main>
</body>
</html>
