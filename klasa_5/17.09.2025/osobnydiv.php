<?php
    $conn = mysqli_connect("localhost", "root", "", "komis");
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Strona</title>
        <style>
            .sek {
                border: 2px solid black;
                width: 200px;
                margin: 10px;
                padding: 10px;
                display: inline-block;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <?php
            $query = mysqli_query($conn, "SELECT marka, model, kolor FROM samochody");
            while ($data = mysqli_fetch_array($query)) {
                echo "<section class='sek'>" .$data[0]." <br> ".$data[1]." <br> ".$data[2]."</section>";
            }
        ?>
    </body>
</html>
<?php
    mysqli_close($conn);
?>