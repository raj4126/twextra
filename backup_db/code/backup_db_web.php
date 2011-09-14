<?php 

if (isset ( $argc ) && ($argc > 0)) {
	$docroot = "/var/www/vhosts/twextra.com/httpdocs";
}else{
	$docroot = $_SERVER ['DOCUMENT_ROOT'];
}

$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
require_once $docroot."/tw.lib.php";
require_once $docroot . "/models/twextra_model.php";

validate_access_twetest();

$database = "twextradb";

$database_info = "create database IF NOT EXISTS $database;
                  use $database;";

$create_tw_token_table_stmt = "
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
";

$create_msg_table_stmt = "
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
";

$model = new TwextraModel();
//$model->backupDbTwToken($database_info, $create_tw_token_table_stmt);
sleep(5);
$model->backupDbMessage($database_info, $create_msg_table_stmt);
	
?>
	
