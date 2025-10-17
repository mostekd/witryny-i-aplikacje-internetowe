<?php
require_once '../database/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Walidacja i czyszczenie danych
$nick = $conn->real_escape_string(trim($_POST['nick']));
$email = $conn->real_escape_string(trim($_POST['email']));
$tresc = $conn->real_escape_string(trim($_POST['tresc']));

// Podstawowa walidacja
if (empty($nick) || empty($email) || empty($tresc)) {
    header('Location: ../index.php?error=empty_fields');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../index.php?error=invalid_email');
    exit;
}

if (strlen($tresc) > 1000) {
    header('Location: ../index.php?error=content_too_long');
    exit;
}

// Dodawanie wpisu
$sql = "insert into ksiega_gosci (nick, email, tresc) values ('$nick', '$email', '$tresc')";

if ($conn->query($sql)) {
    header('Location: ../index.php?success=entry_added');
} else {
    header('Location: ../index.php?error=db_error');
}