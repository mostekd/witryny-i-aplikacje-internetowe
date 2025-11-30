<?php
/**
 * Strona główna biblioteki
 */

session_start();

require_once __DIR__ . '/database/config.php';

// Spróbuj załadować dane z bazy, ale obsługuj błędy
$banersArray = [];
$page = isset($_GET['page']) ? sanitize($_GET['page']) : 'home';

try {
    require_once __DIR__ . '/database/News.php';
    require_once __DIR__ . '/database/Baner.php';
    
    $banerObj = new Baner();
    $banersResult = $banerObj->getAll(true);
    
    if ($banersResult) {
        while ($baner = $banersResult->fetch_assoc()) {
            $banersArray[] = $baner;
        }
    }
} catch (Exception $e) {
    // Jeśli baza nie jest dostępna, użyj domyślnych banerów
    error_log("Błąd bazy danych: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteka Szkoły - Wesoła Szkoła</title>
    <link rel="stylesheet" href="/github/witryny-i-aplikacje-internetowe/klasa_5/biblioteka_szkolna_v2/static/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="header-container">
            <div class="logo">
                <i class="fas fa-book"></i>
                <span>Biblioteka Szkolna</span>
            </div>
            <nav>
                <ul>
                    <li><a href="?page=home" class="<?php echo $page === 'home' ? 'active' : ''; ?>">Strona główna</a></li>
                    <li><a href="?page=search" class="<?php echo $page === 'search' ? 'active' : ''; ?>">Katalog</a></li>
                    <li><a href="?page=guestbook" class="<?php echo $page === 'guestbook' ? 'active' : ''; ?>">Księga gości</a></li>
                    <li><a href="?page=contact" class="<?php echo $page === 'contact' ? 'active' : ''; ?>">Kontakt</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- BANNER / CAROUSEL -->
    <div class="banner-container" id="banner-container">
        <?php if (!empty($banersArray)): ?>
            <?php foreach ($banersArray as $index => $baner): ?>
                <div class="banner <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo IMAGES_PATH; ?>/baner/<?php echo (basename($baner['sciezka_zdjecia'])); ?>" 
                         alt="<?php echo ($baner['tytul'] ?? 'Baner'); ?>">
                </div>
            <?php endforeach; ?>
            
            <div class="banner-controls">
                <?php foreach ($banersArray as $index => $baner): ?>
                    <div class="banner-dot <?php echo $index === 0 ? 'active' : ''; ?>"></div>
                <?php endforeach; ?>
            </div>
            <button class="banner-controls-arrow prev">&lt;</button>
            <button class="banner-controls-arrow next">&gt;</button>
        <?php else: ?>
            <div class="banner active" style="background: linear-gradient(135deg, #2c3e50, #e74c3c); display: flex; align-items: center; justify-content: center;">
                <div style="text-align: center; color: white;">
                    <h2>Biblioteka Szkoły - Wesoła Szkoła</h2>
                    <p>ul. Szkolna 1, 54-230 Gdańsk</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- MAIN CONTENT -->
    <main>
        <!-- SIDEBAR - ANIMOWANE MENU I WIDGETY -->
        <aside class="sidebar">
            <!-- Menu nawigacyjne -->
            <div class="sidebar-menu">
                <h3><i class="fas fa-bars"></i> Menu</h3>
                <ul>
                    <li><a href="?page=home" class="<?php echo $page === 'home' ? 'active' : ''; ?>">Strona główna</a></li>
                    <li><a href="?page=search" class="<?php echo $page === 'search' ? 'active' : ''; ?>">Wyszukiwanie</a></li>
                    <li><a href="?page=guestbook" class="<?php echo $page === 'guestbook' ? 'active' : ''; ?>">Księga gości</a></li>
                    <li><a href="?page=contact" class="<?php echo $page === 'contact' ? 'active' : ''; ?>">Kontakt</a></li>
                </ul>
            </div>

            <!-- Widget - Pogoda -->
            <div class="widget">
                <div class="weather-widget">
                    <h4>Pogoda</h4>
                    <div class="weather-icon">⛅</div>
                    <p>Ładowanie...</p>
                </div>
            </div>

            <!-- Widget - Kalendarz -->
            <div class="widget">
                <div class="calendar-widget">
                    <!-- Kalendarz zostanie wygenerowany przez JavaScript -->
                </div>
            </div>

            <!-- Widget - Losowe zdjęcie -->
            <div class="widget">
                <h4>Artykuły</h4>
                <?php 
                    $randomImage = null;
                    try {
                        if (isset($newsObj)) {
                            $randomImage = $newsObj->getRandomImage();
                        }
                    } catch (Exception $e) {
                        // Baza niedostępna
                    }
                    
                    if ($randomImage):
                ?>
                    <img src="<?php echo IMAGES_PATH; ?>/<?php echo (basename($randomImage)); ?>" 
                         alt="Artykuł" style="width: 100%; border-radius: 4px; max-height: 200px; object-fit: cover;">
                    <p style="margin-top: 1rem; text-align: center; color: #999; font-size: 0.9rem;">Losowe zdjęcie z artykułów</p>
                <?php else: ?>
                    <p style="text-align: center; color: #999;">Brak artykułów ze zdjęciami</p>
                <?php endif; ?>
            </div>
        </aside>

        <!-- CONTENT AREA -->
        <div class="content">
            <?php
                // Routing stron
                switch ($page) {
                    case 'search':
                        include __DIR__ . '/website/search.php';
                        break;
                    case 'guestbook':
                        include __DIR__ . '/website/guestbook.php';
                        break;
                    case 'contact':
                        include __DIR__ . '/website/contact.php';
                        break;
                    case 'article':
                        include __DIR__ . '/website/article.php';
                        break;
                    case 'home':
                    default:
                        include __DIR__ . '/website/home.php';
                        break;
                }
            ?>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>O nas</h3>
                <p><strong><?php echo LIBRARY_NAME; ?></strong></p>
                <p><?php echo LIBRARY_ADDRESS; ?></p>
                <p>Tel: <?php echo LIBRARY_PHONE; ?></p>
            </div>
            <div class="footer-section">
                <h3>Kontakt</h3>
                <p>E-mail: <a href="mailto:<?php echo LIBRARY_EMAIL; ?>"><?php echo LIBRARY_EMAIL; ?></a></p>
                <p>Godziny otwarcia:<br>Pn-Pt: 8:00-16:00<br>So: 8:00-12:00</p>
            </div>
            <div class="footer-section">
                <h3>Szybkie linki</h3>
                <ul>
                    <li><a href="?page=search">Katalog książek</a></li>
                    <li><a href="?page=guestbook">Księga gości</a></li>
                    <li><a href="?page=contact">Kontakt</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Informacje</h3>
                <p>Domyślny okres wypożyczenia: <?php echo DEFAULT_LOAN_PERIOD; ?> dni</p>
                <p>© 2025 Biblioteka Szkolna - Wesoła Szkoła</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Wszystkie prawa zastrzeżone. Wykonane dla TEB Częstochowa.</p>
        </div>
    </footer>

    <script src="<?php echo STATIC_PATH; ?>/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
