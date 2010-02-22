-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 20. Februar 2010 um 17:47
-- Server Version: 5.1.37
-- PHP-Version: 5.2.10-2ubuntu6.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `flses`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flses_bankaccess`
--

CREATE TABLE IF NOT EXISTS `flses_bankaccess` (
  `userid` int(11) NOT NULL,
  `accountid` int(11) NOT NULL,
  `status` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`userid`,`accountid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `flses_bankaccess`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flses_bankaccounts`
--

CREATE TABLE IF NOT EXISTS `flses_bankaccounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `money` int(11) NOT NULL,
  `statement` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `flses_bankaccounts`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flses_chars`
--

CREATE TABLE IF NOT EXISTS `flses_chars` (
  `account` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file` varchar(26) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(26) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `money` int(11) DEFAULT NULL,
  `kills` int(11) NOT NULL,
  `missions` int(11) DEFAULT NULL,
  `group` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `systems` int(11) DEFAULT NULL,
  `bases` int(11) DEFAULT NULL,
  `holes` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `login` datetime DEFAULT NULL,
  PRIMARY KEY (`file`),
  KEY `account` (`account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `flses_chars`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flses_logs`
--

CREATE TABLE IF NOT EXISTS `flses_logs` (
  `user` int(11) NOT NULL,
  `plugin` text COLLATE utf8_bin NOT NULL,
  `action` text COLLATE utf8_bin NOT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `flses_logs`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flses_menu`
--

CREATE TABLE IF NOT EXISTS `flses_menu` (
  `parent` varchar(20) COLLATE utf8_bin NOT NULL,
  `name` varchar(20) COLLATE utf8_bin NOT NULL,
  `order` int(11) NOT NULL,
  `img` text COLLATE utf8_bin NOT NULL,
  `img_on` text COLLATE utf8_bin NOT NULL,
  `access` text COLLATE utf8_bin NOT NULL,
  `class` text COLLATE utf8_bin NOT NULL,
  `function` text COLLATE utf8_bin NOT NULL,
  KEY `parent` (`parent`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `flses_menu`
--

INSERT INTO `flses_menu` (`parent`, `name`, `order`, `img`, `img_on`, `access`, `class`, `function`) VALUES
('', 'Bank', 40, './images/subCommodityBlue.gif', '', '', 'Bank', 'overview'),
('Bank', 'Bank - Overview', 10, './images/subCommodityBlue.gif', '', '', 'Bank', 'overview'),
('', 'Admin', 60, '', '', 'admin=1', 'Admin', 'overview'),
('Home', 'Home - Overview', 10, 'images/home_off.gif', 'images/home_on.gif', '', 'Home', 'overview'),
('', 'Home', 10, 'images/home_off.gif', 'images/home_on.gif', '', 'Home', 'overview'),
('Admin', 'Plugins', 10, '', '', 'admin=1', 'Admin', 'plugins');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flses_plugins`
--

CREATE TABLE IF NOT EXISTS `flses_plugins` (
  `classname` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `url` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `class` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `active` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`classname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `flses_plugins`
--

INSERT INTO `flses_plugins` (`classname`, `url`, `class`, `active`) VALUES
('Home', './plugins/Home/Home.php', 'Home', 'true'),
('Bank', './plugins/Bank/Bank.php', 'Bank', 'true'),
('Login', './plugins/Login/Login.php', 'Login', 'true'),
('Guild', './plugins/Guild/guild.class.php', 'Guild', 'true'),
('Guild_admin', './plugins/Guild/guild.admin.class.php', 'Guild_admin', 'true'),
('Admin', './plugins/Admin/Admin.php', 'Admin', 'true'),
('Utils', './plugins/Utils/Utils.php', 'Utils', 'true'),
('messagingcontrol', './plugins/FLHook Classes/messagingcontrol.php', 'flhook', 'true'),
('playercontrol', './plugins/FLHook Classes/playercontrol.php', 'flhook', 'true'),
('servercontrol', './plugins/FLHook Classes/servercontrol.php', 'flhook', 'true'),
('cashcontrol', './plugins/FLHook Classes/cashcontrol.php', 'flhook', 'true'),
('cargocontrol', './plugins/FLHook Classes/cargocontrol.php', 'flhook', 'true'),
('bancontrol', './plugins/FLHook Classes/bancontrol.php', 'flhook', 'true'),
('admincontrol', './plugins/FLHook Classes/admincontrol.php', 'flhook', 'true');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flses_userchars`
--

CREATE TABLE IF NOT EXISTS `flses_userchars` (
  `uid` int(11) NOT NULL,
  `charfile` varchar(26) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `charname` varchar(26) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  UNIQUE KEY `uid` (`uid`,`charfile`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `flses_userchars`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flses_users`
--

CREATE TABLE `flses_users` (
  `id` int(11) NOT NULL auto_increment,
  `name` text character set utf8 collate utf8_bin NOT NULL,
  `password` text character set utf8 collate utf8_bin NOT NULL,
  `access` text character set utf8 collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `flses_bankstatements`
--

CREATE TABLE `flses_bankstatements` (
  `id` int(11) NOT NULL,
  `msg_id` int(11) NOT NULL auto_increment,
  `statement` text collate latin1_german2_ci NOT NULL,
  PRIMARY KEY  (`msg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci AUTO_INCREMENT=1 ;

