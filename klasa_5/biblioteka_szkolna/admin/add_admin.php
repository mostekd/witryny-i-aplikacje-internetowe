<?php
session_start();
require_once '../database/models/admin.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = new Admin();
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $fullname = trim($_POST['full_name']);
    if ($admin->register($username, $password, $email, $fullname)) {
        $msg = '<div class="alert alert-success">administrator dodany pomyślnie.</div>';
    } else {
        $msg = '<div class="alert alert-error">błąd lub login już istnieje.</div>';
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>dodaj administratora</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>➕ dodaj administratora</h2>
  <nav>
    <a href="dashboard.php">panel</a>
    <a href="admins_list.php">lista adminów</a>
    <a href="logout.php">wyloguj</a>
  </nav>
</header>
<main>
  <div class="card">
    <?= $msg ?>
    <form method="post">
      <label>login</label>
      <input type="text" name="username" required>
      <label>hasło</label>
      <input type="password" name="password" required>
      <label>imię i nazwisko</label>
      <input type="text" name="full_name" required>
      <label>email</label>
      <input type="email" name="email" required>
      <input type="submit" value="dodaj administratora">
    </form>
  </div>
</main>
</body>
</html>
