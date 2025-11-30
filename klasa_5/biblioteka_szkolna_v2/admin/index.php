<?php
/**
 * Główny panel administratora
 */

session_start();

require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../database/Ksiazka.php';
require_once __DIR__ . '/../database/Uczen.php';
require_once __DIR__ . '/../database/Wypozyczenie.php';
require_once __DIR__ . '/../database/News.php';
require_once __DIR__ . '/../database/Baner.php';
require_once __DIR__ . '/../database/KsiegaGosci.php';

// Sprawdzenie czy zalogowany
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'dashboard';
$subaction = isset($_GET['subaction']) ? sanitize($_GET['subaction']) : '';

// Inicjalizacja obiektów modeli
$ksiazkaObj = new Ksiazka();
$uczenObj = new Uczen();
$wypozyczeniaObj = new Wypozyczenie();
$newsObj = new News();
$banerObj = new Baner();
$ksiegaObj = new KsiegaGosci();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administracyjny - Biblioteka Szkolna</title>
    <link rel="stylesheet" href="/github/witryny-i-aplikacje-internetowe/klasa_5/biblioteka_szkolna_v2/static/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- HEADER -->
        <div class="admin-header">
            <h1><i class="fas fa-tachometer-alt"></i> Panel administracyjny</h1>
            <div class="admin-user">
                <span><i class="fas fa-user"></i> <?php echo ($_SESSION['admin_imie'] . ' ' . $_SESSION['admin_nazwisko']); ?></span>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Wyloguj</a>
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="admin-sidebar">
            <h3><i class="fas fa-bars"></i> Menu</h3>
            <ul>
                <li><a href="?action=dashboard" class="<?php echo $action === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Pulpit</a></li>
                <li><a href="?action=ksiazki" class="<?php echo $action === 'ksiazki' ? 'active' : ''; ?>"><i class="fas fa-book"></i> Książki</a></li>
                <li><a href="?action=uczniowie" class="<?php echo $action === 'uczniowie' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Uczniowie</a></li>
                <li><a href="?action=wypozyczenia" class="<?php echo $action === 'wypozyczenia' ? 'active' : ''; ?>"><i class="fas fa-exchange-alt"></i> Wypożyczenia</a></li>
                <li><a href="?action=raporty" class="<?php echo $action === 'raporty' ? 'active' : ''; ?>"><i class="fas fa-chart-bar"></i> Raporty</a></li>
                <li><a href="?action=news" class="<?php echo $action === 'news' ? 'active' : ''; ?>"><i class="fas fa-newspaper"></i> Artykuły</a></li>
                <li><a href="?action=banery" class="<?php echo $action === 'banery' ? 'active' : ''; ?>"><i class="fas fa-image"></i> Banery</a></li>
                <li><a href="?action=ksiega" class="<?php echo $action === 'ksiega' ? 'active' : ''; ?>"><i class="fas fa-comments"></i> Księga gości</a></li>
                <li><a href="?action=ustawienia" class="<?php echo $action === 'ustawienia' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Ustawienia</a></li>
            </ul>
        </div>

        <!-- CONTENT -->
        <div class="admin-content">
            <?php
                // Routing administratora
                switch ($action) {
                    case 'ksiazki':
                        include __DIR__ . '/pages/ksiazki.php';
                        break;
                    case 'uczniowie':
                        include __DIR__ . '/pages/uczniowie.php';
                        break;
                    case 'wypozyczenia':
                        include __DIR__ . '/pages/wypozyczenia.php';
                        break;
                    case 'raporty':
                        include __DIR__ . '/pages/raporty.php';
                        break;
                    case 'news':
                        include __DIR__ . '/pages/news.php';
                        break;
                    case 'banery':
                        include __DIR__ . '/pages/banery.php';
                        break;
                    case 'ksiega':
                        include __DIR__ . '/pages/ksiega_gosci.php';
                        break;
                    case 'ustawienia':
                        include __DIR__ . '/pages/ustawienia.php';
                        break;
                    case 'dashboard':
                    default:
                        include __DIR__ . '/pages/dashboard.php';
                        break;
                }
            ?>
        </div>
    </div>

    <script src="/github/witryny-i-aplikacje-internetowe/klasa_5/biblioteka_szkolna_v2/static/js/main.js"></script>
</body>
</html>
