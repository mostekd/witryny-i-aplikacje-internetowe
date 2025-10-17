<?php
require_once __DIR__ . '/../db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $pass = $_POST['password'] ?? '';
    $stmt = executeQuery('SELECT * FROM admins WHERE login = ? LIMIT 1', 's', [$login]);
    $admin = fetchOne($stmt);
    if ($admin && password_verify($pass, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Błędny login lub hasło';
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin - login</title>
<link rel="stylesheet" href="/klasa_5/biblioteka_szkolna/style.css">
</head><body>
<div class="admin-login">
  <h2>Logowanie administratora</h2>
  <?php if (!empty($error)) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?>
  <form method="post">
    <label>Login<br><input name="login"></label><br>
    <label>Hasło<br><input name="password" type="password"></label><br>
    <button type="submit">Zaloguj</button>
  </form>
</div>
</body></html>
