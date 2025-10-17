<?php
// Hook do dodawania wydarzeń kalendarza przy wypożyczeniu książki
function dodaj_wydarzenie_wypozyczenia($wypozyczenie_id) {
    global $conn;
    
    // Pobierz dane wypożyczenia
    $sql = "select w.id, w.data_wypozyczenia, w.data_zwrotu, w.uczen_id, 
            k.tytul as tytul_ksiazki
            from wypozyczenia w 
            join ksiazki k on w.ksiazka_id = k.id 
            where w.id = $wypozyczenie_id";
    
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        // Dodaj wydarzenie przypominające o terminie zwrotu
        $tytul = "Zwrot książki: " . $row['tytul_ksiazki'];
        $opis = "Termin zwrotu książki '" . $row['tytul_ksiazki'] . "'";
        $data_rozpoczecia = $row['data_wypozyczenia'];
        $data_zwrotu = $row['data_zwrotu'];
        $uczen_id = $row['uczen_id'];
        
        $sql = "insert into kalendarz_wydarzenia 
                (uzytkownik_id, tytul, opis, data_rozpoczecia, data_zakonczenia, typ, kolor) 
                values 
                ($uczen_id, '$tytul', '$opis', '$data_rozpoczecia', '$data_zwrotu', 'wypozyczenie', '#ff5722')";
        
        $conn->query($sql);
    }
}

// Hook do aktualizacji wydarzeń kalendarza przy zmianie daty zwrotu
function aktualizuj_wydarzenie_wypozyczenia($wypozyczenie_id) {
    global $conn;
    
    $sql = "select w.id, w.data_wypozyczenia, w.data_zwrotu, w.uczen_id, 
            k.tytul as tytul_ksiazki
            from wypozyczenia w 
            join ksiazki k on w.ksiazka_id = k.id 
            where w.id = $wypozyczenie_id";
    
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $uczen_id = $row['uczen_id'];
        $data_zwrotu = $row['data_zwrotu'];
        
        // Aktualizuj istniejące wydarzenie
        $sql = "update kalendarz_wydarzenia 
                set data_zakonczenia = '$data_zwrotu'
                where uzytkownik_id = $uczen_id 
                and typ = 'wypozyczenie'
                and data_rozpoczecia = '" . $row['data_wypozyczenia'] . "'";
        
        $conn->query($sql);
    }
}

// Hook do usuwania wydarzeń kalendarza przy zwrocie książki
function usun_wydarzenie_wypozyczenia($wypozyczenie_id) {
    global $conn;
    
    $sql = "select w.id, w.data_wypozyczenia, w.uczen_id
            from wypozyczenia w 
            where w.id = $wypozyczenie_id";
    
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $uczen_id = $row['uczen_id'];
        $data_wypozyczenia = $row['data_wypozyczenia'];
        
        // Usuń wydarzenie z kalendarza
        $sql = "delete from kalendarz_wydarzenia 
                where uzytkownik_id = $uczen_id 
                and typ = 'wypozyczenie'
                and data_rozpoczecia = '$data_wypozyczenia'";
        
        $conn->query($sql);
    }
}
?>