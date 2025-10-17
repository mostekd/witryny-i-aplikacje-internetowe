<?php
session_start();
require_once '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $conn->real_escape_string($_POST['login']);
    $password = $_POST['password'];
    
    $sql = "select id, haslo from administrator where login='$login' limit 1";
    $result = $conn->query($sql);
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['haslo'])) {
            $_SESSION['admin_id'] = $row['id'];
            header('Location: index.php');
            exit;
        }
    }
    
    $error = 'Nieprawidłowy login lub hasło';
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Panel administratora</title>
    <link rel="stylesheet" href="../css/styl_admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>Panel administratora</h1>
            <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" class="login-form">
                <input type="text" name="login" placeholder="Login" required>
                <input type="password" name="password" placeholder="Hasło" required>
                <button type="submit">Zaloguj</button>
            </form>
        </div>
    </div>
</body>
</html>