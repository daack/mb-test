CREATE TABLE `messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) NOT NULL,
  `originator` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `chunks` int(11) DEFAULT '0',
  `udh_uid` varchar(2) DEFAULT NULL,
  `sent` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) CHARSET=utf8;
