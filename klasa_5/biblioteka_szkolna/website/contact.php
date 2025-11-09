<?php
    require_once '../database/models/contact.php';
    $msg = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $contact = new Contact();
        $ok = $contact->send_message($_POST['topic'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['message']);
        $msg = $ok ? "Wiadomość została wysłana pomyślnie." : "Błąd podczas wysyłania wiadomości.";
    }
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Kontakt | Biblioteka Szkolna</title>
        <link rel="stylesheet" href="../static/css/user.css">
    </head>
    <body>
        <header>
        <h1>Kontakt z biblioteką</h1>
        <nav>
            <a href="index.php">Strona główna</a> |
            <a href="books.php">Książki</a> |
            <a href="guestbook.php">Księga gości</a>
        </nav>
        </header>

        <main>
        <?php if ($msg): ?><p><b><?= htmlspecialchars($msg) ?></b></p><?php endif; ?>

        <form method="post">
            <label>Temat:</label>
            <select name="topic">
            <option>inna sprawa</option>
            <option>zapytanie o dostępność książki</option>
            <option>prośba o rezerwację</option>
            </select><br>

            <label>Imię:</label> <input type="text" name="first_name"><br>
            <label>Nazwisko:</label> <input type="text" name="last_name"><br>
            <label>Email:</label> <input type="email" name="email" required><br>
            <label>Wiadomość:</label><br>
            <textarea name="message" rows="6" cols="50" required></textarea><br>
            <button type="submit">Wyślij</button>
        </form>
        </main>
    </body>
</html>
