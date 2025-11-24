<?php
require_once __DIR__ . '/../db.php';

class Images {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Pobiera losowe obrazy (z tabeli images)
    public function get_random_images($limit = 3) {
        $limit = (int)$limit;
        $sql = "SELECT * FROM images ORDER BY RAND() LIMIT $limit";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function get_random_image() {
        $imgs = $this->get_random_images(1);
        return $imgs[0] ?? null;
    }
}

?>
