<?php
/**
 * Strona kontaktu - formularz kontaktowy
 */

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $typ = isset($_POST['typ']) ? sanitize($_POST['typ']) : '';
    $imie = isset($_POST['imie']) ? sanitize($_POST['imie']) : '';
    $nazwisko = isset($_POST['nazwisko']) ? sanitize($_POST['nazwisko']) : '';
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $tresc = isset($_POST['tresc']) ? sanitize($_POST['tresc']) : '';

    // Walidacja
    if (empty($typ) || empty($imie) || empty($nazwisko) || empty($email) || empty($tresc)) {
        $message = 'Wszystkie pola są wymagane.';
        $messageType = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Podaj poprawny adres e-mail.';
        $messageType = 'danger';
    } elseif (strlen($tresc) < 10) {
        $message = 'Wiadomość musi mieć co najmniej 10 znaków.';
        $messageType = 'danger';
    } else {
        // Przygotowanie wiadomości e-mail
        $headers = "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";

        $temat_mail = "Nowa wiadomość z formularza kontaktowego - " . htmlspecialchars($typ);
        
        $wiadomosc_mail = "Nowa wiadomość ze strony biblioteki.\n\n";
        $wiadomosc_mail .= "Typ wiadomości: " . htmlspecialchars($typ) . "\n";
        $wiadomosc_mail .= "Imię: " . htmlspecialchars($imie) . "\n";
        $wiadomosc_mail .= "Nazwisko: " . htmlspecialchars($nazwisko) . "\n";
        $wiadomosc_mail .= "E-mail: " . htmlspecialchars($email) . "\n";
        $wiadomosc_mail .= "Data wysłania: " . date('d.m.Y H:i:s') . "\n\n";
        $wiadomosc_mail .= "Treść wiadomości:\n";
        $wiadomosc_mail .= htmlspecialchars($tresc) . "\n\n";
        $wiadomosc_mail .= "---\n";
        $wiadomosc_mail .= "Biblioteka Szkoły - Wesoła Szkoła\n";
        $wiadomosc_mail .= LIBRARY_ADDRESS . "\n";
        $wiadomosc_mail .= "Tel: " . LIBRARY_PHONE . "\n";

        // Wysłanie do biblioteki
        $wyslan_do_biblioteki = mail(LIBRARY_EMAIL, $temat_mail, $wiadomosc_mail, $headers);

        // Wysłanie kopii do użytkownika
        $headers_kopia = "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers_kopia .= "From: " . LIBRARY_EMAIL . "\r\n";

        $temat_kopia = "Potwierdzenie wysłania wiadomości - Biblioteka Szkoły";
        $wiadomosc_kopia = "Szanowny Panie/Pani " . htmlspecialchars($imie) . " " . htmlspecialchars($nazwisko) . ",\n\n";
        $wiadomosc_kopia .= "Potwierdzamy otrzymanie Twojej wiadomości.\n";
        $wiadomosc_kopia .= "Nasz zespół zajmie się Twoją sprawą i skontaktuje się z Tobą tak szybko, jak będzie to możliwe.\n\n";
        $wiadomosc_kopia .= "Typ wiadomości: " . htmlspecialchars($typ) . "\n";
        $wiadomosc_kopia .= "Data wysłania: " . date('d.m.Y H:i:s') . "\n\n";
        $wiadomosc_kopia .= "Treść Twojej wiadomości:\n";
        $wiadomosc_kopia .= htmlspecialchars($tresc) . "\n\n";
        $wiadomosc_kopia .= "---\n";
        $wiadomosc_kopia .= "Biblioteka Szkoły - Wesoła Szkoła\n";
        $wiadomosc_kopia .= LIBRARY_ADDRESS . "\n";
        $wiadomosc_kopia .= "E-mail: " . LIBRARY_EMAIL . "\n";
        $wiadomosc_kopia .= "Tel: " . LIBRARY_PHONE . "\n";

        $wyslan_do_uzytkownika = mail($email, $temat_kopia, $wiadomosc_kopia, $headers_kopia);

        if ($wyslan_do_biblioteki && $wyslan_do_uzytkownika) {
            $message = 'Wiadomość została wysłana pomyślnie. Otrzymałeś również kopię na swoją skrzynkę e-mail.';
            $messageType = 'success';
            $_POST = [];
        } else {
            $message = 'Błąd przy wysyłaniu wiadomości. Spróbuj ponownie.';
            $messageType = 'danger';
        }
    }
}
?>

