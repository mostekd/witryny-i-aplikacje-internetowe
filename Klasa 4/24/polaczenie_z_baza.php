<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "bazadanych";

	$conn = new mysqli($servername, $username, $password);

	if ($conn->connect_error) {
	    die("Błąd połączenia: " . $conn->connect_error);
	}

	$query = "CREATE DATABASE IF NOT EXISTS $dbname";
	if ($conn->query($query) === TRUE) {
	    echo "Baza danych '$dbname' jest gotowa.";
	} else {
	    echo "Błąd podczas tworzenia bazy danych: " . $conn->error;
	}

	$conn->close();

	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
	    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
	} else {
	    echo "\nPołączono z bazą danych '$dbname'.";
	}

	$conn->close();
?>
