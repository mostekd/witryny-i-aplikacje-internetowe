<?php
    $conn = mysqli_connect("localhost","root","","hurtownia");
?>

<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Opinie klientów</title>
        <style>
            * {
                font-family: 'Tahoma','sans-serif';
            }

            header {
                background: tomato;
                color: white;
                padding: 10px;
            }

            main {
                background: seashell;
                height: 500px;
                overflow: auto;
            }

            #stopka1,#stopka2,#stopka3,#stopka4 {
                background: tomato;
                color: white;
                width: 25%;
                height: 160px;
                float: left;
            }

            .opinia {
                background: white;
                color: dimgray;
                width: 60%;
                height: 220px;
                margin: 40px auto;
                box-shadow: 0px 0px 10px 5px dimgrey;
            }

            img {
                margin: 10px;
                border-radius: 10px;
                float: left;
            }

            .cytat {
                padding: 10px;
                font-size: 150%;
                font-style: italic;
            }

            h1,h2 {
                text-align: center;
            }

            h4 {
                text-align: right;
                margin-right: 40px;
                font-size: 150%;
                font-style: italic;
                float: right;
            }

            a {
                color: white;
            }
        </style>
    </head>
    <body>
        <header>
            <h1>Hurtownia spożywcza</h1>
        </header>
        <main>
            <h2>Opinie naszych klientów</h2>
            <?php
                $query = "SELECT k.zdjecie, k.imie, o.opinia FROM klienci AS k, opinie AS o, typy AS t WHERE k.id = o.klienci_id AND t.id = k.typy_id AND t.id IN (2,3);";
                $result = $conn->query($query);
                
                while($row = $result -> fetch_array()) {
                    echo "<div class='opinia'>";
                        echo "<img src='$row[0]' alt='klient'>";
                        echo "<div class='cytat'>$row[2]</div>";
                        echo "<h4>$row[1]</h4>";
                    echo "</div>";
                }
            ?>
        </main>
        <footer>
            <section id="stopka1">
                <h3>Współpracują z nami</h3>
                <a href="http://sklep.pl/">Sklep 1</a>
            </section>
            <section id="stopka2">
                <h3>Nasi top klienci</h3>
                <ol>
                    <?php
                        $query = "SELECT imie, nazwisko, punkty FROM klienci ORDER BY punkty DESC LIMIT 3;";
                        $result = $conn->query($query);

                        while($row = $result -> fetch_array()) {
                            echo "<li>$row[0] $row[1], $row[2]</li>";
                        }
                    ?>
                </ol>
            </section>
            <section id="stopka3">
                <h3>Skontaktuj się</h3>
                <p>telefon: 111222333</p>
            </section>
            <section id="stopka4">
                <h3>Autor: Dawid Mostowski</h3>
            </section>
        </footer>
    </body>
</html>

<?php
    $conn -> close();
?>