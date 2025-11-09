<?php
    require_once __DIR__ . '/../db.php';

    class Book {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        public function get_all_books() {
            $sql = "SELECT * FROM books WHERE active = 1 ORDER BY title ASC";
            $result = $this->db->query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        public function get_book_by_id($id) {
            $stmt = $this->db->prepare("SELECT * FROM books WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        }

        public function add_book($title, $author, $year, $isbn, $publisher) {
            $stmt = $this->db->prepare("
                INSERT INTO books (title, author, year, isbn, publisher)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssiss", $title, $author, $year, $isbn, $publisher);
            return $stmt->execute();
        }

        public function update_book($id, $title, $author, $year, $isbn, $publisher) {
            $stmt = $this->db->prepare("
                UPDATE books SET title=?, author=?, year=?, isbn=?, publisher=?, updated_at=NOW() WHERE id=?
            ");
            $stmt->bind_param("ssissi", $title, $author, $year, $isbn, $publisher, $id);
            return $stmt->execute();
        }

        public function delete_book($id) {
            $stmt = $this->db->prepare("DELETE FROM books WHERE id=?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        }
    }
?>
