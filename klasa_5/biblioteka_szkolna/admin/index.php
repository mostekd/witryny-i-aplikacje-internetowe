<?php
require_once '../database/models/admin.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = new Admin();
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if ($admin->login($username, $password)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $msg = "❌ nieprawidłowy login lub hasło.";
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>logowanie administratora</title>
<link rel="stylesheet" href="../static/css/admin.css">
<style>
/* drobne nadpisania tylko tu */
body { background: linear-gradient(135deg,#6366f1,#a855f7); height:100vh; display:flex; align-items:center; justify-content:center; color:#fff;}
.login-box { background: rgba(255,255,255,0.12); padding:28px; border-radius:12px; width:360px; box-shadow:0 8px 30px rgba(0,0,0,0.35);}
label { display:block; margin-top:10px; color:#fff;}
input[type=text], input[type=password] { width:100%; padding:10px; border-radius:8px; border:none; margin-top:6px;}
input[type=submit] { margin-top:15px; width:100%; padding:10px; background:#22c55e; border-radius:8px; border:none; color:white; font-weight:600;}
</style>
</head>
<body>
<div class="login-box">
  <h2 style="text-align:center">🔐 logowanie administratora</h2>
  <?php if ($msg): ?><div class="alert alert-error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <form method="post">
    <label>login</label>
    <input type="text" name="username" required autocomplete="username">
    <label>hasło</label>
    <input type="password" name="password" required autocomplete="current-password">
    <input type="submit" value="zaloguj się">
  </form>
</div>
</body>
</html>
