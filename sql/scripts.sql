--message(id, tweet, url, created, view_count, last_viewed, remote_ip, blocked, tid, user_id, created_date, message_id_reply, prefix ) 
--values (0,'hello from curl','5f6l87',2010-05-25 17:04:53,0,,'173.201.185.107',0,0,'',,'','hello from curl... ')
----------------------------------------------------------------------------------------------------------------- 

--SEE backup_db.php for more recent table definitions<<<<<<<<<<<<
create database IF NOT EXISTS twextradb;

use twextradb;
--------------------------------------------------
--Sept 21, 2010 (Twextra)
CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) unsigned NOT NULL default '0',
  `tweet` text collate utf8_bin NOT NULL,
  `url` varchar(255) collate utf8_bin NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `view_count` int(11) unsigned NOT NULL default '0',
  `last_viewed` int(10) unsigned NOT NULL default '0',
  `remote_ip` char(16) collate utf8_bin default '000.000.000.000',
  `blocked` smallint(6) default '0',
  `tid` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `created_date` date NOT NULL default '0000-00-00',
  `message_id_reply` varchar(6) collate utf8_bin NOT NULL default '''''',
  `prefix` varchar(140) character set utf8 NOT NULL default '''''',
  `deleted` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`url`),
  KEY `created_date` (`created_date`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-------------------------------------------------------
--Sept 21, 2010 (Twextra)
CREATE TABLE IF NOT EXISTS `tw_token` (
  `tid` int(10) unsigned NOT NULL default '0',
  `user_id_hash` varchar(100) NOT NULL,
  `oauth_token` varchar(100) NOT NULL default '''''',
  `oauth_token_secret` varchar(100) NOT NULL default '''''',
  `oauth_verifier` varchar(50) NOT NULL default '''''',
  `user_id` int(10) unsigned NOT NULL,
  `screen_name` varchar(50) NOT NULL default '''''',
  `name` varchar(25) NOT NULL default '''''',
  `location` varchar(40) NOT NULL default '''''',
  `description` varchar(200) NOT NULL default '''''',
  `user_image_url` varchar(255) NOT NULL default '''''',
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_id_hash` (`user_id_hash`),
  UNIQUE KEY `oauth_token` (`oauth_token`),
  UNIQUE KEY `screen_name` (`screen_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-------------------------------------------------------
--Sept 21, 2010 (Twextra)
CREATE TABLE `stat` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `remote_ip` varchar(20) NOT NULL,
  `access_date` date NOT NULL,
  `access_timestamp` timestamp NULL default CURRENT_TIMESTAMP,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=240442 DEFAULT CHARSET=utf8 AUTO_INCREMENT=240442 ;
---------------------------------------------------------------------------
--Sept 21, 2010 (Twextra)
CREATE TABLE IF NOT EXISTS `api_auth` (
  `email` varchar(100) collate utf8_bin default NULL,
  `first_name` varchar(50) collate utf8_bin default NULL,
  `last_name` varchar(50) collate utf8_bin default NULL,
  `user_id` int(10) unsigned default NULL,
  `phone` varchar(20) collate utf8_bin default NULL,
  `application` varchar(200) collate utf8_bin default NULL,
  `company` varchar(200) collate utf8_bin default NULL,
  `api_key` int(10) unsigned NOT NULL,
  `api_key_hash` char(40) character set ascii default NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`api_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;
-------------------------------------------------------
--Sept 21, 2010 (Twextra)
CREATE TABLE IF NOT EXISTS `api_auth_daily_count` (
  `api_key` int(10) unsigned NOT NULL,
  `daily_count` int(10) default NULL,
  `daily_max` int(10) unsigned NOT NULL default 1000,
  `updated` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`api_key`,`updated`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-------------------------------------------------------
--CREATE TABLE `sessions` (
--  `session_id` varchar(100) NOT NULL default '',
--  `session_data` text NOT NULL,
--  `expires` int(11) NOT NULL default '0',
--  PRIMARY KEY  (`session_id`)
--) TYPE=MyISAM;
-------------------------------------------------------


