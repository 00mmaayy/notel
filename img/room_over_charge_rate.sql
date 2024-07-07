-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2022 at 12:44 PM
-- Server version: 5.5.10
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `notel`
--

-- --------------------------------------------------------

--
-- Table structure for table `room_over_charge_rate`
--

CREATE TABLE IF NOT EXISTS `room_over_charge_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `over_rate` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `room_over_charge_rate`
--

INSERT INTO `room_over_charge_rate` (`id`, `over_rate`) VALUES
(1, 1.66666666667);
