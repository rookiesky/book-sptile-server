# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.7.21)
# Database: reptile
# Generation Time: 2018-12-13 04:29:56 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table book
# ------------------------------------------------------------

DROP TABLE IF EXISTS `book`;

CREATE TABLE `book` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `author` varchar(30) NOT NULL DEFAULT '' COMMENT '作者',
  `sort` int(11) NOT NULL COMMENT '分类',
  `thumb` varchar(200) DEFAULT 'null',
  `summary` tinytext,
  `view` int(11) NOT NULL DEFAULT '0' COMMENT '点击量',
  `add_day` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table book_contents
# ------------------------------------------------------------

DROP TABLE IF EXISTS `book_contents`;

CREATE TABLE `book_contents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '',
  `book_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `sequence` int(11) NOT NULL COMMENT '排序',
  `add_day` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table book_reptile_counter
# ------------------------------------------------------------

DROP TABLE IF EXISTS `book_reptile_counter`;

CREATE TABLE `book_reptile_counter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table book_reptile_link_yes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `book_reptile_link_yes`;

CREATE TABLE `book_reptile_link_yes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(200) NOT NULL DEFAULT '',
  `book_id` int(11) NOT NULL,
  `add_day` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table book_reptile_list_error
# ------------------------------------------------------------

DROP TABLE IF EXISTS `book_reptile_list_error`;

CREATE TABLE `book_reptile_list_error` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '',
  `book_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `add_day` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table book_reptile_list_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `book_reptile_list_link`;

CREATE TABLE `book_reptile_list_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(220) NOT NULL DEFAULT '',
  `book_id` int(11) NOT NULL,
  `title` varchar(190) NOT NULL DEFAULT '',
  `number` int(11) NOT NULL,
  `lock` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table book_reptile_list_rehear
# ------------------------------------------------------------

DROP TABLE IF EXISTS `book_reptile_list_rehear`;

CREATE TABLE `book_reptile_list_rehear` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `link` varchar(200) NOT NULL DEFAULT '',
  `book_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `counter` int(11) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table book_reptile_rehear
# ------------------------------------------------------------

DROP TABLE IF EXISTS `book_reptile_rehear`;

CREATE TABLE `book_reptile_rehear` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(200) NOT NULL DEFAULT '',
  `msg` tinytext NOT NULL,
  `number` int(11) NOT NULL DEFAULT '3',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table book_sort
# ------------------------------------------------------------

DROP TABLE IF EXISTS `book_sort`;

CREATE TABLE `book_sort` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL DEFAULT '',
  `sequence` int(11) NOT NULL DEFAULT '10' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