<h1><i class="fas fa-envelope"></i> Kontakt</h1>
<p>Masz pytania lub uwagi? Chętnie się z Tobą skontaktujem. Wypełnij poniższy formularz, a my odpowiemy najszybciej jak będziemy w stanie.</p>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<!-- INFORMACJE KONTAKTOWE -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); border-left: 4px solid #3498db;">
        <h3 style="color: #3498db; margin-bottom: 1rem;">Adres</h3>
        <p>
            <strong><?php echo LIBRARY_NAME; ?></strong><br>
            <?php echo LIBRARY_ADDRESS; ?>
        </p>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); border-left: 4px solid #27ae60;">
        <h3 style="color: #27ae60; margin-bottom: 1rem;">Telefon</h3>
        <p>
            <strong><?php echo LIBRARY_PHONE; ?></strong><br>
            Pn-Pt: 8:00-16:00<br>
            So: 8:00-12:00
        </p>
    </div>

    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); border-left: 4px solid #e74c3c;">
        <h3 style="color: #e74c3c; margin-bottom: 1rem;">E-mail</h3>
        <p>
            <a href="mailto:<?php echo LIBRARY_EMAIL; ?>" style="color: #e74c3c; text-decoration: none; font-weight: bold;">
                <?php echo LIBRARY_EMAIL; ?>
            </a>
        </p>
    </div>
</div>

<hr style="margin: 2rem 0;">

<!-- FORMULARZ KONTAKTOWY -->
<div class="form-section">
    <h3>Formularz kontaktowy</h3>
    <form method="POST" class="form">
        <div class="form-group">
            <label for="typ">Temat wiadomości *</label>
            <select id="typ" name="typ" required>
                <option value="">-- Wybierz temat --</option>
                <option value="Zapytanie o dostępność książki" <?php echo (isset($_POST['typ']) && $_POST['typ'] === 'Zapytanie o dostępność książki') ? 'selected' : ''; ?>>Zapytanie o dostępność książki</option>
                <option value="Prośba o rezerwację" <?php echo (isset($_POST['typ']) && $_POST['typ'] === 'Prośba o rezerwację') ? 'selected' : ''; ?>>Prośba o rezerwację</option>
                <option value="Inna sprawa" <?php echo (isset($_POST['typ']) && $_POST['typ'] === 'Inna sprawa') ? 'selected' : ''; ?>>Inna sprawa</option>
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="imie">Imię *</label>
                <input type="text" id="imie" name="imie" 
                       placeholder="Wpisz swoje imię"
                       value="<?php echo htmlspecialchars($_POST['imie'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="nazwisko">Nazwisko *</label>
                <input type="text" id="nazwisko" name="nazwisko" 
                       placeholder="Wpisz swoje nazwisko"
                       value="<?php echo htmlspecialchars($_POST['nazwisko'] ?? ''); ?>"
                       required>
            </div>
        </div>

        <div class="form-group">
            <label for="email">E-mail *</label>
            <input type="email" id="email" name="email" 
                   placeholder="Wpisz swój adres e-mail"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required>
        </div>

        <div class="form-group">
            <label for="tresc">Treść wiadomości *</label>
            <textarea id="tresc" name="tresc" 
                      placeholder="Wpisz treść swojej wiadomości"
                      minlength="10"
                      required><?php echo htmlspecialchars($_POST['tresc'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Wyślij wiadomość
            </button>
            <button type="reset" class="btn btn-secondary" style="margin-left: 1rem;">
                <i class="fas fa-redo"></i> Wyczyść formularz
            </button>
        </div>
    </form>
</div>
