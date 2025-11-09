<?php
    session_start();
    $msg = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $msg = "Logowanie uczniów zostanie dodane w kolejnej wersji (integracja z bazą students).";
    }
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Logowanie ucznia | Biblioteka Szkolna</title>
        <link rel="stylesheet" href="../static/css/user.css">
    </head>
    <body>
        <header>
        <h1>Logowanie ucznia</h1>
        </header>
        <main>
        <?php if ($msg): ?><p><?= htmlspecialchars($msg) ?></p><?php endif; ?>
        <form method="post">
            <label>Email:</label> <input type="email" name="email" required><br>
            <label>PESEL:</label> <input type="text" name="pesel" required><br>
            <button type="submit">Zaloguj</button>
        </form>
        </main>
    </body>
</html>
