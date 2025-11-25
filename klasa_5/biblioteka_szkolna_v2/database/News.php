<?php
/**
 * Klasa modelu News
 * Zarządzanie newsami/artykułami w bazie danych
 */

require_once __DIR__ . '/config.php';

class News {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Pobiera wszystkie opublikowane artykuły
     */
    public function getAll($tylko_opublikowane = true) {
        $sql = "SELECT * FROM news";
        if ($tylko_opublikowane) {
            $sql .= " WHERE opublikowany = TRUE";
        }
        $sql .= " ORDER BY data_publikacji DESC";
        return $this->db->query($sql);
    }

    /**
     * Pobiera artykuł po ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT * FROM news WHERE id = ? AND opublikowany = TRUE"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $artykul = $result->fetch_assoc();
        $stmt->close();
        return $artykul;
    }

    /**
     * Pobiera artykuł po ID (dla admina)
     */
    public function getByIdAdmin($id) {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $artykul = $result->fetch_assoc();
        $stmt->close();
        return $artykul;
    }

    /**
     * Dodaje nowy artykuł
     */
    public function add($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO news (tytul, wstep, tresc, autor, zdjecie, opublikowany)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $opublikowany = isset($data['opublikowany']) ? 1 : 0;
        $stmt->bind_param(
            "sssssi",
            $data['tytul'],
            $data['wstep'],
            $data['tresc'],
            $data['autor'],
            $data['zdjecie'],
            $opublikowany
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Aktualizuje artykuł
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE news 
             SET tytul = ?, wstep = ?, tresc = ?, autor = ?, zdjecie = ?, opublikowany = ?
             WHERE id = ?"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $opublikowany = isset($data['opublikowany']) ? 1 : 0;
        $stmt->bind_param(
            "sssssii",
            $data['tytul'],
            $data['wstep'],
            $data['tresc'],
            $data['autor'],
            $data['zdjecie'],
            $opublikowany,
            $id
        );
        
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Usuwa artykuł
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Zwiększa licznik wyświetleń
     */
    public function incrementViews($id) {
        $stmt = $this->db->prepare(
            "UPDATE news SET ilosc_wyswietlen = ilosc_wyswietlen + 1 WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Pobiera liczbę artykułów
     */
    public function getCount($tylko_opublikowane = true) {
        $sql = "SELECT COUNT(*) as cnt FROM news";
        if ($tylko_opublikowane) {
            $sql .= " WHERE opublikowany = TRUE";
        }
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['cnt'];
    }

    /**
     * Pobiera losowe zdjęcie z artykułów
     */
    public function getRandomImage() {
        $result = $this->db->query(
            "SELECT zdjecie FROM news 
             WHERE opublikowany = TRUE AND zdjecie IS NOT NULL AND zdjecie != '' 
             ORDER BY RAND() LIMIT 1"
        );
        $row = $result->fetch_assoc();
        return $row ? $row['zdjecie'] : null;
    }
}
?>
