<?php

$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
//configuration parameters:
$config_params = Config::getConfigParams();
$cookie_name = $config_params['cookie_name'];
$hostname = $config_params['hostname'];

//delete cookie
   //setcookie('tw_tok', '', time()-3600, "/");
   setcookie($cookie_name, '', time()-3600, "/");
   setcookie ( $cookie_name, '', time () - 3600 * 24 * 14, "/");
  //unset($_COOKIE['tw_tok']);
  unset($_COOKIE[$cookie_name]);

   //destroy session state, and session
   unset($_SESSION['user']);
   unset($_SESSION['tid']);
	session_unset();
	if (session_id() != ""){
		session_destroy();
	}
	
header("Location: $hostname/router.php?route=signout");
exit;
?>