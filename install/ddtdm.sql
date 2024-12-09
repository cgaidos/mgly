-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2016 at 02:47 PM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: ddtdm
--
CREATE DATABASE IF NOT EXISTS ddtdm DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE ddtdm;

-- --------------------------------------------------------

--
-- Table structure for table silo
--

DROP TABLE IF EXISTS silo;
CREATE TABLE IF NOT EXISTS silo (
  id int(2) NOT NULL AUTO_INCREMENT,
  dollar char(3) NOT NULL DEFAULT 'XXX',
  tete int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Dumping data for table silo
--

INSERT INTO silo (id, dollar, tete) VALUES
(1, 'ZZZ', 88),
(2, 'BBB', 20);
