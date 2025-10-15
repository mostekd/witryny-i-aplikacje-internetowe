-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Paź 15, 2025 at 08:25 AM
-- Wersja serwera: 8.0.43-0ubuntu0.24.04.1
-- Wersja PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `auta`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klasa`
--

CREATE TABLE `klasa` (
  `id` int NOT NULL,
  `klasa` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `klasa`
--

INSERT INTO `klasa` (`id`, `klasa`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klienci`
--

CREATE TABLE `klienci` (
  `id` int NOT NULL,
  `nazwisko` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci NOT NULL,
  `imie` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci NOT NULL,
  `pesel` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `klienci`
--

INSERT INTO `klienci` (`id`, `nazwisko`, `imie`, `pesel`) VALUES
(1, 'Kowalski', 'Jan', ''),
(2, 'Nowak', 'Antoni', ''),
(3, 'Lipska', 'Ewa', ''),
(4, 'Ludwikowski', 'Tadeusz', ''),
(5, 'Marcinek', 'Adam', ''),
(6, 'Szymanek', 'Anna', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `samochody`
--

CREATE TABLE `samochody` (
  `id` int NOT NULL,
  `klasa_id` int NOT NULL,
  `marka` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci NOT NULL,
  `model` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci NOT NULL,
  `rocznik` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `samochody`
--

INSERT INTO `samochody` (`id`, `klasa_id`, `marka`, `model`, `rocznik`) VALUES
(1, 1, 'ford', 'ka', 2017),
(2, 2, 'seat', 'toledo', 2016),
(3, 3, 'opel', 'zafira', 2018),
(4, 2, 'fiat', '500X', 2018),
(5, 3, 'opel', 'insignia', 2017);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wypozyczenia`
--

CREATE TABLE `wypozyczenia` (
  `id` int NOT NULL,
  `klienci_id` int NOT NULL,
  `samochody_id` int NOT NULL,
  `data` date NOT NULL,
  `ilosc_dni` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `wypozyczenia`
--

INSERT INTO `wypozyczenia` (`id`, `klienci_id`, `samochody_id`, `data`, `ilosc_dni`) VALUES
(1, 6, 4, '2020-05-22', 2),
(2, 1, 5, '2020-06-18', 3),
(3, 2, 4, '2020-05-15', 2),
(4, 1, 3, '2020-06-30', 3),
(5, 3, 4, '2020-09-07', 1),
(6, 4, 3, '2020-09-02', 5),
(7, 5, 2, '2020-09-20', 3);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `klasa`
--
ALTER TABLE `klasa`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `klienci`
--
ALTER TABLE `klienci`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `samochody`
--
ALTER TABLE `samochody`
  ADD PRIMARY KEY (`id`),
  ADD KEY `klasa_id_fk` (`klasa_id`);

--
-- Indeksy dla tabeli `wypozyczenia`
--
ALTER TABLE `wypozyczenia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `samochody_id_fk` (`samochody_id`),
  ADD KEY `klienci_id_fk` (`klienci_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `klasa`
--
ALTER TABLE `klasa`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `klienci`
--
ALTER TABLE `klienci`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `samochody`
--
ALTER TABLE `samochody`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wypozyczenia`
--
ALTER TABLE `wypozyczenia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `samochody`
--
ALTER TABLE `samochody`
  ADD CONSTRAINT `klasa_id_fk` FOREIGN KEY (`klasa_id`) REFERENCES `klasa` (`id`);

--
-- Constraints for table `wypozyczenia`
--
ALTER TABLE `wypozyczenia`
  ADD CONSTRAINT `klienci_id_fk` FOREIGN KEY (`klienci_id`) REFERENCES `klienci` (`id`),
  ADD CONSTRAINT `samochody_id_fk` FOREIGN KEY (`samochody_id`) REFERENCES `samochody` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
