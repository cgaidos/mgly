-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 14, 2017 at 12:06 PM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `moowgly` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `moowgly`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moowgly`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
CREATE TABLE IF NOT EXISTS `activity` (
  `act_code` char(3) NOT NULL,
  `act_en` varchar(255) NOT NULL,
  `act_cat_code` char(3) NOT NULL,
  `act_cat_en` varchar(255) NOT NULL,
  PRIMARY KEY (`act_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`act_code`, `act_en`, `act_cat_code`, `act_cat_en`) VALUES
('ate', 'Architecture', 'cul', 'Culture'),
('che', 'Chess', 'gam', 'Game'),
('dan', 'Danse', 'art', 'Art'),
('ftb', 'Football', 'spo', 'Sport'),
('pai', 'Painting', 'art', 'Art'),
('ski', 'Ski', 'spo', 'Sport');

-- --------------------------------------------------------

--
-- Table structure for table `certificate`
--

DROP TABLE IF EXISTS `certificate`;
CREATE TABLE IF NOT EXISTS `certificate` (
  `cer_code` char(3) NOT NULL,
  `cer_name` varchar(255) NOT NULL,
  PRIMARY KEY (`cer_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `certificate`
--

INSERT INTO `certificate` (`cer_code`, `cer_name`) VALUES
('eco', 'Guaranteed welfare'),
('jus', 'Judiciary approval'),
('moo', 'Moowgli agreement');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `country_code` char(2) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  PRIMARY KEY (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_code`, `country_name`) VALUES
('CH', 'Switzerland'),
('FR', 'France'),
('GE', 'Germany'),
('IT', 'Italy');

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
CREATE TABLE IF NOT EXISTS `currency` (
  `currency_code` char(3) NOT NULL,
  `currency_name` varchar(255) NOT NULL,
  PRIMARY KEY (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`currency_code`, `currency_name`) VALUES
('CHF', 'Switzerland franc'),
('EUR', 'Euro');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `language_code` varchar(10) NOT NULL,
  `language_name` varchar(255) NOT NULL,
  `iso_language` char(2) NOT NULL,
  PRIMARY KEY (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`language_code`, `language_name`, `iso_language`) VALUES
('eng', 'English', 'en'),
('fra', 'French', 'fr'),
('ger', 'German', 'de'),
('ita', 'Italian', 'it');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` char(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `family_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `profile_guest` enum('no','yes') NOT NULL DEFAULT 'no',
  `profile_host` enum('no','yes') NOT NULL DEFAULT 'no',
  `token` char(36) NOT NULL,
  `active` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `family_name`, `first_name`, `birth_date`, `profile_guest`, `profile_host`, `token`, `active`) VALUES
('51212e27-0288-11e7-a2ed-0090f5cd14da', 'gwendal@eptamel.com', '5f4dcc3b-5aa7-45d6-9d83-27deb882cf99', 'Dupond', 'Pierre', '1960-08-01', 'no', 'yes', '89d240c9-0d38-4441-b5df-001886c47c95', '1'),
('512134a0-0288-11e7-a2ed-0090f5cd14da', 'cgaidos@eptamel.com', '5f4dcc3b-5aa7-45d6-9d83-27deb882cf99', 'Gaidosky', 'Christosian', '1960-08-01', 'no', 'yes', '76890f93-9405-4676-94f5-adf2a0b2f831', '1'),
('9771c63b-f306-4220-85d6-c3c69e82b886', 'emiliemoysson@gmail.com', '5f4dcc3b-5aa7-45d6-9d83-27deb882cf99', 'Moysson', 'emilie', '1977-01-30', 'yes', 'no', '9771c63b-f306-4220-85d6-c3c69e82b886', '1'),
('bf785009-0281-11e7-a2ed-0090f5cd14da', 'anaxnake@gmail.com', '5f4dcc3b-5aa7-45d6-9d83-27deb882cf99', 'Gaidos', 'Christos', '1960-08-01', 'yes', 'yes', 'bf785009-0281-11e7-a2ed-0090f5cd14da', '1'),
('f259bb65-e0bb-4e87-afa4-5d7c132c066e', 'gwendaldugue@gmail.com', '5f4dcc3b-5aa7-45d6-9d83-27deb882cf99', 'Dugué', '   Gwendal', '1986-08-01', 'yes', 'no', 'f2e12c96-7b2c-4681-9a65-cfcfb49a6fc7', '1');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
