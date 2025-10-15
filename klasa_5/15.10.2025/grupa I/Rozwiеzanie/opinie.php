<?php
    $conn = new mysqli("localhost","root","","hurtownia");
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Opinie klientów</title>
        <link rel="stylesheet" href="styl3.css">
    </head>
    <body>
        <header>
            <h1>Hurtownia spożywcza</h1>
        </header>

        <main>
            <h2>Opinie naszych klientów</h2>
            <?php
                // Skrypt #1
                $sql = "SELECT klienci.zdjecie, klienci.imie, opinie.opinia FROM klienci, opinie, typy WHERE klienci.id = opinie.klienci_id AND typy.id = klienci.typy_id AND typy.id IN (2,3);";
                $result = $conn->query($sql);
                
                while($row = $result -> fetch_array()) {
                    echo "<div class='opinia'>";
                        echo "<img src='$row[0]' alt='klient'>";
                        echo "<div class='cytat'>$row[2]</div>";
                        echo "<h4>$row[1]</h4>";
                    echo "</div>";
                }
            ?>
        </main>

        <div id="stopka1">
            <h3>Współpracują z nami</h3>
            <a href="http://skle.pl/">Sklep 1</a>
        </div>

        <div id="stopka2">
            <h3>Nasi top klienci</h3>
            <ol>
                <?php
                    // Skrypt #2
                    $sql = "SELECT imie, nazwisko, punkty FROM klienci ORDER BY punkty DESC LIMIT 3;";
                    $result = $conn->query($sql);

                    while($row = $result -> fetch_array()) {
                        echo "<li>$row[0] $row[1], $row[2]</li>";
                    }
                ?>
            </ol>
        </div>

        <div id="stopka3">
            <h3>Skontaktuj się</h3>
            <p>telefon: 111222333</p>
        </div>

        <div id="stopka4">
            <h3>Autor: <a href="https://ee-informatyk.pl/" target="_blank" style="color: unset;text-decoration: none;">EE-Informatyk.pl</a></h3>
        </div>
    </body>
</html>

<?php
    $conn -> close();
?>