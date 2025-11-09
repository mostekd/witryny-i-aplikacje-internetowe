<?php
require_once '../database/models/admin.php';
session_start();

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = new Admin();
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($admin->login($username, $password)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $msg = "❌ Nieprawidłowy login lub hasło.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Logowanie administratora | Biblioteka Szkolna</title>
<link rel="stylesheet" href="../static/css/user.css">
<style>
body {
  background: linear-gradient(135deg, #6366f1, #a855f7);
  font-family: Arial, sans-serif;
  color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.login-box {
  background: rgba(255, 255, 255, 0.15);
  padding: 30px;
  border-radius: 20px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
  width: 360px;
  backdrop-filter: blur(10px);
  animation: fadeIn 0.7s ease;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
}

label {
  display: block;
  margin-top: 10px;
}

input[type=text],
input[type=password] {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 8px;
  margin-top: 5px;
}

input[type=submit] {
  margin-top: 15px;
  width: 100%;
  background-color: #22c55e;
  color: white;
  border: none;
  padding: 10px;
  font-size: 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s;
}

input[type=submit]:hover {
  background-color: #16a34a;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-15px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>
<div class="login-box">
  <h2>🔐 Logowanie administratora</h2>
  <?php if ($msg): ?>
    <p style="color:#fee2e2;text-align:center;"><?= htmlspecialchars($msg) ?></p>
  <?php endif; ?>
  <form method="post">
    <label>Login:</label>
    <input type="text" name="username" required>

    <label>Hasło:</label>
    <input type="password" name="password" required>

    <input type="submit" value="Zaloguj się">
  </form>

  <p style="text-align:center;margin-top:10px;">
    Nie masz konta? <a href="register.php" style="color:#93c5fd;">Zarejestruj administratora</a>
  </p>
</div>
</body>
</html>
