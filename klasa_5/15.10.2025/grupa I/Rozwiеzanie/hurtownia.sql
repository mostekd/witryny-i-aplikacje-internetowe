-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 28 Mar 2023, 19:40
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
-- Baza danych: `hurtownia`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klienci`
--

CREATE TABLE `klienci` (
  `id` int(10) UNSIGNED NOT NULL,
  `Typy_id` int(10) UNSIGNED NOT NULL,
  `imie` text DEFAULT NULL,
  `nazwisko` text DEFAULT NULL,
  `zdjecie` varchar(20) DEFAULT NULL,
  `punkty` int(10) UNSIGNED DEFAULT NULL,
  `adres` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `klienci`
--

INSERT INTO `klienci` (`id`, `Typy_id`, `imie`, `nazwisko`, `zdjecie`, `punkty`, `adres`) VALUES
(1, 2, 'Anna', 'Kowalska', 'anna.jpg', 2000, NULL),
(2, 2, 'Ewa', 'Nowakowska', 'ewa.jpg', 2045, NULL),
(3, 1, 'Krystyna', 'Nowakowska', 'krycha.jpg', 100, NULL),
(4, 3, 'Jan', 'Nowak', 'janek.jpg', 3305, NULL),
(5, 2, 'John', 'Smith', 'john.jpg', 2005, NULL),
(6, 3, 'Judy', 'Beck', 'judy.jpg', 3055, NULL),
(7, 1, 'Marcin', 'Kowalski', 'marcin.jpg', 1045, NULL),
(8, 1, 'Kris', 'Smith', 'Kris.jpg', 0, NULL),
(9, 1, 'Jacek', 'Jackowski', 'jacek.jpg', 45, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `opinie`
--

CREATE TABLE `opinie` (
  `id` int(10) UNSIGNED NOT NULL,
  `Klienci_id` int(10) UNSIGNED NOT NULL,
  `opinia` text DEFAULT NULL,
  `ocena` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `opinie`
--

INSERT INTO `opinie` (`id`, `Klienci_id`, `opinia`, `ocena`) VALUES
(1, 1, 'Dobra współpraca, zawsze świeże towary i miła obsługa', 6),
(2, 2, 'Polecam hurtownię za profesjonalne podejście do klienta', 6),
(3, 3, 'Wszystkie transakcje przebiegły sprawnie', 5),
(4, 4, 'Polecam hurtownię za profesjonalne podejście do klienta', 5),
(5, 5, 'Wszystkie transakcje przebiegły sprawnie', 5),
(6, 6, 'Truskawki, które zakupiłam były nieświeże, poza tym wszystko ok', 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `typy`
--

CREATE TABLE `typy` (
  `id` int(10) UNSIGNED NOT NULL,
  `nazwa` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `typy`
--

INSERT INTO `typy` (`id`, `nazwa`) VALUES
(1, 'zwykły'),
(2, 'złoty'),
(3, 'platynowy');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `klienci`
--
ALTER TABLE `klienci`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `opinie`
--
ALTER TABLE `opinie`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `typy`
--
ALTER TABLE `typy`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `klienci`
--
ALTER TABLE `klienci`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `opinie`
--
ALTER TABLE `opinie`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `typy`
--
ALTER TABLE `typy`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
