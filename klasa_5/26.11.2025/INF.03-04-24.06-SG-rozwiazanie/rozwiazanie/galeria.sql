-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Paź 12, 2024 at 12:31 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `galeria`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `autorzy`
--

CREATE TABLE `autorzy` (
  `id` int(10) UNSIGNED NOT NULL,
  `imie` varchar(10) DEFAULT NULL,
  `nazwisko` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `autorzy`
--

INSERT INTO `autorzy` (`id`, `imie`, `nazwisko`) VALUES
(1, 'Sylwia', 'Nowak'),
(2, 'Jan', 'Przybylski'),
(3, 'Jadwiga', 'Kowalska'),
(4, 'Ewelina', 'Nowakowska'),
(5, 'Krzysztof', 'Kot'),
(6, 'Przemysław', 'Dobrowolski'),
(7, 'Ewa', 'Dobrowolska'),
(8, 'Marcin', 'Kowalewski'),
(9, 'Jolanta', 'Biała'),
(10, 'Monika', 'Szczęsna'),
(11, 'Edyta', 'Nowak');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zdjecia`
--

CREATE TABLE `zdjecia` (
  `id` int(10) UNSIGNED NOT NULL,
  `autorzy_id` int(10) UNSIGNED NOT NULL,
  `tytul` text DEFAULT NULL,
  `plik` varchar(50) DEFAULT NULL,
  `polubienia` int(10) UNSIGNED DEFAULT NULL,
  `rozmiarPliku` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `zdjecia`
--

INSERT INTO `zdjecia` (`id`, `autorzy_id`, `tytul`, `plik`, `polubienia`, `rozmiarPliku`) VALUES
(1, 3, 'Rajd Monte Carlo', 'car.jpg', 5, NULL),
(2, 2, 'Moja kotka niezbyt lubi psa sąsiadów ', 'cat.jpg', 10, NULL),
(3, 2, 'W czasie ostatniej wycieczki uchwyciłem taki widok', 'jesien.jpg', 2, NULL),
(4, 7, 'Urocze jeziorko w dolinie gór', 'jeziorko.jpg', 40, NULL),
(5, 9, 'Slava Ukraini!', 'kiev.jpg', 100, NULL),
(6, 3, 'Motyl', 'motyl.jpg', 55, NULL),
(7, 3, 'Mama z dwójką swoich małych na pastwisku', 'owce.jpg', 3, NULL),
(8, 9, 'Czeska Praga', 'prague.jpg', 80, NULL),
(9, 4, 'Gdzieś w Londynie', 'taxi.jpg', 4, NULL),
(10, 4, 'Moje tulipany', 'tulipany.jpg', 30, NULL),
(11, 3, 'Dzięcioł Duży, chroniony', 'woodpecker.jpg', 50, NULL),
(12, 9, 'Miasto 100 mostów, czyli Wrocław', 'wroclaw.jpg', 75, NULL);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `autorzy`
--
ALTER TABLE `autorzy`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `zdjecia`
--
ALTER TABLE `zdjecia`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `autorzy`
--
ALTER TABLE `autorzy`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `zdjecia`
--
ALTER TABLE `zdjecia`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
