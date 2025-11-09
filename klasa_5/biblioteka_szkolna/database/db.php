<?php
    require_once __DIR__ . '/config.php';

    class Database {
        private $connection;

        public function __construct() {
            $this->connect();
        }

        private function connect() {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($this->connection->connect_error) {
                die("błąd połączenia z bazą danych: " . $this->connection->connect_error);
            }
            $this->connection->set_charset("utf8mb4");
        }

        public function query($sql) {
            return $this->connection->query($sql);
        }

        public function prepare($sql) {
            return $this->connection->prepare($sql);
        }

        public function escape($value) {
            return $this->connection->real_escape_string($value);
        }

        public function insert_id() {
            return $this->connection->insert_id;
        }

        public function close() {
            $this->connection->close();
        }
    }
?>
