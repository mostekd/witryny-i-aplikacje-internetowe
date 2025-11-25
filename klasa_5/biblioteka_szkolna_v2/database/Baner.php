<?php
/**
 * Klasa modelu Baner
 * Zarządzanie banerami w bazie danych
 */

require_once __DIR__ . '/config.php';

class Baner {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Pobiera wszystkie aktywne banery
     */
    public function getAll($tylko_aktywne = true) {
        $sql = "SELECT * FROM banery";
        if ($tylko_aktywne) {
            $sql .= " WHERE aktywny = TRUE";
        }
        $sql .= " ORDER BY kolejnosc ASC";
        return $this->db->query($sql);
    }

    /**
     * Pobiera baner po ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM banery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $baner = $result->fetch_assoc();
        $stmt->close();
        return $baner;
    }

    /**
     * Dodaje nowy baner
     */
    public function add($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO banery (sciezka_zdjecia, tytul, opis, kolejnosc, aktywny)
             VALUES (?, ?, ?, ?, ?)"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $aktywny = isset($data['aktywny']) ? 1 : 0;
        $stmt->bind_param(
            "sssii",
            $data['sciezka_zdjecia'],
            $data['tytul'],
            $data['opis'],
            $data['kolejnosc'],
            $aktywny
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Aktualizuje baner
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE banery 
             SET sciezka_zdjecia = ?, tytul = ?, opis = ?, kolejnosc = ?, aktywny = ?
             WHERE id = ?"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $aktywny = isset($data['aktywny']) ? 1 : 0;
        $stmt->bind_param(
            "sssiiii",
            $data['sciezka_zdjecia'],
            $data['tytul'],
            $data['opis'],
            $data['kolejnosc'],
            $aktywny,
            $id
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Usuwa baner
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM banery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Pobiera liczbę banerów
     */
    public function getCount($tylko_aktywne = true) {
        $sql = "SELECT COUNT(*) as cnt FROM banery";
        if ($tylko_aktywne) {
            $sql .= " WHERE aktywny = TRUE";
        }
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['cnt'];
    }
}
?>
