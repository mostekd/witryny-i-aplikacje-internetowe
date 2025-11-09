a<?php
    require_once '../database/models/guestbook.php';
    $guest = new Guestbook();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $guest->add_entry($_POST['nickname'], $_POST['email'], $_POST['message']);
    }

    $entries = $guest->get_approved_entries();
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Księga gości | Biblioteka Szkolna</title>
        <link rel="stylesheet" href="../static/css/user.css">
    </head>
    <body>
        <header>
        <h1>Księga gości</h1>
        <nav>
            <a href="index.php">Strona główna</a> |
            <a href="books.php">Książki</a> |
            <a href="contact.php">Kontakt</a>
        </nav>
        </header>

        <main>
        <h2>Zostaw swój wpis</h2>
        <form method="post">
            <label>Pseudonim:</label> <input type="text" name="nickname" required><br>
            <label>Email:</label> <input type="email" name="email"><br>
            <label>Wiadomość:</label><br>
            <textarea name="message" rows="5" cols="50" required></textarea><br>
            <button type="submit">Dodaj wpis</button>
        </form>

        <h2>Wpisy użytkowników</h2>
        <?php if (empty($entries)): ?>
            <p>Brak zatwierdzonych wpisów.</p>
        <?php else: ?>
            <?php foreach ($entries as $e): ?>
            <div style="border:1px solid #ccc;padding:10px;margin:10px 0;">
                <b><?= htmlspecialchars($e['nickname']) ?></b> napisał(a):<br>
                <?= nl2br(htmlspecialchars($e['message'])) ?><br>
                <small><?= htmlspecialchars($e['created_at']) ?></small>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </main>
    </body>
</html>
