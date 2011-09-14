<?php

if (session_id () == "") session_start ();

$docroot = $_SERVER ['DOCUMENT_ROOT'];

require_once $docroot . "/models/twextra_model.php";
require_once $docroot . "/tw.lib.php";

if($_REQUEST['auth'] !=='raj4126'){
	echo "Access denied!";
	exit;
}

//validate_access_admin();

$remote_address = $_SERVER['REMOTE_ADDR'];

$docroot = $_SERVER ['DOCUMENT_ROOT'];

require_once $docroot . "/config.php";

$config_params = Config::getConfigParams();

$hostname = $config_params['hostname'];
//----------------------------------------------
$page = "<html>

<h3>Twextra Daily & Monthly Stats</h3>

<img src='$hostname/graphs/phpgraphlib/graphs.lib.php?graph_type=daily_stats' />
<img src='$hostname/graphs/phpgraphlib/graphs.lib.php?graph_type=messages_stats' />
<img src='$hostname/graphs/phpgraphlib/graphs.lib.php?graph_type=monthly_stats' />";

$page .= "</html>";

echo $page;
?>