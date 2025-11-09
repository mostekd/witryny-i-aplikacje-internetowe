<?php
    require_once __DIR__ . '/../db.php';

    class Loan {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        // Dodanie nowego wypożyczenia
        public function add_loan($book_id, $student_id, $admin_id, $date_due) {
            $stmt = $this->db->prepare("
                INSERT INTO loans (book_id, student_id, date_due, created_by_admin)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iisi", $book_id, $student_id, $date_due, $admin_id);
            $ok = $stmt->execute();

            if ($ok) {
                $this->log_loan_action($this->db->insert_id(), $book_id, $student_id, $admin_id, 'borrow');
            }

            return $ok;
        }

        // Oznaczenie książki jako zwróconej
        public function return_loan($loan_id, $admin_id) {
            $stmt = $this->db->prepare("
                UPDATE loans SET returned = 1, date_returned = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param("i", $loan_id);
            $ok = $stmt->execute();

            if ($ok) {
                // pobranie danych wypożyczenia
                $loan = $this->get_loan_by_id($loan_id);
                $this->log_loan_action($loan_id, $loan['book_id'], $loan['student_id'], $admin_id, 'return');
            }

            return $ok;
        }

        // Pobiera wypożyczenia ucznia (profil)
        public function get_loans_by_student($student_id) {
            $stmt = $this->db->prepare("
                SELECT l.*, b.title
                FROM loans l
                JOIN books b ON l.book_id = b.id
                WHERE l.student_id = ?
                ORDER BY l.date_borrowed DESC
            ");
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $res = $stmt->get_result();
            return $res->fetch_all(MYSQLI_ASSOC);
        }

        // Pobranie pojedynczego wypożyczenia
        public function get_loan_by_id($loan_id) {
            $stmt = $this->db->prepare("SELECT * FROM loans WHERE id = ?");
            $stmt->bind_param("i", $loan_id);
            $stmt->execute();
            $res = $stmt->get_result();
            return $res->fetch_assoc();
        }

        // Rejestracja w historii wypożyczeń
        private function log_loan_action($loan_id, $book_id, $student_id, $admin_id, $action) {
            $stmt = $this->db->prepare("
                INSERT INTO loan_history (loan_id, book_id, student_id, admin_id, action)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iiiis", $loan_id, $book_id, $student_id, $admin_id, $action);
            $stmt->execute();
        }
    }
?>
