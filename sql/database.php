<?php

$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";

class DbConnect{
	
	protected static $db=null;

	private function __construct(){}

	public static function getInstance(){
		//configuration parameters:
		$config_params = Config::getConfigParams();
		$hostdb = $config_params['hostdb'];
		$database = $config_params['database'];
		$pwdb = $config_params['pwdb'];
		$userdb = $config_params['userdb']; 
		
		if ( self::$db==null ){
			//self::$db = new mysqli("173.201.217.33", "twetest", "Spearmint1", "twetest");
			self::$db = new mysqli($hostdb, $database, $pwdb, $userdb);
		}
		return self::$db;
	}
}

?>