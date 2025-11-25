<?php
/**
 * Klasa modelu Ksiazka
 * Zarządzanie książkami w bazie danych
 */

require_once __DIR__ . '/config.php';

class Ksiazka {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Pobiera wszystkie książki (aktywne)
     */
    public function getAll($aktywne_tylko = true) {
        $sql = "SELECT * FROM ksiazki";
        if ($aktywne_tylko) {
            $sql .= " WHERE aktywna = TRUE";
        }
        $sql .= " ORDER BY data_dodania DESC";
        return $this->db->query($sql);
    }

    /**
     * Pobiera książkę po ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ksiazki WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $kniha = $result->fetch_assoc();
        $stmt->close();
        return $kniha;
    }

    /**
     * Wyszukuje książki po kryteriach
     */
    public function search($tytul = '', $autor = '', $rok_wydania = '', $aktywne_tylko = true) {
        $sql = "SELECT * FROM ksiazki WHERE 1=1";
        
        if ($tytul) {
            $sql .= " AND tytul LIKE '%" . $this->db->escape_string($tytul) . "%'";
        }
        
        if ($autor) {
            $sql .= " AND autor LIKE '%" . $this->db->escape_string($autor) . "%'";
        }
        
        if ($rok_wydania) {
            $sql .= " AND rok_wydania = " . intval($rok_wydania);
        }
        
        if ($aktywne_tylko) {
            $sql .= " AND aktywna = TRUE";
        }
        
        $sql .= " ORDER BY tytul ASC";
        return $this->db->query($sql);
    }

    /**
     * Dodaje nową książkę
     */
    public function add($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO ksiazki (tytul, autor, wydawnictwo, rok_wydania, isbn, aktywna, uwagi, ilosc_kopii)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param(
            "sssisisi",
            $data['tytul'],
            $data['autor'],
            $data['wydawnictwo'],
            $data['rok_wydania'],
            $data['isbn'],
            $data['aktywna'],
            $data['uwagi'],
            $data['ilosc_kopii']
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Aktualizuje książkę
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE ksiazki 
             SET tytul = ?, autor = ?, wydawnictwo = ?, rok_wydania = ?, isbn = ?, aktywna = ?, uwagi = ?, ilosc_kopii = ?
             WHERE id = ?"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param(
            "sssisisii",
            $data['tytul'],
            $data['autor'],
            $data['wydawnictwo'],
            $data['rok_wydania'],
            $data['isbn'],
            $data['aktywna'],
            $data['uwagi'],
            $data['ilosc_kopii'],
            $id
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Usuwa książkę
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM ksiazki WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Pobiera ilość dostępnych kopii książki
     */
    public function getAvailableCopies($id_ksiazki) {
        $stmt = $this->db->prepare(
            "SELECT (k.ilosc_kopii - COUNT(w.id)) as dostepne
             FROM ksiazki k
             LEFT JOIN wypozyczenia w ON k.id = w.id_ksiazki AND w.data_zwrotu IS NULL
             WHERE k.id = ?
             GROUP BY k.id"
        );
        $stmt->bind_param("i", $id_ksiazki);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? max(0, $row['dostepne']) : 0;
    }

    /**
     * Pobiera liczbę wszystkich książek
     */
    public function getCount($aktywne_tylko = true) {
        $sql = "SELECT COUNT(*) as cnt FROM ksiazki";
        if ($aktywne_tylko) {
            $sql .= " WHERE aktywna = TRUE";
        }
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['cnt'];
    }
}
?>
