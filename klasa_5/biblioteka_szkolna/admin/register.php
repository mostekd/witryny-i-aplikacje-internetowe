<?php
require_once '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $email = trim($_POST['email']);
    $haslo = $_POST['haslo'];
    $haslo2 = $_POST['haslo2'];

    $errors = [];
    if (empty($login) || empty($email) || empty($haslo) || empty($haslo2)) {
        $errors[] = 'Wszystkie pola są wymagane.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Nieprawidłowy adres email.';
    }
    if ($haslo !== $haslo2) {
        $errors[] = 'Hasła muszą być identyczne.';
    }
    if (strlen($haslo) < 6) {
        $errors[] = 'Hasło musi mieć co najmniej 6 znaków.';
    }
    $login = $conn->real_escape_string($login);
    $email = $conn->real_escape_string($email);
    $check = $conn->query("select id from administrator where login='$login' or email='$email'");
    if ($check->num_rows > 0) {
        $errors[] = 'Taki login lub email już istnieje.';
    }
    if (empty($errors)) {
        $hash = password_hash($haslo, PASSWORD_DEFAULT);
        $conn->query("insert into administrator (login, haslo, email) values ('$login', '$hash', '$email')");
        header('Location: login.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja administratora</title>
    <link rel="stylesheet" href="../css/styl_admin.css">
</head>
<body>
    <div class="admin-header"><h1>Rejestracja administratora</h1></div>
    <main class="admin-main">
        <form method="post" class="admin-form" autocomplete="off">
            <div class="form-group">
                <label>Login</label>
                <input type="text" name="login" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Hasło</label>
                <input type="password" name="haslo" required>
            </div>
            <div class="form-group">
                <label>Powtórz hasło</label>
                <input type="password" name="haslo2" required>
            </div>
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?= implode('<br>', $errors) ?>
            </div>
            <?php endif; ?>
            <div class="form-buttons">
                <button type="submit" class="btn-submit">Zarejestruj</button>
                <a href="login.php" class="btn-cancel">Powrót do logowania</a>
            </div>
        </form>
    </main>
</body>
</html>
