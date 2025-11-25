-- Baza danych dla Biblioteki Szkolnej
-- TEB Częstochowa
-- Schemat bazy danych MySQL

CREATE DATABASE IF NOT EXISTS biblioteka_szkolna CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteka_szkolna;

-- Tabela administratorów
CREATE TABLE IF NOT EXISTS admini (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL UNIQUE,
    haslo VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    imie VARCHAR(100),
    nazwisko VARCHAR(100),
    data_utworzenia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ostatnia_logowanie TIMESTAMP NULL,
    aktywny BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela książek
CREATE TABLE IF NOT EXISTS ksiazki (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tytul VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    wydawnictwo VARCHAR(200),
    rok_wydania INT,
    isbn VARCHAR(20) UNIQUE,
    aktywna BOOLEAN DEFAULT TRUE,
    uwagi TEXT,
    data_dodania TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modyfikacji TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ilosc_kopii INT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela uczniów
CREATE TABLE IF NOT EXISTS uczniowie (
    id INT PRIMARY KEY AUTO_INCREMENT,
    imie VARCHAR(100) NOT NULL,
    nazwisko VARCHAR(100) NOT NULL,
    pesel VARCHAR(11) UNIQUE,
    email VARCHAR(100),
    klasa VARCHAR(20),
    uwagi TEXT,
    data_dodania TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modyfikacji TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    aktywny BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela wypożyczeń
CREATE TABLE IF NOT EXISTS wypozyczenia (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_ksiazki INT NOT NULL,
    id_ucznia INT NOT NULL,
    data_wypozyczenia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_zwrotu TIMESTAMP NULL,
    data_planowanego_zwrotu TIMESTAMP NULL,
    uwagi TEXT,
    FOREIGN KEY (id_ksiazki) REFERENCES ksiazki(id) ON DELETE CASCADE,
    FOREIGN KEY (id_ucznia) REFERENCES uczniowie(id) ON DELETE CASCADE,
    INDEX idx_aktywne (data_zwrotu),
    INDEX idx_ucznia (id_ucznia)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela newsów/artykułów
CREATE TABLE IF NOT EXISTS news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tytul VARCHAR(255) NOT NULL,
    wstep VARCHAR(500),
    tresc TEXT NOT NULL,
    autor VARCHAR(100) NOT NULL,
    zdjecie VARCHAR(255),
    data_publikacji TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modyfikacji TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    opublikowany BOOLEAN DEFAULT TRUE,
    ilosc_wyswietlen INT DEFAULT 0,
    INDEX idx_publikacja (opublikowany, data_publikacji DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela banerów
CREATE TABLE IF NOT EXISTS banery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sciezka_zdjecia VARCHAR(255) NOT NULL,
    tytul VARCHAR(255),
    opis TEXT,
    kolejnosc INT DEFAULT 0,
    aktywny BOOLEAN DEFAULT TRUE,
    data_dodania TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_kolejnosc (kolejnosc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela księgi gości (wpisy zatwierdzone)
CREATE TABLE IF NOT EXISTS ksiegi_gosci (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nick VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    tresc TEXT NOT NULL,
    data_dodania TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    widoczny BOOLEAN DEFAULT TRUE,
    INDEX idx_data (data_dodania DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela księgi gości (wpisy do zatwierdzenia)
CREATE TABLE IF NOT EXISTS ksiegi_gosci_pending (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nick VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    tresc TEXT NOT NULL,
    data_dodania TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    zatwierdzony BOOLEAN DEFAULT FALSE,
    INDEX idx_zatwierdzony (zatwierdzony)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela wiadomości kontaktowych (do logowania)
CREATE TABLE IF NOT EXISTS wiadomosci_kontaktowe (
    id INT PRIMARY KEY AUTO_INCREMENT,
    typ VARCHAR(50) NOT NULL,
    imie VARCHAR(100) NOT NULL,
    nazwisko VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    wiadomosc TEXT NOT NULL,
    data_wyslanija TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    przeczytana BOOLEAN DEFAULT FALSE,
    INDEX idx_przeczytana (przeczytana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela okresu wypożyczenia
CREATE TABLE IF NOT EXISTS ustawienia (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nazwa_ustawienia VARCHAR(100) NOT NULL UNIQUE,
    wartosc VARCHAR(255),
    typ VARCHAR(50),
    opis TEXT,
    data_modyfikacji TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wstawianie domyślnych danych
INSERT INTO ustawienia (nazwa_ustawienia, wartosc, typ, opis) VALUES
('okres_wypozyczenia', '14', 'int', 'Domyślny okres wypożyczenia w dniach'),
('nazwa_biblioteki', 'Biblioteka Szkoły - Wesoła Szkoła', 'string', 'Nazwa biblioteki'),
('email_biblioteki', 'biblioteka@wesolaszkola.pl', 'string', 'E-mail biblioteki'),
('adres_biblioteki', 'ul. Szkolna 1, 54-230 Gdańsk', 'string', 'Adres biblioteki'),
('telefon_biblioteki', '+48 58 123 45 67', 'string', 'Numer telefonu biblioteki')
ON DUPLICATE KEY UPDATE wartosc=wartosc;

-- Wstawianie danych testowych administratora (login: admin, hasło: admin123)
INSERT INTO admini (login, haslo, email, imie, nazwisko) VALUES
('admin', '$2y$10$YJvXWBLkUYa4YSG3OnBv5eUBvQJPPqRQW4LnWFsKKAqNhHNJiGKcy', 'admin@wesolaszkola.pl', 'Administrator', 'Systemu')
ON DUPLICATE KEY UPDATE login=login;

-- Przykładowe dane testowe
INSERT INTO ksiazki (tytul, autor, wydawnictwo, rok_wydania, isbn, aktywna, uwagi) VALUES
('Pani Dalloway', 'Virginia Woolf', 'Penguin Books', 1925, '978-0-14-118936-9', TRUE, 'Klasyk literatury angielskiej'),
('Zbrodnia i kara', 'Fiodor Dostojewski', 'PWN', 1866, '978-83-01-14657-0', TRUE, 'Powieść psychologiczna'),
('Mistrz i Małgorzata', 'Michaił Bułhakow', 'Demart', 1967, '978-83-7567-042-0', TRUE, 'Powieść filozoficzna'),
('Solaris', 'Stanisław Lem', 'Wydawnictwo Literackie', 1961, '978-83-08-02968-2', TRUE, 'Science fiction'),
('W pustyni i w puszczy', 'Henryk Sienkiewicz', 'Greg', 1911, '978-83-7339-714-4', TRUE, 'Przygoda i przyroda')
ON DUPLICATE KEY UPDATE tytul=tytul;

INSERT INTO uczniowie (imie, nazwisko, pesel, email, klasa, uwagi) VALUES
('Jan', 'Kowalski', '12345678901', 'jan.kowalski@example.com', '3a', 'Aktywny czytelnik'),
('Anna', 'Nowak', '12345678902', 'anna.nowak@example.com', '3b', 'Zainteresowana literaturą'),
('Tomasz', 'Lewandowski', '12345678903', 'tomasz.lewandowski@example.com', '4a', 'Będę czytać więcej'),
('Maria', 'Zielińska', '12345678904', 'maria.zielinska@example.com', '4b', 'Czytelniczka'),
('Piotr', 'Kamiński', '12345678905', 'piotr.kaminski@example.com', '5a', 'Pasjonuje się lekturą')
ON DUPLICATE KEY UPDATE imie=imie;

-- Indeksy
CREATE INDEX idx_ksiazki_aktywna ON ksiazki(aktywna);
CREATE INDEX idx_uczniowie_aktywny ON uczniowie(aktywny);
CREATE INDEX idx_news_opublikowany ON news(opublikowany);
CREATE INDEX idx_banery_aktywny ON banery(aktywny);
