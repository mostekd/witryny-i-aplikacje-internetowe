<?php
    require_once __DIR__ . '/../db.php';

    class Student {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        // Logowanie ucznia po loginie i haśle
        public function login($login, $password) {
            $stmt = $this->db->prepare("SELECT * FROM students WHERE login = ?");
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $res = $stmt->get_result();
            $student = $res->fetch_assoc();

            if ($student && password_verify($password, $student['password_hash'])) {
                return $student;
            }
            return null;
        }

        // Dodanie ucznia (wykorzystywane przez admina)
        public function add_student($first_name, $last_name, $login, $password, $email) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO students (first_name, last_name, login, password_hash, email)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssss", $first_name, $last_name, $login, $hash, $email);
            return $stmt->execute();
        }
    }
?>
