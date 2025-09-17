<?php
$conn = new mysqli('localhost', 'root', '', 'komis');
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
$sql = "SELECT marka, model, rocznik, kolor FROM samochody";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Samochody w komisie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $img = "images/" . $row['kolor'];
            echo '<div class="car-card">';
            if (file_exists($img)) {
                echo '<img src="'.$img.'" alt="'.$row['kolor'].'" class="car-img">';
            } else {
                echo '<img src="images/default.jpg" alt="brak zdjęcia" class="car-img">';
            }
            echo '<div class="car-desc">';
            echo '<h2>' . htmlspecialchars($row['marka']) . '</h2>';
            echo '<p>Model: ' . htmlspecialchars($row['model']) . '</p>';
            echo '<p>Rocznik: ' . htmlspecialchars($row['rocznik']) . '</p>';
            echo '</div></div>';
        }
    } else {
        echo "Brak samochodów w bazie.";
    }
    $conn->close();
    ?>
    </div>
</body>
</html>