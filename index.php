<?php 

$docroot = $_SERVER['DOCUMENT_ROOT'];
require_once $docroot . "/tw.lib.php";
$tweet = '';
$error='';
if (isset ( $_REQUEST ['message_id'] )) {
	$message_id = $_REQUEST ['message_id'];
} else {
	$message_id = '';
}
index($tweet, $error, $message_id);
?>