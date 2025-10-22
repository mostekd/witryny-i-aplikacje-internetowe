<?php
    $conn = new mysqli ("localhost", "root", "", "egzamin");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <style>
            body {
                font-family: 'Georgia';
                background: #FFFAFA;
            }

            #logo {
                background: #FF7F50;
                width: 25%;
                height: 100px;
                float: left;
            }

            #baner {
                background: #FF7F50;
                text-align: center;
                width: 75%;
                height: 100px;
                font-size: 130%;
                float: left;
            }

            main {
                clear: both;
                background: #FF7F50;
                padding: 70px;
            }

            #lewy {
                width: 40%;
                height: 400px;
                float: left;
            }

            #prawy {
                width: 60%;
                height: 400px;
                text-align: right;
                float: left;
            }

            footer {
                clear: both;
                background: #FF7F50;
                padding: 20px;
            }

            form {
                margin: 80px;
            }

            table {
                color: white;
                text-align: center;
                width: 80%;
                border: 1px dotted white;
            }

            tr:hover {
                background: #D3D3D3;
                color: black;
            }
        </style>
    </head>
    <body>
        <div id="logo">
            <img src="wzor.png" alt="wzór BMI">
        </div>

        <div id="baner">
            <h1>Oblicz swoje BMI</h1>
        </div>

        <main>
            <table>
                <tr>
                    <th>Interpretacja BMI</th>
                    <th>Wartość minimalna</th>
                    <th>Wartość maksymalna</th>
                </tr>
                <?php
                    // Skrypt #1
                    $sql = "SELECT wart_min, wart_max, informacja FROM bmi;";
                    $result = $conn->query($sql);
                    
                    while($row = $result -> fetch_array()) {
                        echo "<tr>";
                            echo "<td>$row[2]</td>";
                            echo "<td>$row[0]</td>";
                            echo "<td>$row[1]</td>";
                        echo "</tr>";
                    }
                ?>
            </table>
        </main>

        <div id="lewy">
            <h2>Podaj wagę i wzrost</h2>
            <form action="bmi.php" method="post">
                <label for="waga">Waga:</label> <input type="number" name="waga" id="waga" min="1"><br>
                <label for="wzrost">Wzrost w cm:</label> <input type="number" name="wzrost" id="wzrost" min="1"><br>
                <button type="submit">Oblicz i zapamiętaj wynik</button>
            </form>
            <?php
                // Skrypt #2
                if(!empty($_POST["waga"]) && !empty($_POST["wzrost"])) {
                    $waga = $_POST["waga"];
                    $wzrost = $_POST["wzrost"];

                    echo "Twoja waga: $waga; Twój wzrost: $wzrost<br>";
                    $bmi = 10000 * ($waga / ($wzrost * $wzrost));
                    echo "Bmi wynosi: $bmi";

                    if ($bmi < 18) {
                        $bmi_id = 1;
                    }
                    elseif ($bmi >= 19 && $bmi <= 25) {
                        $bmi_id = 2;
                    }
                    elseif ($bmi >= 26 && $bmi <= 30) {
                        $bmi_id = 3;
                    }
                    elseif ($bmi > 30) {
                        $bmi_id = 4;
                    }

                    $data_pomiaru = date("Y-m-d");

                    $sql = "INSERT INTO wynik (bmi_id, data_pomiaru, wynik) VALUES ($bmi_id, '$data_pomiaru', $bmi);";
                    $result = $conn->query($sql);
                }
            ?>
        </div>

        <div id="prawy">
            <img src="rys1.png" alt="ćwiczenia">
        </div>

        <footer>
            Autor: <a href="https://ee-informatyk.pl/" target="_blank" style="color: unset;text-decoration: none;">EE-Informatyk.pl</a> <a href="kwerendy.txt">Zobacz kwerendy</a>
        </footer>
    </body>
</html>

<?php
    $conn -> close();
?>