<?php 

if (isset ( $argc ) && ($argc > 0)) {
	$docroot = "/var/www/vhosts/twextra.com/httpdocs";
}else{
	$docroot = $_SERVER ['DOCUMENT_ROOT'];
}

require_once $docroot . "/config.php";
//require_once $docroot."/tw.lib.php";
//require_once $docroot . "/models/twextra_model.php";

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

backupDbTwToken($database_info, $create_tw_token_table_stmt);
sleep(5);
//backupDbMessage($database_info, $create_msg_table_stmt);

//--------------------------------------------------------------------
function backupDbMessage($database_info, $create_msg_table_stmt) {

		
		global $docroot;

		//get data base connection;
		$link = DbConnect::getInstance ();
		
		$command = "SET NAMES 'utf8'";
		$stmt = mysqli_prepare ( $link, $command );
		mysqli_stmt_execute ( $stmt );

		

		$command = "select count(*) from message";

		if ($stmt = mysqli_prepare ( $link, $command )) {

			//mysqli_stmt_bind_param($stmt, 'i', $wid);

			mysqli_stmt_execute ( $stmt );

			//mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result ( $stmt, $total_row_count );

			mysqli_stmt_fetch ( $stmt );
		}

		mysqli_stmt_close ( $stmt );
		
		$link = DbConnect::getInstance ();
		
		$command = "SET NAMES 'utf8'";
		$stmt = mysqli_prepare ( $link, $command );
		mysqli_stmt_execute ( $stmt );
		
		echo "total row count- message: ", $total_row_count;
		

		$chunk_size = 10;

		$offset = 0;
		$data = '';
		$data .= $database_info;
		$data .= $create_msg_table_stmt;
		$timestamp = date("Y_m_d_H_i_s");

		

		$link = getInstance ();

		

		while ( $offset < $total_row_count ) {

			$command = "select id, tweet, url, created, view_count, last_viewed, remote_ip, blocked, tid, user_id, created_date, 
								message_id_reply, prefix
	 					from message order by created asc limit $offset, $chunk_size";


			if ($stmt = mysqli_prepare ( $link, $command )) {

				//mysqli_stmt_bind_param($stmt, 'i', $wid);

				mysqli_stmt_execute ( $stmt );

				//mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result ( $stmt, $id, $tweet, $url, $created, $view_count, $last_viewed, $remote_ip, $blocked, $tid, 
				                          $user_id, $created_date, $message_id_reply, $prefix );

				
				 $tweet = mysqli_real_escape_string($link, $tweet);
				 $prefix = mysqli_real_escape_string($link, $prefix);

				

				$data .= " insert ignore into message(id, tweet, url, created, view_count, last_viewed, remote_ip, blocked, tid, user_id, created_date, 
			message_id_reply, prefix ) values ";

				while ( mysqli_stmt_fetch ( $stmt ) ) {

					$id = empty ( $id ) ? 0 : $id;

					$tid = empty ( $tid ) ? 0 : $tid;

					$data .= "($id,'$tweet','$url','$created',$view_count,$last_viewed,'$remote_ip',$blocked,$tid,'$user_id','$created_date',
                           '$message_id_reply','$prefix'),";

				}

				$data = substr ( $data, 0, - 1 );
				$data .= ";\n";

				

			} else {

				echo "<br>error";

			}
			$offset = $offset + $chunk_size;
		}

		

		

		//file_put_contents ( "db_backup_$timestamp.txt", $data, FILE_APPEND );

		file_put_contents ( $docroot."/backup_db/backup_db_message_$timestamp.sql", $data);

		echo "--end";

		
		mysqli_stmt_close ( $stmt );

		return;

	}

//.......................................................................................	

	function backupDbTwToken($database_info, $create_tw_token_table_stmt) {

		global $docroot;

		//get data base connection;
		$link = getInstance ();

		$command = "SET NAMES 'utf8'";

		$stmt = mysqli_prepare ( $link, $command );

		mysqli_stmt_execute ( $stmt );
		
		$command = "select count(*) from tw_token";
		if ($stmt = mysqli_prepare ( $link, $command )) {
			//mysqli_stmt_bind_param($stmt, 'i', $wid);
			mysqli_stmt_execute ( $stmt );
			//mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result ( $stmt, $total_row_count );
			mysqli_stmt_fetch ( $stmt );
		}
		mysqli_stmt_close ( $stmt );
		
		$link = DbConnect::getInstance ();
		
		$command = "SET NAMES 'utf8'";
		$stmt = mysqli_prepare ( $link, $command );
		mysqli_stmt_execute ( $stmt );
		
		echo "total row count- tw_token: ", $total_row_count;
		
		$total_row_count = 20;
		
		$chunk_size = 10;
		$offset = 0;
		$data = '';
		$data .= $database_info;
		$data .= $create_tw_token_table_stmt;
		$timestamp = date("Y_m_d_H_i_s");
		
		$link = DbConnect::getInstance ();
		
		while ( $offset < $total_row_count ) {
			$command = "select tid, user_id_hash, oauth_token, oauth_token_secret, oauth_verifier, user_id, 
			                   screen_name, name, location, description, user_image_url, created
	 					from tw_token order by created asc limit $offset, $chunk_size";

			if ($stmt = mysqli_prepare ( $link, $command )) {
				//mysqli_stmt_bind_param($stmt, 'i', $wid);
				mysqli_stmt_execute ( $stmt );
				//mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result ( $stmt, $tid, $user_id_hash, $oauth_token, $oauth_token_secret, $oauth_verifier, $user_id, 
				                          $screen_name, $name, $location, $description, $user_image_url, $created);
				
				 $screen_name = mysqli_real_escape_string($link, $screen_name);
				 $name = mysqli_real_escape_string($link, $name);
				 $location = mysqli_real_escape_string($link, $location);
				 $description = mysqli_real_escape_string($link, $description);
				
				$data .= " insert ignore into tw_token(tid, user_id_hash, oauth_token, oauth_token_secret, oauth_verifier, user_id, 
			                   screen_name, name, location, description, user_image_url, created ) values ";
				while ( mysqli_stmt_fetch ( $stmt ) ) {
					$id = empty ( $id ) ? 0 : $id;
					$tid = empty ( $tid ) ? 0 : $tid;
					$data .= "($tid, '$user_id_hash', '$oauth_token', '$oauth_token_secret', '$oauth_verifier', $user_id, 
				                          '$screen_name', '$name', '$location', '$description', '$user_image_url', '$created'),";
				}
				$data = substr ( $data, 0, - 1 );
				$data .= ";\n";
				
			} else {
				echo "<br>error";
			}
			$offset = $offset + $chunk_size;
		}
		
		
		//file_put_contents ( "db_backup_$timestamp.txt", $data, FILE_APPEND );
		file_put_contents ( $docroot."/backup_db/backup_db_tw_token_$timestamp.sql", $data);
		//file_put_contents('junk.sql','test');
		//file_put_contents ( "backup_db_tw_token.sql", $data);
		echo "--end";
		
		mysqli_stmt_close ( $stmt );
		return;
	}	
//-----------------------------------------------------------------
	function getInstance(){
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$hostdb = $config_params['hostdb'];
		$database = $config_params['database'];
		$pwdb = $config_params['pwdb'];
		$userdb = $config_params['userdb']; 
		
		$db = new mysqli($hostdb, $database, $pwdb, $userdb);
		
		return $db;
	}
//-----------------------------------------------------------------

	
?>
	
