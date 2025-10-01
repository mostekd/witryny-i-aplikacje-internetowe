<?php
    $conn = mysqli_connect('127.0.0.1', 'root', '', 'zwierzeta');
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kartkówka</title>
    </head>
    <body>
        <ol>
            <?php
                $query = mysqli_query($conn, "SELECT imie, wlasciciel, telefon FROM zwierzeta");
                while ($data  =  mysqli_fetch_array($query)){
                    echo "<li>".$data['imie']." - ".$data['wlasciciel']." - ".$data['telefon']."</li>";
                };
            ?>
        </ol>
        <table border="1">
            <?php
                    $query = mysqli_query($conn, "SELECT imie, wlasciciel, telefon FROM zwierzeta");
                    while ($data  =  mysqli_fetch_array($query)){
                        echo "<tr>
                                <td>".$data['imie']."</td>
                                <td>".$data['wlasciciel']."</td>
                                <td>".$data['telefon']."</td>
                            </tr>";
                    };
            ?>
        </table>
    </body>
</html>
<?php
    mysqli_close($conn);
?>