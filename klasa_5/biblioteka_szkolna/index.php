<?php
require_once 'database/db_connect.php';

// Pobieranie ustawień
$settings = [];
$result = $conn->query("select klucz, wartosc from ustawienia");
while ($row = $result->fetch_assoc()) {
    $settings[$row['klucz']] = $row['wartosc'];
}

// Pobieranie newsów
$newsLimit = isset($settings['posts_per_page']) ? (int)$settings['posts_per_page'] : 5;
$sql = "select n.*, z.sciezka as zdjecie_sciezka 
        from news n 
        left join zdjecia z on n.zdjecie_id = z.id 
        order by n.data_publikacji desc 
        limit $newsLimit";
$newsResult = $conn->query($sql);

// Pobieranie banera
$bannerSql = "select sciezka, opis from zdjecia where do_banera = 1 order by data_dodania desc limit 1";
$bannerResult = $conn->query($bannerSql);
$banner = $bannerResult->fetch_assoc();

// Pobieranie książek do wyszukiwarki
$booksSql = "select k.id, k.tytul, k.autor, k.isbn, k.rok_wydania, k.aktywna, z.sciezka as zdjecie_sciezka 
             from ksiazki k 
             left join zdjecia z on k.zdjecie_id = z.id 
             order by k.tytul";
$booksResult = $conn->query($booksSql);
$books = [];
while ($row = $booksResult->fetch_assoc()) {
    $books[] = $row;
}

?><!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($settings['site_name'] ?? 'Biblioteka Szkolna') ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="main-header">
        <?php if ($banner): ?>
        <div class="banner">
            <img src="<?= htmlspecialchars($banner['sciezka']) ?>" alt="<?= htmlspecialchars($banner['opis']) ?>">
        </div>
        <?php endif; ?>
        <h1><?= htmlspecialchars($settings['site_name'] ?? 'Biblioteka Szkolna') ?></h1>
    </header>

    <nav class="main-nav">
        <a href="#news">Aktualności</a>
        <a href="#search">Szukaj książki</a>
        <a href="#guestbook">Księga gości</a>
        <a href="admin/login.php">Panel admina</a>
    </nav>

    <main>
        <!-- Widgety -->
        <section class="widgets">
            <?php if (($settings['show_weather_widget'] ?? '1') == '1'): ?>
            <div class="widget weather-widget">
                <?php include 'components/weather.php'; ?>
            </div>
            <?php endif; ?>
            <div class="widget calendar-widget">
                <?php include 'components/calendar.php'; ?>
            </div>
        </section>

        <!-- News -->
        <section id="news" class="news-section">
            <h2>Aktualności</h2>
            <?php while ($news = $newsResult->fetch_assoc()): ?>
            <article class="news-item">
                <?php if ($news['zdjecie_sciezka']): ?>
                <img src="<?= htmlspecialchars($news['zdjecie_sciezka']) ?>" alt="News image" class="news-image">
                <?php endif; ?>
                <div class="news-content">
                    <h3><?= htmlspecialchars($news['tytul']) ?></h3>
                    <div class="news-meta">
                        <span><?= date('d.m.Y', strtotime($news['data_publikacji'])) ?></span>
                        <span>Autor: <?= htmlspecialchars($news['autor']) ?></span>
                    </div>
                    <p class="news-lead"><?= htmlspecialchars($news['wstep']) ?></p>
                    <div class="news-body"><?= nl2br(htmlspecialchars($news['tresc'])) ?></div>
                </div>
            </article>
            <?php endwhile; ?>
        </section>

        <!-- Wyszukiwarka książek -->
        <section id="search" class="search-section">
            <h2>Wyszukiwarka książek</h2>
            <input type="text" id="bookSearch" placeholder="Wpisz tytuł, autora lub ISBN..." oninput="filterBooks(this.value)">
            <div class="books-list" id="booksList">
                <?php foreach ($books as $book): ?>
                <div class="book-item <?= $book['aktywna'] ? '' : 'inactive' ?>">
                    <?php if ($book['zdjecie_sciezka']): ?>
                    <img src="<?= htmlspecialchars($book['zdjecie_sciezka']) ?>" alt="Okładka" class="book-cover">
                    <?php endif; ?>
                    <div class="book-info">
                        <h3><?= htmlspecialchars($book['tytul']) ?></h3>
                        <p>Autor: <?= htmlspecialchars($book['autor']) ?></p>
                        <p>ISBN: <?= htmlspecialchars($book['isbn']) ?></p>
                        <p>Rok wydania: <?= htmlspecialchars($book['rok_wydania']) ?></p>
                        <?php if (!$book['aktywna']): ?><span class="badge badge-inactive">Niedostępna</span><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Księga gości -->
        <section id="guestbook">
            <?php include 'components/guestbook.php'; ?>
        </section>
    </main>

    <footer class="main-footer">
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name'] ?? 'Biblioteka Szkolna') ?>. Wszelkie prawa zastrzeżone.</p>
    </footer>

    <script>
    function filterBooks(query) {
        query = query.toLowerCase();
        const books = document.getElementById('booksList').getElementsByClassName('book-item');
        for (let book of books) {
            const text = book.textContent.toLowerCase();
            book.style.display = text.includes(query) ? '' : 'none';
        }
    }
    </script>
</body>
</html>