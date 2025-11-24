<?php
    require_once __DIR__ . '/../db.php';

    class Contact {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        // Zapis wiadomości kontaktowej do bazy
        public function send_message($topic, $first_name, $last_name, $email, $message) {
            $stmt = $this->db->prepare("
                INSERT INTO contacts (topic, first_name, last_name, email, message)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssss", $topic, $first_name, $last_name, $email, $message);
                $ok = $stmt->execute();

                // Po zapisaniu do bazy spróbuj wysłać e-mail do biblioteki i kopię do nadawcy.
                if ($ok) {
                    $to = 'biblioteka@wesolaszkola.pl';
                    $subject = "[Kontakt] " . $topic . " - wiadomość od " . trim($first_name . ' ' . $last_name);
                    $body = "Temat: " . $topic . "\n\n";
                    $body .= "Imię i nazwisko: " . trim($first_name . ' ' . $last_name) . "\n";
                    $body .= "Email nadawcy: " . $email . "\n\n";
                    $body .= "Treść wiadomości:\n" . $message . "\n";

                    $headers = [];
                    $headers[] = 'From: ' . ($first_name || $last_name ? ($first_name . ' ' . $last_name) : 'Kontakt') . " <no-reply@wesolaszkola.pl>";
                    // Dołącz kopię do nadawcy
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $headers[] = 'Cc: ' . $email;
                    }
                    $headers[] = 'Content-Type: text/plain; charset=UTF-8';

                    // Użycie funkcji mail() - może nie działać w środowisku lokalnym bez konfiguracji MTA.
                    @mail($to, $subject, $body, implode("\r\n", $headers));
                }

                return $ok;
        }

        // Pobranie wszystkich wiadomości (panel admina)
        public function get_all_messages() {
            $sql = "SELECT * FROM contacts ORDER BY sent_at DESC";
            $result = $this->db->query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        // Oznaczenie wiadomości jako przetworzonej
        public function mark_processed($id, $admin_id) {
            $stmt = $this->db->prepare("
                UPDATE contacts
                SET processed = 1, processed_by = ?, processed_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param("ii", $admin_id, $id);
            return $stmt->execute();
        }
    }
?>
