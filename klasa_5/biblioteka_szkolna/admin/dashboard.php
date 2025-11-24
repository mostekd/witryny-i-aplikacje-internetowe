<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }

$db = new Database();
// proste statystyki
$count_books = $db->query("select count(*) as c from books")->fetch_assoc()['c'] ?? 0;
$count_students = $db->query("select count(*) as c from students")->fetch_assoc()['c'] ?? 0;
$count_loans = $db->query("select count(*) as c from loans")->fetch_assoc()['c'] ?? 0;
$recent_logins = $db->query("select user_type,user_id,ip_address,success,created_at from login_history order by created_at desc limit 5");
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>panel administratora</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>📚 panel administratora</h2>
  <nav>
    <a href="dashboard.php">strona główna</a>
    <a href="books.php">książki</a>
    <a href="students.php">uczestnicy</a>
    <a href="loans.php">wypożyczenia</a>
    <a href="news.php">news</a>
    <a href="guestbook.php">księga gości</a>
    <a href="admins_list.php">admini</a>
    <a href="login_history.php">historia logowań</a>
    <a href="settings.php">ustawienia</a>
    <a href="reports.php">raporty</a>
    <a href="logout.php">wyloguj</a>
  </nav>
</header>
<main>
  <div class="card">
    <h3>witaj, <?= htmlspecialchars($_SESSION['admin_name']) ?>!</h3>
    <p>podstawowe statystyki systemu:</p>
    <ul>
      <li>książki: <strong><?= $count_books ?></strong></li>
      <li>uczniowie: <strong><?= $count_students ?></strong></li>
      <li>wypożyczenia: <strong><?= $count_loans ?></strong></li>
    </ul>
  </div>

  <div class="card">
    <h4>ostatnie próby logowania</h4>
    <table>
      <tr><th>typ</th><th>user_id</th><th>ip</th><th>status</th><th>data</th></tr>
      <?php while ($r = $recent_logins->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['user_type']) ?></td>
        <td><?= htmlspecialchars($r['user_id']) ?></td>
        <td><?= htmlspecialchars($r['ip_address']) ?></td>
        <td><?= $r['success'] ? '✅' : '❌' ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</main>
</body>
</html>
