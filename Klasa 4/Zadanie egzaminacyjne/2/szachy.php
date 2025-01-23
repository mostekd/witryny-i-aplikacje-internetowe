<?php
// PHP Script to connect to the database and perform tasks
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "szachy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function fetchPlayers($conn) {
    $query = "SELECT pseudonim, tytul, ranking, klasa FROM zawodnicy ORDER BY ranking DESC LIMIT 10";
    $result = $conn->query($query);
    $pozycja = 1; // Zmienna dla numeracji wierszy

    if ($result->num_rows > 0) {
        echo "<table border='1'><tr><th>Pozycja</th><th>Pseudonim</th><th>Tyłuł</th><th>Ranking</th><th>Klasa</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $pozycja++ . "</td><td>" . $row["pseudonim"] . "</td><td>" . $row["tytul"] . "</td><td>" . $row["ranking"] . "</td><td>" . $row["klasa"] . "</td></tr>";
        }
        echo "</table>";
    } 
    else {
        echo "Brak danych do wyświetlenia.";
    }
}

function fetchTwoPlayess($conn) {
    $query = "SELECT `pseudonim`, `klasa` FROM `zawodnicy` ORDER BY RAND() LIMIT 2;";
    $result = $conn->query($query);

    if($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<p>".$row["pseudonim"]."</p>";
        }
    } 
    else {
        echo "Brak danych do wyświetlenia.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOŁO SZACHOWE</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <section class="naglowek">
        <h2>Koło szachowe gambit piona</h2>
    </section>
    <section class="lewy">
        <h4>Polecane linki</h4>
            <li><a href="./kwerendy/kw1.png">kwerenda1</a></li>
            <li><a href="./kwerendy/kw2.png"">kwerenda2</a></li>
            <li><a href="./kwerendy/kw3.png"">kwerenda3</a></li>
            <li><a href="./kwerendy/kw4.png"">kwerenda3</a></li>
        <img src="./logo.png" alt="Logo koła">
    </section>
    <section class="prawy">
        <h3>Najlepsi gracze naszego koła</h3>
        <?php
            fetchPlayers(new mysqli($servername, $username, $password, $dbname));
        ?>
        <form action="">
            <input type="submit" name="fetchTwoPlayess" value="Losuj nową parę graczy" onclick="fetchTwoPlayess($conn)" />
        </form>
        <p>Legenda: AM - Absolutny Mistrz, SM - Szkolny Mistrz, PM - Mistrz Poziomu, KM - Mistrz Klasowy</p>
    </section>
    <section class="stopka">
        <p>Stronę wykonał: 11</p>
    </section>
</body>
</html>
