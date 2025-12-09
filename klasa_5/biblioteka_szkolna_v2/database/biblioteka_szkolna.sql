-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 09, 2025 at 08:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biblioteka_szkolna`
--
CREATE DATABASE IF NOT EXISTS `biblioteka_szkolna` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `biblioteka_szkolna`;

-- --------------------------------------------------------

--
-- Table structure for table `admini`
--

DROP TABLE IF EXISTS `admini`;
CREATE TABLE IF NOT EXISTS `admini` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `imie` varchar(100) DEFAULT NULL,
  `nazwisko` varchar(100) DEFAULT NULL,
  `data_utworzenia` timestamp NOT NULL DEFAULT current_timestamp(),
  `ostatnia_logowanie` timestamp NULL DEFAULT NULL,
  `aktywny` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admini`
--

INSERT INTO `admini` (`id`, `login`, `haslo`, `email`, `imie`, `nazwisko`, `data_utworzenia`, `ostatnia_logowanie`, `aktywny`) VALUES
(1, 'admin', '$2y$10$urt2D2L6gym3LOPRoulLr.qmYFTcq1ytUeUHgxe/0fJIm8kNGoUIC', 'admin@wesolaszkola.pl', 'Administrator', 'Systemu', '2025-11-24 21:23:14', '2025-12-03 08:14:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `banery`
--

DROP TABLE IF EXISTS `banery`;
CREATE TABLE IF NOT EXISTS `banery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sciezka_zdjecia` varchar(255) NOT NULL,
  `tytul` varchar(255) DEFAULT NULL,
  `opis` text DEFAULT NULL,
  `kolejnosc` int(11) DEFAULT 0,
  `aktywny` tinyint(1) DEFAULT 1,
  `data_dodania` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_kolejnosc` (`kolejnosc`),
  KEY `idx_banery_aktywny` (`aktywny`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banery`
--

INSERT INTO `banery` (`id`, `sciezka_zdjecia`, `tytul`, `opis`, `kolejnosc`, `aktywny`, `data_dodania`) VALUES
(1, 'zwiadowcy_1.png', 'Zwiadowcy część 1', '', 1, 1, '2025-11-29 12:14:42'),
(2, 'zwiadowcy_2.png', 'Zwiadowcy część 2', '', 2, 1, '2025-11-30 08:45:00'),
(3, 'zwiadowcy_3.png', 'Zwiadowcy część 3', '', 3, 1, '2025-11-30 12:21:02'),
(4, 'zwiadowcy_4.png', 'Zwiadowcy część 4', '', 4, 1, '2025-11-30 12:22:35');

-- --------------------------------------------------------

--
-- Table structure for table `ksiazki`
--

DROP TABLE IF EXISTS `ksiazki`;
CREATE TABLE IF NOT EXISTS `ksiazki` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tytul` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `wydawnictwo` varchar(200) DEFAULT NULL,
  `rok_wydania` int(11) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `aktywna` tinyint(1) DEFAULT 1,
  `uwagi` text DEFAULT NULL,
  `data_dodania` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_modyfikacji` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ilosc_kopii` int(11) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `idx_ksiazki_aktywna` (`aktywna`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ksiazki`
--

INSERT INTO `ksiazki` (`id`, `tytul`, `autor`, `wydawnictwo`, `rok_wydania`, `isbn`, `aktywna`, `uwagi`, `data_dodania`, `data_modyfikacji`, `ilosc_kopii`) VALUES
(1, 'Pani Dalloway', 'Virginia Woolf', 'Penguin Books', 1925, '978-0-14-118936-9', 1, 'Klasyk literatury angielskiej', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1),
(2, 'Zbrodnia i kara', 'Fiodor Dostojewski', 'PWN', 1866, '978-83-01-14657-0', 1, 'Powieść psychologiczna', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1),
(3, 'Mistrz i Małgorzata', 'Michaił Bułhakow', 'Demart', 1967, '978-83-7567-042-0', 1, 'Powieść filozoficzna', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1),
(4, 'Solaris', 'Stanisław Lem', 'Wydawnictwo Literackie', 1961, '978-83-08-02968-2', 1, 'Science fiction', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1),
(5, 'W pustyni i w puszczy', 'Henryk Sienkiewicz', 'Greg', 1911, '978-83-7339-714-4', 1, 'Przygoda i przyroda', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ksiegi_gosci`
--

DROP TABLE IF EXISTS `ksiegi_gosci`;
CREATE TABLE IF NOT EXISTS `ksiegi_gosci` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tresc` text NOT NULL,
  `data_dodania` timestamp NOT NULL DEFAULT current_timestamp(),
  `widoczny` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_data` (`data_dodania`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ksiegi_gosci_pending`
--

DROP TABLE IF EXISTS `ksiegi_gosci_pending`;
CREATE TABLE IF NOT EXISTS `ksiegi_gosci_pending` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `tresc` text NOT NULL,
  `data_dodania` timestamp NOT NULL DEFAULT current_timestamp(),
  `zatwierdzony` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_zatwierdzony` (`zatwierdzony`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tytul` varchar(255) NOT NULL,
  `wstep` varchar(500) DEFAULT NULL,
  `tresc` text NOT NULL,
  `autor` varchar(100) NOT NULL,
  `zdjecie` varchar(255) DEFAULT NULL,
  `data_publikacji` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_modyfikacji` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `opublikowany` tinyint(1) DEFAULT 1,
  `ilosc_wyswietlen` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_publikacja` (`opublikowany`,`data_publikacji`),
  KEY `idx_news_opublikowany` (`opublikowany`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `tytul`, `wstep`, `tresc`, `autor`, `zdjecie`, `data_publikacji`, `data_modyfikacji`, `opublikowany`, `ilosc_wyswietlen`) VALUES
(1, 'test', 'test', 'testtesttest', 'test', 'miasto_kosci.png', '2025-11-30 12:24:12', '2025-11-30 12:33:03', 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `uczniowie`
--

DROP TABLE IF EXISTS `uczniowie`;
CREATE TABLE IF NOT EXISTS `uczniowie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imie` varchar(100) NOT NULL,
  `nazwisko` varchar(100) NOT NULL,
  `pesel` varchar(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `klasa` varchar(20) DEFAULT NULL,
  `uwagi` text DEFAULT NULL,
  `data_dodania` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_modyfikacji` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `aktywny` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pesel` (`pesel`),
  KEY `idx_uczniowie_aktywny` (`aktywny`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `uczniowie`
--

INSERT INTO `uczniowie` (`id`, `imie`, `nazwisko`, `pesel`, `email`, `klasa`, `uwagi`, `data_dodania`, `data_modyfikacji`, `aktywny`) VALUES
(1, 'Jan', 'Kowalski', '12345678901', 'jan.kowalski@example.com', '3a', 'Aktywny czytelnik', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1),
(2, 'Anna', 'Nowak', '12345678902', 'anna.nowak@example.com', '3b', 'Zainteresowana literaturą', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1),
(3, 'Tomasz', 'Lewandowski', '12345678903', 'tomasz.lewandowski@example.com', '4a', 'Będę czytać więcej', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1),
(4, 'Maria', 'Zielińska', '12345678904', 'maria.zielinska@example.com', '4b', 'Czytelniczka', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1),
(5, 'Piotr', 'Kamiński', '12345678905', 'piotr.kaminski@example.com', '5a', 'Pasjonuje się lekturą', '2025-11-24 21:23:14', '2025-11-24 21:23:14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ustawienia`
--

DROP TABLE IF EXISTS `ustawienia`;
CREATE TABLE IF NOT EXISTS `ustawienia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazwa_ustawienia` varchar(100) NOT NULL,
  `wartosc` varchar(255) DEFAULT NULL,
  `typ` varchar(50) DEFAULT NULL,
  `opis` text DEFAULT NULL,
  `data_modyfikacji` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nazwa_ustawienia` (`nazwa_ustawienia`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ustawienia`
--

INSERT INTO `ustawienia` (`id`, `nazwa_ustawienia`, `wartosc`, `typ`, `opis`, `data_modyfikacji`) VALUES
(1, 'okres_wypozyczenia', '14', 'int', 'Domyślny okres wypożyczenia w dniach', '2025-11-24 21:23:14'),
(2, 'nazwa_biblioteki', 'Biblioteka Szkoły - Wesoła Szkoła', 'string', 'Nazwa biblioteki', '2025-11-24 21:23:14'),
(3, 'email_biblioteki', 'biblioteka@wesolaszkola.pl', 'string', 'E-mail biblioteki', '2025-11-24 21:23:14'),
(4, 'adres_biblioteki', 'ul. Szkolna 1, 54-230 Gdańsk', 'string', 'Adres biblioteki', '2025-11-24 21:23:14'),
(5, 'telefon_biblioteki', '+48 58 123 45 67', 'string', 'Numer telefonu biblioteki', '2025-11-24 21:23:14');

-- --------------------------------------------------------

--
-- Table structure for table `wiadomosci_kontaktowe`
--

DROP TABLE IF EXISTS `wiadomosci_kontaktowe`;
CREATE TABLE IF NOT EXISTS `wiadomosci_kontaktowe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typ` varchar(50) NOT NULL,
  `imie` varchar(100) NOT NULL,
  `nazwisko` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `wiadomosc` text NOT NULL,
  `data_wyslanija` timestamp NOT NULL DEFAULT current_timestamp(),
  `przeczytana` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_przeczytana` (`przeczytana`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wypozyczenia`
--

DROP TABLE IF EXISTS `wypozyczenia`;
CREATE TABLE IF NOT EXISTS `wypozyczenia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_ksiazki` int(11) NOT NULL,
  `id_ucznia` int(11) NOT NULL,
  `data_wypozyczenia` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_zwrotu` timestamp NULL DEFAULT NULL,
  `data_planowanego_zwrotu` timestamp NULL DEFAULT NULL,
  `uwagi` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_ksiazki` (`id_ksiazki`),
  KEY `idx_aktywne` (`data_zwrotu`),
  KEY `idx_ucznia` (`id_ucznia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `wypozyczenia`
--
ALTER TABLE `wypozyczenia`
  ADD CONSTRAINT `wypozyczenia_ibfk_1` FOREIGN KEY (`id_ksiazki`) REFERENCES `ksiazki` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wypozyczenia_ibfk_2` FOREIGN KEY (`id_ucznia`) REFERENCES `uczniowie` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
