<?php
/**
 * Konfiguracja bazy danych
 */

// Stałe do połączenia z bazą danych
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'biblioteka_szkolna');
define('DB_CHARSET', 'utf8mb4');

// E-mail biblioteki
define('LIBRARY_EMAIL', 'biblioteka@wesolaszkola.pl');
define('LIBRARY_NAME', 'Biblioteka Szkoły - Wesoła Szkoła');
define('LIBRARY_ADDRESS', 'ul. Szkolna 1, 54-230 Gdańsk');
define('LIBRARY_PHONE', '+48 58 123 45 67');

// Domyślny okres wypożyczenia (dni)
define('DEFAULT_LOAN_PERIOD', 14);

// Ścieżki - absolutne dla PHP
define('BASE_PATH', dirname(dirname(__FILE__)));
define('ADMIN_PATH', BASE_PATH . '/admin');
define('WEBSITE_PATH', BASE_PATH . '/website');
define('DATABASE_PATH', BASE_PATH . '/database');

// Ścieżki - relatywne dla przeglądarki (HTTP)
$script_name = dirname($_SERVER['SCRIPT_NAME']);
if ($script_name === '/' || $script_name === '\\') {
    $script_name = '';
}
define('IMAGES_PATH', $script_name . '/images');
define('STATIC_PATH', $script_name . '/static');

// Klasa do zarządzania połączeniem z bazą danych
class Database {
    private $connection;
    private static $instance = null;

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function connect() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        if ($this->connection->connect_error) {
            die('Błąd połączenia z bazą danych: ' . $this->connection->connect_error);
        }
        
        $this->connection->set_charset(DB_CHARSET);
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        return $this->connection->query($sql);
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }

    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    public function affectedRows() {
        return $this->connection->affected_rows;
    }

    public function beginTransaction() {
        $this->connection->begin_transaction();
    }

    public function commit() {
        $this->connection->commit();
    }

    public function rollback() {
        $this->connection->rollback();
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

// Funkcja pomocnicza do zabezpieczenia danych
function sanitize($data) {
    $db = Database::getInstance();
    return $db->escape(trim($data));
}

// Funkcja do wyświetlania błędów
function showError($message) {
    echo '<div class="alert alert-danger">' . ($message) . '</div>';
}

// Funkcja do wyświetlania sukcesu
function showSuccess($message) {
    echo '<div class="alert alert-success">' . ($message) . '</div>';
}

// Funkcja do wyświetlania informacji
function showInfo($message) {
    echo '<div class="alert alert-info">' . ($message) . '</div>';
}
?>
