-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 16 Kwi 2023, 18:55
-- Wersja serwera: 10.4.27-MariaDB
-- Wersja PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `weterynarz`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uslugi`
--

CREATE TABLE `uslugi` (
  `id` int(10) UNSIGNED NOT NULL,
  `nazwa` text DEFAULT NULL,
  `cena` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `uslugi`
--

INSERT INTO `uslugi` (`id`, `nazwa`, `cena`) VALUES
(1, 'pazury', 30),
(2, 'mycie', 20),
(3, 'czesanie', 10),
(4, 'uszy', 30);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zwierzeta`
--

CREATE TABLE `zwierzeta` (
  `id` int(10) UNSIGNED NOT NULL,
  `usluga_id` int(11) NOT NULL,
  `rodzaj` int(10) UNSIGNED DEFAULT NULL,
  `imie` text DEFAULT NULL,
  `wlasciciel` text DEFAULT NULL,
  `telefon` text DEFAULT NULL,
  `nastepna_wizyta` date DEFAULT NULL,
  `szczepienie` year(4) DEFAULT NULL,
  `opis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Zrzut danych tabeli `zwierzeta`
--

INSERT INTO `zwierzeta` (`id`, `usluga_id`, `rodzaj`, `imie`, `wlasciciel`, `telefon`, `nastepna_wizyta`, `szczepienie`, `opis`) VALUES
(1, 3, 1, 'Fafik', 'Adam Kowalski', '111222333', '2017-06-30', 2016, 'problemy z uszami'),
(2, 2, 1, 'Brutus', 'Anna Wysocka', '222333444', '2017-06-26', 2016, 'zapalenie krtani'),
(4, 1, 1, 'Saba', 'Monika Nowak', '333444555', NULL, 2015, 'kroplówka'),
(5, 0, 1, 'Alma', 'Jan Kowalewski', '444555666', '2017-07-03', NULL, 'antybiotyk'),
(6, 4, 2, 'Figaro', 'Anna Kowalska', '555666777', NULL, NULL, 'problemy z uszami'),
(7, 0, 2, 'Dika', 'Katarzyna Kowal', '666777888', '2017-06-30', 2016, 'operacja'),
(8, 2, 2, 'Fuks', 'Jan Nowak', '888999111', '2017-07-04', 2016, 'antybiotyk');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `uslugi`
--
ALTER TABLE `uslugi`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `zwierzeta`
--
ALTER TABLE `zwierzeta`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `uslugi`
--
ALTER TABLE `uslugi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `zwierzeta`
--
ALTER TABLE `zwierzeta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
