<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Ustawianie ciasteczka</title>
    </head>
    <body>
        <h2>Ciasteczko w PHP</h2>
        <form method="post">
            <label>Podaj czas (w sekundach): </label>
            <input type="number" name="czas" min="1">
            <input type="submit" name="ustaw" value="Ustaw ciasteczko">
            <input type="submit" name="sprawdz" value="Sprawdź ciasteczko">
        </form>
        <hr>
        <?php
        if (isset($_POST['ustaw'])) {
            $czas = intval($_POST['czas']);
            setcookie("moje_ciasteczko", "Technikum TEB Edukacja", time() + $czas);
            echo "<p>Ustawiono ciasteczko na $czas sekund.</p>";
        }

        if (isset($_COOKIE['moje_ciasteczko'])) {
            echo "<p style='color:green; font-weight:bold;'>Ciasteczko jest ustawione</p>";
        } else {
            echo "<p style='color:red; font-weight:bold;'>Brak ciasteczka</p>";
        }
        ?>
    </body>
</html>
