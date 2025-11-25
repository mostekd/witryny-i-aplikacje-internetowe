<?php
/**
 * Ustawienia systemu
 */

$message = '';
$messageType = '';

// Obsługa zmian ustawień
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = sanitize($_POST['action']);
    
    if ($post_action === 'update_settings') {
        // Tutaj można dodać logikę aktualizacji ustawień w bazie danych
        // Na razie pokazujemy informację
        $message = 'Ustawienia zostały zapisane (demo).';
        $messageType = 'success';
    }
}
?>

<h2><i class="fas fa-cog"></i> Ustawienia</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="form-section">
    <h3>Podstawowe ustawienia biblioteki</h3>
    <form method="POST" class="form">
        <input type="hidden" name="action" value="update_settings">

        <div class="form-group">
            <label for="nazwa">Nazwa biblioteki</label>
            <input type="text" id="nazwa" name="nazwa" 
                   value="<?php echo LIBRARY_NAME; ?>" disabled>
        </div>

        <div class="form-group">
            <label for="adres">Adres</label>
            <input type="text" id="adres" name="adres" 
                   value="<?php echo LIBRARY_ADDRESS; ?>" disabled>
        </div>

        <div class="form-group">
            <label for="email">E-mail kontaktowy</label>
            <input type="email" id="email" name="email" 
                   value="<?php echo LIBRARY_EMAIL; ?>" disabled>
        </div>

        <div class="form-group">
            <label for="telefon">Telefon</label>
            <input type="text" id="telefon" name="telefon" 
                   value="<?php echo LIBRARY_PHONE; ?>" disabled>
        </div>

        <div class="form-group">
            <label for="okres_wypozyczenia">Domyślny okres wypożyczenia (dni)</label>
            <input type="number" id="okres_wypozyczenia" name="okres_wypozyczenia" 
                   min="1" value="<?php echo DEFAULT_LOAN_PERIOD; ?>" disabled>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Podstawowe ustawienia są zmieniane w pliku konfiguracyjnym database/config.php
        </div>
    </form>
</div>

<hr style="margin: 2rem 0;">

<div class="form-section">
    <h3>Statystyka systemu</h3>
    
    <p><strong>Zalogowany administrator:</strong> <?php echo htmlspecialchars($_SESSION['admin_imie'] . ' ' . $_SESSION['admin_nazwisko']); ?></p>
    <p><strong>E-mail administratora:</strong> <?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
    <p><strong>Bieżący czas serwera:</strong> <?php echo date('d.m.Y H:i:s'); ?></p>
    
    <hr>
    
    <h4>Dane w bazie</h4>
    <ul>
        <li>Książek: <?php echo $ksiazkaObj->getCount(false); ?></li>
        <li>Uczniów: <?php echo $uczenObj->getCount(false); ?></li>
        <li>Aktywnych wypożyczeń: <?php echo $wypozyczeniaObj->getActiveCount(); ?></li>
        <li>Artykułów: <?php echo $newsObj->getCount(false); ?></li>
        <li>Banerów: <?php echo $banerObj->getCount(false); ?></li>
        <li>Wpisów w księdze gości: <?php echo $ksiegaObj->getCount(); ?></li>
        <li>Oczekujących wpisów do księgi gości: <?php echo $ksiegaObj->getPendingCount(); ?></li>
    </ul>
</div>

<hr style="margin: 2rem 0;">

<div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); border-left: 4px solid #3498db;">
    <h4><i class="fas fa-question-circle"></i> Pomoc</h4>
    <p>Ta strona zawiera ustawienia systemu biblioteki. Dla zmian podstawowych danych, skontaktuj się z administratorem systemu.</p>
    
    <h5>Szybkie linki:</h5>
    <ul>
        <li><a href="?action=dashboard" style="color: #3498db;">Wróć do pulpitu</a></li>
        <li><a href="logout.php" style="color: #e74c3c;">Wyloguj się</a></li>
    </ul>
</div>
