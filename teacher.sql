-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2015 at 01:57 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `teacher`
--

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE IF NOT EXISTS `quiz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `desc` text COLLATE utf8_unicode_ci,
  `score_mastered` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id`, `url`, `name`, `desc`, `score_mastered`) VALUES
(1, '/learning_keys1.html', 'learning keys 1', NULL, 70),
(2, '/counting1.html', 'basic counting 1 to 9', NULL, 70);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_gives_skill`
--

CREATE TABLE IF NOT EXISTS `quiz_gives_skill` (
  `quiz` int(11) NOT NULL,
  `skill` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) NOT NULL,
  KEY `quiz` (`quiz`),
  KEY `skill` (`skill`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `quiz_gives_skill`
--

INSERT INTO `quiz_gives_skill` (`quiz`, `skill`, `level`) VALUES
(1, 'typing', 5),
(2, 'counting', 5);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_needs_skill`
--

CREATE TABLE IF NOT EXISTS `quiz_needs_skill` (
  `quiz` int(11) NOT NULL,
  `skill` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) NOT NULL,
  KEY `quiz` (`quiz`),
  KEY `skill` (`skill`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `quiz_needs_skill`
--

INSERT INTO `quiz_needs_skill` (`quiz`, `skill`, `level`) VALUES
(2, 'typing', 3);

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE IF NOT EXISTS `result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student` int(11) NOT NULL,
  `quiz` int(11) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_taken` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `student` (`student`),
  KEY `quiz` (`quiz`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=33 ;

--
-- Dumping data for table `result`
--

INSERT INTO `result` (`id`, `student`, `quiz`, `datetime`, `time_taken`, `score`) VALUES
(1, 1, 1, '2015-08-13 21:46:05', 1, 10),
(2, 1, 1, '2015-08-13 21:47:21', 1, 10),
(4, 1, 1, '2015-08-13 21:49:41', 1, 10),
(8, 1, 1, '2015-08-13 21:53:16', 1, 1),
(9, 1, 1, '2015-08-13 21:53:41', 1, 1),
(10, 1, 1, '2015-08-13 21:54:04', 1, 1),
(11, 1, 1, '2015-08-13 21:58:25', 0, 1),
(12, 1, 1, '2015-08-13 22:07:15', 0, 1),
(13, 1, 1, '2015-08-13 22:20:00', 0, 1),
(14, 1, 1, '2015-08-13 22:23:09', 0, 1),
(15, 1, 1, '2015-08-13 22:24:30', 0, 1),
(16, 1, 1, '2015-08-13 22:26:21', 0, 1),
(17, 1, 1, '2015-08-13 22:26:31', 0, 1),
(18, 1, 1, '2015-08-13 22:26:48', 0, 1),
(19, 1, 1, '2015-08-13 22:30:05', 0, 1),
(20, 1, 1, '2015-08-13 22:35:52', 8, 1),
(21, 1, 1, '2015-08-13 22:37:42', 79, 1),
(22, 1, 1, '2015-08-13 23:05:18', 53, 101),
(23, 1, 1, '2015-08-13 23:09:56', 22, 102),
(24, 1, 1, '2015-08-13 23:10:48', 22, 254),
(25, 1, 1, '2015-08-13 23:41:41', 10, 271),
(26, 1, 1, '2015-08-13 23:42:19', 8, 748),
(32, 1, 2, '2015-08-13 23:56:31', 30, 455);

-- --------------------------------------------------------

--
-- Table structure for table `skill`
--

CREATE TABLE IF NOT EXISTS `skill` (
  `skill` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`skill`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `skill`
--

INSERT INTO `skill` (`skill`, `description`) VALUES
('counting', 'basic counting'),
('letters', 'know about letters'),
('typing', 'typing on a keyboard');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE IF NOT EXISTS `student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `name`, `date_of_birth`) VALUES
(1, 'Conrad Henderson', '2012-12-07');

-- --------------------------------------------------------

--
-- Table structure for table `student_has_skill`
--

CREATE TABLE IF NOT EXISTS `student_has_skill` (
  `student` int(11) NOT NULL,
  `skill` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) NOT NULL,
  KEY `student` (`student`,`skill`),
  KEY `skill` (`skill`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quiz_gives_skill`
--
ALTER TABLE `quiz_gives_skill`
  ADD CONSTRAINT `quiz` FOREIGN KEY (`quiz`) REFERENCES `quiz` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_needs_skill`
--
ALTER TABLE `quiz_needs_skill`
  ADD CONSTRAINT `quiz_needs_skill_skill` FOREIGN KEY (`skill`) REFERENCES `skill` (`skill`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_needs_skill_quiz` FOREIGN KEY (`quiz`) REFERENCES `quiz` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `result`
--
ALTER TABLE `result`
  ADD CONSTRAINT `result_quiz` FOREIGN KEY (`quiz`) REFERENCES `quiz` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `result_student` FOREIGN KEY (`student`) REFERENCES `student` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_has_skill`
--
ALTER TABLE `student_has_skill`
  ADD CONSTRAINT `student_has_skill_skill` FOREIGN KEY (`skill`) REFERENCES `skill` (`skill`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_has_skill_student` FOREIGN KEY (`student`) REFERENCES `student` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
