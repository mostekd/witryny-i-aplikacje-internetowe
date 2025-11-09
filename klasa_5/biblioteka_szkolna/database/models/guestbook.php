<?php
    require_once __DIR__ . '/../db.php';

    class Guestbook {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        // Dodanie wpisu przez użytkownika (na zatwierdzenie)
        public function add_entry($nickname, $email, $message) {
            $stmt = $this->db->prepare("
                INSERT INTO guestbook_entries (nickname, email, message)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $nickname, $email, $message);
            return $stmt->execute();
        }

        // Pobranie zatwierdzonych wpisów
        public function get_approved_entries() {
            $sql = "SELECT nickname, message, created_at FROM guestbook_entries WHERE approved = 1 ORDER BY created_at DESC";
            $result = $this->db->query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        // Akceptacja wpisu (panel admina)
        public function approve_entry($id, $admin_id) {
            $stmt = $this->db->prepare("
                UPDATE guestbook_entries
                SET approved = 1, approved_by = ?, approved_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param("ii", $admin_id, $id);
            return $stmt->execute();
        }

        // Lista wszystkich oczekujących wpisów
        public function get_pending_entries() {
            $sql = "SELECT * FROM guestbook_entries WHERE approved = 0 ORDER BY created_at ASC";
            $result = $this->db->query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }
    }
?>
