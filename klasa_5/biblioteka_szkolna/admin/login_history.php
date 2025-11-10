<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }

$db = new Database();

$user_type = $_GET['user_type'] ?? '';
$status = $_GET['status'] ?? '';

$where = [];
$params = [];
$types = '';

if ($user_type !== '') { 
    $where[] = "user_type = ?"; 
    $params[] = $user_type; 
    $types .= 's'; 
}
if ($status !== '') { 
    $where[] = "success = ?"; 
    $params[] = (int)$status; 
    $types .= 'i'; 
}

$sql = "SELECT user_type, user_id, ip_address, user_agent, success, created_at
        FROM login_history";

if ($where) { $sql .= " WHERE " . implode(" AND ", $where); }

$sql .= " ORDER BY created_at DESC LIMIT 500";

$stmt = $db->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Historia logowań</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>🕓 Historia logowań</h2>
  <nav>
    <a href="dashboard.php">Panel</a>
    <a href="logout.php">Wyloguj</a>
  </nav>
</header>

<main>
  <div class="card">
    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;">
      <select name="user_type">
        <option value="">Wszyscy</option>
        <option value="admin" <?= $user_type==='admin' ? 'selected' : '' ?>>Administratorzy</option>
        <option value="student" <?= $user_type==='student' ? 'selected' : '' ?>>Uczniowie</option>
      </select>
      <select name="status">
        <option value="">Wszystkie</option>
        <option value="1" <?= $status==='1' ? 'selected' : '' ?>>Udane</option>
        <option value="0" <?= $status==='0' ? 'selected' : '' ?>>Nieudane</option>
      </select>
      <button type="submit">Filtruj</button>
    </form>

    <table>
      <tr><th>Typ użytkownika</th><th>ID</th><th>IP</th><th>Agent</th><th>Status</th><th>Data</th></tr>
      <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['user_type']) ?></td>
        <td><?= htmlspecialchars($r['user_id']) ?></td>
        <td><?= htmlspecialchars($r['ip_address']) ?></td>
        <td><?= htmlspecialchars(substr($r['user_agent'],0,50)) ?>...</td>
        <td><?= $r['success'] ? '✅' : '❌' ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</main>
</body>
</html>
