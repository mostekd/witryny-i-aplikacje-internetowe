<?php
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    function fetchPlayers($conn) {
        $conn = new mysqli("localhost", "root", "", "szachy");
        $query = "SELECT pseudonim, tytul, ranking, klasa FROM zawodnicy ORDER BY ranking DESC LIMIT 10";
        $result = $conn->query($query);
        $position = 1;

        if ($result->num_rows > 0) {
            echo "<table><tr><th>Pozycja</th><th>Pseudonim</th><th>Tyłuł</th><th>Ranking</th><th>Klasa</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $position++ . "</td><td>" . $row["pseudonim"] . "</td><td>" . $row["tytul"] . "</td><td>" . $row["ranking"] . "</td><td>" . $row["klasa"] . "</td></tr>";
            }
            echo "</table>";
        } 
        else {
            echo "Brak danych do wyświetlenia.";
        }
        $conn->close();
    }

    
    
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
    <header>
        <h2><b>Koło szachowe gambit piona</b></h2>
    </header>
    <nav>
        <h4>Polecane linki</h4>
        <ol>
            <li><a href="./kwerendy/kw1.png">kwerenda1</a></li>
            <li><a href="./kwerendy/kw2.png"">kwerenda2</a></li>
            <li><a href="./kwerendy/kw3.png"">kwerenda3</a></li>
            <li><a href="./kwerendy/kw4.png"">kwerenda4</a></li>
        </ol>
        <img src="./logo.png" alt="Logo koła">
    </nav>
    <main>
        <h3>Najlepsi gracze naszego koła</h3>
        <?php
            fetchPlayers($conn);
        ?>
        <form action="szachy.php" method="POST">
            <input type="submit" name="fetchTwoPlayess" value="Losuj nową parę graczy" />
        </form>
        <?php
        if(isset($_POST))
        {
            $conn = new mysqli("localhost", "root", "", "szachy");
            $query = "SELECT `pseudonim`, `klasa` FROM `zawodnicy` ORDER BY RAND() LIMIT 2;";
            $result = $conn->query($query);

            if($result->num_rows > 0) {
                echo "<h4>";
                while ($row = $result->fetch_assoc()) {
                    echo $row["pseudonim"] . " " . $row["klasa"] . " ";
                }
                echo "</h4>";
            } 
            else {
                echo "Brak danych do wyświetlenia.";
            }
            $conn->close();
        }
        ?>
        <p>Legenda: AM - Absolutny Mistrz, SM - Szkolny Mistrz, PM - Mistrz Poziomu, KM - Mistrz Klasowy</p>
    </main>
    <footer>
        <p>Stronę wykonał: 11</p>
    </footer>
</body>
</html>
