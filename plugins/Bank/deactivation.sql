-- Delete Plugin Specific Tables --

DROP TABLE `flses_bankaccess`, `flses_bankaccounts`;

-- Delete Menu entrys --

DELETE FROM `flses_menue` WHERE `name` = 'Bank' LIMIT 1;
DELETE FROM `flses_menue` WHERE `name` = 'Bank - Overview' LIMIT 1;
DELETE FROM `flses_menue` WHERE `name` = 'Bank - Manage' LIMIT 1;