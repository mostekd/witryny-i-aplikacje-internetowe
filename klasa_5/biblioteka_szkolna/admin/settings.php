<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }
$db = new Database();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $id = $_SESSION['admin_id'];
    $row = $db->query("select password_hash from admin where id = $id")->fetch_assoc();
    if ($row && password_verify($current, $row['password_hash'])) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $db->prepare("update admin set password_hash = ? where id = ?");
        $stmt->bind_param("si", $hash, $id);
        $stmt->execute();
        $msg = '<div class="alert alert-success">hasło zmienione pomyślnie</div>';
    } else {
        $msg = '<div class="alert alert-error">nieprawidłowe bieżące hasło</div>';
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>ustawienia</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>⚙️ ustawienia</h2>
  <nav><a href="dashboard.php">panel</a><a href="logout.php">wyloguj</a></nav>
</header>
<main>
  <div class="card">
    <?= $msg ?>
    <h4>zmiana hasła</h4>
    <form method="post">
      <label>bieżące hasło</label><input type="password" name="current_password" required>
      <label>nowe hasło</label><input type="password" name="new_password" required>
      <input type="submit" name="change_password" value="zmień hasło">
    </form>
  </div>
</main>
</body>
</html>
