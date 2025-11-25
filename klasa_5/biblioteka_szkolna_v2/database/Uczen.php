<?php
/**
 * Klasa modelu Uczen
 * Zarządzanie uczniami w bazie danych
 */

require_once __DIR__ . '/config.php';

class Uczen {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Pobiera wszystkich uczniów
     */
    public function getAll($aktywni_tylko = true) {
        $sql = "SELECT * FROM uczniowie";
        if ($aktywni_tylko) {
            $sql .= " WHERE aktywny = TRUE";
        }
        $sql .= " ORDER BY nazwisko ASC, imie ASC";
        return $this->db->query($sql);
    }

    /**
     * Pobiera ucznia po ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM uczniowie WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $uczen = $result->fetch_assoc();
        $stmt->close();
        return $uczen;
    }

    /**
     * Wyszukuje uczniów po kryteriach
     */
    public function search($imie = '', $nazwisko = '', $klasa = '', $aktywni_tylko = true) {
        $sql = "SELECT * FROM uczniowie WHERE 1=1";
        
        if ($imie) {
            $sql .= " AND imie LIKE '%" . $this->db->escape_string($imie) . "%'";
        }
        
        if ($nazwisko) {
            $sql .= " AND nazwisko LIKE '%" . $this->db->escape_string($nazwisko) . "%'";
        }
        
        if ($klasa) {
            $sql .= " AND klasa = '" . $this->db->escape_string($klasa) . "'";
        }
        
        if ($aktywni_tylko) {
            $sql .= " AND aktywny = TRUE";
        }
        
        $sql .= " ORDER BY nazwisko ASC, imie ASC";
        return $this->db->query($sql);
    }

    /**
     * Dodaje nowego ucznia
     */
    public function add($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO uczniowie (imie, nazwisko, pesel, email, klasa, uwagi, aktywny)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $active = isset($data['aktywny']) ? $data['aktywny'] : true;
        $stmt->bind_param(
            "ssssssi",
            $data['imie'],
            $data['nazwisko'],
            $data['pesel'],
            $data['email'],
            $data['klasa'],
            $data['uwagi'],
            $active
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Aktualizuje ucznia
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE uczniowie 
             SET imie = ?, nazwisko = ?, pesel = ?, email = ?, klasa = ?, uwagi = ?, aktywny = ?
             WHERE id = ?"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $active = isset($data['aktywny']) ? $data['aktywny'] : true;
        $stmt->bind_param(
            "ssssssii",
            $data['imie'],
            $data['nazwisko'],
            $data['pesel'],
            $data['email'],
            $data['klasa'],
            $data['uwagi'],
            $active,
            $id
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Usuwa ucznia
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM uczniowie WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Pobiera wypożyczenia ucznia (aktywne i archiwalne)
     */
    public function getWypozyczenia($id_ucznia) {
        $stmt = $this->db->prepare(
            "SELECT w.*, k.tytul, k.autor, k.isbn
             FROM wypozyczenia w
             JOIN ksiazki k ON w.id_ksiazki = k.id
             WHERE w.id_ucznia = ?
             ORDER BY w.data_wypozyczenia DESC"
        );
        $stmt->bind_param("i", $id_ucznia);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Pobiera aktywne wypożyczenia ucznia
     */
    public function getAktywneWypozyczenia($id_ucznia) {
        $stmt = $this->db->prepare(
            "SELECT w.*, k.tytul, k.autor, k.isbn
             FROM wypozyczenia w
             JOIN ksiazki k ON w.id_ksiazki = k.id
             WHERE w.id_ucznia = ? AND w.data_zwrotu IS NULL
             ORDER BY w.data_wypozyczenia DESC"
        );
        $stmt->bind_param("i", $id_ucznia);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Pobiera liczbę uczniów
     */
    public function getCount($aktywni_tylko = true) {
        $sql = "SELECT COUNT(*) as cnt FROM uczniowie";
        if ($aktywni_tylko) {
            $sql .= " WHERE aktywny = TRUE";
        }
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['cnt'];
    }
}
?>
