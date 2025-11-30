<?php
/**
 * Panel logowania administratora
 */

session_start();

require_once __DIR__ . '/../database/config.php';

// Jeśli już zalogowany, przejdź do panelu
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = isset($_POST['login']) ? sanitize($_POST['login']) : '';
    $haslo = isset($_POST['haslo']) ? $_POST['haslo'] : '';

    if (empty($login) || empty($haslo)) {
        $error = 'Login i hasło są wymagane.';
    } else {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT id, login, haslo, email, imie, nazwisko FROM admini WHERE login = ? AND aktywny = TRUE");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Weryfikacja hasła przy użyciu password_verify
            if (password_verify($haslo, $admin['haslo'])) {
                // Zalogowanie sukces
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_login'] = $admin['login'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_imie'] = $admin['imie'];
                $_SESSION['admin_nazwisko'] = $admin['nazwisko'];
                
                // Aktualizacja czasu ostatniego logowania
                $updateStmt = $db->prepare("UPDATE admini SET ostatnia_logowanie = NOW() WHERE id = ?");
                $updateStmt->bind_param("i", $admin['id']);
                $updateStmt->execute();
                $updateStmt->close();
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Błędne hasło.';
            }
        } else {
            $error = 'Administrator nie znaleziony.';
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie do panelu administracyjnego - Biblioteka Szkolna</title>
    <link rel="stylesheet" href="/github/witryny-i-aplikacje-internetowe/klasa_5/biblioteka_szkolna_v2/static/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2><i class="fas fa-lock"></i> Panel administracyjny</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form">
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" id="login" name="login" 
                           placeholder="Wpisz login"
                           value="<?php echo ($_POST['login'] ?? ''); ?>"
                           required autofocus>
                </div>

                <div class="form-group">
                    <label for="haslo">Hasło</label>
                    <input type="password" id="haslo" name="haslo" 
                           placeholder="Wpisz hasło"
                           required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Zaloguj się
                    </button>
                </div>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; color: #666; font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> Panel dostępny wyłącznie dla administratorów
            </p>
        </div>
    </div>

    <script src="/github/witryny-i-aplikacje-internetowe/klasa_5/biblioteka_szkolna_v2/static/js/main.js"></script>
</body>
</html>
