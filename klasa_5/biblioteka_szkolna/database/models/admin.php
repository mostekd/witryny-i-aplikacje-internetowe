<?php
    require_once __DIR__ . '/../db.php';

    class Admin {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        public function login($username, $password) {
            $stmt = $this->db->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();

            $success = 0;
            if ($user && password_verify($password, $user['password_hash'])) {
                $success = 1;
                $this->update_last_login($user['id']);
                $_SESSION['admin_id'] = $user['id'];
            }

            $this->log_login('admin', $user['id'] ?? 0, $success);
            return $success;
        }

        private function update_last_login($id) {
            $stmt = $this->db->prepare("UPDATE admin SET last_login = NOW() WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }

        private function log_login($type, $user_id, $success) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

            $stmt = $this->db->prepare("
                INSERT INTO login_history (user_type, user_id, ip_address, user_agent, success)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sissi", $type, $user_id, $ip, $agent, $success);
            $stmt->execute();
        }
    }
?>
