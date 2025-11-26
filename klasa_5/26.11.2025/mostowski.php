<?php
    $conn = new mysqli("localhost","root","","galeria");
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Galeria</title>
        <style>
            * {
                font-family: 'Helvetica';
            }

            header,
            footer {
                background-color: Sienna;
                color: white;
                padding: 10px;
                text-align: center;
            }

            #lewy,
            #prawy {
                background-color: NavajoWhite;
                width: 15%;
                height: 700px;
                float: left;
            }

            main {
                width: 70%;
                height: 700px;
                overflow: scroll;
                float: left;
            }

            footer {
                clear: both;
            }

            img {
                width: 100%;
            }

            main > div {
                float: left;
                width: 46%;
                margin: 2%;
                position: relative;
            }

            h3, p, a {
                opacity: 0;
                position: absolute;
            }

            h3 {
                top: 5%;
            }

            p {
                top: 30%;
            }

            a {
                top: 70%;
                left: 70%;
                background-color: Sienna;
                padding: 15px;
            }
            
            main div img {
                transition: opacity 0.5s ease;
                opacity: 1;
            }
            
            main div:hover img {
                opacity: 0.3;
            }
            
            main div:hover h3,
            main div:hover p,
            main div:hover a {
                opacity: 1;
            }
        </style>
    </head>
    <body>
        <header>
            <h1>Zdjęcia</h1>
        </header>

        <div id="lewy">
            <h2>Tematy zdjęć</h2>
            <ol>
                <li>Zwierzęta</li>
                <li>Krajobrazy</li>
                <li>Miasta</li>
                <li>Przyroda</li>
                <li>Samochody</li>
            </ol>
        </div>
        
        <main>
            <?php
                $sql = "SELECT plik, tytul, polubienia, imie, nazwisko FROM zdjecia JOIN autorzy ON autorzy_id = autorzy.id ORDER BY nazwisko;";
                $result = $conn->query(query: $sql);
                while($row = $result -> fetch_array()) {
                    echo "<div>";
                        echo "<img src='$row[0]' alt='zdjęcie'>";
                        echo "<h3>$row[1]</h3>";

                        if($row[2] > 40) {
                            echo "<p>Autor: $row[3] $row[4].<br>Wiele osób polubiło ten obraz</p>";
                        }
                        else {
                            echo "<p>Autor: $row[3] $row[4].</p>";
                        }

                        echo "<a href='$row[0]' download='$row[0]'>Pobierz</a>";

                    echo "</div>";
                }
            ?>
        </main>

        <div id="prawy">
            <h2>Najbardziej lubiane</h2>
            <?php
                $sql = "SELECT tytul, plik FROM zdjecia WHERE polubienia >= 100;";
                $result = $conn->query(query: $sql);
                while($row = $result -> fetch_array()) {
                    echo "<img src='$row[1]' alt='$row[0]'>";
                }
            ?>
            <strong>Zobacz wszystkie nasze zdjęcia</strong>
        </div>

        <footer>
            <h5>Stronę wykonał: Dawid Mostowski</h5>
        </footer>
    </body>
</html>

<?php
    $conn -> close();
?>

<!-- 
Zapytanie 1: SELECT tytul, plik FROM zdjecia WHERE polubienia >= 100;
Zapytanie 2: SELECT plik, tytul, polubienia, imie, nazwisko FROM zdjecia JOIN autorzy ON autorzy_id = autorzy.id ORDER BY nazwisko;
Zapytanie 3: SELECT imie, COUNT(*) FROM zdjecia JOIN autorzy ON autorzy_id = autorzy.id GROUP BY imie;
Zapytanie 4: ALTER TABLE zdjecia ADD COLUMN rozmiarPliku INT;
-->