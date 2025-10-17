<?php
require_once '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imie = trim($_POST['imie']);
    $nazwisko = trim($_POST['nazwisko']);
    $pesel = trim($_POST['pesel']);
    $email = trim($_POST['email']);
    $klasa = trim($_POST['klasa']);
    $haslo = $_POST['haslo'];
    $haslo2 = $_POST['haslo2'];

    $errors = [];
    if (empty($imie) || empty($nazwisko) || empty($pesel) || empty($email) || empty($haslo) || empty($haslo2)) {
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
    if (!preg_match('/^[0-9]{11}$/', $pesel)) {
        $errors[] = 'PESEL musi mieć 11 cyfr.';
    }
    $pesel = $conn->real_escape_string($pesel);
    $email = $conn->real_escape_string($email);
    $check = $conn->query("select id from uczniowie where pesel='$pesel' or email='$email'");
    if ($check->num_rows > 0) {
        $errors[] = 'Taki PESEL lub email już istnieje.';
    }
    if (empty($errors)) {
        $imie = $conn->real_escape_string($imie);
        $nazwisko = $conn->real_escape_string($nazwisko);
        $klasa = $conn->real_escape_string($klasa);
        $hash = password_hash($haslo, PASSWORD_DEFAULT);
        $conn->query("insert into uczniowie (imie, nazwisko, pesel, email, klasa, uwagi) values ('$imie', '$nazwisko', '$pesel', '$email', '$klasa', 'Rejestracja online')");
        $student_id = $conn->insert_id;
        $conn->query("insert into preferencje_uzytkownika (uzytkownik_id) values ($student_id)");
        // Możesz dodać logowanie lub przekierowanie
        header('Location: ../index.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja użytkownika</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="header-user"><h1>Rejestracja użytkownika</h1></div>
    <main class="section">
        <form method="post" class="guestbook-form-user" autocomplete="off">
            <div class="form-group-user">
                <label>Imię</label>
                <input type="text" name="imie" required>
            </div>
            <div class="form-group-user">
                <label>Nazwisko</label>
                <input type="text" name="nazwisko" required>
            </div>
            <div class="form-group-user">
                <label>PESEL</label>
                <input type="text" name="pesel" required pattern="[0-9]{11}" maxlength="11">
            </div>
            <div class="form-group-user">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group-user">
                <label>Klasa</label>
                <input type="text" name="klasa">
            </div>
            <div class="form-group-user">
                <label>Hasło</label>
                <input type="password" name="haslo" required>
            </div>
            <div class="form-group-user">
                <label>Powtórz hasło</label>
                <input type="password" name="haslo2" required>
            </div>
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?= implode('<br>', $errors) ?>
            </div>
            <?php endif; ?>
            <div class="form-buttons">
                <button type="submit" class="btn-submit-user">Zarejestruj</button>
                <a href="../index.php" class="btn-cancel">Powrót</a>
            </div>
        </form>
    </main>
</body>
</html>
