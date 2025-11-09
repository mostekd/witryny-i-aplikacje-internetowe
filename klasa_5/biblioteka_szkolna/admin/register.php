<?php
require_once '../database/db.php';

$db = new Database();
$msg = "";

// Obsługa formularza rejestracji
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $fullname = trim($_POST['full_name']);

    // Sprawdzenie czy login już istnieje
    $stmt = $db->prepare("SELECT id FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $msg = "❌ Taki login już istnieje.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO admin (username, password_hash, email, full_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hash, $email, $fullname);
        if ($stmt->execute()) {
            $msg = "✅ Konto administratora zostało utworzone pomyślnie.";
        } else {
            $msg = "❌ Błąd podczas zapisu do bazy danych.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Rejestracja administratora | Biblioteka Szkolna</title>
<link rel="stylesheet" href="../static/css/user.css">
<style>
main {
  max-width: 450px;
  margin-top: 80px;
}
</style>
</head>
<body>
<main>
  <h2>🧑‍💼 Rejestracja administratora</h2>
  <p style="color:gray;">Utwórz konto administratora, aby zarządzać biblioteką.</p>

  <?php if ($msg): ?>
    <p><b><?= htmlspecialchars($msg) ?></b></p>
  <?php endif; ?>

  <form method="post">
    <label>Login:</label>
    <input type="text" name="username" required>

    <label>Hasło:</label>
    <input type="password" name="password" required>

    <label>Imię i nazwisko:</label>
    <input type="text" name="full_name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <input type="submit" value="Utwórz konto">
  </form>

  <p style="margin-top:20px;">
    Masz już konto? <a href="index.php">Zaloguj się</a>.
  </p>
</main>
</body>
</html>
