-- Insert Plugin Specific DB Tables --

CREATE TABLE IF NOT EXISTS `flses_bankaccess` (
  `userid` int(11) NOT NULL,
  `accountid` int(11) NOT NULL,
  `status` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`userid`,`accountid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `flses_bankaccounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `money` int(11) NOT NULL,
  `statement` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- Generate the Menue Entrys --

INSERT INTO `flses_menu` (`parent`, `name`, `order`, `img`, `img_on`, `access`, `class`, `function`) VALUES
('', 'Bank', 10, './images/subCommodityBlue.gif', '', '', 'Bank', 'overview'),
('Bank', 'Bank - Overview', 1, './images/subCommodityBlue.gif', '', '', 'Bank', 'overview');

-- Plugin Entrys --

INSERT INTO `flses_plugins` (`classname`, `url`, `class`, `active`) VALUES
('Bank', './plugins/Bank/Bank.php', 'Bank', 'true');