<?php
    require_once '../database/models/news.php';
    $newsModel = new News();
    $articles = $newsModel->get_published_news();
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Biblioteka Szkolna</title>
        <link rel="stylesheet" href="../static/css/user.css">
    </head>
    <body>
        <header>
        <h1>📚 Biblioteka Szkolna</h1>
        <nav>
            <a href="index.php">Strona główna</a> |
            <a href="books.php">Książki</a> |
            <a href="guestbook.php">Księga gości</a> |
            <a href="contact.php">Kontakt</a> |
            <a href="login.php">Zaloguj się</a>
        </nav>
        </header>

        <main>
        <h2>Aktualności</h2>
        <?php if (empty($articles)): ?>
            <p>Brak aktualności do wyświetlenia.</p>
        <?php else: ?>
            <?php foreach ($articles as $item): ?>
            <article>
                <h3><?= htmlspecialchars($item['title']) ?></h3>
                <small>Opublikowano: <?= htmlspecialchars($item['published_at']) ?></small>
                <p><?= nl2br(htmlspecialchars($item['excerpt'])) ?></p>
            </article>
            <hr>
            <?php endforeach; ?>
        <?php endif; ?>
        </main>
    </body>
</html>
