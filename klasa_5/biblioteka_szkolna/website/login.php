<?php
require_once '../database/models/student.php';
session_start();

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentModel = new Student();
    $login = $_POST['login'];
    $password = $_POST['password'];
    $student = $studentModel->login($login, $password);

    if ($student) {
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['first_name'];
        header("Location: profile.php");
        exit;
    } else {
        $msg = "❌ Nieprawidłowy login lub hasło.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Logowanie | Biblioteka Szkolna</title>
<link rel="stylesheet" href="../static/css/user.css">
</head>
<body>
<?php include 'includes/menu.php'; ?>
<main>
  <h2>🔐 Logowanie ucznia</h2>
  <?php if ($msg): ?><p style="color:red;"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
  <form method="post">
    <label>Login:</label>
    <input type="text" name="login" required>

    <label>Hasło:</label>
    <input type="password" name="password" required>

    <input type="submit" value="Zaloguj się">
  </form>
</main>
</body>
</html>
