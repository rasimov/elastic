-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2013 at 07:40 
-- Server version: 5.1.41
-- PHP Version: 5.3.1

CREATE SCHEMA IF NOT EXISTS example;

USE example;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `example`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET cp1251,
  `content` longtext CHARACTER SET cp1251 NOT NULL,
  `publish_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `short_description` mediumtext CHARACTER SET cp1251 NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=530 ;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `name`, `content`, `publish_date`, `short_description`, `user_id`) VALUES
(529, 'test12', '    Help screens may have reference items on them that lead to another help\r\nscreen. Also, the main page has the "~Help Index~@Index@", which lists all the\r\ntopics available in the help file and in some cases helps to find the needed\r\ninformation faster.\r\n\r\n    You may use #Tab# and #Shift-Tab# keys to move the cursor from one\r\nreference item to another, then press #Enter# to go to a help screen describing\r\nthat item. With the mouse, you may click a reference to go to the help screen\r\nabout that item.\r\n\r\n    If text does not completely fit in the help window, a scroll bar is\r\ndisplayed. In such case #cursor keys# can be used to scroll text.\r\n', '2011-03-28 13:36:34', 'short description 12', 3),
(462, 'test 2 ', '    Press #Shift-F2# for ~plugins~@Plugins@ help.\r\n\r\n    #Help# is shown by default in a reduced windows, you can maximize it by\r\npressing #F5# "#Zoom#", pressing #F5# again will restore the window to the\r\nprevious size.\r\n', '2011-03-22 15:00:00', 'short description 233', 4),
(463, 'test3', ' Redistribution and use in source and binary forms, with or without\r\nmodification, are permitted provided that the following conditions\r\nare met:\r\n 1. ^<wrap>Redistributions of source code must retain the above copyright\r\nnotice, this list of conditions and the following disclaimer.\r\n 2. Redistributions in binary form must reproduce the above copyright\r\nnotice, this list of conditions and the following disclaimer in the\r\ndocumentation and/or other materials provided with the distribution.\r\n 3. The name of the authors may not be used to endorse or promote products\r\nderived from this software without specific prior written permission.\r\n', '2011-03-22 22:00:00', 'short description 3r', 3);

--
-- Triggers `documents`
--
DROP TRIGGER IF EXISTS `DocumentInsert`;
DELIMITER //
CREATE TRIGGER `DocumentInsert` AFTER INSERT ON `documents`
 FOR EACH ROW BEGIN
    INSERT IGNORE INTO queue SET id = NEW.id;
  END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `DocumentUpdate`;
DELIMITER //
CREATE TRIGGER `DocumentUpdate` BEFORE UPDATE ON `documents`
 FOR EACH ROW BEGIN
    INSERT IGNORE INTO queue SET id = NEW.id;
  END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

DROP TABLE IF EXISTS `queue`;
CREATE TABLE IF NOT EXISTS `queue` (
  `id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `queue`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL DEFAULT '0',
  `username` varchar(255) CHARACTER SET cp1251 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`) VALUES
(3, 'Firstname1 Lastname1'),
(4, 'Firstname2 Lastname2');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
