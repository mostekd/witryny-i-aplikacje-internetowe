<?php
    $conn = new mysqli("localhost","root","","egzamin5");

    if(isset($_POST["wpis"])) {
        $wpis = $_POST["wpis"];
        $sql = "UPDATE zadania SET wpis = '$wpis' WHERE dataZadania = '2020-07-13';";
        $result = $conn->query($sql);
    }
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mój kalendarz</title>
        <link rel="stylesheet" href="styl5.css">
    </head>
    <body>
        <div id="baner-lewy">
            <img src="logo1.png" alt="Mój kalendarz">
        </div>

        <div id="baner-prawy">
            <h1>KALENDARZ</h1>
            <?php
                $sql = "SELECT miesiac, rok FROM zadania WHERE dataZadania = '2020-07-01';";
                $result = $conn->query($sql);

                while($row = $result -> fetch_array()) {
                    echo "<h3>miesiąc: ".$row["miesiac"].", rok: ".$row["rok"]."</h3>";
                }
            ?>
        </div>

        <main>
            <?php
                $sql = "SELECT dataZadania, wpis FROM zadania WHERE miesiac = 'lipiec';";
                $result = $conn->query($sql);

                while($row = $result -> fetch_array()) {
                    echo "<div class='dzien'>";
                        echo "<h5>".$row["dataZadania"]."</h5>";
                        echo "<p>".$row["wpis"]."</p>";
                    echo "</div>";
                }
            ?>
        </main>

        <footer>
            <form action="kalendarz.php" method="post">
                <label for="wpis">dodaj wpis:</label> <input type="text" name="wpis" id="wpis"> <button type="submit">DODAJ</button>
                <p>Stronę wykonał: Dawid Mostowski</p>
            </form>
        </footer>
    </body>
</html>

<?php
    $conn -> close();
?>