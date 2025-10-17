<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Obsługa formularza ustawień
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['settings'] as $klucz => $wartosc) {
        $klucz = $conn->real_escape_string($klucz);
        $wartosc = $conn->real_escape_string($wartosc);
        
        $sql = "insert into ustawienia (klucz, wartosc) 
                values ('$klucz', '$wartosc')
                on duplicate key update wartosc = '$wartosc'";
                
        if ($conn->query($sql)) {
            $conn->query("insert into logi_admin (admin_id, akcja) 
                         values ($admin_id, 'Zaktualizowano ustawienie: $klucz = $wartosc')");
        }
    }
    header('Location: settings.php?success=1');
    exit;
}

include 'layout/header.php';
?>

<div class="admin-container">
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Ustawienia systemu</h1>
        </div>

        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Ustawienia zostały zaktualizowane pomyślnie.
        </div>
        <?php endif; ?>

        <div class="settings-container">
            <form method="post" class="settings-form">
                <div class="settings-section">
                    <h2>Ustawienia wypożyczeń</h2>
                    
                    <?php
                    $result = $conn->query("select * from ustawienia where klucz = 'okres_wypozyczenia'");
                    $okres_wypozyczenia = $result->fetch_assoc()['wartosc'];
                    ?>
                    
                    <div class="form-group">
                        <label>Domyślny okres wypożyczenia (dni)</label>
                        <input type="number" name="settings[okres_wypozyczenia]" 
                               value="<?= $okres_wypozyczenia ?>" min="1" max="90" required>
                        <small>Liczba dni, na jaką książka jest standardowo wypożyczana</small>
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Ustawienia powiadomień</h2>
                    
                    <?php
                    $result = $conn->query("select * from ustawienia where klucz in ('email_notifications', 'notification_days_before')");
                    $settings = [];
                    while ($row = $result->fetch_assoc()) {
                        $settings[$row['klucz']] = $row['wartosc'];
                    }
                    ?>
                    
                    <div class="form-group">
                        <label>Włącz powiadomienia email</label>
                        <select name="settings[email_notifications]">
                            <option value="1" <?= ($settings['email_notifications'] ?? '') == '1' ? 'selected' : '' ?>>Tak</option>
                            <option value="0" <?= ($settings['email_notifications'] ?? '') == '0' ? 'selected' : '' ?>>Nie</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Wyślij powiadomienie na (dni przed terminem)</label>
                        <input type="number" name="settings[notification_days_before]" 
                               value="<?= $settings['notification_days_before'] ?? '3' ?>" min="1" max="14">
                    </div>
                </div>

                <div class="settings-section">
                    <h2>Ustawienia strony głównej</h2>
                    
                    <?php
                    $result = $conn->query("select * from ustawienia where klucz in ('site_name', 'posts_per_page', 'show_weather_widget')");
                    while ($row = $result->fetch_assoc()) {
                        $settings[$row['klucz']] = $row['wartosc'];
                    }
                    ?>
                    
                    <div class="form-group">
                        <label>Nazwa biblioteki</label>
                        <input type="text" name="settings[site_name]" 
                               value="<?= htmlspecialchars($settings['site_name'] ?? 'Biblioteka Szkolna') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Liczba newsów na stronie</label>
                        <input type="number" name="settings[posts_per_page]" 
                               value="<?= $settings['posts_per_page'] ?? '5' ?>" min="1" max="20">
                    </div>
                    
                    <div class="form-group">
                        <label>Pokaż widget pogody</label>
                        <select name="settings[show_weather_widget]">
                            <option value="1" <?= ($settings['show_weather_widget'] ?? '') == '1' ? 'selected' : '' ?>>Tak</option>
                            <option value="0" <?= ($settings['show_weather_widget'] ?? '') == '0' ? 'selected' : '' ?>>Nie</option>
                        </select>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Zapisz ustawienia</button>
                    <button type="reset" class="btn-reset">Resetuj zmiany</button>
                </div>
            </form>
        </div>
    </main>
</div>

<style>
.settings-container {
    padding: 1rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.settings-section {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.settings-section h2 {
    color: #333;
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #666;
    font-weight: bold;
}

.form-group small {
    display: block;
    color: #666;
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-buttons {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
}

.btn-submit,
.btn-reset {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

.btn-submit {
    background: #2196F3;
    color: white;
}

.btn-reset {
    background: #f5f5f5;
    color: #333;
}

.btn-submit:hover {
    background: #1976D2;
}

.btn-reset:hover {
    background: #e0e0e0;
}

.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
}

.alert-success {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}
</style>

<?php include 'layout/footer.php'; ?>