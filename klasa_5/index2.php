<?php
    $conn = mysqli_connect("localhost", "root", "", "komis");
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Strona</title>
    </head>
    <body>
        <ul>
            <?php
            $query = mysqli_query($conn, "SELECT marka, model, kolor FROM samochody");
            while ($data = mysqli_fetch_array($query)) {
                echo "<li>" .$data[0]." ".$data[1]." ".$data[2]."</li><br>";
            }
        ?>
        </ul>
    </body>
</html>
<?php
    mysqli_close($conn);
?>