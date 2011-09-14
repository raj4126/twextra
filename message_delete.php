<?php 
if (session_id () == "") session_start ();
$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
require_once $docroot . "/models/twextra_model.php";
require_once $docroot . "/controllers/twextra_controller.php";
require_once $docroot . "/tw.lib.php";

//validate access;
validate_access_twetest();

delete_message();

function delete_message() {
	$screen_name = $_SESSION ['user'];
	//configuration parameters:
	$config_params = Config::getConfigParams ();
	$css = $config_params ['css'];
	$tweet_size_max = $config_params ['tweet_size_max'];
	$tweet_size_max_google = $config_params ['tweet_size_max_google'];
	$hostname = $config_params ['hostname'];
	$docroot = $config_params ['docroot'];
	$debug = $config_params ['debug'];
	$enable_stats = $config_params ['enable_stats'];
	
	$script_path = __FUNCTION__;
	
	$message_list_delete = $_REQUEST['delete'];
	
	$model = new TwextraModel();
	$model->deleteTweetList($message_list_delete);
	
	header ( "Location:$hostname/displayMessageHistory.php" );
	exit ();
}
?>