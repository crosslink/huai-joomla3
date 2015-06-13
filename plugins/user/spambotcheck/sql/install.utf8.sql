CREATE TABLE IF NOT EXISTS `#__spambot_attempts` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', 
	`action` varchar(255), 
	`email` varchar(255), 
	`ip` varchar(15),
	`username` varchar(255), 
	`engine` varchar(255), 
	`request` varchar(255), 
	`raw_return` varchar(255), 
	`parsed_return` varchar(255), 
	`attempt_date` varchar(255),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

