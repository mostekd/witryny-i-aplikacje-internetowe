<?php
    $conn = mysqli_connect("localhost","root","","egzamin5");

    if(isset($_POST["wpis"])) {
        $wpis = $_POST["wpis"];
        $query = "UPDATE zadania SET wpis = '$wpis' WHERE dataZadania = '2020-07-13';";
        $result = $conn->query($query);
    }
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mój kalendarz</title>
        <style>
            body {
                font-family: 'Arial';
            }

            #baner-lewy {
                background: #483D8B;
                height: 150px;
                width: 30%;
                float: left;
            }

            #baner-prawy {
                background: #483D8B;
                color: white;
                height: 150px;
                width: 70%;
                float: left;
            }

            main {
                clear: both;
            }

            .dzien {
                background: #AFEEEE;
                width: 150px;
                height: 100px;
                margin: 3px;
                border: 1px solid;
                float: left;
            }

            footer {
                clear: both;
                background: #483D8B;
                color: white;
                padding: 10px;
            }

            h5 {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <section id="baner-lewy">
            <img src="logo1.png" alt="Mój kalendarz">
        </section>
        <header id="baner-prawy">
            <h1>KALENDARZ</h1>
            <?php
                $query = "SELECT miesiac, rok FROM zadania WHERE dataZadania = '2020-07-01';";
                $result = $conn->query($query);

                while($row = $result -> fetch_array()) {
                    echo "<h3>miesiąc: ".$row["miesiac"].", rok: ".$row["rok"]."</h3>";
                }
            ?>
        </header>
        <main>
            <?php
                $query = "SELECT dataZadania, wpis FROM zadania WHERE miesiac = 'lipiec';";
                $result = $conn->query($query);

                while($row = $result -> fetch_array()) {
                    echo "<section class='dzien'>";
                        echo "<h5>".$row["dataZadania"]."</h5>";
                        echo "<p>".$row["wpis"]."</p>";
                    echo "</section>";
                }
            ?>
        </main>
        <footer>
            <form action="mostowski.php" method="post">
                <label for="wpis">dodaj wpis:</label> 
                <input type="text" name="wpis" id="wpis"> 
                <button type="submit">DODAJ</button>
                <p>Stronę wykonał: Dawid Mostowski 5A</p>
            </form>
        </footer>
    </body>
</html>

<?php
    $conn -> close();
?>