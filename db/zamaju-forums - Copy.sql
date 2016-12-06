-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2016 at 12:11 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `naija_so`
--

-- --------------------------------------------------------

--
-- Table structure for table `nso_users`
--

CREATE TABLE IF NOT EXISTS `nso_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login` varchar(60) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `date_registered` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `nso_users`
--

INSERT INTO `nso_users` (`id`, `login`, `password`, `date_registered`) VALUES
(3, 'orji4y@gmail.com', '1fb889f5d6e72d85870c8d218a787a67', '2016-06-04 19:31:40'),
(4, 'orji4y@hotmail.com', '1fb889f5d6e72d85870c8d218a787a67', '2016-06-05 16:42:13'),
(9, 'orji4y@yahoo.com', 'ee585d40fd52b1b86480262a9345473a', '2016-07-03 11:06:04');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
