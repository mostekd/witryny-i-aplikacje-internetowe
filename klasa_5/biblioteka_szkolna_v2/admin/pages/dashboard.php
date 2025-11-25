<?php
/**
 * Dashboard - pulpit administratora
 */

// Pobieranie statystyk
$ksiazek = $ksiazkaObj->getCount(false);
$uczniow = $uczenObj->getCount(false);
$wypozyczen = $wypozyczeniaObj->getActiveCount();
$newsow = $newsObj->getCount(true);
$pendingWpisow = $ksiegaObj->getPendingCount();
?>

<h2><i class="fas fa-home"></i> Pulpit</h2>
<p>Witaj w panelu administracyjnym Biblioteki Szkolnej.</p>

<div class="dashboard-grid">
    <div class="dashboard-card">
        <h3>Książki</h3>
        <div class="number"><?php echo $ksiazek; ?></div>
        <p>Łącznie: <?php echo $ksiazek; ?> pozycji</p>
        <a href="?action=ksiazki" class="btn btn-primary" style="width: 100%; text-align: center;">
            <i class="fas fa-arrow-right"></i> Zarządzaj
        </a>
    </div>

    <div class="dashboard-card">
        <h3>Uczniowie</h3>
        <div class="number"><?php echo $uczniow; ?></div>
        <p>Zarejestrowanych</p>
        <a href="?action=uczniowie" class="btn btn-primary" style="width: 100%; text-align: center;">
            <i class="fas fa-arrow-right"></i> Zarządzaj
        </a>
    </div>

    <div class="dashboard-card">
        <h3>Aktywne wypożyczenia</h3>
        <div class="number" style="color: #e74c3c;"><?php echo $wypozyczen; ?></div>
        <p>Nie zwróconych</p>
        <a href="?action=wypozyczenia" class="btn btn-primary" style="width: 100%; text-align: center;">
            <i class="fas fa-arrow-right"></i> Zarządzaj
        </a>
    </div>

    <div class="dashboard-card">
        <h3>Artykuły</h3>
        <div class="number"><?php echo $newsow; ?></div>
        <p>Opublikowanych</p>
        <a href="?action=news" class="btn btn-primary" style="width: 100%; text-align: center;">
            <i class="fas fa-arrow-right"></i> Zarządzaj
        </a>
    </div>

    <div class="dashboard-card">
        <h3>Księga gości</h3>
        <div class="number" style="color: #f39c12;"><?php echo $pendingWpisow; ?></div>
        <p>Oczekujących wpisów</p>
        <a href="?action=ksiega" class="btn btn-primary" style="width: 100%; text-align: center;">
            <i class="fas fa-arrow-right"></i> Zatwierdzaj
        </a>
    </div>
</div>

<hr style="margin: 2rem 0;">

<h3>Ostatnia aktywność</h3>
<p>Zalogowano: <?php echo isset($_SESSION['last_login']) ? $_SESSION['last_login'] : 'Bieżąca sesja'; ?></p>
