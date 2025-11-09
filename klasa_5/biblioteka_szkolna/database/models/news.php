<?php
    require_once __DIR__ . '/../db.php';

    class News {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        // Pobiera opublikowane newsy
        public function get_published_news() {
            $sql = "SELECT * FROM news WHERE is_published = 1 ORDER BY published_at DESC";
            $result = $this->db->query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        // Pobiera pojedynczy artykuł po slug
        public function get_news_by_slug($slug) {
            $stmt = $this->db->prepare("SELECT * FROM news WHERE slug = ? AND is_published = 1");
            $stmt->bind_param("s", $slug);
            $stmt->execute();
            $res = $stmt->get_result();
            return $res->fetch_assoc();
        }

        // Dodaje nowy artykuł (dla panelu admina)
        public function add_news($title, $slug, $excerpt, $content, $author) {
            $stmt = $this->db->prepare("
                INSERT INTO news (title, slug, excerpt, content, author, is_published, published_at)
                VALUES (?, ?, ?, ?, ?, 1, NOW())
            ");
            $stmt->bind_param("sssss", $title, $slug, $excerpt, $content, $author);
            return $stmt->execute();
        }
    }
?>
