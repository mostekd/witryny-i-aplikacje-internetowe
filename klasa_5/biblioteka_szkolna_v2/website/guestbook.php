<?php
/**
 * Księga gości
 */

require_once __DIR__ . '/../database/KsiegaGosci.php';

$ksiegaObj = new KsiegaGosci();
$message = '';
$messageType = '';

// Obsługa dodawania wpisu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick = isset($_POST['nick']) ? sanitize($_POST['nick']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $tresc = isset($_POST['tresc']) ? sanitize($_POST['tresc']) : '';

    // Walidacja
    if (empty($nick) || empty($email) || empty($tresc)) {
        $message = 'Wszystkie pola są wymagane.';
        $messageType = 'danger';
    } elseif (strlen($nick) < 2 || strlen($nick) > 100) {
        $message = 'Nick musi mieć od 2 do 100 znaków.';
        $messageType = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Podaj poprawny adres e-mail.';
        $messageType = 'danger';
    } elseif (strlen($tresc) < 10 || strlen($tresc) > 5000) {
        $message = 'Treść wpisu musi mieć od 10 do 5000 znaków.';
        $messageType = 'danger';
    } else {
        // Dodanie do oczekujących
        if ($ksiegaObj->addPending($nick, $email, $tresc)) {
            $message = 'Twój wpis został dodany i oczekuje na zatwierdzenie. Powinien pojawić się na stronie wkrótce.';
            $messageType = 'success';
            // Czyszczenie formularza
            $_POST = [];
        } else {
            $message = 'Błąd przy dodawaniu wpisu. Spróbuj ponownie.';
            $messageType = 'danger';
        }
    }
}

// Pobieranie zatwierdzone wpisów
$postsResult = $ksiegaObj->getAll();
?>

<h1><i class="fas fa-comments"></i> Księga gości</h1>
<p>Zostaw swoją opinię lub pozdrowienie w naszej księdze gości. Wszystkie wpisy są moderowane przed publikacją.</p>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- FORMULARZ DODAWANIA WPISU -->
<div class="form-section">
    <h3>Dodaj wpis do księgi gości</h3>
    <form method="POST" class="form">
        <div class="form-group">
            <label for="nick">Twój nick *</label>
            <input type="text" id="nick" name="nick" 
                   placeholder="Wpisz swój nick (2-100 znaków)"
                   value="<?php echo htmlspecialchars($_POST['nick'] ?? ''); ?>"
                   minlength="2" maxlength="100" required>
        </div>

        <div class="form-group">
            <label for="email">Twój e-mail *</label>
            <input type="email" id="email" name="email" 
                   placeholder="Wpisz swój adres e-mail"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-group">
            <label for="tresc">Treść wpisu *</label>
            <textarea id="tresc" name="tresc" 
                      placeholder="Wpisz treść wpisu (10-5000 znaków)"
                      minlength="10" maxlength="5000" required><?php echo htmlspecialchars($_POST['tresc'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Wyślij wpis
            </button>
            <button type="reset" class="btn btn-secondary" style="margin-left: 1rem;">
                <i class="fas fa-redo"></i> Wyczyść formularz
            </button>
        </div>
    </form>
</div>

<hr style="margin: 2rem 0;">

<!-- WYŚWIETLANIE WPISÓW -->
<h2>Wpisy w księdze gości</h2>

<?php if ($postsResult && $postsResult->num_rows > 0): ?>
    <div class="articles-container">
        <?php while ($post = $postsResult->fetch_assoc()): ?>
            <div class="article" style="border-left-color: #3498db;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; color: var(--primary-color); font-size: 1.2rem;">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['nick']); ?>
                    </h3>
                    <span class="article-date" style="margin: 0;">
                        <i class="fas fa-calendar"></i>
                        <?php 
                            $date = new DateTime($post['data_dodania']);
                            echo $date->format('d.m.Y H:i');
                        ?>
                    </span>
                </div>

                <p style="margin: 1rem 0; color: #666; font-size: 0.9rem;">
                    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($post['email']); ?>
                </p>

                <div style="margin: 1rem 0; padding: 1rem; background-color: #f9f9f9; border-radius: 4px;">
                    <?php echo htmlspecialchars($post['tresc']); ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Księga gości jest pusta. Bądź pierwszy i zostaw nam pozdrowienie!
    </div>
<?php endif; ?>
