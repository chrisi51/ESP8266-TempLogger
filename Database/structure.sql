-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               8.0.16-7 - Percona Server (GPL), Release '7', Revision '613e312'
-- Server Betriebssystem:        debian-linux-gnu
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportiere Datenbank Struktur für tempdata
CREATE DATABASE IF NOT EXISTS `tempdata` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `tempdata`;

-- Exportiere Struktur von View tempdata.all
-- Erstelle temporäre Tabelle um View Abhängigkeiten zuvorzukommen
CREATE TABLE `all` 
) ENGINE=MyISAM;

-- Exportiere Struktur von Tabelle tempdata.data
CREATE TABLE IF NOT EXISTS `data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mac` varchar(128) DEFAULT NULL,
  `temp` float NOT NULL COMMENT 'Temperatur',
  `humidity` float NOT NULL COMMENT 'Luftfeuchte',
  `tempindex` float NOT NULL COMMENT 'Temperatur Index',
  `taupunkt` float NOT NULL COMMENT 'Taupunkt',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=159999 DEFAULT CHARSET=latin1;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle tempdata.sensors
CREATE TABLE IF NOT EXISTS `sensors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `mac` varchar(256) DEFAULT NULL,
  `kommentar` text,
  `raum` text,
  `firmware` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von View tempdata.all
-- Entferne temporäre Tabelle und erstelle die eigentliche View
DROP TABLE IF EXISTS `all`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `all` AS select `s`.`name` AS `name`,`s`.`mac` AS `mac`,`a`.`datum` AS `datum`,`a`.`temp` AS `temp`,`a`.`humidity` AS `humidity`,`a`.`tempindex` AS `tempindex`,`a`.`taupunkt` AS `taupunkt` from (`analog_data` `a` left join `sensors` `s` on((convert(`a`.`mac` using utf8mb4) = `s`.`mac`)));

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
