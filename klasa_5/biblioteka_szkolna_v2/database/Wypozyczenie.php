<?php
/**
 * Klasa modelu Wypozyczenie
 * Zarządzanie wypożyczeniami w bazie danych
 */

require_once __DIR__ . '/config.php';

class Wypozyczenie {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Pobiera wszystkie wypożyczenia
     */
    public function getAll() {
        $result = $this->db->query(
            "SELECT w.*, k.tytul, k.autor, u.imie, u.nazwisko, u.email
             FROM wypozyczenia w
             JOIN ksiazki k ON w.id_ksiazki = k.id
             JOIN uczniowie u ON w.id_ucznia = u.id
             ORDER BY w.data_wypozyczenia DESC"
        );
        return $result;
    }

    /**
     * Pobiera aktywne wypożyczenia (nie zwrócone)
     */
    public function getActive() {
        $result = $this->db->query(
            "SELECT w.*, k.tytul, k.autor, u.imie, u.nazwisko, u.email
             FROM wypozyczenia w
             JOIN ksiazki k ON w.id_ksiazki = k.id
             JOIN uczniowie u ON w.id_ucznia = u.id
             WHERE w.data_zwrotu IS NULL
             ORDER BY w.data_wypozyczenia DESC"
        );
        return $result;
    }

    /**
     * Pobiera wypożyczenie po ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT w.*, k.tytul, k.autor, u.imie, u.nazwisko, u.email
             FROM wypozyczenia w
             JOIN ksiazki k ON w.id_ksiazki = k.id
             JOIN uczniowie u ON w.id_ucznia = u.id
             WHERE w.id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $wypozyczenie = $result->fetch_assoc();
        $stmt->close();
        return $wypozyczenie;
    }

    /**
     * Dodaje nowe wypożyczenie
     */
    public function add($id_ksiazki, $id_ucznia, $dni_wypozyczenia = null) {
        if ($dni_wypozyczenia === null) {
            $dni_wypozyczenia = DEFAULT_LOAN_PERIOD;
        }

        $data_planowanego_zwrotu = date('Y-m-d H:i:s', strtotime("+$dni_wypozyczenia days"));

        $stmt = $this->db->prepare(
            "INSERT INTO wypozyczenia (id_ksiazki, id_ucznia, data_planowanego_zwrotu)
             VALUES (?, ?, ?)"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("iis", $id_ksiazki, $id_ucznia, $data_planowanego_zwrotu);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Zwraca wypożyczoną książkę
     */
    public function returnBook($id_wypozyczenia) {
        $stmt = $this->db->prepare(
            "UPDATE wypozyczenia SET data_zwrotu = NOW() WHERE id = ?"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $id_wypozyczenia);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Aktualizuje wypożyczenie (np. datę zwrotu)
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE wypozyczenia 
             SET data_planowanego_zwrotu = ?, uwagi = ?
             WHERE id = ?"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("ssi", $data['data_planowanego_zwrotu'], $data['uwagi'], $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Usuwa wypożyczenie
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM wypozyczenia WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Pobiera knihy przetrzymywane powyżej okresu
     */
    public function getOverdueBooks($dni = null) {
        if ($dni === null) {
            $dni = DEFAULT_LOAN_PERIOD;
        }

        $result = $this->db->query(
            "SELECT w.*, k.tytul, k.autor, u.imie, u.nazwisko, u.email,
                    DATEDIFF(NOW(), w.data_wypozyczenia) as dni_wypozyczenia,
                    DATEDIFF(NOW(), w.data_wypozyczenia) - $dni as dni_przekroczenia
             FROM wypozyczenia w
             JOIN ksiazki k ON w.id_ksiazki = k.id
             JOIN uczniowie u ON w.id_ucznia = u.id
             WHERE w.data_zwrotu IS NULL 
             AND DATEDIFF(NOW(), w.data_wypozyczenia) > $dni
             ORDER BY w.data_wypozyczenia ASC"
        );
        return $result;
    }

    /**
     * Pobiera liczbę aktywnych wypożyczeń
     */
    public function getActiveCount() {
        $result = $this->db->query(
            "SELECT COUNT(*) as cnt FROM wypozyczenia WHERE data_zwrotu IS NULL"
        );
        $row = $result->fetch_assoc();
        return $row['cnt'];
    }
}
?>
