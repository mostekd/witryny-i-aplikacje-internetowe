<?php
/**
 * Klasa modelu KsiegaGosci
 * Zarządzanie księgą gości w bazie danych
 */

require_once __DIR__ . '/config.php';

class KsiegaGosci {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Pobiera wszystkie zatwierdzane wpisy
     */
    public function getAll() {
        $result = $this->db->query(
            "SELECT * FROM ksiegi_gosci WHERE widoczny = TRUE ORDER BY data_dodania DESC"
        );
        return $result;
    }

    /**
     * Pobiera wpis po ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT * FROM ksiegi_gosci WHERE id = ? AND widoczny = TRUE"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $wpis = $result->fetch_assoc();
        $stmt->close();
        return $wpis;
    }

    /**
     * Dodaje nowy wpis do księgi gości
     */
    public function add($nick, $email, $tresc) {
        $stmt = $this->db->prepare(
            "INSERT INTO ksiegi_gosci (nick, email, tresc, widoczny)
             VALUES (?, ?, ?, TRUE)"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("sss", $nick, $email, $tresc);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Usuwa wpis z księgi gości
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM ksiegi_gosci WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Pobiera liczbę wpisów
     */
    public function getCount() {
        $result = $this->db->query(
            "SELECT COUNT(*) as cnt FROM ksiegi_gosci WHERE widoczny = TRUE"
        );
        $row = $result->fetch_assoc();
        return $row['cnt'];
    }

    /**
     * Pobiera wszystkie oczekujące wpisy (do zatwierdzenia)
     */
    public function getPending() {
        $result = $this->db->query(
            "SELECT * FROM ksiegi_gosci_pending ORDER BY data_dodania DESC"
        );
        return $result;
    }

    /**
     * Pobiera oczekujący wpis po ID
     */
    public function getPendingById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ksiegi_gosci_pending WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $wpis = $result->fetch_assoc();
        $stmt->close();
        return $wpis;
    }

    /**
     * Dodaje nowy oczekujący wpis
     */
    public function addPending($nick, $email, $tresc) {
        $stmt = $this->db->prepare(
            "INSERT INTO ksiegi_gosci_pending (nick, email, tresc)
             VALUES (?, ?, ?)"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("sss", $nick, $email, $tresc);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Zatwierdza wpis i przenosi go z oczekujących
     */
    public function approve($id_pending) {
        $pending = $this->getPendingById($id_pending);
        
        if (!$pending) {
            return false;
        }

        $this->db->begin_transaction();

        try {
            // Dodaj do zatwierdzonych
            $stmt = $this->db->prepare(
                "INSERT INTO ksiegi_gosci (nick, email, tresc)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $pending['nick'], $pending['email'], $pending['tresc']);
            $stmt->execute();
            $stmt->close();

            // Usuń z oczekujących
            $stmt = $this->db->prepare("DELETE FROM ksiegi_gosci_pending WHERE id = ?");
            $stmt->bind_param("i", $id_pending);
            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Odrzuca wpis
     */
    public function reject($id_pending) {
        $stmt = $this->db->prepare("DELETE FROM ksiegi_gosci_pending WHERE id = ?");
        $stmt->bind_param("i", $id_pending);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Pobiera liczbę oczekujących wpisów
     */
    public function getPendingCount() {
        $result = $this->db->query(
            "SELECT COUNT(*) as cnt FROM ksiegi_gosci_pending"
        );
        $row = $result->fetch_assoc();
        return $row['cnt'];
    }
}
?>
