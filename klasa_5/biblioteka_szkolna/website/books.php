<?php
    require_once '../database/models/book.php';
    $bookModel = new Book();

    $search = $_GET['q'] ?? '';
    $books = $bookModel->get_all_books();

    if ($search) {
        $search = strtolower($search);
        $books = array_filter($books, function($b) use ($search) {
            return str_contains(strtolower($b['title']), $search)
                || str_contains(strtolower($b['author']), $search);
        });
    }
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Książki | Biblioteka Szkolna</title>
        <link rel="stylesheet" href="../static/css/user.css">
    </head>
    <body>
        <header>
        <h1>Książki</h1>
        <nav>
            <a href="index.php">Strona główna</a> |
            <a href="guestbook.php">Księga gości</a> |
            <a href="contact.php">Kontakt</a>
        </nav>
        </header>

        <main>
        <form method="get">
            <input type="text" name="q" placeholder="Szukaj tytułu lub autora" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Szukaj</button>
        </form>

        <h2>Dostępne książki</h2>
        <?php if (empty($books)): ?>
            <p>Brak wyników.</p>
        <?php else: ?>
            <table border="1" cellpadding="6" cellspacing="0">
            <tr><th>Tytuł</th><th>Autor</th><th>Rok</th><th>Wydawca</th></tr>
            <?php foreach ($books as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['title']) ?></td>
                <td><?= htmlspecialchars($b['author']) ?></td>
                <td><?= htmlspecialchars($b['year']) ?></td>
                <td><?= htmlspecialchars($b['publisher']) ?></td>
            </tr>
            <?php endforeach; ?>
            </table>
        <?php endif; ?>
        </main>
    </body>
</html>
