<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $temat = $_POST['temat'] ?? '';
    $imie = $_POST['imie'] ?? '';
    $nazwisko = $_POST['nazwisko'] ?? '';
    $tresc = $_POST['tresc'] ?? '';
    $email = $_POST['email'] ?? '';
    if ($temat && $imie && $nazwisko && $tresc && $email) {
        $to = 'biblioteka@wesolaszkola.pl';
        $subject = 'Kontakt: ' . $temat;
        $message = "Imię: $imie\nNazwisko: $nazwisko\nE-mail: $email\nTreść: $tresc";
        $headers = "From: $email\r\nReply-To: $email\r\n";
        mail($to, $subject, $message, $headers);
        mail($email, 'Kopia wiadomości do biblioteki', $message, $headers);
        echo '<p>Wiadomość została wysłana.</p>';
    }
}
?>
<h2>Kontakt</h2>
<form method="post">
  <select name="temat" required>
    <option value="">Wybierz temat</option>
    <option>Zapytanie o dostępność książki</option>
    <option>Prośba o rezerwację</option>
    <option>Inna sprawa</option>
  </select>
  <input type="text" name="imie" placeholder="Imię" required>
  <input type="text" name="nazwisko" placeholder="Nazwisko" required>
  <input type="email" name="email" placeholder="Twój e-mail" required>
  <textarea name="tresc" placeholder="Treść wiadomości" required></textarea>
  <button type="submit">Wyślij</button>
  <button type="reset">Resetuj</button>
</form>