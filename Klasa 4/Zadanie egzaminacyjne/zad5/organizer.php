<?php
    $conn = mysqli_connect('localhost', 'root', '', 'kalendarz');
    if (!$conn) {
        die('Błąd połączenia: ' . mysqli_connect_error());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wpis'])) {
        $nowy_wpis = mysqli_real_escape_string($conn, $_POST['wpis']);
        $query = "UPDATE zadania SET wpis = '$nowy_wpis' WHERE dataZadania = '2020-08-09'";
        mysqli_query($conn, $query);
    }

    $query = "SELECT dataZadania, wpis FROM zadania WHERE MONTH(dataZadania) = 8";
    $result = mysqli_query($conn, $query);
    $zadania = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $zadania[] = $row;
        }
    }
    mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sierpniowy kalendarz</title>
        <link rel="stylesheet" href="styl5.css">
    </head>
    <body>
        <section style="display: flex;">
            <div style="width: 40%; background-color: #7DA453; color: white; height: 120px;">
                <h1>Organizer: SIERPIEŃ</h1>
            </div>
            <div style="width: 40%; background-color: #7DA453; color: white; height: 120px;">
                <form method="POST">
                    <label for="wpis">Zapisz wydarzenie:</label>
                    <input type="text" name="wpis" id="wpis">
                    <button type="submit">OK</button>
                </form>
            </div>
            <div style="width: 20%; background-color: #7DA453; color: white; height: 120px;">
                <img src="logo2.png" alt="sierpień" width="120" height="120">
            </div>
        </section>
        <main>
            <?php foreach ($zadania as $zadanie): ?>
                <section style="background-color: #AED581; width: 200px; height: 100px; margin: 7px; border: 1px solid #7DA453;">
                    <h5 style="text-align: center;"><?= $zadanie['dataZadania'] ?></h5>
                    <p><?= $zadanie['wpis'] ?></p>
                </section>
            <?php endforeach; ?>
        </main>
        <footer style=a"background-color: #7DA453; color: white; padding: 5px;">
            <p>Stronę wykonał: 11</p>
        </footer>
    </body>
</html>
