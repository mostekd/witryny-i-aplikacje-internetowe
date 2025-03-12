<?php
    $polaczenie = mysqli_connect('localhost', 'root', '', '');

    $wynik = mysqli_query($polaczenie,"SHOW DATABASES LIKE 'losowabaza'");

    if (mysqli_num_rows($wynik) == 0) {
         mysqli_query($polaczenie, "CREATE DATABASE losowabaza");
         mysqli_select_db($polaczenie, 'losowabaza');

         $tabela = "CREATE TABLE dane (
         id INT AUTO_INCREMENT PRIMARY KEY,
         imie VARCHAR(50),
         nazwisko VARCHAR(50),
         data_urodzenia DATE,
         wyksztalcenie VARCHAR(50),
         zawod VARCHAR(50),
         wynagrodzenie DECIMAL(10,2)
         )";

         if (mysqli_query($polaczenie, $tabla)) {
            echo "Baza danych i tabela zostały utworzone. <br>";
            echo '
            <form action="" method="POST">
                <input type="submit" name="uzupelnij_dane" value="Uzupełnij dane losowymi danymi">
            </form>';
         }
    } else {
        echo "Baza danych już istnieje. <br>";
        echo '
            <form action="" method="POST">
                <input type="submit" name="uzupelnij_dane" value="Uzupełnij dane losowymi danymi">
            </form>';
    } 

    if (isset($_POST['uzupelnij_dane'])) {
        $imie = ['Anna', 'Jan', 'Maria', 'Piotr', 'Katarzyna', 'Andrzej', 'Magdalena', 'Tomasz', 'Agnieszka', 'Marek'];
        $nazwisko = ['Kowalska', 'Nowak', 'Wiśniewska', 'Wójcik', 'Kowalczyk', 'Kamińska', 'Lewandowska', 'Zielińska', 'Szymańska', 'Woźniak'];
        $data_urodzenia = ['1980-01-15', '1985-05-22', '1990-08-03', '1975-12-10', '1995-04-18', '1988-07-25', '1992-11-30', '1983-02-14', '1998-09-05', '1970-06-20'];
        $wyksztalcenie = ['podstawowe', 'średnie', 'wyższe', 'zawodowe', 'technikum', 'licencjat', 'mgr', 'inżynier', 'doktor', 'profesor'];
        $zawod = ['lekarz', 'nauczyciel', 'programista', 'elektryk', 'księgowa', 'kierowca', 'sprzedawca', 'murarz', 'piekarz', 'fryzjer'];
        $wynagrodzenie = [3000.00, 4500.50, 5200.00, 2800.75, 6000.00, 3500.00, 4200.00, 4800.50, 5500.00, 3800.25];

        for ( $i = 0; $i < 50; $i++ ) {
            $random_imie = $imie[array_rand($imie)];
            $random_nazwisko = $nazwisko[array_rand($nazwisko)];
            $random_data_urodzenia = $data_urodzenia[array_rand($data_urodzenia)];
            $random_wyksztalcenie = $wyksztalcenie[array_rand($wyksztalcenie)];
            $random_zawod = $zawod[array_rand($zawod)];
            $random_wynagrodzenie = $wynagrodzenie[array_rand($wynagrodzenie)];

            $query = "INSERT INTO dane (imie, nazwisko, data_urodzenia, wyksztalcenie, zawod, wynagrodzenie) 
            VALUES ('$random_imie', '$random_nazwisko', '$random_data_urodzenia', '$random_wyksztalcenie', '$random_zawod', '$random_wynagrodzenie')";
            mysqli_query($polaczenie, $query);
        }
        echo "Dane zostały uzupełnione losowymi wartościami";
    }

    mysqli_close($polaczenie);
?>