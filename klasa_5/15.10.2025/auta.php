<?php
    if (isset($_POST['zapisz'])) {
        $klasa_id = $_POST['klasa_id'];
        $marka = $_POST['marka'];
        $model = $_POST['model'];
        $rocznik = $_POST['rocznik'];

        $polaczenie = mysqli_connect('localhost', 'root', '', 'auta');

        $zapytanie = "INSERT INTO samochody (klasa_id, marka, model, rocznik)
        VALUES ('$klasa_id', '$marka', '$model', '$rocznik')";

        $wynik = mysqli_query($polaczenie, $zapytanie);

        mysqli_close($polaczenie);
    }
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <title>Dodaj samochód</title>
    </head>
    <body>
        <h2>Formularz dodawania samochodu</h2>

        <form method="post">
            <label>klasa_id:
                <input type="number" name="klasa_id" required>
            </label><br><br>

            <label>marka:
                <input type="text" name="marka" required>
            </label><br><br>

            <label>model:
                <input type="text" name="model" required>
            </label><br><br>

            <label>rocznik:
                <input type="number" name="rocznik" required>
            </label><br><br>

            <button name="zapisz" type="submit">Zapisz do bazy</button>
        </form>
    </body>
</html>
