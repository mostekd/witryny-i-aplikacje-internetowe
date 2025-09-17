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
        <table border="1">
            <tr>
                <th>Marka</th>
                <th>Model</th>
                <th>Kolor</th>
            </tr>
            <?php
                $query = mysqli_query($conn, "SELECT marka, model, kolor FROM samochody");
                while ($data = mysqli_fetch_array($query)) {
                    echo "<tr><td>" .$data[0]." </td><td> ".$data[1]." </td><td> ".$data[2]."</td></tr>";
                }
            ?>
        </table>
    </body>
</html>
<?php
    mysqli_close($conn);
?>