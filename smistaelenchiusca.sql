-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Lug 16, 2022 alle 20:04
-- Versione del server: 10.5.15-MariaDB-0+deb11u1
-- Versione PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smistaelenchiusca`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `localita`
--

CREATE TABLE `localita` (
  `id` int(11) NOT NULL,
  `id_usca` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `localita`
--

INSERT INTO `localita` (`id`, `id_usca`, `nome`) VALUES
(3, 1, 'MESSINA'),
(4, 1, 'MESSINA (ME)'),
(5, 2, 'TAORMINA (ME)'),
(6, 2, 'GIARDINI NAXOS (ME)'),
(7, 2, 'GIARDINI-NAXOS (ME)');

-- --------------------------------------------------------

--
-- Struttura della tabella `usca`
--

CREATE TABLE `usca` (
  `id` int(11) NOT NULL,
  `descrizione` varchar(30) NOT NULL,
  `chiave` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `usca`
--

INSERT INTO `usca` (`id`, `descrizione`, `chiave`) VALUES
(1, 'Messina (Nord, Centro, Sud)', 'MESSINA'),
(2, 'Taormina', 'TAORMINA'),
(3, 'Barcellona', 'BARCELLONA'),
(4, 'Ionica', 'IONICA'),
(5, 'Lipari', 'LIPARI'),
(6, 'Longano', 'LONGANO'),
(7, 'Milazzo', 'MILAZZO'),
(8, 'Milazzo-Barcellona', 'MILAZZOBARCELLONA'),
(9, 'Mistretta', 'MISTRETTA'),
(10, 'Nebrodi', 'NEBRODI'),
(11, 'Patti', 'PATTI'),
(12, 'Patti Scolastica', 'PATTISCOLASTICA'),
(13, 'Peloritani', 'PELORITANI'),
(14, 'Peloritani 2', 'PELORITANI2'),
(15, 'Roccalumera', 'ROCCALUMERA'),
(16, 'San\'Agata di Militello', 'SANTAGATA'),
(17, 'Saponara', 'SAPONARA'),
(18, 'Tirrenica', 'TIRRENICA');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `localita`
--
ALTER TABLE `localita`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_localita_usca` (`id_usca`);

--
-- Indici per le tabelle `usca`
--
ALTER TABLE `usca`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `localita`
--
ALTER TABLE `localita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `usca`
--
ALTER TABLE `usca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `localita`
--
ALTER TABLE `localita`
  ADD CONSTRAINT `fk_localita_usca` FOREIGN KEY (`id_usca`) REFERENCES `usca` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
