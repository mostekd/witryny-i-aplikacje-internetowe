<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Weterynarz</title>
        <link rel="stylesheet" href="weterynarz.css">
    </head>
    <body>
        <header>
            <h1>GABINET WETERYNARYJNY</h1>
        </header>

        <div id="lewy">
            <h2>PSY</h2>

            <?php
                // Skrypt #1
                $conn = new mysqli("localhost","root","","weterynarz");

                $sql = "SELECT id,imie,wlasciciel FROM Zwierzeta WHERE rodzaj=1;";
                $result = $conn->query($sql);

                while($row = $result -> fetch_array()) {
                    echo $row["id"]." ".$row["imie"]." ".$row["wlasciciel"]."<br>";
                }

                $conn -> close();
            ?>

            <h2>KOTY</h2>

            <?php
                // Skrypt #2
                $conn = new mysqli("localhost","root","","weterynarz");

                $sql = "SELECT id,imie,wlasciciel FROM Zwierzeta WHERE rodzaj=2;";
                $result = $conn->query($sql);

                while($row = $result -> fetch_array()) {
                    echo $row["id"]." ".$row["imie"]." ".$row["wlasciciel"]."<br>";
                }

                $conn -> close();
            ?>
        </div>

        <div id="srodkowy">
            <h2>SZCZEGÓŁOWA INFORMACJA O ZWIERZĘTACH</h2>
            <?php
                // Skrypt #3
                $conn = new mysqli("localhost","root","","weterynarz");

                $sql = "SELECT imie, telefon, szczepienie, opis FROM Zwierzeta;";
                $result = $conn->query($sql);

                while($row = $result -> fetch_array()) {
                    echo "Pacjent: ".$row["imie"]."<br>";
                    echo "Telefon właściciela: ".$row["telefon"].", ostatnie szczepienie".$row["szczepienie"].", informacje: ".$row["opis"];
                    echo "<hr>";
                }

                $conn -> close();
            ?>
        </div>

        <div id="prawy">
            <h2>WETERYNARZ</h2>
            <a href="logo.jpg"><img src="logo-mini.jpg" alt="logo.jpg"></a>
            <p>Krzysztof Nowakowski, lekarz weterynarii</p>
            <h2>GODZINY PRZYJĘĆ</h2>
            <table>
                <tr>
                    <td>Poniedziałek</td>
                    <td>15:00 - 19:00</td>
                </tr>
                <tr>
                    <td>Wtorek</td>
                    <td>15:00 - 19:00</td>
                </tr>
            </table>
        </div>
    </body>
</html>