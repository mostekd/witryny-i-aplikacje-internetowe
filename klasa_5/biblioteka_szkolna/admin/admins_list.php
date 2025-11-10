<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }

$db = new Database();
$res = $db->query("select id, username, full_name, email, created_at, last_login from admin order by id asc");
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>lista administratorów</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>👥 lista administratorów</h2>
  <nav>
    <a href="dashboard.php">panel</a>
    <a href="add_admin.php">dodaj admina</a>
    <a href="logout.php">wyloguj</a>
  </nav>
</header>
<main>
  <div class="card">
    <table>
      <tr><th>id</th><th>login</th><th>imię</th><th>email</th><th>utworzono</th><th>ostatnie log</th></tr>
      <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['username']) ?></td>
        <td><?= htmlspecialchars($r['full_name']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
        <td><?= htmlspecialchars($r['last_login'] ?? '-') ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</main>
</body>
</html>
